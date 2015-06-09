angular.module('app.ctr.baraholka', ['service.baraholka', 'angularFileUpload'])
    .controller('baraholkaCtrl',['$scope', '$rootScope', '$location', '$animate', 'baraholkaService','$routeParams', 'FileUploader', function($scope,$rootScope,$location,$animate,baraholkaService,$routeParams, FileUploader) {

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
            // console.info('onWhenAddingFileFailed', item, filter, options);
        };
        uploader.onAfterAddingFile = function(fileItem) {
            // console.info('onAfterAddingFile', fileItem);
            $scope.res = uploader.queue.length/3;
        };
        uploader.onAfterAddingAll = function(addedFileItems) {
            // console.info('onAfterAddingAll', addedFileItems);
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

        angular.element(document.querySelector('#fileInput')).on('change',handleFileSelect);


        baraholkaService.getCategoriesBaraholka().success(function (data) {
            $scope.baraholka = data.baraholka.children;
        });

}]);


