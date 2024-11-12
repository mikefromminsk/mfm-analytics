function openReview(success) {
    trackCall(arguments)
    showBottomSheet('/mfm-analytics/review/index.html', success, function ($scope) {
        $scope.like = function like() {
            trackCall(arguments)
            showSuccess("Thank you for your feedback", $scope.back)
        }

        $scope.dislike = function dislike(message) {
            trackCall(arguments)
            showSuccess("Thank you for your feedback", $scope.back)
        }
    })
}