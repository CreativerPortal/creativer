var app = angular.module('app', ['ngRoute', 'ui.router', 'app.ctr.person', 'app.ctr.album', 'app.ctr.catalog', 'app.ctr.baraholka', 'app.ctr.messages', 'app.ctr.header', 'app.ctr.shop', 'app.ctr.album.create', 'app.ctr.people', 'app.ctr.event', 'monospaced.elastic', 'ngImgCrop','ui.tinymce','ngSanitize', 'ngTouch', 'rgkevin.datetimeRangePicker', 'ui.bootstrap'])
    .config(['$routeProvider', '$httpProvider', '$stateProvider', '$urlRouterProvider', function ($routeProvider, $httpProvider, $stateProvider, $urlRouterProvider) {


        $urlRouterProvider.otherwise("/");
        $stateProvider.state('create_album', {
            url: '/create_album',
            templateUrl: '/create_album',
            controller: 'createAlbumCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('edit_album', {
            url: '/edit_album/:id_album_edit',
            templateUrl: '/edit_album',
            controller: 'albumCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('main', {
            url: '/',
            templateUrl: '/main_tmp',
            controller: 'catalogCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('feedback', {
            url: '/feedback',
            templateUrl: '/feedback_tmp',
            controller: 'personCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('settings', {
            url: '/settings',
            templateUrl: '/settings_tmp',
            controller: 'personCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('products_search', {
            url: '/products/search/:products_search_text',
            templateUrl: '/products_tmp',
            controller: 'catalogCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('people', {
            url: '/people/search/:people_search',
            templateUrl: '/people_tmp',
            controller: 'peopleCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('products_all', {
            url:'/products',
            templateUrl: '/products_tmp',
            controller: 'catalogCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('products', {
            url:'/products/:id_products',
            templateUrl: '/products_tmp',
            controller: 'catalogCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('products_page', {
            url:'/products/:id_products/:page',
            templateUrl: '/products_tmp',
            controller: 'catalogCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('products_page.state2', {
            url: '/:url_img/:key_img',
            templateUrl: '/show_product_photo_tmp',
            controller: 'catalogCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('services_all', {
            url: '/services',
            templateUrl: '/services_tmp',
            controller: 'catalogCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('services', {
            url: '/services/:id_services',
            templateUrl: '/services_tmp',
            controller: 'catalogCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('services_page', {
            url: '/services/:id_services/:page',
            templateUrl: '/services_tmp',
            controller: 'catalogCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('services_search', {
            url: '/services/search/:services_search_text',
            templateUrl: '/services_tmp',
            controller: 'catalogCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('followers_id', {
            url: '/followers/:id',
            templateUrl: function ($stateParams){
                var url = "/followers_tmp/" + $stateParams.id;
                return url;
            },
            controller: 'personCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('following', {
            url: '/following/:id',
            templateUrl: function ($stateParams){
                var url = "/following_tmp/" + $stateParams.id;
                return url;
            },
            controller: 'personCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('baraholka', {
            url: '/baraholka',
            templateUrl: '/baraholka_tmp',
            controller: 'baraholkaCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('viewforum_empty', {
            url: '/viewforum',
            templateUrl: '/viewforum_tmp',
            controller: 'baraholkaCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('viewforum', {
            url: '/viewforum/:id_category',
            templateUrl: '/viewforum_tmp',
            controller: 'baraholkaCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('viewforum_page', {
            url: '/viewforum/:id_category/:page',
            templateUrl: '/viewforum_tmp',
            controller: 'baraholkaCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('viewtopic', {
            url: '/viewtopic/:id_post',
            templateUrl: '/viewtopic_tmp',
            controller: 'baraholkaCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('fleamarketposting', {
            url: '/fleamarketposting',
            templateUrl: '/fleamarketposting_tmp',
            controller: 'baraholkaCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('edit_fleamarketposting', {
            url: '/edit_fleamarketposting/:id_fleamarketposting',
            templateUrl: '/edit_fleamarketposting_tmp',
            controller: 'baraholkaCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('events', {
            url: '/events',
            templateUrl: '/events_tmp',
            controller: 'eventCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('event', {
            url: '/event/:id',
            templateUrl: '/event_tmp',
            controller: 'eventCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('create_event', {
            url: '/create_event',
            templateUrl: function (){
                var date = new Date();
                var url = "/create_event_tmp/" + date;
                return url;
            },
            controller: 'eventCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('edit_event', {
            url: '/edit_event/:id_edit',
            templateUrl: '/edit_event_tmp',
            controller: 'eventCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('events_search', {
            url: '/events/search/:events_search_text',
            templateUrl: '/search_events_tmp',
            controller: 'eventCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('messages', {
            url: '/messages',
            templateUrl: '/messages_tmp',
            controller: 'messagesCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('chat', {
            url: '/chat/:id_user_chat',
            templateUrl: '/chat_tmp',
            controller: 'messagesCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('shops', {
            url: '/shops/:id_category',
            templateUrl: '/shops_tmp',
            controller: 'shopCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('create_shop', {
            url: '/create_shop',
            templateUrl: '/create_shop_tmp',
            controller: 'shopCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('album', {
            url: '/album/:id_album',
            templateUrl: function ($stateParams){
                var url = "/album_tmp/" + $stateParams.id_album;
                return url;
            },
            controller: 'albumCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('album.photo', {
            url: '/:url_img/:key_img',
            templateUrl: function ($stateParams){
                var url = "/show_photo_tmp";
                return url;
            },
            controller: 'albumCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('id', {
            url: '/:id',
            templateUrl: function ($stateParams){
                var url = "/person_tmp/" + $stateParams.id;
                return url;
            },
            controller: 'personCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('id.state2', {
            url: '/:key_post/:key_post_img',
            templateUrl: function ($stateParams){
                var url = "/show_post_photo_tmp";
                return url;
            },
            controller: 'personCtrl',
            reloadOnSearch: true
        });


        $httpProvider.interceptors.push(function($q, $injector) {
            return {
                'request': function(config) {
                    // request your $rootscope messaging should be here?
                    return config;
                },

                'requestError': function(rejection) {
                    // request error your $rootscope messagin should be here?
                    return $q.reject(rejection);
                },


                'response': function(response) {
                    // response your $rootscope messagin should be here?

                    return response;
                },

                'responseError': function(rejection) {
                    var status = rejection.status;

                    if (status == 401) {
                        window.location = "./";
                        return;
                    }
                    return $q.reject(rejection);

                }
            };
        });
    }]);

