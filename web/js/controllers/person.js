angular.module('app.ctr.person', ['service.personal', 'angularFileUpload', 'service.socket', 'ngImgCrop', 'multi-select-tree', 'service.chat'])
    .controller('personCtrl',['$window', '$scope', '$rootScope', '$timeout', '$location', 'personalService','$routeParams', 'FileUploader', 'socket', 'chat', function($window, $scope,$rootScope,$timeout,$location,personalService,$routeParams, FileUploader, socket, chat) {

    // init controller

    if($routeParams.id){
        personalService.getUser({id: $routeParams.id}).success(function (data) {
            $rootScope.user = $scope.user = data.user;
            $scope.favorit = false;
            for(key in $scope.user.favorits_with_me){
                if($scope.user.favorits_with_me[key].id ==  $rootScope.id_user){
                    $scope.favorit = true;
                }
            }
        })
    }else{
        personalService.getUser({id: $rootScope.id_user}).success(function (data) {
            $rootScope.user = $scope.user = data.user;
            $scope.favorit = false;
            for(key in $scope.user.favorits_with_me){
                if($scope.user.favorits_with_me[key].id ==  $rootScope.id_user){
                    $scope.favorit = true;
                }
            }
        })
    }

    if($routeParams.id_album && !$scope.user){
        personalService.getUserByAlbumId({id: $routeParams.id_album}).success(function (data) {
            $scope.$apply(function () {
                $scope.user = data.user;
            });
        })
    }

    if(!$rootScope.data){
        personalService.getAllCategories().success(function (data) {
            $rootScope.data = $scope.data = data.categories;
            $scope.selectOnly1Or2 = function(item, selectedItems) {
                if (selectedItems  !== undefined && selectedItems.length >= 20) {
                    return false;
                } else {
                    return true;
                }
            };
        });
    }else{
        $scope.data = $rootScope.data;
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
    }


    chat.init();
    socket.emit("new message",{id_user: $scope.id_user})
    $window.onfocus = function(){
        socket.emit("new message",{id_user: $scope.id_user})
    }
    $scope.math = window.Math;


    $scope.savePost = function(wall,wall_id, text){
        //var user = {
        //    user: {
        //        id: 0,
        //        username: $rootScope.username,
        //        lastname: $rootScope.lastname,
        //        avatar: $rootScope.avatar
        //    },
        //    text: text
        //}
        //wall.posts.unshift(user);
        $scope.loader = true;
        personalService.savePost({wall_id:wall_id,text:$scope.text_post,id: $routeParams.id}).success(function (data) {
            $scope.loader = false;
            if(!uploader.queue.length){
                $scope.text_post = '';
            }
            $scope.new_post_id = data.post.id;
            uploader.uploadAll();
        });
    }

    $scope.saveComment = function(post, post_id, text){
        var user = {
            user: {
                id: 0,
                username: $rootScope.username,
                lastname: $rootScope.lastname,
                avatar: $rootScope.avatar
            },
            text: text
        }
        $scope.loader = true;
        post.comments.push(user);
        personalService.saveComment({post_id:post_id,text:text,id: $routeParams.id}).success(function (data) {
            $scope.user = data.user;
            $scope.loader = false;
        });
    }

    $scope.saveField = function(event,field){
        var text = angular.element(event.target).val();
        var json = {};
        json[field] = text;

        var result = JSON.stringify(json, '', 1);

        personalService.saveField(result).success(function (data) {
            $scope.user = data.user;
        });
    }

    $scope.addFavorits = function(id){
        $scope.loader_favorit = true;
        personalService.addFavorits({id:id}).success(function (data) {
            $scope.loader_favorit = false;
            $scope.user = data.user;
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
            $scope.user = data.user;
                $scope.favorit = false;
                for(key in $scope.user.favorits_with_me){
                    if($scope.user.favorits_with_me[key].id ==  $rootScope.id_user){
                        $scope.favorit = true;
                        break;
                    }
                }
        });
    }

    $scope.editPost = function(id){
        $scope.editUploaderPost = new FileUploader({
            url: 'save_post_images'
        });

        $scope.editUploaderPost.onAfterAddingFile = function(fileItem) {

            fileItem.formData.push({post_id: id});

            $scope.editUploaderPost.uploadAll();
        };

        $scope.editUploaderPost.onCompleteItem = function(fileItem, response, status, headers) {
            var posts = $scope.user.wall.posts;

            for(var key in posts){
                if(posts[key].id == id){
                    response.new = true;
                    $scope.user.wall.posts[key].post_images.push(response);
                    $scope.imaging($scope.user.wall.posts[key]);
                }
            }
            $scope.id_post_baraholka = response.id;
        };

    }


    $scope.editTextPost = function(id,text){
        personalService.editTextPost({id: id, text: text}).success(function (data) {
        });
    }

    $scope.removeComment = function(key,comments,id){
        personalService.removeComment({id: id}).success(function (data) {
            comments.splice(key,1);
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

    $scope.removePost = function(post_id,key){
        $scope.loader = true;
        personalService.removePost({post_id:post_id}).success(function (data) {
            $scope.loader = false;
            $scope.user.wall.posts.splice(key,1);
        });
    }

    $scope.removeImgPost = function(img_id,post_id){
        personalService.removeImgPost({img_id: img_id,post_id:post_id}).success(function (data) {
            var posts = $scope.user.wall.posts;
            for(var key in posts){
                if(posts[key].id == post_id){
                    for(var k in posts[key].post_images){
                        if($scope.user.wall.posts[key].post_images[k].id == img_id)
                        $scope.user.wall.posts[key].post_images.splice(k,1);
                        $scope.imaging($scope.user.wall.posts[key]);
                    }
                }
            }
        });
    }

    $scope.$on('$routeChangeStart', function(next, current) {
        if(current.params.id != undefined && current.params.id != next.targetScope.user.id){
            $rootScope.user = $scope.user = undefined;
        }
    });

    // ALBUM

    if($routeParams.id){
        var uploader = $scope.uploader = new FileUploader({
            url: 'save_post_images'
        });
    }else{
        var uploader = $scope.uploader = new FileUploader({
            url: 'upload_album'
        });
    }

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
    uploader.onAfterAddingFile = function(fileItem) {
        $scope.res = uploader.queue.length/3;
    };
    $rootScope.$on("loadImageAll", function(){
            var count = angular.element(document.querySelectorAll('.album canvas')).length;
            $rootScope.canvas = angular.element(document.querySelectorAll('canvas'));

            var width = 532;
            var count_row = Math.ceil(count / 4);

            var count_images = Math.ceil(count / count_row);
            var images_row = [];

            for (var i = 0; i < count_row; i++) {
                if ((count_row - 1) == i) {
                    var cm = Math.ceil(count_images);
                    var cm1 = Math.floor(count_images);
                    var mass = $rootScope.images.slice(i * cm1, i * cm1 + cm);
                    images_row.push(mass);
                } else {
                    var cm = Math.floor(count_images);
                    var mass = $rootScope.images.slice(i * cm, i * cm + cm);
                    images_row.push(mass);  // количество картинок в строке
                }
            }

            var p = 0;
            for (var j = 0; j < count_row; j++) {
                var delim = (width - images_row[j].length * 4) * images_row[j][0].height;
                var delit = images_row[j][0].width;

                for (var k = 1; k < images_row[j].length; k++) {
                    delit = delit + images_row[j][k].width * (images_row[j][0].height / images_row[j][k].height);
                }

                var height = Math.floor(delim / delit);//высота строки

                for (var k = 0; k < images_row[j].length; k++) {
                    var width_im = images_row[j][k].width / images_row[j][k].height * height;
                    $rootScope.canvas[p].setAttribute('width', width_im);
                    $rootScope.canvas[p].setAttribute('height', height);
                    $rootScope.canvas[p].getContext('2d').drawImage(images_row[j][k], 0, 0, width_im, height);
                    p = p + 1;
                }
            }
    });

    $scope.imaging = function(post){

            var count = post.post_images.length;

            var width = 455;
            var count_row = Math.ceil(count / 4);

            var count_images = Math.ceil(count / count_row);
            var post_images_row = [];

            for (var i = 0; i < count_row; i++) {
                if ((count_row - 1) == i) {
                    var cm = Math.ceil(count_images);
                    var cm1 = Math.floor(count_images);
                    var mass = post.post_images.slice(i * cm1, i * cm1 + cm);
                    post_images_row.push(mass);
                } else {
                    var cm = Math.floor(count_images);
                    var mass = post.post_images.slice(i * cm, i * cm + cm);
                    post_images_row.push(mass);  // количество картинок в строке
                }
            }

            var p = 0;
            for (var j = 0; j < count_row; j++) {
                var delim = (width - post_images_row[j].length * 4) * post_images_row[j][0].height;
                var delit = post_images_row[j][0].width;

                for (var k = 1; k < post_images_row[j].length; k++) {
                    delit = delit + post_images_row[j][k].width * (post_images_row[j][0].height / post_images_row[j][k].height);
                }

                var height = Math.floor(delim / delit);//высота строки

                for (var k = 0; k < post_images_row[j].length; k++) {
                    var width_im = post_images_row[j][k].width / post_images_row[j][k].height * height;
                    post.post_images[p].width = width_im;
                    post.post_images[p].height = height;
                    p = p + 1;
                }
            }

    }

    $scope.removeImg = function(key){
        if(key != undefined){
            $rootScope.images.splice(key,1);
        }
        if($rootScope.count){
            $rootScope.count = $rootScope.count - 1;
        }
        $timeout(function(){
            $rootScope.$emit("loadImageAll");
        }, 500)
    };

    uploader.onAfterAddingAll = function(addedFileItems,key) {
        // console.info('onAfterAddingAll', addedFileItems);
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
        $scope.id_post_baraholka = response.id;
    };
    uploader.onCompleteAll = function() {
        if($routeParams.id){
            uploader.queue = [];
            $scope.text_post = '';
            personalService.getUser({id: $routeParams.id}).success(function (data) {
                $rootScope.user = $scope.user = data.user;
                $scope.favorit = false;
                for(key in $scope.user.favorits_with_me){
                    if($scope.user.favorits_with_me[key].id ==  $rootScope.id_user){
                        $scope.favorit = true;
                    }
                }
            })
        }
    };

    uploader.onBeforeUploadItem = function (item) {
        if($routeParams.id){
            item.formData.push({post_id: $scope.new_post_id});
        }
        uploader.uploadAll();
    };


}]);


