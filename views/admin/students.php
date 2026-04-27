<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<h2>Students</h2>

<form method="POST" action="?page=admin.saveStudent">

    <input type="text" name="name" placeholder="Name">
    <input type="email" name="email" placeholder="Email">
    <input type="password" name="password" placeholder="Password">

    <button class="btn btn-primary">Save</button>
</form>

<hr>

<table>
<tr>
    <th>Name</th>
    <th>Email</th>
</tr>

<?php foreach ($students ?? [] as $s): ?>
<tr>
    <td><?= htmlspecialchars($s['name']) ?></td>
    <td><?= htmlspecialchars($s['email']) ?></td>
</tr>
<?php endforeach; ?>

</table>

<?php include 'layout/footer.php'; ?>
