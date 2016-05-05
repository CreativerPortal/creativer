angular.module('app.ctr.album', ['service.album', 'angularFileUpload', 'service.personal', 'service.socket', 'angularFileUpload', 'service.chat', 'angularSearchTree'])
    .controller('albumCtrl',['$scope', '$window', '$rootScope', '$location', '$timeout', 'albumService', 'personalService', '$stateParams', 'FileUploader', 'socket', 'chat', 'searchTree', function($scope,$window,$rootScope,$location,$timeout,albumService,personalService,$stateParams, FileUploader, socket, chat, SearchTree) {

        if(!$stateParams.url_img && $stateParams.key_img){
            $location.path("/album/"+$stateParams.id_album);
        }

        var key_album, name_album;

    if(($stateParams.id_album_edit || $stateParams.id_album) && $scope.user) {
        var exists_album = false;
        var id_album = $stateParams.id_album?$stateParams.id_album:$stateParams.id_album_edit;
        for(var key in $scope.user.albums){
            if(id_album == $scope.user.albums[key].id){
                exists_album = true;
                key_album = key;
                name_album = $scope.user.albums[key].title;
            }
        }
        if(!exists_album){
            albumService.getUserByAlbumId({id: id_album}).success(function (data) {
                $rootScope.title = data.user.username+' '+data.user.lastname;
                $rootScope.description = name_album;
                $rootScope.user = $scope.user = data.user;
                for (key in $scope.user.favorits_with_me) {
                    if ($scope.user.favorits_with_me[key].id == $rootScope.id_user) {
                        $scope.favorit = true;
                        break;
                    } else {
                        $scope.favorit = false;
                    }
                }
                albumService.getAlbumComments({id_album: id_album}).success(function (data) {
                    for (var key in $scope.user.albums) {
                        if ($scope.user.albums[key].id == $stateParams.id_album)
                            $scope.user.albums[key].images = data.images;
                    }
                });
            });
        }else{
            for (key in $scope.user.favorits_with_me) {
                if ($scope.user.favorits_with_me[key].id == $rootScope.id_user) {
                    $scope.favorit = true;
                    break;
                } else {
                    $scope.favorit = false;
                }
            }
        }

        if($scope.user.albums[key_album] && $scope.user.albums[key_album].images[0] && $scope.user.albums[key_album].images[0].image_comments == undefined) {
            for (var key in $scope.user.albums) {
                if ($scope.user.albums[key].id == id_album && $scope.user.albums[key].images[0].image_comments == undefined) {
                    albumService.getAlbumComments({id_album: id_album}).success(function (data) {
                        for (var key in $scope.user.albums) {
                            if ($scope.user.albums[key].id == $stateParams.id_album)
                                $scope.user.albums[key].images = data.images;
                        }
                    });
                    break;
                }
            }
        }
    }else{
        var id_album = $stateParams.id_album?$stateParams.id_album:$stateParams.id_album_edit;
        albumService.getUserByAlbumId({id: id_album}).success(function (data) {
            $rootScope.title = data.user.username+' '+data.user.lastname;
            $rootScope.user = $scope.user = data.user;
            for (key in $scope.user.favorits_with_me) {
                if ($scope.user.favorits_with_me[key].id == $rootScope.id_user) {
                    $scope.favorit = true;
                    break;
                } else {
                    $scope.favorit = false;
                }
            }
            if($stateParams.id_album){
                for(var key in $scope.user.albums){
                    if($scope.user.albums[key].images[0].image_comments == undefined) {
                        albumService.getAlbumComments({id_album: id_album}).success(function (data) {
                            for (var key in $scope.user.albums) {
                                if ($scope.user.albums[key].id == $stateParams.id_album){
                                    $scope.user.albums[key].images = data.images;
                                    $rootScope.description = $scope.user.albums[key].name;
                                }
                            }
                        });
                        break;
                    }
                }
            }

        });
    }


    if($stateParams.id_album_edit){
        albumService.getAlbumById({id_album:$stateParams.id_album_edit}).success(function (data) {
            $rootScope.title = data.name;
            $scope.edit_album = data.album;
            $scope.edit_album.remove_post = false;
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
        if($stateParams.id_album && $scope.user != undefined){
            for(var key in $scope.user.albums){
                if($scope.user.albums[key].id == $stateParams.id_album){
                    $scope.album_key = key;
                }
            }

            if(!$stateParams.key_img && $stateParams.url_img){
                for(var key_img in $scope.user.albums[$scope.album_key].images){
                    if($stateParams.url_img == $scope.user.albums[$scope.album_key].images[key_img].name){
                        $location.replace();
                        $location.path("/album/"+$stateParams.id_album+'/'+$scope.user.albums[$scope.album_key].images[key_img].name+'/'+key_img);
                    }
                }
            }


            if(!$rootScope.image_previews){
                $rootScope.image_previews = [];
            }

            if($stateParams.id_album && $stateParams.key_img && ($scope.user.id != $rootScope.id_user)){
                if(!$rootScope.image_previews[$stateParams.id_album]){
                    $rootScope.image_previews[$stateParams.id_album] = [];
                }
                if($rootScope.image_previews.indexOf($scope.user.albums[$scope.album_key].images[$stateParams.key_img].id) == -1){
                    $rootScope.image_previews[$stateParams.id_album].push($scope.user.albums[$scope.album_key].images[$stateParams.key_img].id);
                }
            }
        }


            if($scope.user){
                for(var key in $scope.user.albums){
                    if($stateParams.id_album == $scope.user.albums[key].id){
                        key_album = key;
                    }
                }
                if($stateParams.key_img)
                $scope.img_id = $scope.user.albums[key_album].images[$stateParams.key_img].id
            }



            if($stateParams.id_album && $scope.user && !$scope.user.albums[key_album].images_likes){
                albumService.getLikesByAlbumId({id_album:id_album}).success(function (data) {
                    $scope.likes = data.likes;
                    for(var key in $scope.user.albums){
                        if(id_album == $scope.user.albums[key].id){
                            key_album = key;
                        }
                    }
                    $scope.user.albums[key_album].images_likes = data.likes;
                });
        }

        if($scope.user && $stateParams.key_img && $scope.user.albums[key_album].images[$stateParams.key_img].viewed == false && $scope.user.id == $rootScope.id_user){
            albumService.setViewed({id:$scope.user && $scope.user.albums[key_album].images[$stateParams.key_img]}).success(function (data) {
                $scope.user.albums[key_album].images[$stateParams.key_img].viewed = true;
            });
        }

    });



    if($stateParams.id_album){
        $scope.id_album = $stateParams.id_album;
    }
    if($stateParams.url_img){
        $scope.url_img = $stateParams.url_img;
        $rootScope.overflow = true;
    }else{
        $rootScope.overflow = false;
    }
    if($stateParams.key_img || $rootScope.key_img){
        $rootScope.key_img = $stateParams.key_img;
        $scope.next_key_img = parseInt($stateParams.key_img)+1;
        $scope.previous = parseInt($stateParams.key_img)-1;
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
        if($rootScope.image_previews.length > 0){
            albumService.imagePreviews({image_previews:$rootScope.image_previews}).success(function (data) {
                $rootScope.image_previews = [];
            });
        }
        $rootScope.overflow = false;
    }

    $scope.deleteImage = function(image_id,key_img,key_album){
        albumService.deleteImage({image_id:image_id}).success(function (data) {
            $scope.user.albums[key_album].images.splice(key_img,1);
            $location.path("/album/"+$stateParams.id_album+'/'+$scope.user.albums[key_album].images[key_img].name+'/'+key_img);
        });
    }


    $scope.saveImageComment = function(image,text){
        if($scope.loader_album_comment == false || $scope.loader_album_comment == undefined) {
            $scope.loader_album_comment = true;
            albumService.saveImageComment({
                image_id: image.id,
                text: text,
                id: $rootScope.id_user
            }).success(function (data) {
                $scope.user.text_comment = undefined;
                image.image_comments.push(data);
                $scope.loader_album_comment = false;
            });
        }
    }

    $scope.like = function(id,album_key,image_key){
        if($scope.user.albums[key_album].images_likes[id].liked){
            $scope.user.albums[album_key].images[image_key].likes = $scope.user.albums[album_key].images[image_key].likes - 1;
            $scope.user.albums[key_album].images_likes[id].liked = !$scope.user.albums[key_album].images_likes[id].liked;
        }else{
            $scope.user.albums[album_key].images[image_key].likes = $scope.user.albums[album_key].images[image_key].likes + 1;
            $scope.user.albums[key_album].images_likes[id].liked = !$scope.user.albums[key_album].images_likes[id].liked;
        }
        albumService.like({image_id:id}).success(function (data) {
            $scope.user.albums[album_key].images[image_key].likes = data.likes;
            $scope.user.likes = data.likes_user;
            $scope.user.albums[album_key].likes = data.likes_album;
            $scope.user.albums[key_album].images_likes[id].liked = data.liked;
        });
    }

    $scope.addFavorits = function(id){
        $scope.loader_favorit = true;
        personalService.addFavorits({id:id}).success(function (data) {
            $scope.loader_favorit = false;
            if(data.user){
                $scope.user.favorits_with_me = data.user.favorits_with_me;
                $scope.user.my_favorits = data.user.my_favorits;
            }else{
                $scope.user.favorits_with_me = data.favorits_with_me;
                $scope.user.my_favorits = data.my_favorits;
            }
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
        $scope.loader_favorit = true;
        personalService.removeFavorits({id:id}).success(function (data) {
            $scope.loader_favorit = false;
            if(data.user){
                $scope.user.favorits_with_me = data.user.favorits_with_me;
                $scope.user.my_favorits = data.user.my_favorits;
            }else{
                $scope.user.favorits_with_me = data.favorits_with_me;
                $scope.user.my_favorits = data.my_favorits;
            }
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

    $scope.removeComment = function(album_key,key_img,id,key){
        albumService.removeComment({id: id}).success(function (data) {
            $scope.user.albums[album_key].images[key_img].image_comments.splice(key,1);
        });
    }

    $scope.mainImage = function(id){
        albumService.mainImage({id:id}).success(function (data) {
        });
    }

    $scope.$watch('selectedItem', function() {
        if($scope.selectedItem){
            $timeout(function(){
                var selectCategories = [];
                for(item in $scope.selectedItem){
                    selectCategories.push($scope.selectedItem[item].id);
                }
                albumService.editCategoriesAlbum({id:$stateParams.id_album_edit,selectCategories:selectCategories}).success(function (data) {
                });
            }, 1000)
        }
    })

    $scope.saveField = function(event,field){
        var text = angular.element(event.target).val();
        var json = {};
        json[field] = text;

        var result = JSON.stringify(json, '', 1);
        personalService.saveField(result).success(function (data) {
            angular.element(event.target).attr('disabled', '');
        });
    }

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
        url: 'upload_edit_album',
        queueLimit: 10
    });

    uploader.filters.push({
        name: 'enforceMaxFileSize',
        fn: function (item) {
            return item.size <= 10485760; // 10 mb
        }
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
        item.formData.push({id_album: $stateParams.id_album_edit});
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
        if(fileItem.isError){
            $scope.error = true;
        }
        $scope.edit_album.images.unshift(response);
        $scope.res = $scope.edit_album.images.length/3;
    };
    uploader.onCompleteAll = function() {};


    }]);


