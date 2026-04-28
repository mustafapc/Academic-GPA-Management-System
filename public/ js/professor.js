$(function () {

    // LOAD COURSES
    $('#semester').change(function () {
        $.get('../../api/courses.php', { semester: $(this).val() }, function (res) {
            let courses = JSON.parse(res);
            $('#course').html('');

            courses.forEach(c => {
                $('#course').append(`<option value="${c.id}">${c.name}</option>`);
            });
        });
    });

    // LOAD STUDENTS
    $('#course').change(function () {
        $.get('../../api/students.php', { course: $(this).val() }, function (res) {
            let students = JSON.parse(res);
            let html = '';

            students.forEach(s => {
                html += `
                <tr data-id="${s.id}">
                    <td>${s.name}</td>
                    <td>${s.id}</td>
                    <td>
                        <select>
                            <option>A</option>
                            <option>B</option>
                            <option>C</option>
                            <option>D</option>
                            <option>F</option>
                        </select>
                    </td>
                </tr>`;
            });

            $('#students').html(html);
        });
    });

    // SAVE GRADES
    $('#save').click(function () {
        let grades = [];

        $('#students tr').each(function () {
            grades.push({
                id: $(this).data('id'),
                grade: $(this).find('select').val()
            });
        });

        $.post('../../api/grades.php', { grades: JSON.stringify(grades) }, function () {
            $('#msg').text('Saved!');
        });
    });

});
$('#semester').change(function () {
    $('#msg').text('Loading courses...');

    $.get('../../api/courses.php', { semester: $(this).val() })
    .done(function (res) {
        let courses = JSON.parse(res);
        $('#course').html('');

        courses.forEach(c => {
            $('#course').append(`<option value="${c.id}">${c.name}</option>`);
        });

        $('#msg').text('');
    })
    .fail(function () {
        $('#msg').text('Error loading courses');
    });
});
$('#semester').change(function () {
    $('#msg').text('Loading courses...');

    $.get('../../api/courses.php', { semester: $(this).val() })
    .done(function (res) {
        let courses = JSON.parse(res);
        $('#course').html('');

        courses.forEach(c => {
            $('#course').append(`<option value="${c.id}">${c.name}</option>`);
        });

        $('#msg').text('');
    })
    .fail(function () {
        $('#msg').text('Error loading courses');
    });
});
if (students.length === 0) {
    $('#students').html('<tr><td colspan="3">No students found</td></tr>');
    return;
};