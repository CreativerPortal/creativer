angular.module('app.ctr.event', ['service.event', 'angularFileUpload', 'service.socket', 'service.chat'])
    .controller('eventCtrl',['$window', '$scope', '$timeout', '$rootScope', '$location', 'eventService','$routeParams', 'FileUploader', 'socket', 'chat', function($window,$scope,$timeout,$rootScope,$location,eventService,$routeParams, FileUploader, socket, chat) {

        $scope.myDatetimeRange = {
            "date": {
                "from": "2015-08-23T09:39:27.549Z",
                "to": "2015-08-23T09:39:27.549Z",
                "min": null,
                "max": null
            },
            "hasTimeSliders": false,
            "hasDatePickers": true
        };
        $scope.myDatetimeLabels = {
            date: {
                from: 'Дата начала события',
                to: 'Дата конца события'
            }
        };

        $scope.saveEvent = function(){
            var tinymceModel = tinymce.get('ui-tinymce-0').getContent();
            eventService.saveEventService({start_date:$scope.myDatetimeRange.date.from, end_date:$scope.myDatetimeRange.date.to, title:$scope.title, content:tinymceModel, city:$scope.selectCity, section:$scope.selectSection}).success(function (data) {

            });
        };

        eventService.getEventSections({}).success(function (data) {
            $scope.section = data.section.children;
        });

        eventService.getCity({}).success(function (data) {
            $scope.city = data.city;
        });

        eventService.getEvents({}).success(function (data) {
            $scope.events = data;
        });

        if($routeParams.id){
            eventService.getEvent({id:$routeParams.id}).success(function (data) {
                $scope.event = data;
                angular.element('.description').html(data.description);
            });
        }

        $scope.tinymceOptions = {
            file_browser_callback : function(field_name, url, type, win){
                                        tinymce.activeEditor.windowManager.open({
                                            file: 'http://creativer.by.my/elfinder',// use an absolute path!
                                            title: 'Проводник',
                                            width: 1350,
                                            height: 560,
                                            resizable: 'yes'
                                        }, {
                                            setUrl: function (url) {
                                                win.document.getElementById(field_name).value = url;
                                            }
                                        });
                                        return false;
            },
            height: '350px',
            width: '750px',
            mode : "textareas",
            skin: 'custom',
            theme : 'modern',
            extended_valid_elements: "iframe[*]",
            plugins: [
                "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
                "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                "save table contextmenu directionality emoticons template paste textcolor"
            ],
            toolbar: "insertfile undo redo bold italic alignleft aligncenter alignright alignjustify bullist numlist outdent indent link image preview media fullpage forecolor backcolor emoticons",
            media_filter_html: false,
            language: "ru"
        };


        var uploader = $scope.uploader = new FileUploader({
            url: 'upload_image_event'
        });

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
            uploader.uploadAll();
        };

        uploader.onBeforeUploadItem = function (item) {
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

        };
        uploader.onCompleteAll = function(fileItem, response, status, headers) {

        };

        chat.init();
        socket.emit("new message",{id_user: $scope.id_user})
        $window.onfocus = function(){
            socket.emit("new message",{id_user: $scope.id_user})
        }

}]);


