<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use Tests\TestCase;

/**
 * The orders page runs one large inline script. A browser ends that block at
 * the first "</script>" it meets, even one sitting inside a JavaScript string,
 * so a stray closing tag silently cuts the script in half. When that happened
 * none of it ran: signed-in staff were left on the login screen and customers
 * could not sign in, while every server-side test still passed.
 */
class OrdersPageScriptTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_whole_script_reaches_the_browser_for_a_guest(): void
    {
        $script = $this->inlineScript($this->get('/orders')->assertOk()->getContent());

        $this->assertStringContainsString("\ninit();", $script);
    }

    public function test_the_whole_script_reaches_the_browser_for_staff(): void
    {
        $staff = User::factory()->create(['role' => 'cashier']);
        $this->withSession(['staff_user_id' => $staff->id]);

        $script = $this->inlineScript($this->get('/orders')->assertOk()->getContent());

        $this->assertStringContainsString("\ninit();", $script);
    }

    public function test_the_script_is_valid_javascript(): void
    {
        $node = (new ExecutableFinder())->find('node');
        if (! $node) {
            $this->markTestSkipped('Node.js is needed to parse the script.');
        }

        // Node only checks files it recognises, so the name must end in .js.
        $file = sys_get_temp_dir().DIRECTORY_SEPARATOR.'orders-script-'.uniqid().'.js';
        file_put_contents($file, $this->inlineScript($this->get('/orders')->getContent()));

        $check = new Process([$node, '--check', $file]);
        $check->run();
        unlink($file);

        $this->assertTrue($check->isSuccessful(), $check->getErrorOutput());
    }

    /** The inline script exactly as a browser's HTML parser delimits it. */
    private function inlineScript(string $html): string
    {
        $this->assertSame(1, preg_match('/<script nonce="[^"]*">/', $html, $open, PREG_OFFSET_CAPTURE));
        $start = $open[0][1] + strlen($open[0][0]);

        return substr($html, $start, strpos($html, '</script>', $start) - $start);
    }
}
