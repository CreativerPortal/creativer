angular.module('app.ctr.baraholka', ['service.baraholka', 'angularFileUpload', 'service.socket', 'service.chat'])
    .controller('baraholkaCtrl',['$window', '$scope', '$timeout', '$rootScope', '$location', 'baraholkaService','$stateParams', 'FileUploader', 'socket', 'chat', function($window,$scope,$timeout,$rootScope,$location,baraholkaService,$stateParams, FileUploader, socket, chat) {


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

        if($stateParams.id_post){
            baraholkaService.getPostById({"post_id": $stateParams.id_post}).success(function (data) {
                $scope.post = data.post;
            });
        }else if($stateParams.id_fleamarketposting){
            $scope.$watchGroup(['post_city'], function() {
                if($scope.post_city){
                    baraholkaService.getPostById({"post_id": $stateParams.id_fleamarketposting}).success(function (data) {
                        console.log(data.post);
                        $scope.post = data.post;
                    });
                }
            })

        }

        $scope.checkPostCategory = function(id_check_post_category){
            baraholkaService.checkPostCategory({"id": $scope.post.id, "id_check_post_category": id_check_post_category}).success(function (data) {
            });
        }

        $scope.$watch("section", function(){
            if($scope.section){
                baraholkaService.checkCategoryBaraholka({"id": $scope.post.id, "id_check_category_baraholka": $scope.section}).success(function (data) {
                });
            }
        })

        $scope.$watch("city", function(){
            if($scope.city){
                baraholkaService.editCity({"id": $scope.post.id, "city": $scope.city}).success(function (data) {
                });
            }
        })

        $scope.editTitle = function(title){
            baraholkaService.editTitle({"id": $scope.post.id, "title": title}).success(function (data) {
            });
        }

        $scope.editFullDescription = function(full_description){
            baraholkaService.editFullDescription({"id": $scope.post.id, "full_description": full_description}).success(function (data) {
            });
        }

        $scope.editDescription = function(description){
            baraholkaService.editDescription({"id": $scope.post.id, "description": description}).success(function (data) {
            });
        }

        $scope.editPrice = function(price){
            baraholkaService.editPrice({"id": $scope.post.id, "price": price}).success(function (data) {
            });
        }

        $scope.editAuction = function(auction){
            baraholkaService.editAuction({"id": $scope.post.id, "auction": auction}).success(function (data) {
            });
        }

        $scope.removeImageBaraholka = function(id_image){
            baraholkaService.removeImageBaraholka({"id": $scope.post.id, "id_image": id_image}).success(function (data) {
                for(var key in $scope.post.images_baraholka){
                    if($scope.post.images_baraholka[key].id == id_image){
                        $scope.post.images_baraholka.splice(key,1);
                    }
                }
            });
        }

        $scope.mainImageBaraholka = function(id_image){
            baraholkaService.mainImageBaraholka({"id": $scope.post.id, "id_image": id_image}).success(function (data) {
            });
        }

        $scope.uncheck = function (id) {
            if ($rootScope.previous_checked == id){
                $rootScope.city = false;
                $rootScope.previous_checked = undefined;
            }else{
                $rootScope.previous_checked = id;
            }
        }

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

        $scope.redirectPostBaraholka = function(){
            $scope.loader = true;
            $location.path("/viewtopic/" + $stateParams.id_fleamarketposting);
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
                    $scope.old_posts = $scope.posts = data.posts.items;
                    $scope.old_posts_page = $scope.posts_page = data.posts;
                    $scope.old_nameCategory = $scope.nameCategory = data.posts.nameCategory;

                    $scope.old_pages = $scope.pages = [];
                    $scope.old_pages[0] = $scope.pages[0] = $scope.posts_page.currentPageNumber;
                    $scope.old_currentPage = $scope.currentPage = $scope.posts_page.currentPageNumber;
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


        $scope.$watch("searchInCategory", function(){

            var text = $scope.searchInCategory;

            if($scope.searchInCategory == ""){
                $scope.search = false;
                $scope.posts = $scope.old_posts;
                $scope.posts_page = $scope.old_posts_page;
                $scope.nameCategory = $scope.old_nameCategory;
                $scope.pages = [];
                $scope.pages[0] = $scope.old_pages[0];
                $scope.currentPage = $scope.old_currentPage;
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
            }

            $timeout(function() {
                if(($scope.searchInCategory == text && text != undefined && text != '') || (text && text.length > 0 && $scope.searchInCategory == 0)){
                    baraholkaService.searchPostsBaraholkaByText({category_id:$stateParams.id_category, search_text: $scope.searchInCategory}).success(function (data) {
                        if(text == $scope.searchInCategory) {
                            $scope.search = true;
                            $scope.posts = data.posts;
                            $scope.posts_page = null;
                            $scope.pages = [];
                        }
                    });
                }
            }, 1000);

        })

        $scope.$watch("searchInBaraholka", function(){

            var text = $scope.searchInBaraholka;

            if($scope.searchInBaraholka == ""){
                $scope.search = false;
                $scope.posts = $scope.old_posts;
                $scope.posts_page = $scope.old_posts_page;
                $scope.nameCategory = $scope.old_nameCategory;
                $scope.pages = [];
                $scope.pages[0] = $scope.old_pages[0];
                $scope.currentPage = $scope.old_currentPage;
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
            }

            $timeout(function() {
                if(($scope.searchInBaraholka == text && text != undefined && text != '') || (text && text.length > 0 && $scope.searchInBaraholka == 0)){
                    baraholkaService.searchPostsBaraholkaByText({category_id:1000, search_text: $scope.searchInBaraholka}).success(function (data) {
                        if(text == $scope.searchInBaraholka){
                            $scope.search = true;
                            $scope.posts = data.posts;
                            $scope.posts_page = null;
                            $scope.pages = [];
                        }
                    });
                }
            }, 1000);

        });

        $scope.searchPostsBaraholkaByText = function(){
            baraholkaService.searchPostsBaraholkaByText({category_id:$stateParams.id_category, search_text: $scope.searchInCategory}).success(function (data) {
                if(text == $scope.searchInCategory) {
                    $scope.search = true;
                    $scope.posts = data.posts;
                    $scope.posts_page = null;
                    $scope.pages = [];
                }
            });

            baraholkaService.searchPostsBaraholkaByText({category_id:1000, search_text: $scope.searchInBaraholka}).success(function (data) {
                if(text == $scope.searchInBaraholka){
                    $scope.search = true;
                    $scope.posts = data.posts;
                    $scope.posts_page = null;
                    $scope.pages = [];
                }
            });
        }

        // UPLOAD

        if($stateParams.id_fleamarketposting){
            var uploader = $scope.uploader = new FileUploader({
                url: 'edit_images_post_baraholka'
            });
        }else{
            var uploader = $scope.uploader = new FileUploader({
                url: 'create_post_baraholka'
            });
        }


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
            if($stateParams.id_fleamarketposting){
                fileItem.formData.push({id: $scope.post.id});
                uploader.uploadAll();
            }
            $scope.res = uploader.queue.length/3;
        };

        uploader.onBeforeUploadItem = function (item) {

            if($stateParams.id_fleamarketposting){
            }else{
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
            if($stateParams.id_fleamarketposting){
                $scope.post.images_baraholka.push({"id": response.id, "name": response.name, "path": response.path});
                uploader.queue = [];
            }
        };
        uploader.onCompleteAll = function(fileItem, response, status, headers) {
            if(!$stateParams.id_fleamarketposting) {
                $location.path("/viewtopic/" + $scope.id_post_baraholka);
            }
        };

        chat.init();
        socket.emit("new message",{id_user: $scope.id_user})
        $window.onfocus = function(){
            socket.emit("new message",{id_user: $scope.id_user})
        }

}]);


