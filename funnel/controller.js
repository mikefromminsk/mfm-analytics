function openFunnel(funnel_name, success) {
    showDialog('/mfm-analytics/funnel/index.html', success, function ($scope) {
        $scope.funnels = {
            "buy": [
                "openTokenProfile",
                "openExchange",
            ]
        }

        $scope.openFunnel = function openFunnel(funnel_name) {
            $scope.funnel_name = funnel_name
            trackCall(arguments)
            postContract("mfm-analytics", "funnel.php", {
                event_names: $scope.funnels[funnel_name].join(","),
            }, function (response) {
                $scope.data = response
            })
        }

        $scope.openFunnel(funnel_name)
    })
}