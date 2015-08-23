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


        eventService.getCity({}).success(function (data) {
            $scope.city = data.city;
        });

        $scope.tinymceOptions = {
            inline: false,
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
                                        return false; },
            mode : "textareas",
            skin: 'custom',
            theme : 'modern',
            plugins: [
                "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
                "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                "save table contextmenu directionality emoticons template paste textcolor"
            ],
            toolbar: "insertfile undo redo bold italic alignleft aligncenter alignright alignjustify bullist numlist outdent indent link image preview media fullpage forecolor backcolor emoticons",
            style_formats: [
                {title: 'Bold text', inline: 'b'},
                {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
                {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
                {title: 'Example 1', inline: 'span', classes: 'example1'},
                {title: 'Example 2', inline: 'span', classes: 'example2'},
                {title: 'Table styles'},
                {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
            ],
            language: "ru"
        };


        var uploader = $scope.uploader = new FileUploader({
            url: 'create_event'
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
            // console.info('onAfterAddingFile', fileItem);
        };

        uploader.onBeforeUploadItem = function (item) {
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

        };
        uploader.onCompleteAll = function(fileItem, response, status, headers) {

        };

        chat.init();
        socket.emit("new message",{id_user: $scope.id_user})
        $window.onfocus = function(){
            socket.emit("new message",{id_user: $scope.id_user})
        }

}]);


