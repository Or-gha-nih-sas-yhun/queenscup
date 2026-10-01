<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password - The Queen's Cup</title>
    <style>
        body{margin:0;min-height:100vh;display:grid;place-items:center;background:#f4faf6;color:#123524;font-family:Arial,sans-serif}
        .card{width:min(420px,calc(100% - 40px));padding:32px;background:#fff;border:1px solid #d8ebdf;border-radius:16px;box-shadow:0 18px 50px #12352418}
        h1{margin:0 0 8px;font-size:24px}p{color:#607568;font-size:14px;line-height:1.5}
        label{display:block;margin:22px 0 7px;font-size:12px;font-weight:bold;text-transform:uppercase}
        input{box-sizing:border-box;width:100%;padding:12px;border:1px solid #cfe2d5;border-radius:8px;font-size:14px}
        button{width:100%;margin-top:18px;padding:13px;border:0;border-radius:8px;background:#12864e;color:#fff;font-weight:bold;cursor:pointer}
        a{display:block;margin-top:18px;text-align:center;color:#12864e;font-size:13px}
        .message{display:none;margin-top:16px;padding:11px;border-radius:8px;background:#e9f8ee;color:#17683d;font-size:13px}
        .error{color:#a3234f}
    </style>
</head>
<body>
<main class="card">
    <h1>Forgot your password?</h1>
    <p>Enter your customer account email and we’ll send you a secure reset link.</p>
    <form id="forgotForm">
        @csrf
        <label for="email">Email address</label>
        <input id="email" name="email" type="email" required autocomplete="email">
        <button type="submit">Send reset link</button>
    </form>
    <div class="message" id="message"></div>
    <a href="{{ url('/orders') }}">Back to customer sign in</a>
</main>
<script>
document.getElementById('forgotForm').addEventListener('submit', function (event) {
    event.preventDefault();
    fetch('{{ route('customer.password.email') }}', {
        method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body:JSON.stringify({email:document.getElementById('email').value})
    }).then(function(response){return response.json().then(function(data){return {ok:response.ok,data:data};});})
      .then(function(result){
          var message=document.getElementById('message');
          message.textContent=result.ok ? result.data.message : ((result.data.errors && result.data.errors.email && result.data.errors.email[0]) || 'Please enter a valid email address.');
          message.className='message'+(result.ok?'':' error'); message.style.display='block';
      });
});
</script>
</body>
</html>
