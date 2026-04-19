<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EduSync Dashboard</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<?php include 'includes/nav.php'; ?>
<?php include 'config/db.php'; ?>

<?php 
$sqlState = $pdo->query("SELECT 
(SELECT COUNT(*) FROM students) AS total_students,
(SELECT COUNT(*) FROM users 
    JOIN roles ON users.role_id = roles.id 
    WHERE roles.role_name = 'Professor'
) AS total_teachers,
(SELECT COUNT(*) FROM courses) AS total_courses,
(SELECT COUNT(*) FROM classes) AS total_classes
");

$stats = $sqlState->fetch(PDO::FETCH_OBJ);

/* ================= LATEST STUDENTS ================= */
$students = $pdo->query("
SELECT 
    students.id,
    users.firstname,
    users.lastname,
    classes.name AS class_name
FROM students
JOIN users ON students.user_id = users.id
JOIN classes ON students.class_id = classes.id
ORDER BY students.id DESC
LIMIT 5
")->fetchAll(PDO::FETCH_OBJ);


/* ================= LATEST COURSES ================= */
$courses = $pdo->query("
SELECT 
    id,
    title,
    total_hours
FROM courses
ORDER BY id DESC
LIMIT 5
")->fetchAll(PDO::FETCH_OBJ);
?>

<div class="container py-4">

  <!-- HEADER -->
  <div class="mb-4">
    <h2 class="fw-bold">Dashboard</h2>
    <p class="text-muted">Welcome back 👋 manage your school system</p>
  </div>

  <!-- STATS CARDS -->
  <div class="row g-3 mb-4">

    <div class="col-md-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div>
            <h6 class="text-muted">Students</h6>
            <h3 class="fw-bold"><?= $stats->total_students ?></h3>
          </div>
          <i class="bi bi-people fs-1 text-primary"></i>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div>
            <h6 class="text-muted">Teachers</h6>
            <h3 class="fw-bold"><?= $stats->total_teachers ?></h3>
          </div>
          <i class="bi bi-person-badge fs-1 text-success"></i>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div>
            <h6 class="text-muted">Courses</h6>
            <h3 class="fw-bold"><?= $stats->total_courses ?></h3>
          </div>
          <i class="bi bi-journal-bookmark fs-1 text-info"></i>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div>
            <h6 class="text-muted">Classes</h6>
            <h3 class="fw-bold"><?= $stats->total_classes ?></h3>
          </div>
          <i class="bi bi-building fs-1 text-warning"></i>
        </div>
      </div>
    </div>

  </div>

  <!-- TABLES ROW -->
  <div class="row g-3">

    <!-- STUDENTS -->
    <div class="col-md-6">
      <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Latest Students</h5>
          <a href="students.php" class="btn btn-sm btn-primary">View All</a>
        </div>

        <div class="card-body p-0">

          <table class="table table-hover mb-0 align-middle">
            <thead class="table-dark">
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Class</th>
              </tr>
            </thead>

            <tbody>
              <?php foreach ($students as $student): ?>
                <tr>
                  <td><?= $student->id ?></td>
                  <td><?= $student->firstname . ' ' . $student->lastname ?></td>
                  <td><span class="badge bg-secondary"><?= $student->class_name ?></span></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

        </div>

      </div>
    </div>

    <!-- COURSES -->
    <div class="col-md-6">
      <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Latest Courses</h5>
          <a href="courses.php" class="btn btn-sm btn-success">View All</a>
        </div>

        <div class="card-body p-0">

          <table class="table table-hover mb-0 align-middle">
            <thead class="table-dark">
              <tr>
                <th>#</th>
                <th>Title</th>
                <th>Hours</th>
              </tr>
            </thead>

            <tbody>
              <?php foreach ($courses as $course): ?>
                <tr>
                  <td><?= $course->id ?></td>
                  <td><?= $course->title ?></td>
                  <td><span class="badge bg-info"><?= $course->total_hours ?>h</span></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

        </div>

      </div>
    </div>

  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>