function openReviews(success) {
    trackCall(arguments)
    showBottomSheet('/mfm-analytics/reviews/index.html', success, function ($scope) {
        postContract("mfm-analytics", "events.php", {
            event_names: ["dislike"],
        }, function (response) {
            $scope.events = response.events
        })
    })
}