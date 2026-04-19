<?php
session_start();
include 'config/db.php';

// if already logged in
if(isset($_SESSION['user_id'])) {
  header("Location: dashboard.php");
  exit;
}

$error = "";

if(isset($_POST['login'])) {

  $email = trim($_POST['email']);
  $password = $_POST['password'];

  // get user
  $sql = $pdo->prepare("SELECT * FROM users WHERE email = ?");
  $sql->execute([$email]);
  $user = $sql->fetch(PDO::FETCH_OBJ);

  // check user + password
  if($user && password_verify($password, $user->password)) {

    $_SESSION['user_id'] = $user->id;
    $_SESSION['name'] = $user->firstname;
    $_SESSION['role_id'] = $user->role_id;

    header("Location: index.php");
    exit;

  } else {
    $error = "Invalid email or password";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - EduSync</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center" style="height:100vh;">

  <div class="card shadow-sm p-4" style="width: 400px;">

    <h3 class="text-center mb-4">Login</h3>

    <?php if($error): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">

      <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>

      <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>

      <button type="submit" name="login" class="btn btn-primary w-100">
        Login
      </button>

    </form>

  </div>

</div>

</body>
</html>