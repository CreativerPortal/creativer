angular.module('app.ctr.album.create', ['service.personal', 'angularFileUpload', 'service.socket', 'ngImgCrop', 'multi-select-tree', 'service.chat'])
    .controller('createAlbumCtrl',['$window', '$scope', '$rootScope', '$timeout', '$location', 'personalService','$routeParams', 'FileUploader', 'socket', 'chat', function($window, $scope,$rootScope,$timeout,$location,personalService,$routeParams, FileUploader, socket, chat) {

    // init controller

    personalService.getUser({id: $rootScope.id_user}).success(function (data) {
        $rootScope.user = $scope.user = data.user;
        $scope.favorit = false;
        for(key in $scope.user.favorits_with_me){
            if($scope.user.favorits_with_me[key].id ==  $rootScope.id_user){
                $scope.favorit = true;
            }
        }
    })

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


    chat.init();
    socket.emit("new message",{id_user: $scope.id_user})
    $window.onfocus = function(){
        socket.emit("new message",{id_user: $scope.id_user})
    }
    $scope.math = window.Math;


    $scope.saveField = function(event,field){
        var text = angular.element(event.target).val();
        var json = {};
        json[field] = text;

        var result = JSON.stringify(json, '', 1);

        personalService.saveField(result).success(function (data) {
            $scope.user = data.user;
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

    $scope.$on('$routeChangeStart', function(next, current) {
        if(current.params.id != undefined && current.params.id != next.targetScope.user.id){
            $rootScope.user = $scope.user = undefined;
        }
    });

        // UPLOAD IMAGES

    $rootScope.images = [];
    $rootScope.canvas = [];

        // ALBUM

    var uploader = $scope.uploader = new FileUploader({
        url: 'upload_album'
    });

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
        var name_album = $scope.album?$scope.album.name:null;
        var description_album = $scope.album?$scope.album.description:null;
        var selectCategories = [];
        for(item in $scope.selectedItem){
            selectCategories.push($scope.selectedItem[item].id);
        }
        personalService.finishUpload({name:name_album,selectCategories:selectCategories,description:description_album}).success(function () {
            $rootScope.user = undefined;
            $location.path("/album/"+$scope.id_post_baraholka);
        });
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



}]);


