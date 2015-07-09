angular.module('app.ctr.messages', ['service.messages', 'service.socket', 'service.chat', 'service.personal', 'angularFileUpload', 'ngImgCrop', 'multi-select-tree'])
    .controller('messagesCtrl',['$window', '$scope', '$rootScope', '$location', '$timeout', 'messagesService', 'personalService', '$routeParams', 'FileUploader', 'socket', 'chat', function($window, $scope,$rootScope,$location,$timeout,messagesService,personalService,$routeParams, FileUploader, socket, chat) {


    messagesService.getUser().success(function (data) {
        $rootScope.user = $scope.user = data.user;
        $scope.favorit = false;
        for(key in $scope.user.favorits_with_me){
            if($scope.user.favorits_with_me[key].id ==  $rootScope.id_user){
                $scope.favorit = true;
            }
        }
    })

    messagesService.getUser({id:$routeParams.id_user_chat}).success(function (data) {
        $scope.companion = data.user;

    })

    $scope.$watchGroup(['user','companion'], function() {

        if($scope.companion && $scope.user){
            $scope.ids = [$scope.companion.id,$scope.user.id];
            $scope.ids = $scope.ids.sort();
            socket.emit('reviewed', {ids: $scope.ids, id_user: $scope.user.id});
            socket.emit("history",{id_user:$scope.user.id, ids:$scope.ids});
        }

        if($scope.id_user_chat == undefined && $scope.user != undefined){
            socket.emit('all messages', {id_user:$scope.user.id});
        }
    });

    if(!$routeParams.id_user_chat){
        socket.on("all messages", function(data) {
            $scope.messages = data;
        });
    }else{
        socket.on("history", function(data) {
            $rootScope.messages = data;
        });
    }


    $scope.uncheck = function (event) {
        if ($scope.previous_checked == event.target.value){
            $scope.checked = false
            $scope.previous_checked = undefined;
        }else{
            $scope.previous_checked = $scope.checked;
        }
    }

    $scope.send_message = function(text){
        if(text != '' && $scope.ids && $scope.user){
            socket.emit('message', {ids: $scope.ids, sender: $scope.user.id, text: text});
        }
    }

        chat.init();

        $scope.updateAvatar = function(image){
        $scope.loader = true;
        personalService.updateAvatar({img:image}).success(function (data) {
            $scope.user = $rootScope.user = data.user;
            $rootScope.avatar = $scope.user.avatar.img;
            $scope.myImage = false;
            $scope.loader = false;
        });
    }


    $window.onfocus = function(){
        socket.emit('reviewed', {ids: $scope.ids, id_user: $scope.user.id});
    }

    $scope.go = function ( path ) {
        $location.path( path );
    };

    socket.on('reviewed', function(data){

        //for(var key in $rootScope.new_messages){
        //    $rootScope.new_messages
        //    if($rootScope.new_messages[key].id_user == data.id_user){
        //        $scope.messages.unshift($rootScope.new_messages[key]);
        //        $rootScope.new_messages = $rootScope.new_messages.slice(key,1);
        //    }
        //}
    });

    //$scope.$watchGroup(['user','new_messages'], function() {
    //    for(var key in $rootScope.new_messages){
    //        if($scope.user && $rootScope.new_messages && $rootScope.new_messages[key].id_recipient == $scope.user.id){
    //            console.log("равны 1");
    //            socket.emit('reviewed', {ids: $scope.ids, id_user: $scope.user.id});
    //        }
    //    }
    //});


    $scope.$on('$routeChangeStart', function(next, current) {
        if(current.params.id != undefined && current.params.id != next.targetScope.user.id){
            $rootScope.user = $scope.user = undefined;
        }
    });

    // ALBUM

    $scope.uploader = new FileUploader();

    var uploader = $scope.uploader = new FileUploader({
        url: 'upload_album'
    });

    $rootScope.images = [];
    $rootScope.canvas = [];

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
       // $scope.res = uploader.queue.length/3;
       // fileItem._file.width = '10px';
        //var canvas = document.querySelectorAll('canvas');
    };
    uploader.onAfterAddingAll = function(addedFileItems,key) {
        // console.info('onAfterAddingAll', addedFileItems);

        setTimeout(function() {
            if(key != undefined){
                $rootScope.images.splice(key,1);
            }
            var count = angular.element(document.querySelectorAll('canvas')).length - 1;
            $rootScope.canvas = angular.element(document.querySelectorAll('canvas'));

            var width = 533;
            var count_row = Math.ceil(count/4);

            var count_images = count / count_row;
            var images_row = [];

            for(var i=0; i < count_row; i++){
                if((count_row-1) == i){
                    var cm = Math.ceil(count_images);
                    var cm1 = Math.floor(count_images);
                    var mass = $rootScope.images.slice(i*cm1,i*cm1+cm);
                    images_row.push(mass);
                }else{
                    var cm = Math.floor(count_images);
                    var mass = $rootScope.images.slice(i*cm,i*cm+cm);
                    images_row.push(mass);  // количество картинок в строке
                }
            }

            var p=0;
            for(var j=0; j<count_row; j++){
                var delim = (width - images_row[j].length*4)*images_row[j][0].height;
                var delit =images_row[j][0].width;

                for(var k=1; k < images_row[j].length; k++){
                    delit=delit+images_row[j][k].width*(images_row[j][0].height/images_row[j][k].height);
                }

                var height = Math.floor(delim/delit);//высота строки

                for(var k=0; k < images_row[j].length; k++){
                    var width_im = images_row[j][k].width / images_row[j][k].height * height;
                    $rootScope.canvas[p].setAttribute('width', width_im );
                    $rootScope.canvas[p].setAttribute('height', height );
                    $rootScope.canvas[p].getContext('2d').drawImage(images_row[j][k], 0, 0, width_im, height);
                    p=p+1;
                }
            }

        }, 1000)

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
        messagesService.finishUpload({name:name_album,selectCategories:selectCategories,description:description_album}).success(function () {
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

    $timeout(function(){
        angular.element(document.querySelector('#fileInput')).on('change', handleFileSelect);
    }, 2000);

    ////////////////////////////////////////////////////


    }]);

