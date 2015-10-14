angular.module('app.ctr.people', ['service.header'])
    .controller('peopleCtrl',['$window', '$scope', '$rootScope', '$location', 'headerService', '$stateParams', function($window,$scope,$rootScope,$location,headerService,$stateParams) {

        if($stateParams.people_search){
            $rootScope.condition = 1;
            headerService.searchPeople({people_search:$stateParams.people_search}).success(function (data) {
                console.log(data.people);
                $scope.people = data.people;
            });
        }

}]);


