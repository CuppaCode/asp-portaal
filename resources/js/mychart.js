import Chart from 'chart.js/auto';
import { TempusDominus } from '@eonasdan/tempus-dominus';

new TempusDominus(document.getElementById('datetimepicker1'), {
    display: {
        icons: {
          time: 'fa fa-clock',
          date: 'fa fa-calendar',
          up: 'fa fa-arrow-up',
          down: 'fa fa-arrow-down',
          previous: 'fa fa-chevron-left',
          next: 'fa fa-chevron-right',
          today: 'fa fa-calendar-check',
          clear: 'fa fa-trash',
          close: 'fa fa-x',
        },
        buttons: {
          today: true,
          clear: false,
          close: true,
        },
        components: {
            clock: false,
        },
      },
      useCurrent: true,
      localization: {
        locale: 'nl',
        dateFormats: {
            LTS: 'h:mm:ss T',
            LT: 'h:mm T',
            L: 'dd-MM-yyyy',
            LL: 'MMMM d, yyyy',
            LLL: 'MMMM d, yyyy h:mm T',
            LLLL: 'dddd, MMMM d, yyyy h:mm T'
          },
          ordinal: (n) => n,
          format: 'L'
      },
});

$(document).ready(function() {

    $("#getAnalData").on("submit", function(e){
        e.preventDefault(); 

        var company = $('#a_company_id').find(":selected").val();
        var sdate = $('#datetimepicker1Input').val();
        var edate = $('#datetimepicker2Input').val();

        $.post("/api/analytics/get-data", { company: company, startdate: sdate, enddate: edate } , function(res) {
            console.log(res);
            alert( "Load was performed." );
            
          });
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