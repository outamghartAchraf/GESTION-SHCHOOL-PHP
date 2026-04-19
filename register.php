<?php
session_start();
include 'config/db.php';

$error = "";
$success = "";

if(isset($_POST['register'])) {

  $firstname = trim($_POST['firstname']);
  $lastname  = trim($_POST['lastname']);
  $email     = trim($_POST['email']);
  $password  = $_POST['password'];
  $confirm   = $_POST['confirm_password'];

  // basic validation
  if($password !== $confirm) {
    $error = "Passwords do not match";
  } else {

    // check if email exists
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);

    if($check->rowCount() > 0) {
      $error = "Email already exists";
    } else {

      // hash password
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

      // insert user (role_id = 1 default for example)
      $sql = $pdo->prepare("INSERT INTO users (firstname, lastname, email, password, role_id) VALUES (?, ?, ?, ?, ?)");
      $sql->execute([$firstname, $lastname, $email, $hashedPassword, 1]);

      $success = "Account created successfully! You can login now.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register - EduSync</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center" style="height:100vh;">

  <div class="card shadow-sm p-4" style="width: 450px;">

    <h3 class="text-center mb-3">Register</h3>

    <?php if($error): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <?php if($success): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST">

      <div class="mb-2">
        <label>First Name</label>
        <input type="text" name="firstname" class="form-control" required>
      </div>

      <div class="mb-2">
        <label>Last Name</label>
        <input type="text" name="lastname" class="form-control" required>
      </div>

      <div class="mb-2">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>

      <div class="mb-2">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>

      <div class="mb-3">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control" required>
      </div>

      <button type="submit" name="register" class="btn btn-success w-100">
        Register
      </button>

    </form>

    <div class="text-center mt-3">
      <a href="login.php">Already have an account? Login</a>
    </div>

  </div>

</div>

</body>
</html>