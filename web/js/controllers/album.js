angular.module('app.ctr.album', ['service.album', 'angularFileUpload'])
    .controller('albumCtrl',['$scope', '$window', '$rootScope', '$location', '$animate', 'albumService','$routeParams', 'FileUploader', function($scope,$window,$rootScope,$location,$animate,albumService,$routeParams, FileUploader) {


    if($routeParams.id_album && $scope.user){
        var bool = false;
        for(key in $scope.user.albums){
            if($scope.user.albums[key].id == $routeParams.id_album)
                bool = true;
        }
    if(!bool){
        albumService.getUserByAlbumId({id: $routeParams.id_album}).success(function (data) {
            $rootScope.user = $scope.user = data.user;
        })
    }
    }else if($routeParams.id_album && !$scope.user){
        albumService.getUserByAlbumId({id: $routeParams.id_album}).success(function (data) {
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
    $scope.height = $window.innerHeight-150;


    // get comments for image
    $scope.$watch('user', function() {
        if($routeParams.key_img && $scope.user){
            $scope.user.comments = null;
            albumService.getImageComments({image_id:$scope.user.albums[$scope.id].images[$routeParams.key_img].id}).success(function (data) {
                    $scope.user.comments = data.image_comments;
                //console.log($scope.user);
            });
        }
    });


    // end init controller


    $scope.deleteImage = function(image_id,key_img,key_album){

        albumService.deleteImage({image_id:image_id}).success(function (data) {
            $scope.user.albums[key_album].images.splice(key_img,1);
            $location.path("/album/"+$routeParams.id_album+'/'+$scope.user.albums[key_album].images[key_img].name+'/'+key_img);
        });
    }

    $scope.saveImageComment = function(image,text){
        var username = $rootScope.username;
        var lastname = $rootScope.lastname;
        var img = $rootScope.img;
        image.image_comments.push({id: 0, username:username, lastname:lastname, avatar: {img:img}, text: text});
        albumService.saveImageComment({image_id:image.id,text:text,id: $rootScope.id_user}).success(function (data) {
            $scope.user.comments = data.image_comments;
        });
    }

    //////////////////////////////////////////////


}]);


