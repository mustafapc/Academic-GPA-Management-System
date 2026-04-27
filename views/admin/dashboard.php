<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<h2>Dashboard</h2>

<p>Welcome Admin</p>

<div class="card">Students: <?= $studentsCount ?? 0 ?></div>
<div class="card">Professors: <?= $professorsCount ?? 0 ?></div>
<div class="card">Semesters: <?= $semestersCount ?? 0 ?></div>
<div class="card">Courses: <?= $coursesCount ?? 0 ?></div>

<?php include 'layout/footer.php'; ?>
