
                    <!-- <div class="col-6" hidden>
                        <div class="card">
                            <canvas id="pieChart"></canvas>
                        </div>
                    </div>
                    <div class="col-6" hidden>
                        <div class="card">
                            <canvas id="doughnutChart"></canvas>
                        </div>
                    </div> -->
<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
<!-- <script type="text/javascript">
  
    const dataCapacity = <?= json_encode($airportCapacity) ?>;
    // Pie chart
    const pieLabels = dataCapacity.map(item => item.web_name);
    const pieData = dataCapacity.map(item => item.booking_count);
    const pieColors = ['#FF6384', '#36A2EB'];
    const ctxp = document.getElementById('pieChart'); 
    var pieChart = new Chart(ctxp, {
        type: 'pie',
        data: {
          labels: pieLabels,
          datasets: [
            {
              data: pieData,
              backgroundColor:pieColors,
            }
          ]
        },
        options: {
            responsive: true,
            plugins: {
              legend: {
                position: 'top',
              },
              title: {
                display: true,
                text: 'Websites'
              }
            }
        }
    });
    const ctxd = document.getElementById('doughnutChart');
    var doughnutChart=new Chart(ctxd, {
        type: 'doughnut',
        data: {
          labels: pieLabels,
          datasets: [
            {
              data: pieData,
              backgroundColor: pieColors,
            }
          ]
        },
        options: {
            responsive: true,
            plugins: {
              legend: {
                position: 'top',
              },
              title: {
                display: true,
                text: 'Supllier Websites'
              }
            }
        }
    });
    // end chart
</script> -->