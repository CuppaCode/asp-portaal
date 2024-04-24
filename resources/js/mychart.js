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
	var damage_costs_graph;
	var saved_costs_graph;

    $("#getAnalData").on("submit", function(e){
      $("#analytics-area").removeClass('d-none');
        if (
            typeof damage_kind === 'object' &&
            !Array.isArray(damage_kind) &&
            damage_kind !== null
        ) {
            damage_kind.destroy();
			
        } 
		
		if (
			typeof damage_costs_graph === 'object' &&
            !Array.isArray(damage_costs_graph) &&
            damage_costs_graph !== null
		) {
			damage_costs_graph.destroy();
		}

		if (
			typeof saved_costs_graph === 'object' &&
            !Array.isArray(saved_costs_graph) &&
            saved_costs_graph !== null
		) {
			saved_costs_graph.destroy();
		}

        e.preventDefault(); 

        var company = $('#a_company_id').find(":selected").val();
        var sdate = $('#datetimepicker1Input').val();
        var edate = $('#datetimepicker2Input').val();

        $.post("/admin/analytics/get-data", { company: company, startdate: sdate, enddate: edate } , function(res) {
            console.log(res);

            var damage_costs_arr = new Array();
            var damage_months_arr = new Array();
            var saved_costs_arr = new Array();

            const saved_costs = res.saved_costs;
            const damage_costs = res.damage_costs;

            damage_costs.forEach(eachDamage);
             
            function eachDamage(item) {
				        console.log(item.month);	
				
                damage_costs_arr.push(item.damage_costs);
                damage_months_arr.push(item.month);
            }

            saved_costs.forEach(eachSavedCost);
            
            function eachSavedCost(item) {
              saved_costs_arr.push(item.saved_costs);
            }

            $('.legend_transportation').text('Transport: '+ res.transport);
            $('.legend_traffic').text('Verkeer: '+ res.traffic);
            $('.legend_other').text('Overig: '+ res.other);

			// Table of damage kind
            const data_damage_kind = {
                labels: [
                  'Transport',
                  'Verkeer',
                  'Overig'
                ],
                datasets: [{
                //   label: 'My First Dataset',
                  data: [res.transport, res.traffic, res.other],
                  backgroundColor: [
                    'rgb(52, 74, 155)',
                    'rgb(40, 161, 81)',
                    'black'
                  ],
                  hoverOffset: 4
                }]
              };
            
            const config_damage_kind = {
                type: 'doughnut',
                data: data_damage_kind,
                options: {
                  plugins: {
                    legend: {
                      display: false,
                    }
                  }
                },
              };
            
            damage_kind = new Chart(
                document.getElementById('kind_accident'),
                config_damage_kind
            );


			// Table of damage costs
            const data_damage_costs = {
            labels: damage_months_arr,
            datasets: [{
                label: 'Schade kosten',
                data: damage_costs_arr,
                backgroundColor: [
                  'rgb(52, 74, 155)',
                  'rgb(40, 161, 81)',
                  'rgb(52, 74, 155)',
                  'rgb(40, 161, 81)',
                  'rgb(52, 74, 155)',
                  'rgb(40, 161, 81)',
                  'rgb(52, 74, 155)',
                  'rgb(40, 161, 81)',
                  'rgb(52, 74, 155)',
                  'rgb(40, 161, 81)',
                  'rgb(52, 74, 155)',
                  'rgb(40, 161, 81)',
                  'rgb(52, 74, 155)',
                  'rgb(40, 161, 81)',
                ],
                borderColor: [
                  'rgb(52, 74, 155)',
                  'rgb(40, 161, 81)',
                  'rgb(52, 74, 155)',
                  'rgb(40, 161, 81)',
                  'rgb(52, 74, 155)',
                  'rgb(40, 161, 81)',
                  'rgb(52, 74, 155)',
                  'rgb(40, 161, 81)',
                  'rgb(52, 74, 155)',
                  'rgb(40, 161, 81)',
                  'rgb(52, 74, 155)',
                  'rgb(40, 161, 81)',
                  'rgb(52, 74, 155)',
                  'rgb(40, 161, 81)',
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
                  },
                  plugins: {
                    legend: {
                      display: false,
                    }
                  },
                },
              };

            damage_costs_graph = new Chart(
                document.getElementById('damage_costs'),
                config_damage_costs
            );


			// Table of saving costs
			const data_saved_costs = {
				labels: damage_months_arr,
				datasets: [
          {
            label: 'Schade kosten',
            data: damage_costs_arr,
            type: 'line',
            order: 1,
            backgroundColor: 'rgb(52, 74, 155)',
            borderColor: 'rgb(52, 74, 155)',
          },
          {
            label: 'Besparing ',
            data: saved_costs_arr,
            type: 'line',
            order: 0,
            backgroundColor: 'rgb(40, 161, 81)',
            borderColor: 'rgb(40, 161, 81)',
          },
        ]
      };

			const config_saved_costs = {
				data: data_saved_costs,
				options: {
				  responsive: true,
				  plugins: {
            legend: {
              position: 'top',
            },
            title: {
              display: false,
            }
				  }
				},
			  };

			  saved_costs_graph = new Chart(
                document.getElementById('saved_costs'),
                config_saved_costs
            );
          });
    });

});


