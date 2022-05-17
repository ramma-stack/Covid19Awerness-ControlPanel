var optionsPostofMonths = {
	annotations: {
		position: 'back'
	},
	dataLabels: {
		enabled: false
	},
	chart: {
		type: 'bar',
		height: 300
	},
	fill: {
		opacity: 1
	},
	plotOptions: {},
	series: [{
		name: 'Published',
		data: [1, 20, 30, 20, 10, 20, 30, 20, 10, 30, 20, 10]
	}],
	colors: '#435ebe',
	xaxis: {
		categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
	},
	yaxis: {
		// tickAmount: 3,
		labels: {
			formatter: function (val) {
				return val.toFixed(0)
			}
		},
	},
}
let optionsGender = {
	series: [20, 80],
	labels: ['Male', 'Female'],
	colors: ['#435ebe', '#55c6e8'],
	chart: {
		type: 'donut',
		width: '100%',
		height: '350px'
	},
	legend: {
		position: 'bottom'
	},
	plotOptions: {
		pie: {
			donut: {
				size: '40%'
			}
		}
	}
}



var chartPostofMonths = new ApexCharts(document.querySelector("#post-of-months"), optionsPostofMonths);
var chartGender = new ApexCharts(document.getElementById('chart-gender'), optionsGender)

chartPostofMonths.render();
chartGender.render()

$.post('includes/postOfMonths.php', function (data) {

	chartPostofMonths.updateSeries([{
		data: data
	}]);

}, 'json');

$.post('includes/gender.php', function (data) {

	console.log(data);
	var result = data.map(function (x) {
		return parseInt(x, 10);
	});
	console.log(result);
	chartGender.updateSeries(result);

}, 'json');