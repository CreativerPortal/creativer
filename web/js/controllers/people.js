angular.module('app.ctr.people', ['service.header'])
    .controller('peopleCtrl',['$window', '$scope', '$rootScope', '$location', 'headerService', '$routeParams', function($window,$scope,$rootScope,$location,headerService,$routeParams) {

        if($routeParams.people_search){
            $rootScope.condition = 1;
            headerService.searchPeople({people_search:$routeParams.people_search}).success(function (data) {
                console.log(data.people);
                $scope.people = data.people;
            });
        }

}]);


