angular.module('app.ctr.header', ['service.header'])
    .controller('headerCtrl',['$window', '$scope', '$rootScope', '$location', 'headerService', '$routeParams', function($window,$scope,$rootScope,$location,headerService,$routeParams) {

    if(!$rootScope.condition){
        $rootScope.condition = 1;
    }

    $rootScope.search = function(){
        if($rootScope.condition == 1){
            $location.path("/people/search/"+$scope.searchText);
        }else if($rootScope.condition == 2){
            $location.path("/products/search/"+$scope.searchText);
        }else if($rootScope.condition == 3){
            $location.path("/services/search/"+$scope.searchText);
        }
    }

}]);


