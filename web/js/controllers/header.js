angular.module('app.ctr.header', ['service.header', 'service.socket'])
    .controller('headerCtrl',['$window', '$scope', '$http', '$rootScope', '$location', 'headerService', '$stateParams', 'socket',  function($window,$scope,$http,$rootScope,$location,headerService,$stateParams,socket) {

    $rootScope.title = "Портал для креативных людей";
    $rootScope.description = "Площадка для ремесленников, общайся, выкладывай свои работы, ищи вдохновение.";

    $scope.date = new Date();

    if(!$rootScope.condition){
        $rootScope.condition = 2;
    }

    headerService.getSoonEvents().success(function (data) {
        $rootScope.events_attend = data;
    })

    $rootScope.search = function(){
        if($scope.searchText == undefined || $scope.searchText == ""){
            if($rootScope.condition == 1){
                $location.path("/people/search/*");
            }else if($rootScope.condition == 2){
                $location.path("/products/search/*");
            }else if($rootScope.condition == 3){
                $location.path("/services/search/*");
            }
        }else{
            if($rootScope.condition == 1){
                $location.path("/people/search/"+$scope.searchText);
            }else if($rootScope.condition == 2){
                $location.path("/products/search/"+$scope.searchText);
            }else if($rootScope.condition == 3){
                $location.path("/services/search/"+$scope.searchText);
            }
        }
    }

    $rootScope.removeUser = function(new_user_id){
        if($rootScope.user && new_user_id != $rootScope.user.id){
            $rootScope.user = null;
        }
    }


    function soundNotification() {
        var audio = new Audio();
        audio.src = '/sound/whistle.mp3';
        audio.autoplay = true;
    }


    socket.on("set notification", function(data) {
        if(data.length > $rootScope.notification.length){
            soundNotification();
        }
        $rootScope.notification = data;
    });

    socket.emit("get notification",{id_user: $rootScope.id_user});
    socket.on("get notification", function(data) {
        $rootScope.notification = data;
    });

    $rootScope.$watch(function() {
        return $http.pendingRequests.length;
    },function(hasPending) {
        if (hasPending) {
            $rootScope.isLoading = true;
        }
        else {
            $rootScope.isLoading = false;
        }
    });

    $rootScope.$on('$stateChangeStart', function(next, current) {
        var path = $location.path();
        for(var key in $rootScope.notification){
            if($rootScope.notification[key].url == path){
                socket.emit("remove notification",{id: $rootScope.notification[key]._id, id_user: $rootScope.id_user});
            }
        }
    });

}]);


