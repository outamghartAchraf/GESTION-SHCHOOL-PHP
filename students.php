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
  <title>EduSync - Students</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include 'includes/nav.php'; ?>
<?php include 'config/db.php'; ?>

<?php

$sqlState = $pdo->query("SELECT 
    students.id,
    users.firstname,
    users.lastname,
    users.email,
    students.student_number,
    classes.name AS class_name
FROM students
JOIN users ON students.user_id = users.id
JOIN classes ON students.class_id = classes.id");

$students = $sqlState->fetchAll(PDO::FETCH_OBJ);

/* ================== ADD STUDENT LOGIC ================== */

if(isset($_POST['add_student'])) {
  $firstname = $_POST['firstname'];
  $lastname = $_POST['lastname'];
  $email = $_POST['email'];
  $student_number = $_POST['student_number'];
  $class_id = $_POST['class_id'];

  // FIX: added role_id (required by FK constraint)
  $sql = $pdo->prepare("INSERT INTO users (firstname, lastname, email, role_id) VALUES (?, ?, ?, ?)");
  $sql->execute([$firstname, $lastname, $email, 1]);

  $user_id = $pdo->lastInsertId();

  $sql = $pdo->prepare("INSERT INTO students (user_id, student_number, class_id) VALUES (?, ?, ?)");
  $sql->execute([$user_id, $student_number, $class_id]);

  header("location: ".$_SERVER['PHP_SELF']);
  exit;
}

/*=========================== DELETE STUDENT LOGIC =========================== */
if (isset($_POST['delete_student'])) {
  $student_id = $_POST['student_id'];

  $sql = $pdo->prepare("DELETE FROM students WHERE id = ?");
  $sql->execute([$student_id]);

  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}

/* ================== EDIT STUDENT LOGIC ================== */
$editStudent = null;

if(isset($_POST['edit_student'])) {
  $student_id = $_POST['edit_student_id'];

  $sqlState = $pdo->prepare("SELECT 
      students.id,
      users.firstname,
      users.lastname,
      users.email,
      students.student_number,
      students.class_id
  FROM students
  JOIN users ON students.user_id = users.id
  WHERE students.id = ?");

  $sqlState->execute([$student_id]);
  $editStudent = $sqlState->fetch(PDO::FETCH_OBJ);
}

/* ================== UPDATE STUDENT ================== */
if(isset($_POST['update_student'])) {
  $student_id = $_POST['student_id'];
  $firstname = $_POST['firstname'];
  $lastname = $_POST['lastname'];
  $email = $_POST['email'];
  $student_number = $_POST['student_number'];
  $class_id = $_POST['class_id'];

  // update users
  $sql = $pdo->prepare("
    UPDATE users 
    JOIN students ON students.user_id = users.id
    SET firstname = ?, lastname = ?, email = ?
    WHERE students.id = ?
  ");
  $sql->execute([$firstname, $lastname, $email, $student_id]);

  // update students
  $sql = $pdo->prepare("UPDATE students SET student_number = ?, class_id = ? WHERE id = ?");
  $sql->execute([$student_number, $class_id, $student_id]);

  header("location: ".$_SERVER['PHP_SELF']);
  exit;
}


$sqlState = $pdo->query("SELECT * FROM classes");
$classes = $sqlState->fetchAll(PDO::FETCH_OBJ);

?>

<div class="container py-4">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="fw-bold">Students</h2>
      <p class="text-muted">Manage all registered students</p>
    </div>

    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addStudentModal">
      <i class="bi bi-plus-circle"></i> Add Student
    </button>
  </div>

  <!-- ADD STUDENT MODAL -->
  <div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">

      <div class="modal-content border-0 shadow">

        <!-- HEADER -->
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">
            <i class="bi bi-person-plus"></i> Add New Student
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <!-- BODY -->
        <div class="modal-body p-4">

          <form method="POST">

            <div class="mb-3">
              <label class="form-label">FirstName</label>
              <input type="text" name="firstname" class="form-control" placeholder="Enter first name">
            </div>

            <div class="mb-3">
              <label class="form-label">LastName</label>
              <input type="text" name="lastname" class="form-control" placeholder="Enter last name">
            </div>

            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" placeholder="Enter email">
            </div>

            <div class="mb-3">
              <label class="form-label">Student Number</label>
              <input type="text" name="student_number" class="form-control" placeholder="Enter student number">
            </div>

            <div class="mb-3">
              <label class="form-label">Class</label>
              <select name="class_id" class="form-select">
                <option selected>Select class</option>
                <?php foreach ($classes as $class): ?>
                  <option value="<?= $class->id ?>"><?= $class->name ?></option>
                <?php endforeach; ?>
              </select>
            </div>

        </div>

        <!-- FOOTER -->
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="add_student" class="btn btn-primary">
            Save Student
          </button>
        </div>

          </form>

      </div>

    </div>
  </div>

  <?php if($editStudent): ?>
<div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5)">

  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">

      <!-- HEADER -->
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title">
          <i class="bi bi-pencil"></i> Edit Student
        </h5>
        <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn-close"></a>
      </div>

      <!-- BODY -->
      <div class="modal-body p-4">

        <form method="POST">

          <input type="hidden" name="student_id" value="<?= $editStudent->id ?>">

          <div class="mb-3">
            <label class="form-label">FirstName</label>
            <input type="text" name="firstname" class="form-control" value="<?= $editStudent->firstname ?>">
          </div>

          <div class="mb-3">
            <label class="form-label">LastName</label>
            <input type="text" name="lastname" class="form-control" value="<?= $editStudent->lastname ?>">
          </div>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= $editStudent->email ?>">
          </div>

          <div class="mb-3">
            <label class="form-label">Student Number</label>
            <input type="text" name="student_number" class="form-control" value="<?= $editStudent->student_number ?>">
          </div>

          <div class="mb-3">
            <label class="form-label">Class</label>
            <select name="class_id" class="form-select">
              <?php foreach ($classes as $class): ?>
                <option value="<?= $class->id ?>"
                  <?= $class->id == $editStudent->class_id ? 'selected' : '' ?>>
                  <?= $class->name ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="modal-footer">
            <button name="update_student" class="btn btn-success w-100">
              Update Student
            </button>
          </div>

        </form>

      </div>

    </div>
  </div>

</div>
<?php endif; ?>

  <!-- Stats Cards -->
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card shadow-sm border-0">
        <div class="card-body d-flex justify-content-between">
          <div>
            <h6 class="text-muted">Total Students</h6>
            <h3 class="fw-bold"><?= count($students) ?></h3>
          </div>
          <i class="bi bi-people fs-1 text-primary"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Table Card -->
  <div class="card shadow-sm border-0">
    <div class="card-header bg-white border-0">
      <div class="d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Students List</h5>
        <input type="text" class="form-control w-25" placeholder="Search...">
      </div>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-dark">
            <tr>
              <th>#</th>
              <th>Student</th>
              <th>Email</th>
              <th>Student Number</th>
              <th>Class</th>
              <th>Actions</th>
            </tr>
          </thead>

          <tbody>

          <?php foreach ($students as $student): ?>
            <tr>
              <td><?= $student->id ?></td>

              <td>
                <div class="d-flex align-items-center">
                  <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-2" style="width:35px;height:35px;">
                    <?= strtoupper(substr((string) ($student->firstname ?? ''), 0, 1)) ?>
                  </div>
                  <div>
                    <div class="fw-semibold">
                      <?= $student->firstname . ' ' . $student->lastname ?>
                    </div>
                  </div>
                </div>
              </td>

              <td><?= $student->email ?></td>

              <td>
                <span class="badge bg-secondary">
                  <?= $student->student_number ?>
                </span>
              </td>

              <td>
                <span class="badge bg-info text-dark">
                  <?= $student->class_name ?>
                </span>
              </td>

              <td>
                <form method="post" class="d-inline">
                  <input type="hidden" name="edit_student_id" value="<?= $student->id ?>">
                  <button type="submit" name="edit_student" class="btn btn-sm btn-warning">
                    <i class="bi bi-pencil"></i>
                  </button>
                </form>
                
                <form method="post" class="d-inline">
                  <input type="hidden" name="student_id" value="<?= $student->id ?>">
                  <button type="submit" name="delete_student" class="btn btn-sm btn-danger">
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