app.directive('scrollbar', function($timeout) {
    return {
        link: function(scope, element, attr) {
            $timeout(function() {
                $(".events__curtain__wrapper").mCustomScrollbar({
                    scrollbarPosition: "inside"
                });
            });
        }
    }
});

app.directive('editPain', function () {
    return{
        scope: {
            obj: '='
        },
        link: function(scope, element, attrs){
            element.on("click", function(el){
               var parent = el.target.parentNode.querySelector('.text_info');
                if(parent){
                    parent.removeAttribute('disabled');
                    parent.focus();
                    chat.init();
                    parent.onkeypress = function(e){
                        if(e.keyCode==13){ //enter && shift

                            e.preventDefault(); //Prevent default browser behavior
                            if (window.getSelection) {
                                var selection = window.getSelection(),
                                    range = selection.getRangeAt(0),
                                    br = document.createTextNode("\t\n"),
                                    textNode = document.createTextNode("\t"); //Passing " " directly will not end up being shown correctly
                                range.deleteContents();//required or not?
                                range.insertNode(br);
                                range.collapse(false);
                                range.insertNode(textNode);
                                range.selectNodeContents(textNode);

                                selection.removeAllRanges();
                                selection.addRange(range);
                                return false;
                            }

                        }
                    };
                }
            // TODO ??????? contentEditable ??? ?????? ??????
            })
        }
    }
    }).directive('animationImage', function () {
    return{
        restrict: 'A',
        // NB: no isolated scope!!
        link: function (scope, element, attrs) {
            // observe changes in attribute - could also be scope.$watch
            attrs.$observe('img', function (value) {
                var img = new Image();
                img.src = value;
                img.onload = function(){
                    if(img.width > img.height){
                        element.css('animation-name', 'horizontal');
                    }else{
                        element.css('animation-name', 'vertical');
                    }
                }
            });
        }
    }}).directive('ngThumb', ['$window', function($window) {
        var helper = {
            support: !!($window.FileReader && $window.CanvasRenderingContext2D),
            isFile: function(item) {
                return angular.isObject(item) && item instanceof $window.File;
            },
            isImage: function(file) {
                var type =  '|' + file.type.slice(file.type.lastIndexOf('/') + 1) + '|';
                return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
            }
        };

        return {
            restrict: 'A',
            template: '<canvas/>',
            link: function(scope, element, attributes) {
                if (!helper.support) return;

                var params = scope.$eval(attributes.ngThumb);

                if (!helper.isFile(params.file)) return;
                if (!helper.isImage(params.file)) return;

                var canvas = element.find('canvas');
                var reader = new FileReader();

                reader.onload = onLoadFile;
                reader.readAsDataURL(params.file);

                function onLoadFile(event) {
                    var img = new Image();
                    img.onload = onLoadImage;
                    img.src = event.target.result;
                }

                function onLoadImage() {
                    var width = params.width || this.width / this.height * params.height;
                    var height = params.height || this.height / this.width * params.width;
                    canvas.attr({ width: width, height: height });
                    canvas[0].getContext('2d').drawImage(this, 0, 0, width, height);
                }
            }
        };
    }]).directive('ngThumbperson', ['$window', '$rootScope', function($window, $rootScope) {
        var helper = {
            support: !!($window.FileReader && $window.CanvasRenderingContext2D),
            isFile: function(item) {
                return angular.isObject(item) && item instanceof $window.File;
            },
            isImage: function(file) {
                var type =  '|' + file.type.slice(file.type.lastIndexOf('/') + 1) + '|';
                return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
            }
        };

        return {
            restrict: 'A',
            template: '<canvas/>',
            link: function(scope, element, attributes) {
                if (!helper.support) return;

                var params = scope.$eval(attributes.ngThumbperson);

                if (!helper.isFile(params.file)) return;
                if (!helper.isImage(params.file)) return;

                var reader = new FileReader();

                reader.onload = onLoadFile;
                reader.readAsDataURL(params.file);


                function onLoadFile(event) {
                    var img = new Image();
                    img.onload = onLoadImage;
                    img.src = event.target.result;
                }

                if(!$rootScope.count){
                    $rootScope.count = 0;
                }


                function onLoadImage() {
                    $rootScope.images.push(this);
                    $rootScope.count++;
                    console.log($rootScope.count);
                    console.log(angular.element(document.querySelectorAll('canvas')).length - 1);
                    if((angular.element(document.querySelectorAll('canvas')).length - 1) == $rootScope.count){
                        $rootScope.$emit("loadImageAll");
                    }
                }
            }
        };
    }]).directive('height__auto', function() {
            return {
                restrict: 'A',
                link: function (scope, element, attrs) {
                    var height = element.height();
                    if (height < 700) {
                        console.log("<<,");
                        angular.element('.button__height').css('display', 'none');
                    } else {
                        angular.element('.button__height').css('display', 'block');
                    }
                }
            }
    }).directive("contenteditable", function() {
    return {
        restrict: "A",
        link: function(scope, element, attrs) {

            function read() {
                element.html(element.text());
            }

            element.bind("blur keyup change", function() {
                scope.$apply(read);
            });
            element.bind("blur", function(){
                scope.editTextPost(scope.post.id, element.text());
            })
        }
    };
}).directive("editerPost", function($compile) {
    return {
        restrict: "A",
        scope: true,
        link: function(scope, element, attrs, parentCtrl) {
            element.bind("click", function(){
                if(document.querySelectorAll("[attache-post]")[0]){
                    document.querySelectorAll("[attache-post]")[0].innerHTML = '';
                    document.querySelectorAll("[attache-post]")[0].removeAttribute("attache-post");
                }
                if(document.querySelectorAll("[contenteditable]")[0])
                    document.querySelectorAll("[contenteditable]")[0].removeAttribute("contenteditable");
                if(document.querySelectorAll("[progress-wrapper]")[0]){
                    document.querySelectorAll("[progress-wrapper]")[0].innerHTML = '';
                    document.querySelectorAll("[progress-wrapper]")[0].removeAttribute("progress-wrapper");
                }
                if(document.querySelectorAll("[remove_img_post]")[0]){
                    var remove_img = document.querySelectorAll("[remove_img_post]");
                    for(var key in remove_img){
                        remove_img[key].innerHTML = "";
                        remove_img[key].removeAttribute("remove_img_post");
                    }
                }

                var text = element.parent().parent().find("p")[0];
                var attache = angular.element(element.parent().parent()[0].querySelector('.attacher'))[0];
                var progress = angular.element(element.parent().parent()[0].querySelector('.progress__wrapper'))[0];
                var images = element.parent().parent().find("img_post");

                attache.setAttribute('attache-post', '');
                text.setAttribute('contenteditable', '');
                progress.setAttribute('progress-wrapper', 'editUploaderPost');
                $compile(attache)(scope);
                $compile(text)(scope);
                $compile(progress)(scope);
                for(var key in images){
                    if(!isNaN(key)){
                        images[key].setAttribute('remove_img_post', '');
                        $compile(images[key])(scope);
                    }
                }
                scope.$parent.$parent.edits = true;
                scope.$apply();

            })
        }
    };
}).directive('attachePost', function(){
    return{
        restrict: "A",
        scope: true,
        template: "<label class='text-blue glyphicon glyphicon-paperclip add__files ng-isolate-scope pointer'>" +
        "<input type='checkbox' class='hidden'>" +
        "<div class='add__files__menu text-white'>" +
        "<ul class='margin-top_10 padding-left_0 margin-left_30'>" +
        "<li>" +
        "<label for='editUploaderPost'>" +
        "Фото" +
        "</label>" +
        "</li>" +
        "<li>Документ</li>" +
        "<li>Видеозапись</li>" +
        "<li>Аудиозапись</li>" +
        "</ul>" +
        "</div>" +
        "</label>"+
        "<input type='file' nv-file-select='' uploader='editUploaderPost' multiple id='editUploaderPost'  class='hidden' ng-disabled='editUploaderPost.isUploading' />",
        link: function(scope, element, attrs){
        }
    }
}).directive('removeImgPost', function(){
    return{
        restrict: "A",
        scope: true,
        template: "<span class='glyphicon glyphicon-remove close_image_post' ng-click='removeImgPost(id_img,id_post)'></span>",
        link: function(scope, element, attrs){
            scope.id_img = attrs.idImg;
            scope.id_post = scope.post.id;
        }
    }
}).directive('progressWrapper', function($compile){
    return{
        restrict: "A",
        scope: true,
        template: "<div class='progress' nv-file-over='' uploader='editUploaderPost' over-class='another-file-over-class' class='well my-drop-zone text-center'>" +
                  "<div class='progress-bar progress-bar-warning progress-bar-striped' role='progressbar' style='width:"+"[[ editUploaderPost.progress ]]"+"%'>[[ editUploaderPost.progress ]] %</div>" +
                  "</div>",
        link: function(scope, element, attrs){
        }
    }
}).directive('completeEdit', function($compile){
    return{
        restrict: "A",
        scope: true,
        link: function(scope, element, attrs){
            element.bind("click", function() {
                if(document.querySelectorAll("[attache-post]")[0]){
                    document.querySelectorAll("[attache-post]")[0].innerHTML = '';
                    document.querySelectorAll("[attache-post]")[0].removeAttribute("attache-post");
                }
                if(document.querySelectorAll("[contenteditable]")[0])
                    document.querySelectorAll("[contenteditable]")[0].removeAttribute("contenteditable");
                if(document.querySelectorAll("[progress-wrapper]")[0]){
                    document.querySelectorAll("[progress-wrapper]")[0].innerHTML = '';
                    document.querySelectorAll("[progress-wrapper]")[0].removeAttribute("progress-wrapper");
                }
                if(document.querySelectorAll("[remove_img_post]")[0]){
                    var remove_img = document.querySelectorAll("[remove_img_post]");
                    for(var key in remove_img){
                        if(!isNaN(key)) {
                            remove_img[key].innerHTML = "";
                            remove_img[key].removeAttribute("remove_img_post");
                        }
                    }
                }

                scope.$parent.$parent.edits = false;
                scope.$apply();
            })
        }
    }
});

app.filter('filterByTags', function () {
    return function(text, limit) {

        var changedString = String(text).replace(/<[^>]+>/gm, '');
        var length = changedString.length;

        return changedString.length > limit ? changedString.substr(0, limit - 1) : changedString;
    }
});

app.config(function($interpolateProvider) {
    $interpolateProvider.startSymbol('[[');
    $interpolateProvider.endSymbol(']]');
});

app.run(function($rootScope, $templateCache, $animate, $timeout) {
    $rootScope.$on('$viewContentLoaded', function() {
        $rootScope.hid = true;
    });

    $rootScope.$on('$routeChangeStart', function(event, next, current) {
        $templateCache.remove('/fleamarketposting_tmp');
        $templateCache.remove('/create_event_tmp');
    });

    $animate.enabled(false);

    $rootScope.myImage=false;
    $rootScope.myCroppedImage=false;

    $timeout(function(){
        angular.element(document.querySelector('#fileInput')).on('change', function(evt) {
            var file=evt.currentTarget.files[0];
            var reader = new FileReader();
            reader.onload = function (evt) {
                $rootScope.$apply(function($rootScope){
                    $rootScope.myImage=evt.target.result;
                });
            };
            reader.readAsDataURL(file);
        });
    }, 2000);

});
