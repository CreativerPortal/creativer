angular.module('app.ctr.album', ['service.album', 'angularFileUpload'])
    .controller('albumCtrl',['$scope', '$rootScope', '$location', '$animate', 'personalService','$routeParams', 'FileUploader', function($scope,$rootScope,$location,$animate,personalService,$routeParams, FileUploader) {


    if($routeParams.id_album && $scope.user){
        var bool = false;
        for(key in $scope.user.albums){
            if($scope.user.albums[key].id == $routeParams.id_album)
                bool = true;
        }
    if(!bool){
        personalService.getUserByAlbumId({id: $routeParams.id_album}).success(function (data) {
            $rootScope.user = $scope.user = data.user;
        })
    }
    }else if($routeParams.id_album && !$scope.user){
        personalService.getUserByAlbumId({id: $routeParams.id_album}).success(function (data) {
            $rootScope.user = $scope.user = data.user;
        })
    }



    if($routeParams.id_album && $scope.user != undefined){
        for(var key in $scope.user.albums){
            if($scope.user.albums[key].id == $routeParams.id_album){
                $scope.id = key;
            }
        }
    }
    if($routeParams.id_album){
        $scope.id_album = $routeParams.id_album;
    }
    if($routeParams.url_img){
        $scope.url_img = $routeParams.url_img;
    }
    if($routeParams.key_img){
        $scope.key_img = $routeParams.key_img;
        $scope.next_key_img = parseInt($routeParams.key_img)+1;
        $scope.previous = parseInt($routeParams.key_img)-1;
        $animate.enabled(false);
    }else{
        $animate.enabled(true);
    }

    $scope.math = window.Math;


    // end init controller


}]);


