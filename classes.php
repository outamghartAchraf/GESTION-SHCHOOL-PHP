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
  <title>EduSync - Classes</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<?php include 'includes/nav.php'; ?>
<?php include 'config/db.php'; ?>

<?php  

$sqlState = $pdo->query("SELECT * FROM classes");
$classes = $sqlState->fetchAll(PDO::FETCH_OBJ);

/*================ ADD CLASS LOGIC ================*/

 if(isset($_POST['add_class'])) {
  $name = $_POST['name'];
  $classroom_number = $_POST['classroom_number'];

  $sql = $pdo->prepare("INSERT INTO classes (name, classroom_number) VALUES (?, ?)");
  $sql->execute([$name, $classroom_number]);

  header("Location: " . $_SERVER['PHP_SELF']);
  exit;

 }

 /*================ DELETE CLASS LOGIC =================*/

if(isset($_POST['delete_id'])) {
  $class_id = $_POST['delete_id'];

  $sql = $pdo->prepare("DELETE FROM classes WHERE id = ?");
  $sql->execute([$class_id]);

  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}

/*================ EDIT CLASS LOGIC =================*/

$editClass = null;

if(isset($_POST['edit_id'])) {
  $class_id = $_POST['edit_id'];

  $sql = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
  $sql->execute([$class_id]);

  $editClass = $sql->fetch(PDO::FETCH_OBJ);
}

/*================ UPDATE CLASS =================*/

if(isset($_POST['update_class'])) {
  $id = $_POST['id'];
  $name = $_POST['name'];
  $classroom_number = $_POST['classroom_number'];

  $sql = $pdo->prepare("UPDATE classes SET name = ?, classroom_number = ? WHERE id = ?");
  $sql->execute([$name, $classroom_number, $id]);

  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}

?>

<div class="container py-4">

  <!-- HEADER -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="fw-bold">Classes</h2>
      <p class="text-muted mb-0">Manage school classes</p>
    </div>
   <button class="btn btn-warning shadow-sm" data-bs-toggle="modal" data-bs-target="#addClassModal">
      <i class="bi bi-plus-circle"></i> Add Class
    </button>
  </div>


 
<!-- ADD CLASS MODAL -->
<div class="modal fade" id="addClassModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">

    <div class="modal-content border-0 shadow">

      <!-- HEADER -->
      <div class="modal-header bg-warning">
        <h5 class="modal-title">
          <i class="bi bi-building"></i> Add New Class
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- FORM START -->
      <form method="POST">

        <!-- BODY -->
        <div class="modal-body p-4">

          <div class="mb-3">
            <label class="form-label">Class Name</label>
            <input type="text" name="name" class="form-control" placeholder="Enter class name" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Classroom Number</label>
            <input type="text" name="classroom_number" class="form-control" placeholder="Enter classroom number" required>
          </div>

        </div>

        <!-- FOOTER -->
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-warning" name="add_class">
            Save Class
          </button>
        </div>

      </form>
      <!-- ✅ FORM END -->

    </div>

  </div>
</div>

<?php if($editClass): ?>
<div class="modal fade show" style="display:block; background:rgba(0,0,0,0.5);" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">

    <div class="modal-content border-0 shadow">

      <div class="modal-header bg-warning">
        <h5 class="modal-title">
          <i class="bi bi-pencil"></i> Edit Class
        </h5>
        <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn-close"></a>
      </div>

      <form method="POST">

        <div class="modal-body p-4">

          <input type="hidden" name="id" value="<?= $editClass->id ?>">

          <div class="mb-3">
            <label class="form-label">Class Name</label>
            <input type="text" name="name" class="form-control" value="<?= $editClass->name ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Classroom Number</label>
            <input type="text" name="classroom_number" class="form-control" value="<?= $editClass->classroom_number ?>" required>
          </div>

        </div>

        <div class="modal-footer">
          <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-secondary">Cancel</a>
          <button type="submit" name="update_class" class="btn btn-warning">
            Update Class
          </button>
        </div>

      </form>

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
            <h6 class="text-muted">Total Classes</h6>
            <h3 class="fw-bold mb-0"><?= count($classes) ?></h3>
          </div>
          <i class="bi bi-building fs-1 text-warning"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- TABLE -->
  <div class="card border-0 shadow-sm">

    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Classes List</h5>
      <input type="text" class="form-control w-25" placeholder="Search classes...">
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">

        <table class="table table-hover align-middle mb-0">
          <thead class="table-dark">
            <tr>
              <th>#</th>
              <th>Class Name</th>
              <th>Classroom</th>
              <th>Actions</th>
            </tr>
          </thead>

          <tbody>

          <?php foreach($classes as $class): ?>
            <tr>
              <td><?= $class->id ?></td>

              <td>
                <div class="d-flex align-items-center">
                  <div class="bg-warning text-dark rounded-circle d-flex justify-content-center align-items-center me-2"
                       style="width:35px;height:35px;">
                    <?= strtoupper(substr((string) ($class->name ?? ''), 0, 1)) ?>
                  </div>
                  <div class="fw-semibold"><?= $class->name ?></div>
                </div>
              </td>

              <td>
                <span class="badge bg-secondary">
                  <?= $class->classroom_number ?>
                </span>
              </td>

              <td>
                
              <form method="post" class="d-inline">
                <input type="hidden" name="edit_id" value="<?=  $class->id ?>">
                <button type="submit" class="btn btn-sm btn-warning">
                  <i class="bi bi-pencil"></i>
                </button>
              </form>

               <form method="POST" class="d-inline">
                  <input type="hidden" name="delete_id" value="<?= $class->id ?>">
                  <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this class?')">
                    <i class="bi bi-trash"></i>
                  </button>
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