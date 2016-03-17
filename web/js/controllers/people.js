angular.module('app.ctr.people', ['service.header'])
    .controller('peopleCtrl',['$window', '$scope', '$rootScope', '$location', 'headerService', '$stateParams', function($window,$scope,$rootScope,$location,headerService,$stateParams) {

        $rootScope.title = "Creativer - Люди";
        $rootScope.description = "Раздел Люди – интервью с мастерами о творчестве, беседы с интересными людьми, куча полезных советов, историй успеха.";

        if($stateParams.people_search){
            $scope.condition = 1;
            headerService.searchPeople({people_search:$stateParams.people_search, page:$stateParams.page }).success(function (data) {
                $scope.people_search = $stateParams.people_search;
                $scope.people = data.people.items;
                $scope.items = data.people;
                $scope.pages = [];
                $scope.pages[0] = $scope.items.currentPageNumber;
                $scope.currentPage = $scope.currentPage = $scope.items.currentPageNumber;
                var length = ($scope.items.totalCount / $scope.items.numItemsPerPage < 5) ? $scope.items.totalCount / $scope.items.numItemsPerPage : 5;
                length--;
                while (length > 0) {
                    if (($scope.pages[0] > 1 && $scope.pages[0] != $scope.currentPage - 2) || ($scope.pages[0] > 1 && $scope.pages[$scope.pages.length - 1] > $scope.items.totalCount / $scope.items.numItemsPerPage )) {
                        $scope.pages.unshift($scope.pages[0] - 1)
                        length = length - 1;
                    } else {
                        var p = parseInt($scope.pages[$scope.pages.length - 1]) + 1;
                        $scope.pages.push(p);
                        length = length - 1;
                    }
                }
            });
        }

}]);


