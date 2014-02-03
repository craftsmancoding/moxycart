<canvas id="sales-canvas" height="450" width="850"></canvas>
<script>

	var lineChartData = {
		labels : ["January","February","March","April","May","June","July","August","September","October","November","December"],
		datasets : [
			{
				fillColor : "rgba(220,220,220,0.5)",
				strokeColor : "rgba(220,220,220,5)",
				pointColor : "rgba(220,220,220,1)",
				pointStrokeColor : "#fff",
				data : [15,65,66,98,2,78,98,23,94,89,44,1000]
			}

		]
		
	}

var myLine = new Chart(document.getElementById("sales-canvas").getContext("2d")).Line(lineChartData);

</script>