angular.module('app.ctr.shop', ['service.shop', 'angularFileUpload', 'service.socket', 'service.chat', 'angularSearchTree'])
    .controller('shopCtrl',['$state', '$window', '$scope', '$timeout', '$rootScope', '$location', 'shopService', '$stateParams', 'FileUploader', 'socket', 'chat', 'searchTree', function($state, $window,$scope,$timeout,$rootScope,$location,shopService,$stateParams, FileUploader, socket, chat, SearchTree) {

        $rootScope.title = "Магазины на Creativer";

        if($stateParams.id_shop){
            shopService.getShopById({id:$stateParams.id_shop}).success(function (data) {
                $rootScope.title = data.name;
                $rootScope.description = data.full_description.substr(0,120);
                $scope.shop = data;
            });
        }

        if ($stateParams.edit_id) {
            shopService.getShopById({id: $stateParams.edit_id}).success(function (data) {
                $scope.shop = data;
                $scope.shop.remove_shop = false;
                var categories = [];
                for (var key in data.categories) {
                    categories.push(data.categories[key]);
                }

                shopService.getCtegoriesShops({}).success(function (data) {
                    $rootScope.data = $scope.data = data.catagories_shops;
                    for (var key in $scope.data) {
                        $scope.data[key].name = $scope.data[key].parent.name + ' :: ' + $scope.data[key].name;
                    }

                    var data = $scope.data;

                    $scope.selectOnly1Or2 = function (item, selectedItems) {
                        if (selectedItems !== undefined && selectedItems.length >= 20) {
                            return false;
                        } else {
                            return true;
                        }
                    };

                    for (var cat in categories) {
                        data = SearchTree()({id: categories[cat].id}, data);
                    }

                    $scope.data = data;
                });
            });
        } else {
            shopService.getCtegoriesShops({}).success(function (data) {
                $rootScope.data = $scope.data = data.catagories_shops;
                for (var key in $scope.data) {
                    $scope.data[key].name = $scope.data[key].parent.name + ' :: ' + $scope.data[key].name;
                }
                $rootScope.data = $scope.data;
                $scope.selectOnly1Or2 = function (item, selectedItems) {
                    if (selectedItems !== undefined && selectedItems.length >= 20) {
                        return false;
                    } else {
                        return true;
                    }
                };
            });
        }

        if($stateParams.id_category){
            shopService.getShopsByCategory({id:$stateParams.id_category}).success(function (data) {
                $scope.posts = data.shops;
            });
        }

        if ($state.current.name == 'create_shop') {
            $scope.address = [];
            $scope.address.push("");
            $scope.add = function () {
                $scope.address.push("");
            };
        }else if($state.current.name == 'edit_shop'){
            $scope.add = function () {
                $scope.shop.address.push("");
            };
        }

        $scope.removeShop = function(id,key){
            shopService.removeShop({id:id}).success(function (data) {
                $location.path("/shops/"+data.id);
            });
        }

        $scope.removeImageShop = function(id_image){
            shopService.removeImageShop({id: $scope.shop.id, id_image: id_image}).success(function (data) {
                for(var key in $scope.shop.images){
                    if($scope.shop.images[key].id == id_image){
                        $scope.shop.images.splice(key,1);
                    }
                }
            });
        }

        $scope.mainImageShop = function(id_image){
            shopService.mainImageShop({"id": $scope.shop.id, "id_image": id_image}).success(function (data) {
            });
        }

        $scope.createShop = function(){
            var data = {};
            data.section = $scope.section;
            data.title = $scope.shop_title;
            data.description = $scope.shop_description;
            data.full_description = $scope.shop_full_description;
        }

        $scope.editName = function(name){
            shopService.editName({id: $scope.shop.id, name: name}).success(function (data) {
            });
        }

        $scope.editDescription = function(description){
            shopService.editDescription({"id": $scope.shop.id, "description": description}).success(function (data) {
            });
        }

        $scope.editFullDescription = function(full_description){
            shopService.editFullDescription({"id": $scope.shop.id, "full_description": full_description}).success(function (data) {
            });
        }

        $scope.editSite = function(site){
            shopService.editSite({"id": $scope.shop.id, "site": site}).success(function (data) {
            });
        }

        $scope.editAddress = function(id,address){
            shopService.editAddress({"id": $scope.shop.id, "id_address": id, "address": address}).success(function (data) {
            });
        }

        $scope.editTelephone = function(telephone){
            shopService.editTelephone({"id": $scope.shop.id, "telephone": telephone}).success(function (data) {
            });
        }

        $scope.editEmail = function(email){
            shopService.editEmail({"id": $scope.shop.id, "email": email}).success(function (data) {
            });
        }

        $scope.editWorkingTime = function(working_time){
            shopService.editWorkingTime({"id": $scope.shop.id, "working_time": working_time}).success(function (data) {
            });
        }

        chat.init();
        socket.emit("new message",{id_user: $scope.id_user})
        $window.onfocus = function(){
            socket.emit("new message",{id_user: $scope.id_user})
        }

        if($stateParams.edit_id){
            var uploader = $scope.uploader = new FileUploader({
                url: 'edit_images_shop',
                queueLimit: 10
            });
        }else{
            var uploader = $scope.uploader = new FileUploader({
                url: 'upload_shop',
                queueLimit: 10
            });
        }

        $scope.redirectShop = function(){
            if($scope.selectedItem){
                var selectCategories = [];
                for(item in $scope.selectedItem){
                    selectCategories.push($scope.selectedItem[item].id);
                }
            }
            if(selectCategories != undefined) {
                var selectCategories = selectCategories.join(',');
                shopService.editCategories({"id": $scope.shop.id, "selectCategories": selectCategories});
            }
            $scope.loader = true;
            $location.path("/shop/" + $stateParams.edit_id);
        }

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

        uploader.onCompleteItem = function(fileItem, response, status, headers) {
            $scope.id_shop = response.id;
            if($stateParams.edit_id){
                $scope.shop.images.push({id: response.id, name: response.name, path: response.path});
            }
        };

        uploader.onCompleteAll = function(response) {
            uploader.queue = [];
            if(!$stateParams.edit_id){
                $location.path("/shop/"+$scope.id_shop);
            }
        };

        var count_shop_images = 0;

        uploader.onAfterAddingFile = function(fileItem) {
            if($stateParams.edit_id){
                fileItem.formData.push({id: $scope.shop.id});
                uploader.uploadAll();
            }
        };

        uploader.onBeforeUploadItem = function (item) {

            count_shop_images = count_shop_images + 1;

            if($scope.shop_title != undefined) {
                item.formData.push({title: $scope.shop_title});
            }
            if($scope.address != undefined) {
                item.formData.push({address: $scope.address});
            }
            if($scope.working_time != undefined) {
                item.formData.push({working_time: $scope.working_time});
            }
            if($scope.email != undefined) {
                item.formData.push({email: $scope.email});
            }
            if($scope.telephone != undefined) {
                item.formData.push({telephone: $scope.telephone});
            }
            if($scope.site != undefined) {
                item.formData.push({site: $scope.site});
            }
            if($scope.shop_description != undefined) {
                item.formData.push({description: $scope.shop_description});
            }
            if($scope.shop_full_description != undefined) {
                item.formData.push({full_description: $scope.shop_full_description});
            }
            if($scope.address && $scope.address[0] != undefined) {
                var address = JSON.stringify($scope.address);
                item.formData.push({address: address});
            }
            if(uploader.queue.length == count_shop_images) {
                item.formData.push({stop: 1});
            }
            if(item.main == 1) {
                item.formData.push({main: 1});
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


