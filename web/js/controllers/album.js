angular.module('app.ctr.album', ['service.album', 'angularFileUpload', 'service.personal', 'service.socket', 'angularFileUpload', 'service.chat', 'angularSearchTree'])
    .controller('albumCtrl',['$scope', '$window', '$rootScope', '$location', '$timeout', 'albumService', 'personalService', '$routeParams', 'FileUploader', 'socket', 'chat', 'searchTree', function($scope,$window,$rootScope,$location,$timeout,albumService,personalService,$routeParams, FileUploader, socket, chat, SearchTree) {


    if(($routeParams.id_album_edit || $routeParams.id_album) && $scope.user) {
        var exists_album = false;
        var id_album = $routeParams.id_album?$routeParams.id_album:$routeParams.id_album_edit;
        for(var key in $scope.user.albums){
            if(id_album == $scope.user.albums[key].id){
                exists_album = true;
            }
        }
        if(!exists_album){
            albumService.getUserByAlbumId({id: id_album}).success(function (data) {
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
        var id_album = $routeParams.id_album?$routeParams.id_album:$routeParams.id_album_edit;
        albumService.getUserByAlbumId({id: id_album}).success(function (data) {
            $rootScope.user = $scope.user = data.user;
            for (key in $scope.user.favorits_with_me) {
                if ($scope.user.favorits_with_me[key].id == $rootScope.id_user) {
                    $scope.favorit = true;
                } else {
                    $scope.favorit = false;
                }
            }

            if($routeParams.id_album){
                albumService.getAlbumComments({id_album:id_album}).success(function (data) {
                    if($scope.user.albums[$routeParams.id_album].images)
                       $scope.user.albums[$routeParams.id_album].images = data.images;
                });
            }
        });
    }


    if($routeParams.id_album_edit){
        albumService.getAlbumById({id_album:$routeParams.id_album_edit}).success(function (data) {
            $scope.edit_album = data.album;
            $scope.edit_album.remove_post = false
            $scope.res = $scope.edit_album.images.length/3;
            var categories = [];
            for(var key in data.album.categories){
                categories.push(data.album.categories[key]);
            }

            personalService.getAllCategories().success(function (data) {
                $rootScope.data = $scope.data = data.categories;
                var search_tree = SearchTree();
                $scope.selectOnly1Or2 = function (item, selectedItems) {
                    if (selectedItems !== undefined && selectedItems.length >= 20) {
                        return false;
                    } else {
                        return true;
                    }
                };

                for (var cat in categories) {
                    $scope.data = search_tree({id: categories[cat].id},$scope.data);
                }

            });

        });
    }



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
        $rootScope.overflow = true;
    }else{
        $rootScope.overflow = false;
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
        $scope.loader = true;
        var user = {
            user: {
                id: 0,
                username: $rootScope.username,
                lastname: $rootScope.lastname,
                avatar: $rootScope.avatar
            },
            text: text,
            date: new Date()
        }
        image.image_comments.push(user);
        albumService.saveImageComment({image_id:image.id,text:text,id: $rootScope.id_user}).success(function (data) {
            $scope.text_comment = undefined;
            $scope.loader = false;
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

    $scope.editTextImage = function(id,text){
        albumService.editTextImage({id:id,text:text}).success(function (data) {
        });
    }

    $scope.editDescriptionAlbum = function(id,description){
        albumService.editDescriptionAlbum({id:id,description:description}).success(function (data) {
        });
    }

    $scope.editNameAlbum = function(id,name){
        albumService.editNameAlbum({id:id,name:name}).success(function (data) {
        });
    }

    $scope.deleteAlbum = function(id){
        albumService.deleteAlbum({id:id}).success(function (data) {
            $location.path("/"+$scope.user.id);
        });
    }

    $scope.removeImage = function(id){
        albumService.removeImage({id:id}).success(function (data) {
            for(var key in $scope.edit_album.images){
                if($scope.edit_album.images[key].id == id){
                    $scope.edit_album.images.splice(key, 1);
                }
            }
        });
    }

    $scope.mainImage = function(id){
        albumService.mainImage({id:id}).success(function (data) {
            $location.path("/"+$scope.user.id);
        });
    }

    $scope.$watch('selectedItem', function() {
        if($scope.selectedItem){
            $timeout(function(){
                var selectCategories = [];
                for(item in $scope.selectedItem){
                    selectCategories.push($scope.selectedItem[item].id);
                }
                albumService.editCategoriesAlbum({id:$routeParams.id_album_edit,selectCategories:selectCategories}).success(function (data) {
                });
            }, 1000)
        }
    })

    $rootScope.updateAvatar = function(image){
        $rootScope.loader = true;
        personalService.updateAvatar({img:image}).success(function (data) {
            $scope.user = data.user;
            $rootScope.avatar = $scope.user.avatar;
            $rootScope.myImage = false;
            $rootScope.loader = false;
        });
    }


    $scope.uploader = new FileUploader();

    var uploader = $scope.uploader = new FileUploader({
        url: 'upload_edit_album'
    });

    $rootScope.images = [];
    $rootScope.canvas = [];

    // FILTERS

    uploader.filters.push({
        name: 'imageFilter',
        fn: function(item /*{File|FileLikeObject}*/, options) {
            var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
            return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
        }
    });

    // CALLBACKS

    uploader.onWhenAddingFileFailed = function(item /*{File|FileLikeObject}*/, filter, options) {
        // console.info('onWhenAddingFileFailed', item, filter, options);
    };
    uploader.onAfterAddingFile = function (item) {
        item.formData.push({id_album: $routeParams.id_album_edit});
        if(item.main == 1) {
            item.formData.push({main: 1});
        }
        uploader.uploadAll();
    };
    uploader.onAfterAddingAll = function(addedFileItems,key) {};
    uploader.onBeforeUploadItem = function(item) {

        // console.info('onBeforeUploadItem', item);
    };
    uploader.onProgressItem = function(fileItem, progress) {
        // console.info('onProgressItem', fileItem, progress);
    };
    uploader.onProgressAll = function(progress) {
        // console.info('onProgressAll', progress);
    };
    uploader.onSuccessItem = function(fileItem, response, status, headers) {
        // console.info('onSuccessItem', fileItem, response, status, headers);
    };
    uploader.onErrorItem = function(fileItem, response, status, headers) {
        // console.info('onErrorItem', fileItem, response, status, headers);
    };
    uploader.onCancelItem = function(fileItem, response, status, headers) {
        // console.info('onCancelItem', fileItem, response, status, headers);
    };
    uploader.onCompleteItem = function(fileItem, response, status, headers) {
        // console.info('onCompleteItem', fileItem, response, status, headers);
        //$scope.id_post_baraholka = response.id;
        $scope.edit_album.images.unshift(response);
        $scope.res = $scope.edit_album.images.length/3;
    };
    uploader.onCompleteAll = function() {};


    }]);


