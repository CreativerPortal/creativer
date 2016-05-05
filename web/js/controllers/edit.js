angular.module('app.ctr.baraholka', ['service.baraholka', 'angularFileUpload', 'service.socket', 'service.chat'])
    .controller('baraholkaCtrl',['$window', '$scope', '$rootScope', '$location', 'baraholkaService', '$stateParams', 'FileUploader', 'socket', 'chat', function($window,$scope,$rootScope,$location,baraholkaService,$stateParams, FileUploader, socket, chat) {

        $rootScope.title = "Редактирование";

        $scope.createPostBaraholka = function(){
            var data = {};
            data.post_id = $scope.post_id;
            data.post_category = $scope.post_category.id;
            data.section = $scope.section;
            data.title = $scope.title;
            data.city = $scope.city;
            data.description = $scope.description;
            data.full_description = $scope.full_description;
            data.full_price = $scope.price;
            data.auction = $scope.auction;
            baraholkaService.createPostBaraholka(data).success(function (data) {

            });
        }

        $scope.saveComment = function(text){
            $scope.loader = true;
            var user = {
                user: {
                    username: $rootScope.username,
                    lastname: $rootScope.lastname,
                    avatar: $rootScope.img
                },
                text: text,
                date: new Date()
            }
            $scope.post.post_comments.push(user);
            baraholkaService.saveComment({post_id:$scope.post.id,text:text}).success(function (data) {
                $scope.user = data.user;
                $scope.text_comment = undefined;
                $scope.loader = false;
            });
        }

        if($scope.baraholka == undefined || $scope.post_category == undefined || $scope.post_city == undefined){
            if($rootScope.baraholka && $rootScope.post_category && $rootScope.post_city){
                $scope.baraholka = $rootScope.baraholka;
                $scope.post_category = $rootScope.post_category;
                $scope.post_city = $rootScope.post_city;
            }else{
                baraholkaService.getDataBaraholka().success(function (data) {
                    $rootScope.baraholka = $scope.baraholka = data.baraholka.children;
                    $rootScope.post_category = $scope.post_category = data.post_category;
                    $rootScope.post_city = $scope.post_city = data.post_city;
                });
            }
        }


        $rootScope.$watchGroup(['city','my_singboard', 'singboard_participate', 'new24', 'post_category_id'], function() {

            if($stateParams.id_category){
                $scope.posts_category = $stateParams.id_category;
                baraholkaService.getPostsByCategory({category_id: $stateParams.id_category,page:$stateParams.page,
                    city:$rootScope.city,
                    my_singboard:$rootScope.my_singboard,
                    singboard_participate:$rootScope.singboard_participate,
                    new24:$rootScope.new24,
                    post_category_id: $rootScope.post_category_id
                }).success(function (data) {
                    $scope.posts = data.posts.items;
                    $scope.posts_page = data.posts;

                    $scope.pages = [];
                    $scope.pages[0] = $scope.posts_page.currentPageNumber;
                    $scope.currentPage = $scope.posts_page.currentPageNumber;
                    var length = ($scope.posts_page.totalCount/$scope.posts_page.numItemsPerPage<5)?$scope.posts_page.totalCount/$scope.posts_page.numItemsPerPage:5;
                    length--;
                    while(length > 0){
                        if($scope.pages[0] > 1){
                            $scope.pages.unshift($scope.pages[0]-1)
                            length = length - 1;
                        }else{
                            var p = parseInt($scope.pages[$scope.pages.length-1]) + 1;
                            $scope.pages.push(p);
                            length = length - 1;
                        }
                    }
                }).error(function(data) {
                    $scope.posts = null;
                    $scope.posts_page = null;
                });
            }

        });



        if($stateParams.id_post){
            baraholkaService.getPostById({"post_id": $stateParams.id_post}).success(function (data) {
                $scope.post = data.post;
            });
        }else if($stateParams.id_post){

        }


        
        var uploader = $scope.uploader = new FileUploader({
            url: 'create_post_baraholka',
            queueLimit: 10
        });

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
        uploader.onBeforeUploadItem = function (item) {

            item.formData.push({post_id: $scope.post_id});
            item.formData.push({post_category: $scope.post_category.id});
            item.formData.push({section: $scope.section});
            item.formData.push({title: $scope.title});
            item.formData.push({city: $scope.city});
            item.formData.push({description: $scope.description});
            item.formData.push({full_description: $scope.full_description});
            item.formData.push({full_price: $scope.price});
            item.formData.push({auction: $scope.auction});

            if(item.main == 1) {
                item.formData.push({main: 1});
            }
            uploader.uploadAll();
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
            $scope.id_post_baraholka = response.id;
        };
        uploader.onCompleteAll = function() {
            $location.path("/viewtopic/"+$scope.id_post_baraholka);
        };

        angular.element(document.querySelector('#fileInput')).on('change',handleFileSelect);

        chat.init();
        socket.emit("new message",{id_user: $scope.id_user})
        $window.onfocus = function(){
            socket.emit("new message",{id_user: $scope.id_user})
        }

        $scope.uncheck = function (id) {
            if ($rootScope.previous_checked == id){
                $rootScope.city = false;
                $rootScope.previous_checked = undefined;
            }else{
                $rootScope.previous_checked = id;
            }
        }

}]);


