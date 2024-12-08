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
        timeScale: {
            timeVisible: true,
            secondsVisible: false,
        }
    }
}

function chartResize(tradeChart, chart) {
    new ResizeObserver(entries => {
        if (entries.length === 0 || entries[0].target !== tradeChart) return
        const newRect = entries[0].contentRect
        chart.applyOptions({height: newRect.height, width: newRect.width})
    }).observe(tradeChart)
}

function createChart(id) {
    var tradeChart = document.getElementById(id)
    tradeChart.id = id + randomString(2)
    var chart = LightweightCharts.createChart(tradeChart, getChartOptions())
    chartResize(tradeChart, chart)
    return chart
}

function addChart($scope, key, accomulate_key) {
    function init() {
        setTimeout(function () {
            if ($scope.candleSeries == null) {
                let chart = createChart("chart")
                $scope.candleSeries = chart.addCandlestickSeries()
                $scope.accomulateSeries = chart.addHistogramSeries({
                    color: '#26a69a',
                    priceFormat: {
                        type: 'volume',
                    },
                    priceScaleId: '',
                })
                $scope.accomulateSeries.priceScale().applyOptions({
                    scaleMargins: {
                        top: 0.9,
                        bottom: 0,
                    },
                })
            }
            $scope.setPeriod($scope.period_name)
        }, !window.chartLoaded ? 300 : 0)
        window.chartLoaded = true
    }

    $scope.periods = ['M', 'H', 'D', 'W']
    $scope.period_name = 'M'
    $scope.setPeriod = function (period_name) {
        $scope.period_name = period_name
        postContract("mfm-analytics", "candles.php", {
            key: key,
            accomulate_key: accomulate_key,
            period_name: period_name,
        }, function (response) {
            if (response.candles != null) {
                $scope.candleSeries.setData(response.candles)
                $scope.accomulateSeries.setData(response.accomulate)
                $scope.showNoData = false
            } else {
                $scope.showNoData = true
            }
        })
    }

    $scope.updateChart = function () {
        $scope.setPeriod($scope.period_name)
    }

    init()
}

function openChartWithAccomulate(key, accomulate_key, success) {
    showBottomSheet('/mfm-analytics/chart/index.html', success, function ($scope) {
        $scope.key = key
        $scope.accomulate_key = accomulate_key
        addChart($scope, key, accomulate_key)
    })
}