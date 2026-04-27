<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<h2>Semesters</h2>

<form method="POST" action="?page=admin.saveSemester">

    <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">

    <input type="text" name="label" placeholder="Label" value="<?= $edit['label'] ?? '' ?>">
    <input type="text" name="academic_year" placeholder="Year" value="<?= $edit['academic_year'] ?? '' ?>">

    <button class="btn btn-primary">Save</button>
</form>

<hr>

<table>
<tr>
    <th>Label</th>
    <th>Year</th>
    <th>Active</th>
    <th>Actions</th>
</tr>

<?php foreach ($semesters ?? [] as $s): ?>
<tr>
    <td><?= htmlspecialchars($s['label']) ?></td>
    <td><?= htmlspecialchars($s['academic_year']) ?></td>
    <td><?= $s['is_active'] ? 'Yes' : 'No' ?></td>
    <td>
        <a href="?page=admin.semesters&id=<?= $s['id'] ?>">Edit</a>

        <form method="POST" action="?page=admin.deleteSemester" style="display:inline;">
            <input type="hidden" name="id" value="<?= $s['id'] ?>">
            <button class="btn btn-danger">Delete</button>
        </form>

        <form method="POST" action="?page=admin.toggleSemester" style="display:inline;">
            <input type="hidden" name="id" value="<?= $s['id'] ?>">
            <button class="btn btn-secondary">Set Active</button>
        </form>
    </td>
</tr>
<?php endforeach; ?>

</table>

<?php include 'layout/footer.php'; ?>
