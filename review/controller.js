function openReview(success) {
    trackCall(arguments)
    showBottomSheet('/mfm-analytics/review/index.html', success, function ($scope) {
        $scope.like = function like() {
            trackCall(arguments)
            showSuccess(str.trank_you_for_your_feedback, $scope.back)
        }

        $scope.dislike = function dislike(message) {
            trackCall(arguments)
            showSuccess(str.trank_you_for_your_feedback, $scope.back)
        }
    })
}