<?php
session_start();
$flashMessage = $_SESSION['flash'] ?? null;
$flashType = $_SESSION['flash_type'] ?? null;
unset($_SESSION['flash'], $_SESSION['flash_type']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <link rel="stylesheet" href="./css/index.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
</head>
<body>
  <div class="container">
    <div class="header">
      <h3>Welcome To The Evoting System</h3>
      <p>Please login to your account</p>
    </div>

    <!-- Flash Message -->
    <?php if ($flashMessage): ?>
      <div class="flash-message <?= $flashType === 'success' ? 'success' : 'error' ?>">
        <?= $flashMessage ?>
      </div>
    <?php endif; ?>

    <!-- Login Form -->
    <div class="login-box" id="signInForm">
      <form action="authentication.php" method="post">
        <h2>Login to start your session</h2>
        <div class="input-box">
          <input type="email" name="email" required />
          <label>Email</label>
        </div>
        <div class="input-box">
          <input type="password" name="password" required />
          <label>Password</label>
        </div>
        <input type="hidden" name="signIn" value="1" />
        <div class="remember-forgot">
          <label><input type="checkbox" /> Remember me</label>
          <a href="#">Forgot password?</a>
        </div>
        <button type="submit">Login</button>
        <div class="remember-forgot1">
          <a href="#" id="showSignUp">Create an account</a>
        </div>
      </form>
    </div>

    <!-- Signup Form -->
    <div class="login-box" id="signUpForm" style="display: none;">
      <form action="authentication.php" method="post">
        <h2>Create an account</h2>
        <div class="input-box"><input type="text" name="username" required /><label>Username</label></div>
        <div class="input-box"><input type="text" name="firstName" required /><label>First Name</label></div>
        <div class="input-box"><input type="text" name="lastName" required /><label>Last Name</label></div>
        <div class="input-box"><input type="text" name="phone" required /><label>Phone Number</label></div>
        <div class="input-box"><input type="email" name="email" required /><label>Email</label></div>
        <div class="input-box"><input type="password" name="password" required /><label>Password</label></div>
        <input type="hidden" name="signUp" value="1" />
        <button type="submit">Register</button>
        <div class="remember-forgot1">
          <a href="#" id="showSignIn">Already have an account? Login here</a>
        </div>
      </form>
    </div>
  </div>

  <script src="https://unpkg.com/ionicons@5.5.2/dist/ionicons.js"></script>
  <script src="./js/index.js"></script>
</body>
</html>
