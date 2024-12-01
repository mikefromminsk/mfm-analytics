function openAnalytics(success) {
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
                "title": "Place orders",
                "events": [
                    "ui:start",
                    "ui:openTokenProfile",
                    "ui:place",
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