angular.module('app.ctr.album', ['service.album', 'service.personal', 'service.socket', 'angularFileUpload', 'service.chat', 'ngImgCrop',])
    .controller('albumCtrl',['$scope', '$window', '$rootScope', '$location', '$timeout', 'albumService', 'personalService', '$routeParams', 'FileUploader', 'socket', 'chat', function($scope,$window,$rootScope,$location,$timeout,albumService,personalService,$routeParams, FileUploader, socket, chat) {


    if($routeParams.id_album && $scope.user) {
        var exists_album = false;
        for(var key in $scope.user.albums){
            if($routeParams.id_album == $scope.user.albums[key].id){
                exists_album = true;
            }
        }
        if(!exists_album){
            albumService.getUserByAlbumId({id: $routeParams.id_album}).success(function (data) {
                $rootScope.user = $scope.user = data.user;
                for (key in $scope.user.favorits_with_me) {
                    if ($scope.user.favorits_with_me[key].id == $rootScope.id_user) {
                        $scope.favorit = true;
                    } else {
                        $scope.favorit = false;
                    }
                }
            });
        }
    }else{
        albumService.getUserByAlbumId({id: $routeParams.id_album}).success(function (data) {
            $rootScope.user = $scope.user = data.user;
            for (key in $scope.user.favorits_with_me) {
                if ($scope.user.favorits_with_me[key].id == $rootScope.id_user) {
                    $scope.favorit = true;
                } else {
                    $scope.favorit = false;
                }
            }

            albumService.getAlbumComments({id_album:$routeParams.id_album}).success(function (data) {
                $scope.user.albums[$routeParams.id_album].images = data.images;
            });
        });
    }

    //if($routeParams.id_album_edit) {
    //    albumService.getAlbumById({id_album: $routeParams.id_album_edit}).success(function (data) {
    //        $scope.user.albums[key_album].images.splice(key_img, 1);
    //        $location.path("/album/" + $routeParams.id_album + '/' + $scope.user.albums[key_album].images[key_img].name + '/' + key_img);
    //    });
    //}


    $scope.$watch('user', function() {
        if($routeParams.id_album && $scope.user != undefined){
            for(var key in $scope.user.albums){
                if($scope.user.albums[key].id == $routeParams.id_album){
                    $scope.album_key = key;
                }
            }

            if(!$routeParams.key_img && $routeParams.url_img){
                for(var key_img in $scope.user.albums[$scope.album_key].images){
                    if($routeParams.url_img == $scope.user.albums[$scope.album_key].images[key_img].name){
                        $location.replace();
                        $location.path("/album/"+$routeParams.id_album+'/'+$scope.user.albums[$scope.album_key].images[key_img].name+'/'+key_img);
                    }
                }
            }


            if(!$rootScope.image_previews){
                $rootScope.image_previews = [];
            }

            if($routeParams.id_album && $routeParams.key_img && ($scope.user.id != $rootScope.id_user)){
                if(!$rootScope.image_previews[$routeParams.id_album]){
                    $rootScope.image_previews[$routeParams.id_album] = [];
                }
                if($rootScope.image_previews.indexOf($scope.user.albums[$scope.album_key].images[$routeParams.key_img].id) == -1){
                    $rootScope.image_previews[$routeParams.id_album].push($scope.user.albums[$scope.album_key].images[$routeParams.key_img].id);
                }
            }

            console.log($rootScope.image_previews);

        }
    });


    if($routeParams.id_album){
        $scope.id_album = $routeParams.id_album;
    }
    if($routeParams.url_img){
        $scope.url_img = $routeParams.url_img;
    }
    if($routeParams.key_img || $rootScope.key_img){
        $rootScope.key_img = $routeParams.key_img;
        $scope.next_key_img = parseInt($routeParams.key_img)+1;
        $scope.previous = parseInt($routeParams.key_img)-1;
    }
    else{
        $rootScope.key_img = undefined;
    }

    $scope.math = window.Math;
    $scope.height = $window.innerHeight-150;


    chat.init();
        socket.emit("new message",{id_user: $scope.id_user})
        $window.onfocus = function(){
        socket.emit("new message",{id_user: $scope.id_user})
    }

        // end init controller


    $scope.closeImg = function(){
        albumService.imagePreviews({image_previews:$rootScope.image_previews}).success(function (data) {
            $rootScope.image_previews = [];
        });
    }

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
        image.image_comments.push({id: 0, username:username, lastname:lastname, avatar: {img:img}, text: text ,date: new Date()});
        albumService.saveImageComment({image_id:image.id,text:text,id: $rootScope.id_user}).success(function (data) {
            $scope.text_comment = undefined;
        });
    }

    $scope.like = function(id,album_key,image_key){
        albumService.like({image_id:id}).success(function (data) {
            $scope.user.albums[album_key].images[image_key].likes = data.likes;
            $scope.user.likes = data.likes_user;
            $scope.user.albums[album_key].likes = data.likes_album;
        });
    }

    $scope.addFavorits = function(id){
        $scope.loader = true;
        personalService.addFavorits({id:id}).success(function (data) {
            $scope.loader = false;
            $rootScope.user = $scope.user = data.user;
            $scope.$apply();
            $scope.favorit = false;
            for(key in $scope.user.favorits_with_me){
                if($scope.user.favorits_with_me[key].id ==  $rootScope.id_user){
                    $scope.favorit = true;
                    break;
                }
            }
        });
    }

    $scope.removeFavorits = function(id){
        $scope.loader = true;
        personalService.removeFavorits({id:id}).success(function (data) {
            $scope.loader = false;
            $rootScope.user = $scope.user = data.user;
            $scope.$apply();
            $scope.favorit = false;
            for(key in $scope.user.favorits_with_me){
                if($scope.user.favorits_with_me[key].id ==  $rootScope.id_user){
                    $scope.favorit = true;
                    break;
                }
            }
        });
    }

    $scope.updateAvatar = function(image){
        $scope.loader = true;
        personalService.updateAvatar({img:image}).success(function (data) {
            $scope.user = data.user;
            $rootScope.avatar = $scope.user.avatar.img;
            $scope.myImage = false;
            $scope.loader = false;
        });
    }

    // crop image

    $scope.myImage=false;
    $scope.myCroppedImage=false;

    var handleFileSelect=function(evt) {
        var file=evt.currentTarget.files[0];
        var reader = new FileReader();
        reader.onload = function (evt) {
            $scope.$apply(function($scope){
                $scope.myImage=evt.target.result;
            });
        };
        reader.readAsDataURL(file);
    };

    $timeout(function(){
        angular.element(document.querySelector('#fileInput')).on('change', handleFileSelect);
    }, 2000);

    ////////////////////////////////////////////////////


}]);


