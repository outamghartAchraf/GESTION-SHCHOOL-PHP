<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EduSync - Users</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- NAVBAR -->
<?php include 'includes/nav.php'; ?>
<?php include 'config/db.php'; ?>

<?php  

session_start();

if(!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

/* USERS */
$sqlState = $pdo->query("SELECT users.*, roles.role_name AS role_name 
FROM users 
LEFT JOIN roles ON users.role_id = roles.id");

$users = $sqlState->fetchAll(PDO::FETCH_OBJ);

/* ROLES */
$sqlState = $pdo->query("SELECT * FROM roles");
$roles = $sqlState->fetchAll(PDO::FETCH_OBJ);


/* ================== ADD USER LOGIC ================== */ 
if(isset($_POST['add_user'])){
  $firstname = $_POST['firstname'];
  $lastname = $_POST['lastname'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $role_id = $_POST['role_id'];

  $sql = $pdo->prepare("INSERT INTO users (firstname, lastname, email, password, role_id) VALUES (?, ?, ?, ?, ?)");
  $sql->execute([$firstname, $lastname, $email, $password, $role_id]);

  header("location: ".$_SERVER['PHP_SELF']);
  exit;
}

/* ================== DELETE USER LOGIC ================== */
if(isset($_POST['delete_user'])){
  $user_id = $_POST['user_id'];

  $sql = $pdo->prepare("DELETE FROM users WHERE id = ?");
  $sql->execute([$user_id]);

  header("location: ".$_SERVER['PHP_SELF']);
  exit;
}

/* ================== EDIT USER LOGIC ================== */

$editUser = null;

if(isset($_POST['edit_user'])){
  $user_id = $_POST['user_id'];

  $sqlState = $pdo->prepare("SELECT * FROM users WHERE id = ?");
  $sqlState->execute([$user_id]);
  $editUser = $sqlState->fetch(PDO::FETCH_OBJ);
}

/* ================== UPDATE USER LOGIC ================== */
if(isset($_POST['update_user'])){
  $user_id = $_POST['user_id'];
  $firstname = $_POST['firstname'];
  $lastname = $_POST['lastname'];
  $email = $_POST['email'];
  $role_id = $_POST['role_id'];

  $sql = $pdo->prepare("UPDATE users SET firstname = ?, lastname = ?, email = ?, role_id = ? WHERE id = ?");
  $sql->execute([$firstname, $lastname, $email, $role_id, $user_id]);

  header("location: ".$_SERVER['PHP_SELF']);
  exit;

}

?>

<div class="container py-4">

  <!-- HEADER -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="fw-bold">Users</h2>
      <p class="text-muted mb-0">Manage system users and roles</p>
    </div>

<button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
  <i class="bi bi-person-plus"></i> Add User
</button>

  </div>

<!-- ADD USER MODAL -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">

    <div class="modal-content">

      <!-- HEADER -->
      <div class="modal-header">
        <h5 class="modal-title">Add New User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- BODY -->
      <div class="modal-body">

        <form method="POST">

          <div class="row g-2">

            <div class="col-md-6">
              <input name="firstname" class="form-control" placeholder="First Name" required>
            </div>

            <div class="col-md-6">
              <input name="lastname" class="form-control" placeholder="Last Name" required>
            </div>

            <div class="col-md-6 mt-2">
              <input name="email" class="form-control" placeholder="Email" required>
            </div>

            <div class="col-md-6 mt-2">
              <input name="password" type="password" class="form-control" placeholder="Password" required>
            </div>

            <div class="col-md-6 mt-2">
              <select name="role_id" class="form-control" required>
                <option value="">Select Role</option>
                <?php foreach($roles as $role): ?>
                  <option value="<?= $role->id ?>"><?= $role->role_name ?></option>
                <?php endforeach; ?>
              </select>
            </div>

          </div>

          <div class="mt-3">
            <button name="add_user" class="btn btn-primary w-100">
              Save User
            </button>
          </div>

        </form>

      </div>

    </div>

  </div>
</div>


<!-- ================= EDIT MODAL ================= -->
<?php if($editUser): ?>
<div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5)">

  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5>Edit User</h5>
        <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn-close"></a>
      </div>

      <div class="modal-body">

        <form method="POST">
          <input type="hidden" name="user_id" value="<?= $editUser->id ?>">

          <div class="row g-2">

            <div class="col-md-6">
              <input name="firstname" class="form-control" value="<?= $editUser->firstname ?>">
            </div>

            <div class="col-md-6">
              <input name="lastname" class="form-control" value="<?= $editUser->lastname ?>">
            </div>

            <div class="col-md-6 mt-2">
              <input name="email" class="form-control" value="<?= $editUser->email ?>">
            </div>

            <div class="col-md-6 mt-2">
              <select name="role_id" class="form-control">
                <?php foreach($roles as $role): ?>
                  <option value="<?= $role->id ?>"
                    <?= $role->id == $editUser->role_id ? 'selected' : '' ?>>
                    <?= $role->role_name ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

          </div>

          <div class="mt-3">
            <button name="update_user" class="btn btn-success w-100">
              Update User
            </button>
          </div>

        </form>

      </div>

    </div>
  </div>

</div>
<?php endif; ?>


  <!-- STATS -->
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div>
            <h6 class="text-muted">Total Users</h6>
            <h3 class="fw-bold mb-0"><?= count($users) ?></h3>
          </div>
          <i class="bi bi-people fs-1 text-primary"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- TABLE CARD -->
  <div class="card border-0 shadow-sm">

    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Users List</h5>
      <input type="text" class="form-control w-25" placeholder="Search users...">
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">

          <thead class="table-dark">
            <tr>
              <th>#</th>
              <th>User</th>
              <th>Email</th>
              <th>Role</th>
              <th>Actions</th>
            </tr>
          </thead>

          <tbody>

          <?php foreach($users as $user): ?>        
            <tr>
              <td><?= $user->id ?></td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-2" style="width:35px;height:35px;">
                    <?= strtoupper(substr((string) ($user->firstname ?? ''), 0, 1)) ?>
                  </div>
                  <div>
                    <div class="fw-semibold"><?= $user->firstname . ' ' . $user->lastname ?></div>
                    <small class="text-muted">ID: <?= $user->id ?></small>
                  </div>
                </div>
              </td>

              <td><?= $user->email ?></td>

              <td>
                <span class="badge bg-info text-dark">
                  <?= $user->role_name ?? 'No Role' ?>
                </span>
              </td>

              <td>
               <form method="post" class="d-inline">
                <input type="hidden" name="user_id" value="<?= $user->id ?>">
                <button type="submit" name="edit_user" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></button>
               </form>
                <form method="post" class="d-inline">
                  <input type="hidden" name="user_id" value="<?= $user->id ?>">
                  <button type="submit" name="delete_user" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                </form>

              </td>
            </tr>
          <?php endforeach; ?>        

          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>