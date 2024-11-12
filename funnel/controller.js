function openFunnel(event_names, success) {
    showBottomSheet('/mfm-analytics/funnel/index.html', success, function ($scope) {
        postContract("mfm-analytics", "events.php", {
            event_names: event_names.join(","),
        }, function (response) {
            $scope.funnel = response.funnel
        })
    })
}