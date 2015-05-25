angular.module('app.ctr.catalog', ['service.catalog', 'angularFileUpload'])
    .controller('catalogCtrl',['$scope', '$rootScope', '$location', '$animate', 'catalogService','$routeParams', 'FileUploader', function($scope,$rootScope,$location,$animate,catalogService,$routeParams, FileUploader) {


    catalogService.getCtegories({id:$routeParams.id_category}).success(function (data) {
        $scope.categories = data.categories[0].children;
        $scope.category = data.category[0];

    });


    $scope.math = window.Math;


    // end init controller


    $scope.deleteImage = function(image_id,key_img,key_album){

        albumService.deleteImage({image_id:image_id}).success(function (data) {
            $scope.user.albums[key_album].images.splice(key_img,1);
            $location.path("/album/"+$routeParams.id_album+'/'+$scope.user.albums[key_album].images[key_img].name+'/'+key_img);
        });
    }

}]);


