import Chart from 'chart.js/auto';

$(document).ready(function() {
    $('#a_company_id').on('select2:select', function () {

        console.log('12312');

    });
});

// const labels = [
//     'January',
//     'February',
//     'March',
//     'April',
//     'May',
//     'June',
// ];

// const data = {
//     labels: labels,
//     datasets: [{
//         label: 'My First dataset',
//         backgroundColor: 'rgb(255, 99, 132)',
//         borderColor: 'rgb(255, 99, 132)',
//         data: [0, 10, 5, 2, 20, 30, 45],
//     }]
// };

// const config = {
//     type: 'line',
//     data: data,
//     options: {}
// };

// new Chart(
//     document.getElementById('myChart'),
//     config
// );