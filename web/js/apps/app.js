var app = angular.module('app', ['ngRoute', 'app.ctr.person', 'app.ctr.album', 'app.ctr.catalog', 'app.ctr.baraholka', 'app.ctr.messages', 'monospaced.elastic', 'ngAnimate'])
    .config(['$routeProvider', '$httpProvider', function ($routeProvider, $httpProvider) {
        $routeProvider.when('/create_album', {
            templateUrl: '/create_album',
            controller: 'personCtrl',
            reloadOnSearch: true
        });
        $routeProvider.when('/', {
            templateUrl: '/main_tmp',
            controller: 'catalogCtrl',
            reloadOnSearch: true
        });
        $routeProvider.when('/feedback', {
            templateUrl: '/feedback_tmp',
            controller: 'baraholkaCtrl',
            reloadOnSearch: true
        });
        $routeProvider.when('/products/:id_products?/:page?', {
            templateUrl: '/products_tmp',
            controller: 'catalogCtrl',
            reloadOnSearch: true
        });
        $routeProvider.when('/services/:id_services?/:page?', {
            templateUrl: '/services_tmp',
            controller: 'catalogCtrl',
            reloadOnSearch: true
        });
        $routeProvider.when('/favorit', {
            templateUrl: '/favorit_tmp',
            controller: 'personCtrl',
            reloadOnSearch: true
        });
        $routeProvider.when('/baraholka', {
            templateUrl: '/baraholka_tmp',
            controller: 'baraholkaCtrl',
            reloadOnSearch: true
        });
        $routeProvider.when('/viewforum/:id_category/:page?', {
            templateUrl: '/viewforum_tmp',
            controller: 'baraholkaCtrl',
            reloadOnSearch: true
        });
        $routeProvider.when('/viewtopic/:id_post', {
            templateUrl: '/viewtopic_tmp',
            controller: 'baraholkaCtrl',
            reloadOnSearch: true
        });
        $routeProvider.when('/fleamarketposting', {
            templateUrl: '/fleamarketposting_tmp',
            controller: 'baraholkaCtrl',
            reloadOnSearch: true
        });
        $routeProvider.when('/messages', {
            templateUrl: '/messages_tmp',
            controller: 'messagesCtrl',
            reloadOnSearch: true
        });
        $routeProvider.when('/chat/:id_user_chat', {
            templateUrl: '/chat_tmp',
            controller: 'messagesCtrl',
            reloadOnSearch: true
        });
        $routeProvider.when('/:id', {
            templateUrl: function ($stateParams){
                var url = "/person_tmp/" + $stateParams.id;
                return url;
            },
            controller: 'personCtrl',
            reloadOnSearch: true
        });
        $routeProvider.when('/album/:id_album/:url_img?/:key_img?', {
            templateUrl: function ($stateParams){
                var url = "/album_tmp/" + $stateParams.id_album;
                return url;
            },
            controller: 'albumCtrl',
            reloadOnSearch: true
        });
        $routeProvider.otherwise({
            redirectTo: '/'
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


app.directive('editPain', function () {
    return{
        scope: {
            obj: '='
        },
        link: function(scope, element, attrs){
            element.on("click", function(el){
               var parent = el.target.parentNode.querySelector('.text_info');
               parent.removeAttribute('disabled');
               parent.focus();        chat.init();
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


                //var canvas = element.find('canvas');
                //$rootScope.canvas.push(canvas);



                function onLoadImage() {
                    $rootScope.images.push(this);
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
    });


app.config(function($interpolateProvider) {
    $interpolateProvider.startSymbol('[[');
    $interpolateProvider.endSymbol(']]');
});

app.run(function($rootScope, $templateCache, $animate) {
    $rootScope.$on('$viewContentLoaded', function() {
        $rootScope.hid = true;
    });

    $rootScope.$on('$routeChangeStart', function(event, next, current) {
        $templateCache.remove('/fleamarketposting_tmp');
    });

    $animate.enabled(false);

});
