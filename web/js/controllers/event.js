angular.module('app.ctr.event', ['service.event', 'angularFileUpload', 'service.socket', 'service.chat', 'service.header', 'angular-momentjs'])
    .controller('eventCtrl',['$state','$window', '$scope', '$timeout', '$rootScope', '$location', 'headerService', 'eventService','$stateParams', 'FileUploader', 'socket', 'chat', '$moment', function($state,$window,$scope,$timeout,$rootScope,$location,headerService,eventService,$stateParams, FileUploader, socket, chat, $moment) {

        $rootScope.title = "Creativer - Новости и события";
        $rootScope.description = "Интервью с мастерами, полезные советы, обзоры, афиша творческих мероприятий и просто важных событий.";


        //$rootScope.full_page = 0;


        $scope.myDatetimeRange = {
            "date": {},
            "hasTimeSliders": false,
            "hasDatePickers": true
        };
        $scope.myDatetimeLabels = {
            date: {
                from: "начало события",
                to: "конец события"
            }
        };

        if($stateParams.events_search_text){
            $scope.searchEventText = $stateParams.events_search_text;
            eventService.searchEvents({text:$stateParams.events_search_text}).success(function (data) {
                $scope.events = data.events;
            })
        }

        $scope.nextMonth = function(){
            $rootScope.target_day = undefined;
            var id_cat = $stateParams.id_cat?$stateParams.id_cat:null;
            if ($state.current.name != 'event')
                eventService.getDatapicker({date:$rootScope.datapicker.next_date,id_cat:id_cat,city:$rootScope.city}).success(function (data) {
                    $rootScope.datapicker = data;
                    $rootScope.events = data.events;
                    $rootScope.datapicker.current_date = $moment($rootScope.datapicker.current_date).format('YYYY-MM-DD');
                    $rootScope.datapicker.next_date = $moment($rootScope.datapicker.current_date).add('months', 1).format('YYYY-MM-DD');
                    $rootScope.datapicker.previous_date = $moment($rootScope.datapicker.current_date).subtract('months', 1).format('YYYY-MM-DD');



                    var count_dayes = 32 - new Date(data.year, data.month - 1, 32).getDate();
                    $rootScope.count_dayes = new Array(count_dayes);
                    $rootScope.days = [];
                    var current_date = new Date($rootScope.datapicker.current_date);

                    for (var i = 1; i <= count_dayes; i++) {
                        $rootScope.days[i] = {'action': 0, 'day': i};
                        for (var k in data.events) {
                            for (var key in data.events) {
                                var sd = $moment(data.events[key].start_date).unix();
                                var ed = $moment(data.events[key].end_date).unix();
                                var cd = $moment(current_date).date(i).unix();
                                if (cd >= sd && cd <= ed) {
                                    $rootScope.days[i] = {'action': 1, 'day': i};
                                    break;
                                }
                            }
                        }

                    }
                });
        }

        $scope.previousMonth = function(){
            $rootScope.target_day = undefined;
            var id_cat = $stateParams.id_cat?$stateParams.id_cat:null;
            if ($state.current.name != 'event')
                eventService.getDatapicker({date:$rootScope.datapicker.previous_date,id_cat:id_cat,city:$rootScope.city}).success(function (data) {
                    $rootScope.datapicker = data;
                    $rootScope.events = data.events;
                    $rootScope.datapicker.current_date = $moment($rootScope.datapicker.current_date).format('YYYY-MM-DD');
                    $rootScope.datapicker.next_date = $moment($rootScope.datapicker.current_date).add('months', 1).format('YYYY-MM-DD');
                    $rootScope.datapicker.previous_date = $moment($rootScope.datapicker.current_date).subtract('months', 1).format('YYYY-MM-DD');

                    var job_date = new Date($rootScope.datapicker.current_date);

                    var count_dayes = 32 - new Date(data.year, data.month - 1, 32).getDate();
                    $rootScope.count_dayes = new Array(count_dayes);
                    $rootScope.days = [];
                    var current_date = new Date($rootScope.datapicker.current_date);


                    for (var i = 1; i <= count_dayes; i++) {
                        $rootScope.days[i] = {'action': 0, 'day': i};
                        for (var k in data.events) {
                            for (var key in data.events) {
                                var sd = $moment(data.events[key].start_date).unix();
                                var ed = $moment(data.events[key].end_date).unix();
                                var cd = $moment(current_date).date(i).unix();
                                if (cd >= sd && cd <= ed) {
                                    $rootScope.days[i] = {'action': 1, 'day': i};
                                    break;
                                }
                            }
                        }

                    }

                });
        }

        $scope.getEventsByDate = function(day){
            if(day != undefined){
                day = parseInt(day);
                $rootScope.targetDate = $moment($rootScope.datapicker.current_date).date(day).format('YYYY/MM/DD');
                var id_cat = $stateParams.id_cat?$stateParams.id_cat:null;
                if ($state.current.name != 'event')
                eventService.getDatapicker({date:$rootScope.datapicker.current_date, target_date:$rootScope.targetDate ,id_cat:id_cat, city:$scope.city}).success(function (data) {
                    $rootScope.events = data.events;
                    $rootScope.target_day = day;
                    });
            }else{
                var id_cat = $stateParams.id_cat?$stateParams.id_cat:null;
                if ($state.current.name != 'event')
                    eventService.getDatapicker({date:$rootScope.datapicker.current_date, id_cat:id_cat, city:$scope.city}).success(function (data) {
                        $rootScope.events = data.events;
                        $rootScope.target_day = day;
                    });
            }

        }

        if($rootScope.cities) {
            $scope.cities = $rootScope.cities;
            $scope.section = $rootScope.section;
        }else{
            eventService.getCityAndSections({}).success(function (data) {
                $scope.cities = $rootScope.cities = data.city;
                $scope.section = $rootScope.section  = data.section[0].children;
            });
        }


        var first_load_city = false;
        $scope.$watchGroup(["city"], function() {
            var id_cat = $stateParams.id_cat ? $stateParams.id_cat : null;
            if (!$stateParams.events_search_text && $state.current.name != 'event')
                if (first_load_city != undefined) {
                    eventService.getDatapicker({id_cat: id_cat, city: $scope.city}).success(function (data) {
                        $rootScope.datapicker = data;
                        $rootScope.events = data.events;
                        $scope.news = data.news;
                        $rootScope.datapicker.current_date = $moment($rootScope.datapicker.current_date).format('YYYY-MM-DD');
                        $rootScope.datapicker.next_date = $moment($rootScope.datapicker.current_date).add('months', 1).format('YYYY-MM-DD');
                        $rootScope.datapicker.previous_date = $moment($rootScope.datapicker.current_date).subtract('months', 1).format('YYYY-MM-DD');

                        var count_dayes = 32 - new Date(data.year, data.month - 1, 32).getDate();
                        $rootScope.count_dayes = new Array(count_dayes);
                        $rootScope.days = new Array();
                        var current_date = new Date($rootScope.datapicker.current_date);


                        for (var i = 1; i <= count_dayes; i++) {
                            $rootScope.days[i] = {'action': 0, 'day': i};
                                for (var key in data.events) {
                                    var sd = $moment(data.events[key].start_date).unix();
                                    var ed = $moment(data.events[key].end_date).unix();
                                    var cd = $moment(current_date).date(i).unix();
                                    if (cd >= sd && cd <= ed) {
                                        $rootScope.days[i] = {'action': 1, 'day': i};
                                        break;
                                    }
                                }
                        }
                        $rootScope.previous_city = $scope.city
                        $rootScope.previous_id_cat = id_cat;
                    });
                }else{
                    first_load_city = true;
                }


            if($stateParams.id_edit){
                    eventService.getEvent({id:$stateParams.id_edit}).success(function (data) {
                        $scope.myDatetimeRange.date.from = data.start_date;
                        $scope.myDatetimeRange.date.to = data.end_date;
                        $scope.name_title = data.name;
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

        if($stateParams.id){
            eventService.getEvent({id:$stateParams.id}).success(function (data) {
                $rootScope.title = data.name;
                $scope.event = data;
                $scope.users_attend = false;
                for(var key in $scope.event.users_attend){
                    if($scope.event.users_attend[key].id == $rootScope.id_user){
                        $scope.event_attend = true;
                    }
                }
                angular.element('.description').html(data.description);
                eventService.reviewedEvent({id:$stateParams.id});
            });
        }

        $scope.saveEvent = function(){
            $scope.loader = true;
            var tinymceModel = tinymce.editors[0].getContent();
            eventService.saveEventService({start_date:$scope.myDatetimeRange.date.from, end_date:$scope.myDatetimeRange.date.to, title:$scope.name_title, content:tinymceModel, city:$scope.selectCity, section:$scope.selectSection}).success(function (data) {
                $location.path("/event/" + data.id);
            });
        };

        $scope.saveEditEvent = function(){
            $scope.loader = true;
            var description = tinymce.editors[0].getContent();
            eventService.saveEditEvent({
                "id": $scope.id_edit,
                "description": description,
                "name": $scope.name_title,
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

        $scope.saveComment = function(event,text){
            if($scope.loader_event == false || $scope.loader_event == undefined) {
                $scope.loader_event = true;
                eventService.saveComment({event_id: $scope.event.id, text: text}).success(function (data) {
                    $scope.event.event_comments.push(data);
                    $scope.event.text_comment = undefined;
                    $scope.emojiMessage.rawhtml = undefined;
                    $scope.loader_event = false;

                    var res = text.indexOf(event.answer_username);
                    if(res == 0 && event.answer_id){
                        socket.emit("set notification",{id_user: $rootScope.id_user, receiver: event.answer_id, type: "answer", url: '/event/'+event.id})
                    }
                });
            }
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

        $scope.uncheck = function (id) {
            if ($rootScope.previous_checked == id){
                $rootScope.city = false;
                $rootScope.previous_checked = undefined;
            }else{
                $rootScope.previous_checked = id;
            }
        }

        $scope.tinymceOptions = {
            file_browser_callback : function(field_name, url, type, win){
                tinymce.activeEditor.windowManager.open({
                    file: 'http://creativer.by/elfinder',// use an absolute path!
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
            url: 'upload_image_event',
            queueLimit: 11
        });

        uploader.filters.push({
            name: 'imageFilter',
            fn: function(item /*{File|FileLikeObject}*/, options) {
                var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
                return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
            }
        });

        uploader.filters.push({
            name: 'enforceMaxFileSize',
            fn: function (item) {
                return item.size <= 10485760; // 10 mb
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


