function openReviews(success) {
    trackCall(arguments)
    showDialog('/mfm-analytics/reviews/index.html', success, function ($scope) {
        $scope.reviews = []
        let likes = []
        let dislikes = []
        postContract("mfm-analytics", "events.php", {
            app: "ui",
            name: "dislike",
        }, function (response) {
            dislikes = response.events
            $scope.reviews = dislikes.concat(likes)
            $scope.$apply()
        })
        postContract("mfm-analytics", "events.php", {
            app: "ui",
            name: "like",
        }, function (response) {
            likes = response.events
            $scope.reviews = dislikes.concat(likes)
            $scope.$apply()
        })

        $scope.sendAnswer = function reviewAnswer(item) {
            trackCall(arguments)
            postContract("mfm-messages", "send_to_address.php", {
                from_address: wallet.address(),
                to_address: item.user_id,
                message: item.answer,
                template: 'feedback',
            }, function () {
                item.answered = true
                $scope.$apply()
            })
        }
    })
}