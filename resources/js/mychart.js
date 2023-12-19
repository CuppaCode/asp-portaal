import Chart from 'chart.js/auto';
import { TempusDominus } from '@eonasdan/tempus-dominus';

$(document).ready(function() {
    const chartdatetimepicker = document.getElementById('datetimepicker1');
    
    if (!chartdatetimepicker) {
        return;
    }
    
    new TempusDominus(chartdatetimepicker, {
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

    var damage_kind;

    $("#getAnalData").on("submit", function(e){
        if (
            typeof damage_kind === 'object' &&
            !Array.isArray(damage_kind) &&
            damage_kind !== null
        ) {
            damage_kind.destroy();
        }
        e.preventDefault(); 

        var company = $('#a_company_id').find(":selected").val();
        var sdate = $('#datetimepicker1Input').val();
        var edate = $('#datetimepicker2Input').val();

        $.post("/api/analytics/get-data", { company: company, startdate: sdate, enddate: edate } , function(res) {
            console.log(res.damage_costs);

            var damage_costs_arr = new Array();
            var damage_months_arr = new Array();
            const damage_costs = res.damage_costs;
            damage_costs.forEach(eachDamage);
             
            function eachDamage(item) {
                damage_costs_arr.push(item.damage_costs);
                damage_months_arr.push(item.month);
            }

            const data_damage_kind = {
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
            
            const config_damage_kind = {
                type: 'doughnut',
                data: data_damage_kind,
              };
            
            damage_kind = new Chart(
                document.getElementById('kind_accident'),
                config_damage_kind
            );

            const data_damage_costs = {
            labels: damage_months_arr,
            datasets: [{
                label: 'My First Dataset',
                data: damage_costs_arr,
                backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(255, 159, 64, 0.2)',
                'rgba(255, 205, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(201, 203, 207, 0.2)'
                ],
                borderColor: [
                'rgb(255, 99, 132)',
                'rgb(255, 159, 64)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)',
                'rgb(54, 162, 235)',
                'rgb(153, 102, 255)',
                'rgb(201, 203, 207)'
                ],
                borderWidth: 1
            }]
            };

            const config_damage_costs = {
                type: 'bar',
                data: data_damage_costs,
                options: {
                  scales: {
                    y: {
                      beginAtZero: true
                    }
                    

                  }
                },
              };

            damage_costs = new Chart(
                document.getElementById('damage_costs'),
                config_damage_costs
            );


          });
    });

});


