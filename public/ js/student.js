$(function () {

    $.get('../../api/gpa.php', { action: 'current' }, function (res) {
        let data = JSON.parse(res);

        $('#gpa').text(data.gpa || 'N/A');

        let html = '';
        if (data.courses) {
            data.courses.forEach(c => {
                html += `<p>${c.name} - ${c.grade || 'Pending'}</p>`;
            });
        }

        $('#courses').html(html);
    });

});
function loadHistory() {
    $.get('../../api/gpa.php', { action: 'history' }, function (res) {
        let data = JSON.parse(res);

        let html = '';

        data.forEach(sem => {
            html += `<h3>${sem.semester} (GPA: ${sem.gpa})</h3>`;

            sem.courses.forEach(c => {
                html += `<p>${c.name} - ${c.grade}</p>`;
            });
        });

        $('#history').html(html);
    });
};
let gpa = data.gpa || 0;

$('#gpa').text(gpa);

if (gpa < 2) {
    $('#gpa').css('color', 'red');
} else if (gpa < 3) {
    $('#gpa').css('color', 'orange');
} else {
    $('#gpa').css('color', 'green');
}:
