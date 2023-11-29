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

            const data = {
                labels: [
                  'Tranport',
                  'Laden',
                  'Overig'
                ],
                datasets: [{
                //   label: 'My First Dataset',
                  data: [res.transport, res.traffic, res.other],
                  backgroundColor: [
                    'red',
                    'blue',
                    'green'
                  ],
                  hoverOffset: 4
                }]
              };
            
            const config = {
                type: 'doughnut',
                data: data,
              };
            
            const damage_kind = new Chart(
                document.getElementById('kind_accident'),
                config
            );

          });
    });

});

