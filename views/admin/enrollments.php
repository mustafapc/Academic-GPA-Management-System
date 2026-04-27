<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<h2>Enrollments</h2>

<form method="POST" action="?page=admin.saveEnrollments">

    <input type="hidden" name="student_id" value="<?= $student['id'] ?? '' ?>">

    <?php foreach ($semesters ?? [] as $s): ?>
        <label>
            <input type="checkbox"
                   name="semester_ids[]"
                   value="<?= $s['id'] ?>"
                   <?= in_array($s['id'], $enrolled ?? []) ? 'checked' : '' ?>>
            <?= htmlspecialchars($s['label']) ?>
        </label><br>
    <?php endforeach; ?>

    <button class="btn btn-primary">Save</button>
</form>

<?php include 'layout/footer.php'; ?>
