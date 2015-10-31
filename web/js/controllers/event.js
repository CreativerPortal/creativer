angular.module('app.ctr.event', ['service.event', 'angularFileUpload', 'service.socket', 'service.chat', 'service.header'])
    .controller('eventCtrl',['$window', '$scope', '$timeout', '$rootScope', '$location', 'headerService', 'eventService','$stateParams', 'FileUploader', 'socket', 'chat', function($window,$scope,$timeout,$rootScope,$location,headerService,eventService,$stateParams, FileUploader, socket, chat) {


        if(!$stateParams.id_edit && !$stateParams.id) {

            if(!$scope.datapicker) {
                $scope.target_day = undefined;
                var id_cat = $stateParams.id_cat?$stateParams.id_cat:null;
                eventService.getDatapicker({id_cat:id_cat}).success(function (data) {
                    $scope.datapicker = data;
                    $scope.events = data.events;
                    $scope.datapicker.next_date = new Date($scope.datapicker.current_date);
                    $scope.datapicker.previous_date = new Date($scope.datapicker.current_date);

                    var job_date = new Date($scope.datapicker.current_date);

                    $scope.datapicker.next_date.setMonth(job_date.getMonth() + 1);
                    $scope.datapicker.previous_date.setMonth(job_date.getMonth() - 1);


                    var count_dayes = 32 - new Date(data.year, data.month - 1, 32).getDate();
                    $scope.count_dayes = new Array(count_dayes);
                    $scope.days = new Array();
                    var current_date = new Date($scope.datapicker.current_date);


                    for (var i = 1; i <= count_dayes; i++) {
                        $scope.days[i] = {'action': 0, 'day': i};
                        for (var k in data.events) {
                            for (var key in data.events[k].events) {
                                var sd = new Date(data.events[k].events[key].start_date).getTime();
                                var ed = new Date(data.events[k].events[key].end_date).getTime();
                                current_date.setDate(i);
                                var cd = current_date.getTime();
                                if (cd >= sd && cd <= ed) {
                                    $scope.days[i] = {'action': 1, 'day': i};
                                    break;
                                }
                            }
                        }
                    }


                    $scope.myDatetimeRange = {
                        "date": {
                            "from": data.current_date,
                            "to": data.current_date,
                            "min": null,
                            "max": null
                        },
                        "hasTimeSliders": false,
                        "hasDatePickers": true
                    };
                    $scope.myDatetimeLabels = {
                        date: {
                            from: '???? ?????? ???????',
                            to: '???? ????? ???????'
                        }
                    };


                });
            }
        }

        $scope.nextMonth = function(){
            $scope.target_day = undefined;
            var id_cat = $stateParams.id_cat?$stateParams.id_cat:null;
            eventService.getDatapicker({date:$scope.datapicker.next_date,id_cat:id_cat}).success(function (data) {
                $scope.datapicker = data;
                $scope.events = data.events;
                $scope.datapicker.next_date = new Date($scope.datapicker.current_date);
                $scope.datapicker.previous_date = new Date($scope.datapicker.current_date);

                var job_date = new Date($scope.datapicker.current_date);

                $scope.datapicker.next_date.setMonth(job_date.getMonth() + 1);
                $scope.datapicker.previous_date.setMonth(job_date.getMonth() - 1);

                var count_dayes = 32 - new Date(data.year, data.month - 1, 32).getDate();
                $scope.count_dayes = new Array(count_dayes);
                $scope.days = new Array();
                var current_date = new Date($scope.datapicker.current_date);

                for (var i = 1; i <= count_dayes; i++) {
                    $scope.days[i] = {'action': 0, 'day': i};
                    for (var k in data.events) {
                        for (var key in data.events[k].events) {
                            var sd = new Date(data.events[k].events[key].start_date).getTime();
                            var ed = new Date(data.events[k].events[key].end_date).getTime();
                            current_date.setDate(i);
                            var cd = current_date.getTime();
                            if (cd >= sd && cd <= ed) {
                                $scope.days[i] = {'action': 1, 'day': i};
                                break;
                            }
                        }
                    }

                }
            });
        }

        $scope.previousMonth = function(){
            $scope.target_day = undefined;
            var id_cat = $stateParams.id_cat?$stateParams.id_cat:null;
            eventService.getDatapicker({date:$scope.datapicker.previous_date,id_cat:id_cat}).success(function (data) {
                $scope.datapicker = data;
                $scope.events = data.events;
                $scope.datapicker.next_date = new Date($scope.datapicker.current_date);
                $scope.datapicker.previous_date = new Date($scope.datapicker.current_date);

                var job_date = new Date($scope.datapicker.current_date);

                $scope.datapicker.next_date.setMonth(job_date.getMonth() + 1);
                $scope.datapicker.previous_date.setMonth(job_date.getMonth() - 1);

                var count_dayes = 32 - new Date(data.year, data.month - 1, 32).getDate();
                $scope.count_dayes = new Array(count_dayes);
                $scope.days = new Array();
                var current_date = new Date($scope.datapicker.current_date);


                for (var i = 1; i <= count_dayes; i++) {
                    $scope.days[i] = {'action': 0, 'day': i};
                    for (var k in data.events) {
                        for (var key in data.events[k].events) {
                            var sd = new Date(data.events[k].events[key].start_date).getTime();
                            var ed = new Date(data.events[k].events[key].end_date).getTime();
                            current_date.setDate(i);
                            var cd = current_date.getTime();
                            if (cd >= sd && cd <= ed) {
                                $scope.days[i] = {'action': 1, 'day': i};
                                break;
                            }
                        }
                    }

                }

            });
        }

        $scope.getEventsByDate = function(day){
            day = parseInt(day);
            $scope.targetDate = new Date($scope.datapicker.current_date);
            $scope.targetDate.setDate(day);
            var id_cat = $stateParams.id_cat?$stateParams.id_cat:null;
            eventService.getDatapicker({date:$scope.datapicker.next_date, target_date:$scope.targetDate ,id_cat:id_cat}).success(function (data) {
                $scope.events = data.events;
                $scope.target_day = day;
            });
        }

        if($rootScope.cities) {
            $scope.city = $rootScope.cities;
            $scope.section = $rootScope.section;
        }else{
            eventService.getCityAndSections({}).success(function (data) {
                $scope.city = $rootScope.cities = data.city;
                $scope.section = $rootScope.section  = data.section[0].children;
            });
        }

        $scope.saveEvent = function(){
            $scope.loader = true;
            var tinymceModel = tinymce.editors[0].getContent();
            eventService.saveEventService({start_date:$scope.myDatetimeRange.date.from, end_date:$scope.myDatetimeRange.date.to, title:$scope.title, content:tinymceModel, city:$scope.selectCity, section:$scope.selectSection}).success(function (data) {
                $location.path("/event/" + data.id);
            });
        };

        if($stateParams.id_edit){
            $scope.$watchGroup(["city","section"], function(){
                if($scope.section && $scope.city){
                    $scope.myDatetimeRange = {
                        "date": {
                            "from": new Date(),
                            "to": new Date(),
                            "min": null,
                            "max": null
                        },
                        "hasTimeSliders": false,
                        "hasDatePickers": true
                    };
                    $scope.myDatetimeLabels = {
                        date: {
                            from: '???? ?????? ???????',
                            to: '???? ????? ???????'
                        }
                    };
                    eventService.getEvent({id:$stateParams.id_edit}).success(function (data) {
                        $scope.myDatetimeRange.date.from = data.start_date;
                        $scope.myDatetimeRange.date.to = data.end_date;
                        $scope.title = data.name;
                        $scope.selectSection = data.event_sections.id;
                        $scope.selectCity = data.event_city.id;
                        $scope.id_edit = data.id;
                        $scope.tinymceModel = data.description;
                        $scope.main_image = data.img;
                        $scope.main_path = data.path;
                        $scope.remove = {'remove_post': false};
                    });
                }
            })
        }

        if($stateParams.id){
            eventService.getEvent({id:$stateParams.id}).success(function (data) {
                $scope.event = data;
                $scope.users_attend = false;
                for(var key in $scope.event.users_attend){
                    if($scope.event.users_attend[key].id == $rootScope.id_user){
                        $scope.event_attend = true;
                    }
                }
                angular.element('.description').html(data.description);
            });
        }


        $scope.saveEditEvent = function(){
            $scope.loader = true;
            var description = tinymce.editors[0].getContent();
            eventService.saveEditEvent({
                "id": $scope.id_edit,
                "description": description,
                "name": $scope.title,
                "event_city_id": $scope.selectCity,
                "event_sections_id": $scope.selectSection,
                "start_date": $scope.myDatetimeRange.date.from,
                "end_date": $scope.myDatetimeRange.date.to
            }).success(function (data) {
                $location.path("/event/" + $stateParams.id_edit);
            });
        }

        $scope.eventAttend = function(){
            eventService.eventAttend({id:$stateParams.id}).success(function (data) {
                $scope.event_attend = data.attend;
                $scope.event.users_attend = data.users;
                headerService.getSoonEvents().success(function (data) {
                    $rootScope.events_attend = data;
                })
            });
        }

        $scope.saveComment = function(text){
            $scope.loader = true;
            eventService.saveComment({event_id:$scope.event.id,text:text}).success(function (data) {
                $scope.event.event_comments.push(data);
                $scope.text_comment = undefined;
                $scope.loader = false;
            });
        }

        $scope.deleteEvent = function(id_event){
            eventService.deleteEvent({"id": id_event}).success(function (data) {
                $location.path("/events");
            });
        }

        $scope.removeComment = function(id,key){
            eventService.removeComment({id: id}).success(function (data) {
                $scope.event.event_comments.splice(key,1);
            });
        }

        $scope.searchEvent = function(){
            $location.path("/events/search/"+$scope.searchEventText);
        }

        $scope.tinymceOptions = {
            file_browser_callback : function(field_name, url, type, win){
                                        tinymce.activeEditor.windowManager.open({
                                            file: 'http://creativer.ml:8080/elfinder',// use an absolute path!
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
            if(uploader.queue.length == 2){
                uploader.queue = new Array(uploader.queue[1]);
            }
            if($stateParams.id_edit){
                fileItem.formData.push({id: $stateParams.id_edit});
            }else{
            }
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
            console.log(response);
            $scope.main_image = response.img;
            $scope.main_path = response.path;
        };
        uploader.onCompleteAll = function(fileItem, response, status, headers) {

        };

        chat.init();
        socket.emit("new message",{id_user: $scope.id_user})
        $window.onfocus = function(){
            socket.emit("new message",{id_user: $scope.id_user})
        }

}]);


