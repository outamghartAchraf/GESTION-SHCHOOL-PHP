<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EduSync - Courses</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body class="bg-light">

  <?php include 'includes/nav.php'; ?>
  <?php include 'config/db.php'; ?>

  <?php

  session_start();

if(!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

  $sqlState = $pdo->query("
    SELECT 
        courses.*,
        users.firstname AS professor_firstname,
        users.lastname AS professor_lastname
    FROM courses
    LEFT JOIN users ON courses.prof_id = users.id
");

  $courses = $sqlState->fetchAll(PDO::FETCH_OBJ);

  /*================ ADD COURSE =================*/
  if (isset($_POST['add_course'])) {
    $title = $_POST['title'];
    $total_hours = $_POST['total_hours'];
    $description = $_POST['description'];
    $prof_id = $_POST['prof_id'];

    $sql = $pdo->prepare("INSERT INTO courses (title, total_hours, description, prof_id) VALUES (?, ?, ?, ?)");
    $sql->execute([$title, $total_hours, $description, $prof_id]);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
  }

  /*================ DELETE COURSE =================*/
  if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];

    $sql = $pdo->prepare("DELETE FROM courses WHERE id = ?");
    $sql->execute([$id]);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
  }

  /*================ EDIT COURSE (GET DATA) =================*/
  $editCourse = null;

  if (isset($_POST['edit_id'])) {
    $id = $_POST['edit_id'];

    $sql = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
    $sql->execute([$id]);

    $editCourse = $sql->fetch(PDO::FETCH_OBJ);
  }

  /*================ UPDATE COURSE =================*/
  if (isset($_POST['update_course'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $total_hours = $_POST['total_hours'];
    $description = $_POST['description'];
    $prof_id = $_POST['prof_id'];

    $sql = $pdo->prepare("UPDATE courses SET title=?, total_hours=?, description=?, prof_id=? WHERE id=?");
    $sql->execute([$title, $total_hours, $description, $prof_id, $id]);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
  }
  ?>

  <div class="container py-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="fw-bold">Courses</h2>
        <p class="text-muted mb-0">Manage all school courses</p>
      </div>
      <button class="btn btn-info shadow-sm" data-bs-toggle="modal" data-bs-target="#addCourseModal">
        <i class="bi bi-plus-circle"></i> Add Course
      </button>
    </div>

    <!-- ================= ADD COURSE MODAL ================= -->
    <div class="modal fade" id="addCourseModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content border-0 shadow">

          <!-- HEADER -->
          <div class="modal-header bg-info text-white">
            <h5 class="modal-title">
              <i class="bi bi-journal-plus"></i> Add New Course
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>

          <!-- BODY -->
          <div class="modal-body p-4">

            <form method="POST">

              <div class="row g-3">

                <div class="col-md-6">
                  <label class="form-label">Course Title</label>
                  <input type="text" name="title" class="form-control" placeholder="Enter course title" required>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Total Hours</label>
                  <input type="number" name="total_hours" class="form-control" placeholder="e.g. 40" required>
                </div>

                <div class="col-12">
                  <label class="form-label">Description</label>
                  <textarea name="description" class="form-control" rows="3" placeholder="Course description"></textarea>
                </div>

                <div class="col-md-12">
                  <label class="form-label">Professor ID</label>
                  <input type="number" name="prof_id" class="form-control" placeholder="Enter professor ID" required>
                </div>

              </div>

              <div class="mt-4">
                <button type="submit" name="add_course" class="btn btn-info w-100">
                  Save Course
                </button>
              </div>

            </form>

          </div>

        </div>

      </div>
    </div>

    <?php if($editCourse): ?>
<div class="modal fade show" style="display:block; background:rgba(0,0,0,0.5);" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">

    <div class="modal-content border-0 shadow">

      <div class="modal-header bg-info text-white">
        <h5 class="modal-title">
          <i class="bi bi-pencil"></i> Edit Course
        </h5>
        <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn-close"></a>
      </div>

      <form method="POST">

        <div class="modal-body p-4">

          <input type="hidden" name="id" value="<?= $editCourse->id ?>">

          <div class="row g-3">

            <div class="col-md-6">
              <label class="form-label">Course Title</label>
              <input type="text" name="title" class="form-control"
                     value="<?= $editCourse->title ?>" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Total Hours</label>
              <input type="number" name="total_hours" class="form-control"
                     value="<?= $editCourse->total_hours ?>" required>
            </div>

            <div class="col-12">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-control"><?= $editCourse->description ?></textarea>
            </div>

            <div class="col-md-12">
              <label class="form-label">Professor ID</label>
              <input type="number" name="prof_id" class="form-control"
                     value="<?= $editCourse->prof_id ?>" required>
            </div>

          </div>

        </div>

        <div class="modal-footer">
          <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-secondary">Cancel</a>
          <button type="submit" name="update_course" class="btn btn-info">
            Update Course
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
              <h6 class="text-muted">Total Courses</h6>
              <h3 class="fw-bold mb-0"><?= count($courses) ?></h3>
            </div>
            <i class="bi bi-journal-bookmark fs-1 text-info"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- TABLE -->
    <div class="card border-0 shadow-sm">

      <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Courses List</h5>
        <input type="text" class="form-control w-25" placeholder="Search courses...">
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">

          <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
              <tr>
                <th>#</th>
                <th>Title</th>
                <th>Hours</th>
                <th>Professor</th>
                <th>Actions</th>
              </tr>
            </thead>

            <tbody>

              <?php foreach ($courses as $course): ?>
                <tr>
                  <td><?= $course->id ?></td>

                  <td>
                    <div class="fw-semibold"><?= $course->title ?></div>
                    <small class="text-muted"><?= substr((string) ($course->description ?? ''), 0, 40) ?>...</small>
                  </td>

                  <td>
                    <span class="badge bg-secondary"><?= $course->total_hours ?>h</span>
                  </td>

                  <td>
                    <?= $course->professor_firstname . ' ' . $course->professor_lastname ?>
                  </td>

                  <td>

                    <!-- EDIT -->
                    <form method="POST" class="d-inline">
                      <input type="hidden" name="edit_id" value="<?= $course->id ?>">
                      <button type="submit" class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil"></i>
                      </button>
                    </form>

                    <!-- DELETE -->
                    <form method="POST" class="d-inline">
                      <input type="hidden" name="delete_id" value="<?= $course->id ?>">
                      <button type="submit" class="btn btn-sm btn-danger"
                        onclick="return confirm('Delete this course?')">
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