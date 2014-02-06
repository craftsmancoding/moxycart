<style>
	#product-sales {
		padding-bottom: 10px;
		padding-left: 50px;
		border-bottom: 1px solid #ddd;
	}
</style>

<form action="#" method="post" id="product-sales">
	<label for="select-year">Select Year:</label>
	<select name="year" id="select-year"></select>
	<div>Total Revenue: <strong>$[[+total]].00</strong></div>
</form>

<canvas id="sales-canvas" height="300" width="850"></canvas>
<script>
	var lineChartData = {
		labels : ["January","February","March","April","May","June","July","August","September","October","November","December"],
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

<script>
var select = document.getElementById('select-year'),
    year = new Date().getFullYear(),
    selected = '';
    html = '<option></option>';

for(i = year; i >= year-18; i--) {
	if(selected_year == i) {
		selected = 'selected';
	} else {
		selected = '';
	}
  html += '<option value="' + i + '" '+selected+' >' + i + '</option>';
}

select.innerHTML = html;
</script>
<script>
	$(function(){
		$('#select-year').on('change',function(e){
			window.location.href = window.location.pathname + '?year='+$(this).val()+'#product-sales';
	    });

	});
</script>