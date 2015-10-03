angular.module('app.ctr.header', ['service.header'])
    .controller('headerCtrl',['$window', '$scope', '$rootScope', '$location', 'headerService', '$stateParams', function($window,$scope,$rootScope,$location,headerService,$stateParams) {

    if(!$rootScope.condition){
        $rootScope.condition = 2;
    }

    headerService.getSoonEvents().success(function (data) {
        $rootScope.events_attend = data;
    })

    $rootScope.search = function(){
        if($rootScope.condition == 1){
            $location.path("/people/search/"+$scope.searchText);
        }else if($rootScope.condition == 2){
            $location.path("/products/search/"+$scope.searchText);
        }else if($rootScope.condition == 3){
            $location.path("/services/search/"+$scope.searchText);
        }
    }

    $rootScope.removeUser = function(){
        $rootScope.user = null;
    }

}]);


