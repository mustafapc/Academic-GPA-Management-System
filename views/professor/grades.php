<?php
?>

<!DOCTYPE html>
<html>
<head>
    <title>Professor Grades</title>
</head>
<body>

<h2>Professor Grades</h2>

<select id="semester"></select>
<select id="course"></select>

<table border="1">
    <thead>
        <tr>
            <th>Name</th>
            <th>ID</th>
            <th>Grade</th>
        </tr>
    </thead>
    <tbody id="students"></tbody>
</table>

<button id="save">Save</button>
<div id="msg"></div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- YOUR JS -->
<script src="../../public/js/professor.js"></script>

</body>
</html>