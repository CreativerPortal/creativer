angular.module('app.ctr.catalog', ['service.catalog', 'service.personal', 'service.album',  'service.socket', 'service.chat', 'angularFileUpload'])
    .controller('catalogCtrl',['$window', '$scope', '$rootScope', '$location', 'catalogService', 'personalService', 'albumService', '$routeParams', 'FileUploader', 'socket', 'chat', function($window,$scope,$rootScope,$location,catalogService,personalService,albumService,$routeParams, FileUploader, socket, chat) {

    if($rootScope.news_events == undefined || !$routeParams.url_img){
        catalogService.getNewsEvents().success(function (data) {
            $rootScope.news_events = $scope.news_events = data;
            if(data[0].description != undefined){
                angular.element('#new_event_1').text(data[0].description.replace(/<[^>]+>|&nbsp;/g,'').slice(0,60)+" ...");
            }
            if(data[1].description != undefined){
                angular.element('#new_event_2').text(data[1].description.replace(/<[^>]+>|&nbsp;/g,'').slice(0,60)+" ...");
            }
        })
    }

    if(!$rootScope.my_user){
        personalService.getUser().success(function (data) {
            $rootScope.user = $scope.user = $rootScope.my_user = data.user;
        })
    }else{
        $scope.user = $rootScope.my_user;
    }


    if($routeParams.id_products && !$rootScope.products){
        catalogService.getProducts({id:$routeParams.id_products}).success(function (data) {
            $rootScope.products = data.products[0].children;
            $rootScope.product = data.product[0];
        });
    }else if($routeParams.id_services && !$rootScope.services){
        catalogService.getServices({id:$routeParams.id_services}).success(function (data) {
            $rootScope.services = data.services[0].children;
            $rootScope.service = data.service[0];
        });
    }

    if($routeParams.url_img){
        $scope.url_img = $routeParams.url_img;
        $rootScope.overflow = true;
    }else{
        $rootScope.overflow = false;
    }
    if($routeParams.key_img || $rootScope.key_img){
        $rootScope.key_img = $routeParams.key_img;
        $scope.next_key_img = parseInt($routeParams.key_img)+1;
        $scope.previous = parseInt($routeParams.key_img)-1;
    }
    else{
        $rootScope.key_img = undefined;
    }

    $scope.math = window.Math;
    $scope.height = $window.innerHeight-150;


        $scope.operators = [
            {value: 'likes', displayName: 'рейтингу'},
            {value: 'views', displayName: 'кол-во просмотров'},
            {value: 'date', displayName: 'дате добавления'},
        ];
        if(!$rootScope.filterCondition){
            $rootScope.filterCondition = 'likes';
        }


        $rootScope.$watch('service', function() {
            if($rootScope.service)
            $rootScope.service.id = $routeParams.id_services;
            if($rootScope.service && $rootScope.service.id){
                for(var key in $rootScope.services){
                    if($rootScope.services[key].child == true){
                        $rootScope.services[key].child = false;
                    }
                }
            }
        });

        $rootScope.$watch('product', function() {
            if($rootScope.product)
            $rootScope.product.id = $routeParams.id_products;
            if($rootScope.product && $rootScope.product.id){
                for(var key in $rootScope.products){
                    if($rootScope.products[key].child == true){
                        $rootScope.products[key].child = false;
                    }
                }
            }
        });

        $routeParams.page = $routeParams.page?$routeParams.page:1;

        if($routeParams.id_services){
            $rootScope.$watch('filterCondition', function() {
                catalogService.getCatalogServiceAlbums({
                    id: $routeParams.id_services,
                    page: $routeParams.page,
                    filter: $rootScope.filterCondition
                }).success(function (data) {
                    $scope.items = data.services;

                    $scope.pages = [];
                    $scope.pages[0] = $scope.items.currentPageNumber;
                    $scope.currentPage = $scope.items.currentPageNumber;
                    var length = ($scope.items.totalCount / $scope.items.numItemsPerPage < 5) ? $scope.items.totalCount / $scope.items.numItemsPerPage : 5;
                    length--;
                    while (length > 0) {
                        console.log(length);
                        if ($scope.pages[0] > 1) {
                            $scope.pages.unshift($scope.pages[0] - 1)
                            length = length - 1;
                        } else {
                            var p = parseInt($scope.pages[$scope.pages.length - 1]) + 1;
                            $scope.pages.push(p);
                            length = length - 1;
                        }
                    }
                })
            })
        }

        if($routeParams.id_products && !$routeParams.url_img || $rootScope.items == undefined){
            $rootScope.page = $routeParams.page;
            $rootScope.$watch('filterCondition', function() {
                catalogService.getCatalogProductAlbums({
                    id: $routeParams.id_products,
                    page: $routeParams.page,
                    filter: $rootScope.filterCondition
                }).success(function (data) {
                    $rootScope.items = $scope.items = data.products;
                    for(var key in $scope.items.items){
                        if($scope.items.items[key].name == $routeParams.url_img){
                            $location.path("/products/"+$scope.product.id+'/'+$scope.page+'/'+$routeParams.url_img+'/'+key);
                        }
                    }

                    var images_id = new Array();
                    for(var key in $scope.items.items){
                        images_id.push($scope.items.items[key].id);
                    }
                    catalogService.getLikesByImagesId({images_id:images_id}).success(function (data) {
                        $scope.items.images_likes = data.likes;
                    });

                    $scope.pages = [];
                    $scope.pages[0] = $scope.items.currentPageNumber;
                    $scope.currentPage = $scope.items.currentPageNumber;
                    var length = ($scope.items.totalCount / $scope.items.numItemsPerPage < 5) ? $scope.items.totalCount / $scope.items.numItemsPerPage : 5;
                    length--;
                    while (length > 0) {
                        console.log(length);
                        if ($scope.pages[0] > 1) {
                            $scope.pages.unshift($scope.pages[0] - 1)
                            length = length - 1;
                        } else {
                            var p = parseInt($scope.pages[$scope.pages.length - 1]) + 1;
                            $scope.pages.push(p);
                            length = length - 1;
                        }
                    }
                })
            })
        }


    $scope.like = function(id,image_key){
        albumService.like({image_id:id}).success(function (data) {
            $scope.items.items[image_key].likes = data.likes;
            $scope.items.images_likes[id].liked = !$scope.items.images_likes[id].liked;
        });
    }

    $scope.saveImageComment = function(image,text){
        $scope.loader = true;
        var user = {
            user: {
                id: 0,
                username: $rootScope.username,
                lastname: $rootScope.lastname,
                avatar: $rootScope.avatar
            },
            text: text,
            date: new Date()
        }
        image.image_comments.push(user);
        albumService.saveImageComment({image_id:image.id,text:text,id: $rootScope.id_user}).success(function (data) {
            $scope.text_comment = undefined;
            $scope.loader = false;
        });
    }

    chat.init();
    socket.emit("new message",{id_user: $scope.id_user})
    $window.onfocus = function(){
        socket.emit("new message",{id_user: $scope.id_user})
    }


    $scope.math = window.Math;


    if($routeParams.products_search_text){
        $rootScope.condition = 2;
        catalogService.getProducts({id:$routeParams.id_products}).success(function (data) {
            $rootScope.products = data.products[0].children;
            $rootScope.product = data.product[0];
        });
        catalogService.searchProducts({search_text:$routeParams.products_search_text}).success(function (data) {
            $scope.items = data.products;
        });
    }else if($routeParams.services_search_text){
        $rootScope.condition = 3;
        catalogService.getServices({id:$routeParams.id_services}).success(function (data) {
            $rootScope.services = data.services[0].children;
            $rootScope.service = data.service[0];
        });
        catalogService.searchServices({search_text:$routeParams.services_search_text}).success(function (data) {
            $scope.items = data.products;
        });
    }


    // end init controller

    $scope.deleteImage = function(image_id,key_img,key_album){
        albumService.deleteImage({image_id:image_id}).success(function (data) {
            $scope.user.albums[key_album].images.splice(key_img,1);
            $location.path("/album/"+$routeParams.id_album+'/'+$scope.user.albums[key_album].images[key_img].name+'/'+key_img);
        });
    }

    $rootScope.$watch('wi_container', function() {


    });


}]);


