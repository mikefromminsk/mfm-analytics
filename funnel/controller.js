function openFunnel(success) {
    trackCall(arguments)
    showDialog('/mfm-analytics/funnel/index.html', success, function ($scope) {
        $scope.funnels = [
            {
                "title": "Email open",
                "events": [
                    "email:send",
                    "ui:referer",
                ]
            },
            {
                "title": "Buy",
                "events": [
                    "ui:start",
                    "ui:openTokenProfile",
                ]
            }
        ]

        for (let funnel of $scope.funnels) {
            postContract("mfm-analytics", "funnel.php", {
                funnel: funnel.events.join(","),
            }, function (response) {
                funnel.response = response
            })
        }

        addChart($scope, "ui:start")
    })
}