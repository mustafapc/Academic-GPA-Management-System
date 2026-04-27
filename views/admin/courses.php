<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<h2>Courses</h2>

<form method="POST" action="?page=admin.saveCourse">

    <input type="text" name="name" placeholder="Course name">
    <input type="number" name="credits" placeholder="Credits">

    <select name="semester_id">
        <?php foreach ($semesters ?? [] as $s): ?>
            <option value="<?= $s['id'] ?>">
                <?= htmlspecialchars($s['label']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button class="btn btn-primary">Save</button>
</form>

<hr>

<table>
<tr>
    <th>Name</th>
    <th>Credits</th>
</tr>

<?php foreach ($courses ?? [] as $c): ?>
<tr>
    <td><?= htmlspecialchars($c['name']) ?></td>
    <td><?= htmlspecialchars($c['credits']) ?></td>
</tr>
<?php endforeach; ?>

</table>

<?php include 'layout/footer.php'; ?>
