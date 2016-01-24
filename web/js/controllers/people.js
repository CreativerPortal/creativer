angular.module('app.ctr.people', ['service.header'])
    .controller('peopleCtrl',['$window', '$scope', '$rootScope', '$location', 'headerService', '$stateParams', function($window,$scope,$rootScope,$location,headerService,$stateParams) {

        if($stateParams.people_search){
            $rootScope.condition = 1;
            headerService.searchPeople({people_search:$stateParams.people_search, page:$stateParams.page }).success(function (data) {
                $rootScope.people_search = $stateParams.people_search;
                $scope.people = data.people.items;
                $rootScope.items = data.people;
                $rootScope.pages = [];
                $rootScope.pages[0] = $scope.items.currentPageNumber;
                $rootScope.currentPage = $scope.currentPage = $scope.items.currentPageNumber;
                var length = ($scope.items.totalCount / $scope.items.numItemsPerPage < 5) ? $scope.items.totalCount / $scope.items.numItemsPerPage : 5;
                length--;
                while (length > 0) {
                    if (($rootScope.pages[0] > 1 && $rootScope.pages[0] != $rootScope.currentPage - 2) || ($rootScope.pages[0] > 1 && $rootScope.pages[$rootScope.pages.length - 1] > $scope.items.totalCount / $scope.items.numItemsPerPage )) {
                        $rootScope.pages.unshift($rootScope.pages[0] - 1)
                        length = length - 1;
                    } else {
                        var p = parseInt($rootScope.pages[$rootScope.pages.length - 1]) + 1;
                        $rootScope.pages.push(p);
                        length = length - 1;
                    }
                }
            });
        }

}]);


