<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - The Queen's Cup</title>
    <style>
        body{margin:0;min-height:100vh;display:grid;place-items:center;background:#f4faf6;color:#123524;font-family:Arial,sans-serif}
        .card{width:min(420px,calc(100% - 40px));padding:32px;background:#fff;border:1px solid #d8ebdf;border-radius:16px;box-shadow:0 18px 50px #12352418}
        h1{margin:0 0 8px;font-size:24px}p{color:#607568;font-size:14px;line-height:1.5}
        label{display:block;margin:18px 0 7px;font-size:12px;font-weight:bold;text-transform:uppercase}
        input{box-sizing:border-box;width:100%;padding:12px;border:1px solid #cfe2d5;border-radius:8px;font-size:14px}
        button{width:100%;margin-top:20px;padding:13px;border:0;border-radius:8px;background:#12864e;color:#fff;font-weight:bold;cursor:pointer}
        a{display:block;margin-top:18px;text-align:center;color:#12864e;font-size:13px}
        .message{display:none;margin-top:16px;padding:11px;border-radius:8px;background:#e9f8ee;color:#17683d;font-size:13px}
        .error{background:#fff0f3;color:#a3234f}
    </style>
</head>
<body>
<main class="card">
    <h1>Set a new password</h1>
    <p>Choose a new password with at least 8 characters.</p>
    <form id="resetForm">
        @csrf
        <input type="hidden" id="token" value="{{ $token }}">
        <label for="email">Email address</label>
        <input id="email" type="email" value="{{ $email }}" required autocomplete="email">
        <label for="password">New password</label>
        <input id="password" type="password" required minlength="8" autocomplete="new-password">
        <label for="confirmation">Confirm password</label>
        <input id="confirmation" type="password" required minlength="8" autocomplete="new-password">
        <button type="submit">Reset password</button>
    </form>
    <div class="message" id="message"></div>
    <a href="{{ url('/orders') }}">Back to customer sign in</a>
</main>
<script>
document.getElementById('resetForm').addEventListener('submit', function (event) {
    event.preventDefault();
    var message=document.getElementById('message');
    var password=document.getElementById('password').value;
    if(password!==document.getElementById('confirmation').value){message.textContent='Passwords do not match.';message.className='message error';message.style.display='block';return;}
    fetch('{{ route('customer.password.update') }}', {
        method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body:JSON.stringify({token:document.getElementById('token').value,email:document.getElementById('email').value,password:password,password_confirmation:document.getElementById('confirmation').value})
    }).then(function(response){return response.json().then(function(data){return {ok:response.ok,data:data};});})
      .then(function(result){
          message.textContent=result.ok ? result.data.message : ((result.data.errors && Object.values(result.data.errors)[0][0]) || 'This reset link is invalid or expired.');
          message.className='message'+(result.ok?'':' error'); message.style.display='block';
          if(result.ok) document.getElementById('resetForm').style.display='none';
      });
});
</script>
</body>
</html>
