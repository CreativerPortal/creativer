angular.module('app.ctr.person', ['service.personal', 'angularFileUpload'])
    .controller('personCtrl',['$scope','personalService','$routeParams', 'FileUploader', function($scope,personalService,$routeParams, FileUploader) {

    personalService.getUser({ id: $routeParams.id }).success(function (data) {
        console.log(data.user);

        $scope.user = data.user;
    });

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
        var text = angular.element(event.target).html();
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


