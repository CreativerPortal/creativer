angular.module('app.ctr.person', ['service.personal', 'angularFileUpload', 'ngImgCrop', 'multi-select-tree', 'service.chat'])
    .controller('personCtrl',['$scope', '$rootScope', '$timeout', '$location', 'personalService','$routeParams', 'FileUploader', 'chat', function($scope,$rootScope,$timeout,$location,personalService,$routeParams, FileUploader, chat) {

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
        $scope.math = window.Math;


    $scope.savePost = function(wall,wall_id, text){
        var username = $rootScope.username;
        var lastname = $rootScope.lastname;
        var img = $rootScope.avatar;
        wall.posts.unshift({id: 0, username:username, lastname:lastname, avatar: {img:img}, text: text});
        personalService.savePost({wall_id:wall_id,text:$scope.text_post,id: $routeParams.id}).success(function (data) {
            $scope.text_post = '';
            $scope.user = data.user;
        });
    }

    $scope.saveComment = function(post, post_id, text){
        var username = $rootScope.username;
        var lastname = $rootScope.lastname;
        var img = $rootScope.img;
        post.comments.push({id: 0, username:username, lastname:lastname, avatar: {img:img}, text: text});
        personalService.saveComment({post_id:post_id,text:text,id: $routeParams.id}).success(function (data) {
            $scope.user = data.user;
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
        $scope.loader = true;
        personalService.addFavorits({id:id}).success(function (data) {
            $scope.loader = false;
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
        $scope.loader = true;
        personalService.removeFavorits({id:id}).success(function (data) {
            $scope.loader = false;
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

    $scope.updateAvatar = function(image){
        $scope.loader = true;
        personalService.updateAvatar({img:image}).success(function (data) {
            $scope.user = data.user;
            $rootScope.avatar = $scope.user.avatar.img;
            $scope.myImage = false;
            $scope.loader = false;
        });
    }

    $scope.removePost = function(post_id){
        personalService.removePost({post_id:post_id}).success(function (data) {

        });
    }

    $scope.$on('$routeChangeStart', function(next, current) {
        if(current.params.id != undefined && current.params.id != next.targetScope.user.id){
            $rootScope.user = $scope.user = undefined;
        }
    });

    // ALBUM

    $scope.uploader = new FileUploader();

    var uploader = $scope.uploader = new FileUploader({
        url: 'upload_album'
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
    uploader.onAfterAddingFile = function(fileItem) {
       // console.info('onAfterAddingFile', fileItem);
       // $scope.res = uploader.queue.length/3;
       // fileItem._file.width = '10px';
        //var canvas = document.querySelectorAll('canvas');
        $scope.res = uploader.queue.length/3;
    };
    uploader.onAfterAddingAll = function(addedFileItems,key) {
        // console.info('onAfterAddingAll', addedFileItems);

        setTimeout(function() {
            if(key != undefined){
                $rootScope.images.splice(key,1);
            }
            var count = angular.element(document.querySelectorAll('canvas')).length - 1;
            $rootScope.canvas = angular.element(document.querySelectorAll('canvas'));

            var width = 533;
            var count_row = Math.ceil(count/4);

            var count_images = count / count_row;
            var images_row = [];

            for(var i=0; i < count_row; i++){
                if((count_row-1) == i){
                    var cm = Math.ceil(count_images);
                    var cm1 = Math.floor(count_images);
                    var mass = $rootScope.images.slice(i*cm1,i*cm1+cm);
                    images_row.push(mass);
                }else{
                    var cm = Math.floor(count_images);
                    var mass = $rootScope.images.slice(i*cm,i*cm+cm);
                    images_row.push(mass);  // количество картинок в строке
                }
            }

            var p=0;
            for(var j=0; j<count_row; j++){
                var delim = (width - images_row[j].length*4)*images_row[j][0].height;
                var delit =images_row[j][0].width;

                for(var k=1; k < images_row[j].length; k++){
                    delit=delit+images_row[j][k].width*(images_row[j][0].height/images_row[j][k].height);
                }

                var height = Math.floor(delim/delit);//высота строки

                for(var k=0; k < images_row[j].length; k++){
                    var width_im = images_row[j][k].width / images_row[j][k].height * height;
                    $rootScope.canvas[p].setAttribute('width', width_im );
                    $rootScope.canvas[p].setAttribute('height', height );
                    $rootScope.canvas[p].getContext('2d').drawImage(images_row[j][k], 0, 0, width_im, height);
                    p=p+1;
                }
            }

        }, 1000)

    };
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
    };
    uploader.onCompleteAll = function() {
        var name_album = $scope.album?$scope.album.name:null;
        var description_album = $scope.album?$scope.album.description:null;
        var selectCategories = [];
        for(item in $scope.selectedItem){
            selectCategories.push($scope.selectedItem[item].id);
        }
        personalService.finishUpload({name:name_album,selectCategories:selectCategories,description:description_album}).success(function () {
            $rootScope.user = undefined;
            $location.path("/#/person");
        });
        console.info('onCompleteAll');
    };

    uploader.onBeforeUploadItem = function (item) {
        if(item.file.title != undefined) {
            item.formData.push({title: item.file.title});
        }

        if(item.file.price != undefined) {
            item.formData.push({price: item.file.price});
        }

        if(item.main == 1) {
            item.formData.push({main: 1});
        }
        uploader.uploadAll();
    };

   // console.info('uploader', uploader);

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


}]);


