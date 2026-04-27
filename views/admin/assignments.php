<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<h2>Assignments</h2>

<form method="POST" action="?page=admin.saveAssignment">

<select name="professor_id">
<?php foreach ($professors ?? [] as $p): ?>
<option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
<?php endforeach; ?>
</select>

<select name="course_id">
<?php foreach ($courses ?? [] as $c): ?>
<option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
<?php endforeach; ?>
</select>

<select name="semester_id">
<?php foreach ($semesters ?? [] as $s): ?>
<option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['label']) ?></option>
<?php endforeach; ?>
</select>

<button class="btn btn-primary">Assign</button>

</form>

<?php include 'layout/footer.php'; ?>
