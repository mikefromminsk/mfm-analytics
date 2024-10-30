function getChartOptions() {
    return {
        layout: {
            background: {color: '#222'},
            textColor: '#DDD',
        },
        grid: {
            vertLines: {color: '#444'},
            horzLines: {color: '#444'},
        },
        crosshair: {
            mode: LightweightCharts.CrosshairMode.Normal,
        },
    }
}

function chartResize(tradeChart, chart) {
    new ResizeObserver(entries => {
        if (entries.length === 0 || entries[0].target !== tradeChart) return;
        const newRect = entries[0].contentRect;
        chart.applyOptions({height: newRect.height, width: newRect.width});
    }).observe(tradeChart)
}

function createChart(id) {
    var tradeChart = document.getElementById(id)
    tradeChart.id = id + randomString(2)
    var chart = LightweightCharts.createChart(tradeChart, getChartOptions())
    chartResize(tradeChart, chart)
    return chart;
}

function addChart($scope, key) {
    function init() {
        setTimeout(function () {
            if ($scope.candleSeries == null) {
                $scope.candleSeries = createChart("chart").addCandlestickSeries();
            }
            $scope.setPeriod($scope.period_name)
        }, !window.chartLoaded ? 300 : 0)
        window.chartLoaded = true
    }

    $scope.periods = ['1M', '1H', '1D', '1W']
    $scope.period_name = '1M'
    $scope.setPeriod = function (period_name) {
        $scope.period_name = period_name
        postContract("mfm-analytics", "candles.php", {
            key: key,
            period_name: period_name,
        }, function (response) {
            var addTime = 0
            if (response.candles != null) {
                for (var candle of response.candles) {
                    addTime += (60 * 60 * 24 * 1000)
                    candle.time = new Date(candle.time * 1000 + addTime).toJSON().slice(0, 10)
                }
                $scope.candleSeries.setData(response.candles)
                $scope.showNoData = false
            } else {
                $scope.showNoData = true
            }
        });
    }

    $scope.updateChart = function () {
        $scope.setPeriod($scope.period_name)
    }

    init()
}

function openChart(key, success) {
    showBottomSheet('/mfm-analytics/chart/index.html', success, function ($scope) {
            $scope.key = key
            addFormats($scope)
            addChart($scope, key)
    })
}