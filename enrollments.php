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
  <title>EduSync - Enrollments</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body class="bg-light">

  <?php include 'includes/nav.php'; ?>
  <?php include 'config/db.php'; ?>

  <?php

  $sqlState = $pdo->query("
SELECT 
    enrollments.id,
    enrollments.status,
    students.student_number,
    users.firstname,
    users.lastname,
    courses.title AS course_title
FROM enrollments
JOIN students ON enrollments.student_id = students.id
JOIN users ON students.user_id = users.id
JOIN courses ON enrollments.course_id = courses.id
");

  $enrollments = $sqlState->fetchAll(PDO::FETCH_OBJ);

  /*================ ADD ENROLLMENT =================*/
if(isset($_POST['add_enrollment'])) {
  $student_id = $_POST['student_id'];
  $course_id = $_POST['course_id'];
  $status = $_POST['status'];

  $sql = $pdo->prepare("INSERT INTO enrollments (student_id, course_id, status) VALUES (?, ?, ?)");
  $sql->execute([$student_id, $course_id, $status]);

  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}

/*================ DELETE ENROLLMENT =================*/
if(isset($_POST['delete_id'])) {
  $id = $_POST['delete_id'];

  $sql = $pdo->prepare("DELETE FROM enrollments WHERE id = ?");
  $sql->execute([$id]);

  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}

/*================ EDIT ENROLLMENT (GET DATA) =================*/
$editEnrollment = null;

if(isset($_POST['edit_id'])) {
  $id = $_POST['edit_id'];

  $sql = $pdo->prepare("SELECT * FROM enrollments WHERE id = ?");
  $sql->execute([$id]);

  $editEnrollment = $sql->fetch(PDO::FETCH_OBJ);
}

/*================ UPDATE ENROLLMENT =================*/
if(isset($_POST['update_enrollment'])) {
  $id = $_POST['id'];
  $student_id = $_POST['student_id'];
  $course_id = $_POST['course_id'];
  $status = $_POST['status'];

  $sql = $pdo->prepare("UPDATE enrollments SET student_id=?, course_id=?, status=? WHERE id=?");
  $sql->execute([$student_id, $course_id, $status, $id]);

  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}

/* students */
$sqlS = $pdo->query("
    SELECT 
        students.id,
        students.student_number,
        users.firstname,
        users.lastname
    FROM students
    JOIN users ON students.user_id = users.id
");

$students = $sqlS->fetchAll(PDO::FETCH_OBJ);

 /* coureses */
 $sqlC = $pdo->query("SELECT * FROM courses");
 $courses = $sqlC->fetchAll(PDO::FETCH_OBJ);

  ?>

  <div class="container py-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="fw-bold">Enrollments</h2>
        <p class="text-muted mb-0">Manage students course enrollments</p>
      </div>
      <button class="btn btn-dark shadow-sm" data-bs-toggle="modal" data-bs-target="#addEnrollmentModal">
        <i class="bi bi-plus-circle"></i> Add Enrollment
      </button>
    </div>

    <!-- ================= ADD ENROLLMENT MODAL ================= -->
    <div class="modal fade" id="addEnrollmentModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content border-0 shadow">

          <!-- HEADER -->
          <div class="modal-header bg-dark text-white">
            <h5 class="modal-title">
              <i class="bi bi-link-45deg"></i> Add New Enrollment
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>

          <!-- BODY -->
          <div class="modal-body p-4">

            <form method="POST">

              <div class="row g-3">

                <!-- Student -->
                <div class="col-md-6">
                  <select name="student_id" class="form-select" required>
                    <option value="">Select Student</option>
                    <?php foreach ($students as $student): ?>
                      <option value="<?= $student->id ?>"><?= $student->firstname . ' ' . $student->lastname ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <!-- Course -->
                <div class="col-md-6">
                  <select name="course_id" class="form-select" required>
                    <option value="">Select Course</option>
                    <?php foreach ($courses as $course): ?>
                      <option value="<?= $course->id ?>"><?= $course->title ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <!-- Status -->
                <div class="col-md-12">
                  <label class="form-label">Status</label>
                  <select name="status" class="form-select" required>
                    <option value="active">Active</option>
                    <option value="completed">Completed</option>
                  </select>
                </div>

              </div>

              <div class="mt-4">
                <button type="submit" name="add_enrollment" class="btn btn-dark w-100">
                  Save Enrollment
                </button>
              </div>

            </form>

          </div>

        </div>

      </div>
    </div>

    <?php if($editEnrollment): ?>
<div class="modal fade show" style="display:block; background:rgba(0,0,0,0.5);" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">

    <div class="modal-content border-0 shadow">

      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title">
          <i class="bi bi-pencil"></i> Edit Enrollment
        </h5>
        <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn-close"></a>
      </div>

      <form method="POST">

        <div class="modal-body p-4">

          <input type="hidden" name="id" value="<?= $editEnrollment->id ?>">

          <div class="row g-3">

            <div class="col-md-6">
              <select name="student_id" class="form-select" required>
                <option value="">Select Student</option>
                <?php foreach ($students as $student): ?>
                  <option value="<?= $student->id ?>" <?= $editEnrollment->student_id == $student->id ? 'selected' : '' ?>>
                    <?= $student->firstname . ' ' . $student->lastname ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-6">
              <select name="course_id" class="form-select" required>
                <option value="">Select Course</option>
                <?php foreach ($courses as $course): ?>
                  <option value="<?= $course->id ?>" <?= $editEnrollment->course_id == $course->id ? 'selected' : '' ?>>
                    <?= $course->title ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-12">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <option value="active" <?= $editEnrollment->status == 'active' ? 'selected' : '' ?>>Active</option>
                <option value="completed" <?= $editEnrollment->status == 'completed' ? 'selected' : '' ?>>Completed</option>
              </select>
            </div>

          </div>

        </div>

        <div class="modal-footer">
          <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-secondary">Cancel</a>
          <button type="submit" name="update_enrollment" class="btn btn-dark">
            Update Enrollment
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
              <h6 class="text-muted">Total Enrollments</h6>
              <h3 class="fw-bold mb-0"><?= count($enrollments) ?></h3>
            </div>
            <i class="bi bi-link-45deg fs-1 text-dark"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- TABLE -->
    <div class="card border-0 shadow-sm">

      <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Enrollments List</h5>
        <input type="text" class="form-control w-25" placeholder="Search enrollments...">
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">

          <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
              <tr>
                <th>#</th>
                <th>Student</th>
                <th>Course</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>

            <tbody>

              <?php foreach ($enrollments as $enroll): ?>
                <tr>
                  <td><?= $enroll->id ?></td>

                  <td>
                    <div class="fw-semibold">
                      <?= $enroll->firstname . ' ' . $enroll->lastname ?>
                    </div>
                    <small class="text-muted"><?= $enroll->student_number ?></small>
                  </td>

                  <td>
                    <span class="badge bg-info text-dark">
                      <?= $enroll->course_title ?>
                    </span>
                  </td>

                  <td>
                    <?php if ($enroll->status == 'active'): ?>
                      <span class="badge bg-success">Active</span>
                    <?php else: ?>
                      <span class="badge bg-secondary">Completed</span>
                    <?php endif; ?>
                  </td>

                  <td>

                    <!-- EDIT -->
                    <form method="POST" class="d-inline">
                      <input type="hidden" name="edit_id" value="<?= $enroll->id ?>">
                      <button type="submit" class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil"></i>
                      </button>
                    </form>

                    <!-- DELETE -->
                    <form method="POST" class="d-inline">
                      <input type="hidden" name="delete_id" value="<?= $enroll->id ?>">
                      <button type="submit" class="btn btn-sm btn-danger"
                        onclick="return confirm('Delete this enrollment?')">
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