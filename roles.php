<?php
session_start();

if(!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EduSync - Roles</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include 'includes/nav.php'; ?>
<?php include 'config/db.php'; ?>

<?php 

$sqlState = $pdo->query("SELECT roles.* FROM roles");
$roles = $sqlState->fetchAll(PDO::FETCH_OBJ);

/* ================== ADD ROLE LOGIC ================== */

if(isset($_POST['add_role'])) {
  $role_name = $_POST['role_name'];

  $sql = $pdo->prepare("INSERT INTO roles (role_name) VALUES (?)");
  $sql->execute([$role_name]);
  header("location: $_SERVER[PHP_SELF]");
  exit;
}

/* ================== DELETE ROLE LOGIC ================== */

if(isset($_POST['delete_role'])){
  $role_id = $_POST['delete_role_id'];

  $sql = $pdo->prepare("DELETE FROM roles WHERE id = ?");
  $sql->execute([$role_id]);
  header("location: $_SERVER[PHP_SELF]");
  exit;

}

/* ================== EDIT ROLE LOGIC ================== */
$editRole = null;
if(isset($_POST['edit_role'])) {
  $role_id = $_POST['edit_role_id'];

  $sqlState = $pdo->prepare("SELECT * FROM roles WHERE id = ?");
  $sqlState->execute([$role_id]);
  $editRole = $sqlState->fetch(PDO::FETCH_OBJ);
}

/* ================== UPDATE ROLE LOGIC ================== */
if(isset($_POST['update_role'])) {
  $role_id = $_POST['role_id'];
  $role_name = $_POST['role_name'];

  $sql = $pdo->prepare("UPDATE roles SET role_name = ? WHERE id = ?");
  $sql->execute([$role_name, $role_id]);
  header("location: $_SERVER[PHP_SELF]");
  exit;
}

?>

<div class="container py-4">

  <!-- HEADER -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="fw-bold">Roles</h2>
      <p class="text-muted mb-0">Manage user roles and permissions</p>
    </div>
<button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addRoleModal">
  <i class="bi bi-shield-plus"></i> Add Role
</button>
  </div>


  <!-- ADD ROLE MODAL -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">

    <div class="modal-content">

      <!-- HEADER -->
      <div class="modal-header">
        <h5 class="modal-title">Add New Role</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- BODY -->
      <div class="modal-body">

        <form method="POST">

          <div class="mb-3">
            <label class="form-label">Role Name</label>
            <input type="text" name="role_name" class="form-control" placeholder="Enter role name" required>
          </div>

          <button type="submit" name="add_role" class="btn btn-primary w-100">
            Save Role
          </button>

        </form>

      </div>

    </div>

  </div>
</div>

<?php if($editRole): ?>

<div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5)">
  <div class="modal-dialog modal-dialog-centered">

    <div class="modal-content">

      <!-- HEADER -->
      <div class="modal-header">
        <h5 class="modal-title">Edit Role</h5>
        <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn-close"></a>
      </div>

      <!-- BODY -->
      <div class="modal-body">

        <form method="POST">

          <!-- ID hidden -->
          <input type="hidden" name="role_id" value="<?= $editRole->id ?>">

          <div class="mb-3">
            <label class="form-label">Role Name</label>
            <input 
              type="text" 
              name="role_name" 
              class="form-control" 
              value="<?= $editRole->role_name ?>" 
              required
            >
          </div>

          <button type="submit" name="update_role" class="btn btn-primary w-100">
            Update Role
          </button>

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
            <h6 class="text-muted">Total Roles</h6>
            <h3 class="fw-bold mb-0"><?= count($roles) ?></h3>
          </div>
          <i class="bi bi-shield-lock fs-1 text-primary"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- TABLE -->
  <div class="card border-0 shadow-sm">

    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Roles List</h5>
      <input type="text" class="form-control w-25" placeholder="Search roles...">
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">

        <table class="table table-hover align-middle mb-0">
          <thead class="table-dark">
            <tr>
              <th>#</th>
              <th>Role</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>

          <tbody>

          <?php foreach($roles as $role): ?>
            <tr>
              <td><?= $role->id ?></td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="bg-dark text-white rounded-circle d-flex justify-content-center align-items-center me-2" style="width:35px;height:35px;">
                    <?= strtoupper(substr((string) ($role->role_name ?? ''), 0, 1)) ?>
                  </div>
                  <div class="fw-semibold"><?= $role->role_name ?></div>
                </div>
              </td>

              <td>
                <span class="badge bg-success">Active</span>
              </td>

              <td>
            <form method="post" class="d-inline">
              <input type="hidden" name="edit_role_id" value="<?= $role->id ?>">
              <button type="submit" name="edit_role" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></button>
            </form>

              <form method="post" class="d-inline">
                <input type="hidden" name="delete_role_id" value="<?= $role->id ?>">
                <button type="submit" name="delete_role" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
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