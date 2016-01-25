angular.module('app.ctr.catalog', ['service.catalog', 'service.personal', 'service.album',  'service.socket', 'service.chat', 'angularFileUpload'])
    .controller('catalogCtrl',['$state', '$window', '$scope', '$rootScope', '$location', 'catalogService', 'personalService', 'albumService', '$stateParams', '$stateParams', 'FileUploader', 'socket', 'chat', function($state,$window,$scope,$rootScope,$location,catalogService,personalService,albumService,$stateParams,$stateParams, FileUploader, socket, chat) {

    $rootScope.title = "Портал для креативных людей!";

    if(!$scope.text_first && $state.current.name != 'main'){
        catalogService.getNewsEvents().success(function (data) {
            $rootScope.news_events = $scope.news_events = data;
            $scope.text_first = data[0]?data[0].description:null;
            $scope.text_second = data[1]?data[1].description:null;
        })
    }

    if(!$rootScope.my_user){
        personalService.getUser().success(function (data) {
            $rootScope.user = $scope.user = $rootScope.my_user = data.user;
        })
    }else{
        $scope.user = $rootScope.my_user;
    }
        
    if(($stateParams.id_products && !$rootScope.products) || $state.current.name == 'products_all'){
        catalogService.getProducts({id:$stateParams.id_products}).success(function (data) {
            $rootScope.products = data.products[0].children;
            $rootScope.product = data.product[0];
        });
    }else if(($stateParams.id_services && !$rootScope.services) || $state.current.name == 'services_all' || $rootScope.service == undefined){
        catalogService.getServices({id:$stateParams.id_services}).success(function (data) {
            $rootScope.services = data.services[0].children;
            $rootScope.service = data.service[0];
            console.log($rootScope.service);
        });
    }

    if($stateParams.url_img){
        $scope.url_img = $stateParams.url_img;
        $rootScope.overflow = true;
    }else{
        $rootScope.overflow = false;
    }
    if($stateParams.key_img || $rootScope.key_img){
        $rootScope.key_img = $stateParams.key_img;
        $scope.next_key_img = parseInt($stateParams.key_img)+1;
        $scope.previous = parseInt($stateParams.key_img)-1;
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
    if(!$rootScope.filterConditionServices){
        $rootScope.filterConditionServices = 'likes';
    }



    $rootScope.$watch('service', function() {
        $rootScope.service_id = $stateParams.id_services;
        if($rootScope.service && $rootScope.service_id){
            for(var key in $rootScope.services){
                if($rootScope.services[key].child == true){
                    $rootScope.services[key].child = false;
                }
            }
        }
    });

    $rootScope.$watch('product', function() {
        $rootScope.product_id = $stateParams.id_products;
        if($rootScope.product && $rootScope.product_id){
            for(var key in $rootScope.products){
                if($rootScope.products[key].child == true){
                    $rootScope.products[key].child = false;
                }
            }
        }
    });


    if($state.current.name == 'products_all'){
        for(var key in $rootScope.products){
                $rootScope.products[key].child = false;
        }
    }else if($state.current.name == 'services_all'){
        for(var key in $rootScope.services){
            $rootScope.services[key].child  = false;
        }
    }

    $stateParams.page = $stateParams.page?$stateParams.page:1;

    if(($state.current.name == 'services_page' || $state.current.name == 'services' || !$rootScope.currentPage || $rootScope.currentPage != $stateParams.page || $stateParams.id_services != $rootScope.id_services) && $stateParams.id_services || $state.current.name == 'services_all'){
        $rootScope.$watch('filterConditionServices', function() {
            catalogService.getCatalogServiceAlbums({
                id: $stateParams.id_services,
                page: $stateParams.page,
                filter: $rootScope.filterConditionServices
            }).success(function (data) {
                $rootScope.items_services = $scope.items_services = data.services;
                $rootScope.id_services = $stateParams.id_services;
                $rootScope.pages_services = [];
                $rootScope.pages_services[0] = $scope.items_services.currentPageNumber;
                $rootScope.currentPage = $scope.currentPage = $scope.items_services.currentPageNumber;

                var length = ($scope.items_services.totalCount / $scope.items_services.numItemsPerPage < 5) ? $scope.items_services.totalCount / $scope.items_services.numItemsPerPage : 5;
                length--;
                while (length > 0) {
                    if (($rootScope.pages_services[0] > 1 && $rootScope.pages_services[0] != $rootScope.currentPage - 2) || ($rootScope.pages_services[0] > 1 && $rootScope.pages_services[$rootScope.pages_services.length - 1] > $scope.items.totalCount / $scope.items.numItemsPerPage )) {
                            $rootScope.pages_services.unshift($rootScope.pages_services[0] - 1)
                        length = length - 1;
                    } else {
                        var p = parseInt($rootScope.pages_services[$rootScope.pages_services.length - 1]) + 1;
                        $rootScope.pages_services.push(p);
                        length = length - 1;
                    }
                }
            })
        })
    }

    if(($state.current.name == 'products_page' || $state.current.name == 'products' || !$rootScope.currentPage || $rootScope.currentPage != $stateParams.page || $stateParams.id_products != $rootScope.id_products) && $stateParams.id_products || $state.current.name == 'products_all'){
        $rootScope.page = $stateParams.page;
        $rootScope.$watch('filterCondition', function() {
            catalogService.getCatalogProductAlbums({
                id: $stateParams.id_products,
                page: $stateParams.page,
                filter: $rootScope.filterCondition
            }).success(function (data) {
                $rootScope.items = $scope.items = data.products;
                for(var key in $scope.items.items){
                    if($scope.items.items[key].name == $stateParams.url_img){
                        $location.path("/products/"+$stateParams.id_products+'/'+$scope.page+'/'+$stateParams.url_img+'/'+key);
                    }
                }

                var images_id = new Array();
                for(var key in $scope.items.items){
                    images_id.push($scope.items.items[key].id);
                }
                catalogService.getLikesByImagesId({images_id:images_id}).success(function (data) {
                    $scope.items.images_likes = data.likes;
                });

                $rootScope.id_products = $stateParams.id_products;
                $rootScope.pages = [];
                $rootScope.pages[0] = $scope.items.currentPageNumber;
                $rootScope.currentPage = $scope.currentPage = $scope.items.currentPageNumber;
                var length = ($scope.items.totalCount / $scope.items.numItemsPerPage < 5) ? $scope.items.totalCount / $scope.items.numItemsPerPage : 5;
                length--;
                while (length > 0) {
                    if (($rootScope.pages[0] > 1 && $rootScope.pages[0] != $rootScope.currentPage - 2) || ($rootScope.pages[0] > 1 && $rootScope.pages[$rootScope.pages.length - 1] > $scope.items.totalCount / $scope.items.numItemsPerPage )) {
                        $rootScope.pages.unshift($rootScope.pages[0] - 1)
                        length = length - 1;
                    } else {
                        var p = parseInt($rootScope.pages[$rootScope.pages.length - 1]) + 1;
                        $rootScope.pages.push(p);
                        length = length - 1;
                    }
                }
            })
        })
    }

    $rootScope.removeProducts = function(){
            $rootScope.items = $scope.items = null;
            $rootScope.pages = null;
    }

    $rootScope.removeServices = function(){
            $rootScope.items_services = $scope.items_services = null;
            $rootScope.pages_services = null;
    }

    $scope.removeComment = function(key_img,id,key){
        albumService.removeComment({id: id}).success(function (data) {
            if($scope.items.items[key_img][0]){
                $scope.items.items[key_img][0].image_comments.splice(key,1);
            }else{
                $scope.items.items[key_img].image_comments.splice(key,1);
            }
        });
    }

    if(!$rootScope.image_previews){
        $rootScope.image_previews = [];
    }

    if($stateParams.key_img && $scope.items){
        if(!$rootScope.image_previews[$scope.items.items[$stateParams.key_img].id_album]){
            $rootScope.image_previews[$scope.items.items[$stateParams.key_img].id_album] = [];
        }
        if($rootScope.image_previews.indexOf($scope.items.items[$stateParams.key_img].id) == -1){
            $rootScope.image_previews[$scope.items.items[$stateParams.key_img].id_album].push($scope.items.items[$stateParams.key_img].id);
        }
    }

    $scope.closeImg = function(){
        albumService.imagePreviews({image_previews:$rootScope.image_previews}).success(function (data) {
            $rootScope.image_previews = [];
        });
        $rootScope.overflow = false;
    }

    $scope.like = function(id,image_key){
        if($scope.items.images_likes[id].liked){
            $scope.items.items[image_key].likes = $scope.items.items[image_key].likes - 1;
            $scope.items.images_likes[id].liked = !$scope.items.images_likes[id].liked;
        }else{
            $scope.items.items[image_key].likes = $scope.items.items[image_key].likes + 1;
            $scope.items.images_likes[id].liked = !$scope.items.images_likes[id].liked;
        }
        albumService.like({image_id:id}).success(function (data) {
            $scope.items.items[image_key].likes = data.likes;
            $scope.items.images_likes[id].liked = data.liked;
        });
    }

    $scope.saveImageComment = function(image,text){
        $scope.loader = true;
        albumService.saveImageComment({image_id:image.id,text:text,id: $rootScope.id_user}).success(function (data) {
            image.image_comments.push(data);
            $scope.user.text_comment = undefined;
            $scope.loader = false;
        });
    }

    chat.init();
    socket.emit("new message",{id_user: $scope.id_user})
    $window.onfocus = function(){
        socket.emit("new message",{id_user: $scope.id_user})
    }


        //if ($state.current.name != 'products_search'){
        //    $rootScope.title = "";
        //    $rootScope.description = "";
        //    $rootScope.image_src = "";
        //}

    $scope.facebook = function(purl, ptitle, path, pname, text) {
        var url  = 'http://www.facebook.com/sharer.php?s=100';
        url += '&p[title]='     + encodeURIComponent(ptitle);
        url += '&p[summary]='   + encodeURIComponent(text);
        url += '&p[url]='       + encodeURIComponent(purl);
        url += '&p[images][0]=' + 'http://creativer.ml/home/album/thums/' + encodeURIComponent(path) + encodeURIComponent(pname);
        $scope.popup(url);
    };
    $scope.twitter = function(purl, ptitle) {
        var url  = 'http://twitter.com/share?';
        url += 'text='      + encodeURIComponent(ptitle);
        url += '&url='      + encodeURIComponent(purl);
        url += '&counturl=' + encodeURIComponent(purl);
        $scope.popup(url);
    };
    $scope.popup = function(url) {
        window.open(url,'','toolbar=0,status=0,width=626,height=436');
    }


    $scope.math = window.Math;

    if($stateParams.products_search_text){
        $rootScope.condition = 2;
        $rootScope.pages = null;
        $scope.items = null;

        if(!$rootScope.products){
            catalogService.getProducts({id:$stateParams.id_products}).success(function (data) {
                $rootScope.products = data.products[0].children;
                $rootScope.product = data.product[0];
            });
        }

        if(!$scope.items || $stateParams.products_search_text != $scope.products_search_text) {
            $scope.products_search_text = $stateParams.products_search_text;
            catalogService.searchProducts({search_text: $stateParams.products_search_text}).success(function (data) {
                $rootScope.items = $scope.items = data.products;

                var images_id = new Array();
                for (var key in $scope.items.items) {
                    images_id.push($scope.items.items[key].id);
                }


                catalogService.getLikesByImagesId({images_id: images_id}).success(function (data) {
                    $scope.items.images_likes = data.likes;
                });

                $rootScope.id_products = $stateParams.id_products;
                $rootScope.pages = [];
                $rootScope.currentPage = 0;
            });
        }
    }else if($stateParams.services_search_text){
        $rootScope.condition = 3;
        $rootScope.pages_services = null;
        $scope.items_services = null;
        catalogService.getServices({id:$stateParams.id_services}).success(function (data) {
            $rootScope.services = data.services[0].children;
            $rootScope.service = data.service[0];
        });
        catalogService.searchServices({search_text:$stateParams.services_search_text}).success(function (data) {
            $rootScope.pages_services = [];
            $rootScope.currentPage = 0;
            $scope.items_services = data.products;
        });
    }


    $scope.deleteImage = function(image_id,key_img,key_album){
        albumService.deleteImage({image_id:image_id}).success(function (data) {
            $scope.user.albums[key_album].images.splice(key_img,1);
            $location.path("/album/"+$stateParams.id_album+'/'+$scope.user.albums[key_album].images[key_img].name+'/'+key_img);
        });
    }

    $rootScope.$watch('wi_container', function() {


    });


}]);


