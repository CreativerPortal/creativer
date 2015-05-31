angular.module('app.ctr.catalog', ['service.catalog', 'angularFileUpload'])
    .controller('catalogCtrl',['$scope', '$rootScope', '$location', '$animate', 'catalogService','$routeParams', 'FileUploader', function($scope,$rootScope,$location,$animate,catalogService,$routeParams, FileUploader) {



    if($routeParams.id_products){

    }else if(!$rootScope.services){
        catalogService.getServices({id:$routeParams.id_services}).success(function (data) {
            $rootScope.services = data.services[0].children;
            $rootScope.service = data.service[0];
        });
    }

        $rootScope.$watch('service', function() {
            if($rootScope.service && $rootScope.service.id){
                $rootScope.service.id = $routeParams.id_services;
                for(var key in $rootScope.services){
                    if($rootScope.services[key].child == true){
                        $rootScope.services[key].child = false;
                    }
                }
            }else if($rootScope.product && $rootScope.product.id){
                $rootScope.product.id = $routeParams.id_products;
                for(var key in $rootScope.products){
                    if($rootScope.products[key].child == true){
                        $rootScope.products[key].child = false;
                    }
                }
            }
        });




        var id_category = $routeParams.id_services?$routeParams.id_services:$routeParams.id_products;

        $routeParams.page = $routeParams.page?$routeParams.page:1;

        catalogService.getCatalogAlbums({id:id_category,page:$routeParams.page}).success(function (data) {
            $scope.albums = data.albums;

            $scope.pages = [];
            $scope.pages[0] = $scope.albums.currentPageNumber;
            $scope.currentPage = $scope.albums.currentPageNumber;
            var length = ($scope.albums.totalCount/$scope.albums.numItemsPerPage<5)?$scope.albums.totalCount/$scope.albums.numItemsPerPage:5;
            length--;
            while(length > 0){
                console.log(length);
                if($scope.pages[0] > 1){
                    $scope.pages.unshift($scope.pages[0]-1)
                    length = length - 1;
                }else{
                    $scope.pages.push($scope.pages[$scope.pages.length-1]+1);
                    length = length - 1;
                }
            }
            console.log($scope.pages);
        })


    $animate.enabled(false);


    $scope.math = window.Math;


    // end init controller


    $scope.deleteImage = function(image_id,key_img,key_album){

        albumService.deleteImage({image_id:image_id}).success(function (data) {
            $scope.user.albums[key_album].images.splice(key_img,1);
            $location.path("/album/"+$routeParams.id_album+'/'+$scope.user.albums[key_album].images[key_img].name+'/'+key_img);
        });
    }

}]);


