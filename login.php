<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In</title>
  <style>
    body {
  font-family: Arial, sans-serif;
  background: url('background.gif') no-repeat center center fixed;
  background-size: cover;
  margin: 0;
  padding: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

    .container {
      background: #fff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
      text-align: center;
    }
    h1 {
      margin-bottom: 20px;
      font-size: 24px;
      color: #333;
    }
    .login-button {
      display: block;
      margin: 10px 0;
      padding: 10px;
      font-size: 16px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      text-decoration: none;
      text-align: center;
      color: white;
    }
    .google-button {
      background-color: #4285F4;
    }
    .google-button:hover {
      background-color: #357ae8;
    }
    .github-button {
      background-color: #333;
    }
    .github-button:hover {
      background-color: #555;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Sign In To Gabai</h1>
    <!-- Google Login Link -->
    <a class="login-button google-button" href="https://accounts.google.com/o/oauth2/v2/auth?client_id=869823649701-777v57c2f1vpkv1cd31fm1it406hjmka.apps.googleusercontent.com&redirect_uri=http://localhost/3api/dashboard.php&response_type=code&scope=openid%20email%20profile">Sign in with Google</a>
    <!-- GitHub Login Link -->
    <a class="login-button github-button" href="https://github.com/login/oauth/authorize?client_id=Ov23liN7KghnvFdDuxpr&redirect_uri=http://localhost/3api/dashboard.php&scope=user">Sign in with GitHub</a>
  </div>
</body>
</html>
