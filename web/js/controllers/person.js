angular.module('app.ctr.person', ['service.personal', 'angularFileUpload'])
    .controller('personCtrl',['$scope', '$rootScope', '$location', '$animate', 'personalService','$routeParams', 'FileUploader', function($scope,$rootScope,$location,$animate,personalService,$routeParams, FileUploader) {

    // init controller

    if($rootScope.user === undefined || $routeParams.id != $rootScope.id_user) {
        if($routeParams.id == undefined){
            var id_user = $rootScope.id_user;
        }else{
            var id_user = $routeParams.id;
        }
        personalService.getUser({id: id_user}).success(function (data) {
            $scope.user = $rootScope.user = data.user;

            if($routeParams.id_album){
                for(var key in $scope.user.albums){
                    if($scope.user.albums[key].id == $routeParams.id_album){
                            $scope.id = key;
                    }
                }
            }
        });
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

    $scope.savePost = function(wall_id){
        personalService.savePost({wall_id:wall_id,text:$scope.text_post,id: $routeParams.id}).success(function (data) {
            $scope.user = data.user;
        });
    }

    $scope.saveComment = function(post_id, text){
        personalService.saveComment({post_id:post_id,text:text,id: $routeParams.id}).success(function (data) {
            console.log(data);
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

    // ALBUM

    $scope.uploader = new FileUploader();

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
        console.info('onWhenAddingFileFailed', item, filter, options);
    };
    uploader.onAfterAddingFile = function(fileItem) {
        console.info('onAfterAddingFile', fileItem);
    };
    uploader.onAfterAddingAll = function(addedFileItems) {
        console.info('onAfterAddingAll', addedFileItems);
    };
    uploader.onBeforeUploadItem = function(item) {
        console.info('onBeforeUploadItem', item);
    };
    uploader.onProgressItem = function(fileItem, progress) {
        console.info('onProgressItem', fileItem, progress);
    };
    uploader.onProgressAll = function(progress) {
        console.info('onProgressAll', progress);
    };
    uploader.onSuccessItem = function(fileItem, response, status, headers) {
        console.info('onSuccessItem', fileItem, response, status, headers);
    };
    uploader.onErrorItem = function(fileItem, response, status, headers) {
        console.info('onErrorItem', fileItem, response, status, headers);
    };
    uploader.onCancelItem = function(fileItem, response, status, headers) {
        console.info('onCancelItem', fileItem, response, status, headers);
    };
    uploader.onCompleteItem = function(fileItem, response, status, headers) {
        console.info('onCompleteItem', fileItem, response, status, headers);
    };
    uploader.onCompleteAll = function() {
        var name_album = $scope.album.name;
        var description_album = $scope.album.description;
        personalService.finishUpload({name:name_album,description:description_album}).success(function () {
            $rootScope.user = undefined;
            $location.path("/");
        });
        console.info('onCompleteAll');
    };

    uploader.onBeforeUploadItem = function (item) {
        if(item.file.name == ''){
            item.file.name = 'описание картинки';
        }
        if(item.main == 1) {
            item.formData.push({main: 1});
        }
        uploader.uploadAll();
    };

    console.info('uploader', uploader);

}]);


