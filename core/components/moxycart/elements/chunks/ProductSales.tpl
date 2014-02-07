<style>
	#product-sales {
		padding-bottom: 10px;
		padding-left: 50px;
		border-bottom: 1px solid #ddd;
	}
</style>

<canvas id="sales-canvas" height="300" width="850"></canvas>
<script>
	var lineChartData = {
		labels : month_year,
		datasets : [
			{
				fillColor : "rgba(151,187,205,0.5)",
				strokeColor : "rgba(151,187,205,1)",
				pointColor : "rgba(151,187,205,1)",
				pointStrokeColor : "#fff",
				data : sales_data
			}

		]
		
	}
	console.log(lineChartData.datasets);

var myLine = new Chart(document.getElementById("sales-canvas").getContext("2d")).Line(lineChartData);

</script>