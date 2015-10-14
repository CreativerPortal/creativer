angular.module('app.ctr.shop', ['service.shop', 'angularFileUpload', 'service.socket', 'service.chat'])
    .controller('shopCtrl',['$window', '$scope', '$timeout', '$rootScope', '$location', 'shopService','$stateParams', 'FileUploader', 'socket', 'chat', function($window,$scope,$timeout,$rootScope,$location,shopService,$stateParams, FileUploader, socket, chat) {

        shopService.getCtegoriesShops({}).success(function (data) {
            $rootScope.data = $scope.data = data.catagories_shops;
            for(var key in $scope.data){
                $scope.data[key].name = $scope.data[key].parent.name+' :: '+$scope.data[key].name;
            }
            $rootScope.data = $scope.data;
            $scope.selectOnly1Or2 = function(item, selectedItems) {
                if (selectedItems  !== undefined && selectedItems.length >= 20) {
                    return false;
                } else {
                    return true;
                }
            };
        });

        if($stateParams.id_category){
            shopService.getShopsByCategory({id:$stateParams.id_category}).success(function (data) {
                $scope.posts = data.shops;
            });
        }

        $scope.removeShop = function(id,key){
            shopService.removeShop({id:id}).success(function (data) {
                $scope.posts.splice(key,1);
            });
        }

        $scope.createShop = function(){
            var data = {};
            data.section = $scope.section;
            data.title = $scope.title;
            data.full_description = $scope.full_description;
            //baraholkaService.createPostBaraholka(data).success(function (data) {
            //
            //});
        }

        chat.init();
        socket.emit("new message",{id_user: $scope.id_user})
        $window.onfocus = function(){
            socket.emit("new message",{id_user: $scope.id_user})
        }

        var uploader = $scope.uploader = new FileUploader({
            url: 'upload_shop'
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

        uploader.onCompleteItem = function(fileItem, response, status, headers) {
            // console.info('onCompleteItem', fileItem, response, status, headers);
            $scope.id_album = response.id;
        };
        uploader.onCompleteAll = function(response) {
            var selectCategories = [];
            for(var key in $scope.selectedItem){
                selectCategories.push($scope.selectedItem[key].id);
            }
            if($scope.id_album){
                $location.path("/album/"+$scope.id_album);
                $scope.id_album = null;
            }
        };

        uploader.onBeforeUploadItem = function (item) {

            if($scope.title != undefined) {
                item.formData.push({title: $scope.title});
            }

            if($scope.full_description != undefined) {
                item.formData.push({full_description: $scope.full_description});
            }

            var selectCategories = [];
            for(var i in $scope.selectedItem){
                selectCategories.push($scope.selectedItem[i].id);
            }

            if(selectCategories != undefined) {
                var selectCategories = selectCategories.join(',');
                item.formData.push({selectCategories: selectCategories});
            }

            if($scope.count_images_uploade == uploader.queue.length) {
                item.formData.push({stop: 'true'});
            }
            uploader.uploadAll();
        };

}]);


