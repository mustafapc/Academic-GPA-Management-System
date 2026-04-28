<?php
if (file_exists('../layouts/header.php')) {
    include('../layouts/header.php');
}
?>

<h2>Dashboard</h2>

<div>GPA: <span id="gpa"></span></div>
<div id="courses"></div>

<?php
if (file_exists('../layouts/footer.php')) {
    include('../layouts/footer.php');
}
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../../public/js/student.js"></script>