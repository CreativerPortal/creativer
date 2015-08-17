angular.module('app.ctr.header', ['service.header'])
    .controller('headerCtrl',['$window', '$scope', '$rootScope', '$location', 'headerService', '$routeParams', function($window,$scope,$rootScope,$location,headerService,$routeParams) {


    $rootScope.search = function(){

        if($scope.condition == 1){
            $location.path("/people/search/"+$scope.searchText);
        }else if($scope.condition == 2){
            $location.path("/products/search/"+$scope.searchText);
        }else if($scope.condition == 3){
            $location.path("/services/search/"+$scope.searchText);
        }
    }

}]);


