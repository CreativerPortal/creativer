angular.module('app.ctr.person', ['service.personal', 'angularFileUpload', 'service.socket', 'ngImgCrop', 'multi-select-tree', 'service.chat'])
    .controller('personCtrl',['$state','$window', '$scope', '$rootScope', '$timeout', '$location', 'personalService','$stateParams', 'FileUploader', 'socket', 'chat', function($state,$window, $scope,$rootScope,$timeout,$location,personalService,$stateParams, FileUploader, socket, chat) {

    // init controller


    if($stateParams.id && !$stateParams.key_post){
        personalService.getUser({id: $stateParams.id}).success(function (data) {
            $rootScope.user = $scope.user = data.user;
            $scope.user.wall.posts = data.posts;
            $scope.favorit = false;
            for(key in $scope.user.favorits_with_me){
                if($scope.user.favorits_with_me[key].id ==  $rootScope.id_user){
                    $scope.favorit = true;
                }
            }
        })
    }else if(!$stateParams.key_post){
        personalService.getUser({id: $rootScope.id_user}).success(function (data) {
            $rootScope.user = $scope.user = data.user;
            $scope.user.wall.posts = data.posts;
            $scope.favorit = false;
            for(key in $scope.user.favorits_with_me){
                if($scope.user.favorits_with_me[key].id ==  $rootScope.id_user){
                    $scope.favorit = true;
                }
            }
            $scope.$watch("svg", function () {
                svgCheckbox();
            });
        })
    }

    if($stateParams.id_album && !$scope.user){
        personalService.getUserByAlbumId({id: $stateParams.id_album}).success(function (data) {
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
    if($stateParams.id_album){
        $scope.id_album = $stateParams.id_album;
    }
    if($stateParams.url_img){
        $scope.url_img = $stateParams.url_img;
    }
    if($stateParams.key_img){
        $scope.key_img = $stateParams.key_img;
        $scope.next_key_img = parseInt($stateParams.key_img)+1;
        $scope.previous = parseInt($stateParams.key_img)-1;
    }
    if($stateParams.key_post){
        $scope.key_post = $stateParams.key_post;
        $scope.key_post_img = $stateParams.key_post_img;
        $scope.next_key_post_img = parseInt($scope.key_post_img)+1
        $scope.previous_key_post_img = parseInt($scope.key_post_img)-1;
        $scope.url_img = $stateParams.url_img;
        $rootScope.overflow = true;
    }else{
        $rootScope.overflow = false;
    }

    if($stateParams.key_post && $scope.user && $scope.user.wall.posts[$scope.key_post].post_images[$scope.key_post_img] == undefined){
        $location.path("/"+$scope.user.id);
    }

    $scope.previousPosts = function(){
        personalService.previousPosts({id:$scope.user.id,offset:$scope.user.wall.posts.length}).success(function (data) {
            for(var key in data.posts){
                $scope.user.wall.posts.push(data.posts[key]);
            }
        });
    }

    $scope.closeImg = function(){
        albumService.imagePreviews({image_previews:$rootScope.image_previews}).success(function (data) {
            $rootScope.image_previews = [];
        });
        $rootScope.overflow = false;
    }

    $scope.height = $window.innerHeight-30;

    chat.init();
    socket.emit("new message",{id_user: $scope.id_user})
    $window.onfocus = function(){
        socket.emit("new message",{id_user: $scope.id_user})
    }
    $scope.math = window.Math;


    $scope.savePost = function(wall,wall_id, text){
        if($scope.loader_post == false || $scope.loader_post == undefined){
            $scope.loader_post = true;
            $scope.count_elment = (uploader.queue.length != 0 && uploaderDoc.queue.length != 0)?2:1;
            personalService.savePost({wall_id:wall_id,text:$scope.text_post,id: $stateParams.id,videos:$scope.videos}).success(function (data) {
                if(!uploader.queue.length){
                    $scope.text_post = '';
                    $scope.videos = [];
                }
                $scope.new_post_id = data.post.id;
                if(uploaderDoc.queue.length){
                    uploaderDoc.uploadAll();
                }
                if(uploader.queue.length){
                    uploader.uploadAll();
                }else{
                    personalService.getUser({id: $stateParams.id}).success(function (data) {
                        $scope.loader_post = false;
                        $scope.user = data.user;
                        $scope.user.wall.posts = data.posts;
                        $scope.favorit = false;
                        for(key in $scope.user.favorits_with_me){
                            if($scope.user.favorits_with_me[key].id ==  $rootScope.id_user){
                                $scope.favorit = true;
                            }
                        }
                    })
                }
            });
        }
    }

    $scope.videos = [];

    $scope.addVideo = function(){
        $scope.videos.push("");
    }

    $scope.saveComment = function(post, post_id, text){
        if($scope.loader_comment == false || $scope.loader_comment == undefined){
            $scope.loader_comment = true;
            personalService.saveComment({post_id:post_id,text:text,id: $stateParams.id}).success(function (data) {
                post.comments.push(data.comment);
                post.text_comment = '';
                $scope.loader_comment = false;
            });
        }
    }

    $scope.saveField = function(event,field){
        var text = angular.element(event.target).val();
        var json = {};
        json[field] = text;

        var result = JSON.stringify(json, '', 1);

        personalService.saveField(result).success(function (data) {
            $rootScope.user = $scope.user = data.user;
            angular.element(event.target).attr('disabled', '');
        });
    }

    $scope.addFavorits = function(id){
        $scope.loader_favorit = true;
        personalService.addFavorits({id:id}).success(function (data) {
            $scope.loader_favorit = false;
            $scope.user = data;
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
            $scope.user = data;
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
        console.log(comments);
        personalService.removeComment({id: id}).success(function (data) {
            comments.splice(key,1);
        });
    }

    $rootScope.updateAvatar = function(image){
        $rootScope.loader = true;
        personalService.updateAvatar({img:image}).success(function (data) {
            $scope.user = data.user;
            $scope.user.wall.posts = data.posts;
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

    $scope.removeVideoPost = function(video_id,post_id){
        personalService.removeVideoPost({video_id: video_id,post_id:post_id}).success(function (data) {
            var posts = $scope.user.wall.posts;
            for(var key in posts){
                if(posts[key].id == post_id){
                    for(var k in posts[key].post_videos){
                        if($scope.user.wall.posts[key].post_videos[k].id == video_id)
                            $scope.user.wall.posts[key].post_videos.splice(k,1);
                    }
                }
            }
        });
    }

    $scope.removeDocumentPost = function(document_id,post_id){
        personalService.removeDocumentPost({document_id: document_id,post_id:post_id}).success(function (data) {
            var posts = $scope.user.wall.posts;
            for(var key in posts){
                if(posts[key].id == post_id){
                    for(var k in posts[key].post_documents){
                        if($scope.user.wall.posts[key].post_documents[k].id == document_id)
                            $scope.user.wall.posts[key].post_documents.splice(k,1);
                    }
                }
            }
        });
    }

    $scope.sendFeedBack = function(){
        $scope.loader = true;
        personalService.sendFeedBack({nick: $scope.nick, telephone: $scope.telephone, message: $scope.message, email: $scope.email}).success(function (data) {
            $scope.sent_letter = true;
            $scope.loader = false;
        });
    }

    $scope.replacePassword = function(oldPassword,newPassword,repeatPassword){
        personalService.replacePassword({oldPassword: oldPassword, newPassword: newPassword, repeatPassword:repeatPassword})
        .success(function (data) {
            $scope.loader_password = false;
            $scope.replace_password = 'true';
        })
        .error(function (data) {
            $scope.loader_password = false;
            $scope.replace_password = 'false';
        })
    }

    $scope.saveName = function(username,lastname){
        personalService.saveName({username: username, lastname: lastname})
            .success(function (data) {
                $scope.loader_username = false;
                $scope.username_lastname = 'true';
                $scope.user.username = username;
                $scope.user.lastname = lastname;
                $scope.username = username;
                $scope.lastname = lastname;
            })
            .error(function (data) {
                $scope.loader_username = false;
                $scope.username_lastname = 'false';
            })
    }

    $scope.changeAutoScroll = function(){
        personalService.changeAutoScroll({autoscroll: $scope.user.autoscroll}).success(function (data) {
            $rootScope.autoscroll = $scope.user.autoscroll;
        });
    }

    $scope.notificationMessage = function(){
        personalService.notificationMessage({notification_message: $scope.user.notification_message}).success(function (data) {
        });
    }

    $scope.notificationComment = function(){
        personalService.notificationComment({notification_comment: $scope.user.notification_comment}).success(function (data) {
        });
    }

    $scope.changeTariff = function(id){
        $scope.loader_tariff = true;
        personalService.changeTariff({id: id}).success(function (data) {
            $rootScope.user = $scope.user = data.user;
            $scope.loader_tariff = false;
        });
    }

    $scope.$on('$routeChangeStart', function(next, current) {
        if(current.params.id != undefined && current.params.id != next.targetScope.user.id){
            $rootScope.user = $scope.user = undefined;
        }
    });

    // ALBUM

    if($stateParams.id){
        var uploader = $scope.uploader = new FileUploader({
            url: 'save_post_images',
            queueLimit: 10
        });
    }else{
        var uploader = $scope.uploader = new FileUploader({
            url: 'upload_album',
            queueLimit: 10
        });
    }

    var uploaderDoc = $scope.uploaderDoc = new FileUploader({
        url: 'save_post_documents',
        queueLimit: 5
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

        uploader.filters.push({
            name: 'enforceMaxFileSize',
            fn: function (item) {
                return item.size <= 10485760; // 10 mb
            }
        });

    uploaderDoc.filters.push({
        name: 'documentFilter',
        fn: function(item /*{File|FileLikeObject}*/, options) {
            var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
            console.log(type);
            return '|vnd.openxmlformats-officedocument.wordprocessingml.document|plain|postscript|vnd.ms-powerpoint|pdf|vnd.openxmlformats-officedocument.spreadsheetml.sheet|vnd.openxmlformats-officedocument.presentationml.presentation|vnd.ms-excel|msword||'.indexOf(type) !== -1;
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
            var width = 536;
            var count_row = Math.ceil(count / 4);
            var count_images = Math.ceil(count / count_row);
            var images_row = [];

            for (var i = 0; i < count_row; i++) {
                if ((count_row - 1) == i) {
                    var cm = Math.ceil(count_images);
                    var cm1 = Math.ceil(count_images);
                    var mass = $rootScope.images.slice(i * cm1, i * cm1 + cm);
                    images_row.push(mass);
                } else {
                    var cm = Math.ceil(count_images);
                    var mass = $rootScope.images.slice(i * cm, i * cm + cm);
                    images_row.push(mass);  // количество картинок в строке
                }
            }

            var p = 0;
            for (var j = 0; j < count_row; j++) {
                var delim = width * images_row[j][0].height;
                var delit = images_row[j][0].width;

                for (var k = 1; k < images_row[j].length; k++) {
                    delit = delit + images_row[j][k].width * images_row[j][0].height / images_row[j][k].height;
                }

                var height = delim / delit;//высота строки

                for (var k = 0; k < images_row[j].length; k++) {
                    var width_im = images_row[j][k].width / images_row[j][k].height * height;
                    width_im = width_im - 2;
                    $rootScope.canvas[p].setAttribute('width', width_im);
                    $rootScope.canvas[p].setAttribute('height', height);
                    $rootScope.canvas[p].getContext('2d').drawImage(images_row[j][k], 0, 0, width_im, height);
                    p = p + 1;
                }
            }
    });

    $scope.imaging = function(post){
        var count = post.post_images.length;
        var width = 453;
        var count_row = Math.ceil(count / 4);
        var count_images = Math.ceil(count / count_row);
        var post_images_row = [];

        for (var i = 0; i < count_row; i++) {
            if ((count_row - 1) == i) {
                var cm = Math.ceil(count_images);
                var cm1 = Math.ceil(count_images);
                var mass = post.post_images.slice(i * cm1, i * cm1 + cm);
                post_images_row.push(mass);
            } else {
                var cm = Math.ceil(count_images);
                var mass = post.post_images.slice(i * cm, i * cm + cm);
                post_images_row.push(mass);  // количество картинок в строке
            }
        }

        var p = 0;
        for (var j = 0; j < count_row; j++) {
            var delim = width * post_images_row[j][0].height;
            var delit = post_images_row[j][0].width;

            for (var k = 1; k < post_images_row[j].length; k++) {
                delit = delit + post_images_row[j][k].width * (post_images_row[j][0].height / post_images_row[j][k].height);
            }

            var height = delim / delit;//высота строки

            for (var k = 0; k < post_images_row[j].length; k++) {
                var width_im = post_images_row[j][k].width / post_images_row[j][k].height * height;
                post.post_images[p].width = width_im - 2;
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


    uploaderDoc.onAfterAddingAll = function(addedFileItems,key) {
        // console.info('onAfterAddingAll', addedFileItems);
        console.log($scope.uploaderDoc.queue);
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
        if(uploader.progress == 100 && (uploaderDoc.progress == 100 || !uploaderDoc.progress)) {
            if($stateParams.id){
            uploader.clearQueue();
            uploaderDoc.clearQueue();
            $scope.text_post = '';
            $rootScope.images = [];
            $rootScope.count = 0;
            personalService.getUser({id: $stateParams.id}).success(function (data) {
                $scope.loader_post = false;
                $rootScope.user = $scope.user = data.user;
                $scope.user.wall.posts = data.posts;
                $scope.favorit = false;
                for (key in $scope.user.favorits_with_me) {
                    if ($scope.user.favorits_with_me[key].id == $rootScope.id_user) {
                        $scope.favorit = true;
                    }
                }
            })
            }
        }
    };

    uploaderDoc.onCompleteAll = function() {
        if(uploaderDoc.progress == 100 && (uploader.progress == 100 || !uploader.progress)){
            uploaderDoc.clearQueue();
            uploader.clearQueue();
            $scope.text_post = '';
            $rootScope.images = [];
            $rootScope.count = 0;
            personalService.getUser({id: $stateParams.id}).success(function (data) {
                $scope.loader_post = false;
                $rootScope.user = $scope.user = data.user;
                $scope.user.wall.posts = data.posts;
                $scope.favorit = false;
                for(key in $scope.user.favorits_with_me){
                    if($scope.user.favorits_with_me[key].id ==  $rootScope.id_user){
                        $scope.favorit = true;
                    }
                }
            })
        }
    }

    uploader.onBeforeUploadItem = function (item) {
        if($stateParams.id){
            item.formData.push({post_id: $scope.new_post_id});
        }
        uploader.uploadAll();
    };

    uploaderDoc.onBeforeUploadItem = function (item) {
        if($stateParams.id){
            item.formData.push({post_id: $scope.new_post_id});
        }
        uploaderDoc.uploadAll();
    };

}]);


