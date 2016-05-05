var app = angular.module('app', ['ngRoute', 'ui.router', 'app.ctr.person', 'app.ctr.album', 'app.ctr.catalog', 'app.ctr.baraholka', 'app.ctr.messages', 'app.ctr.header', 'app.ctr.shop', 'app.ctr.album.create', 'app.ctr.people', 'app.ctr.event', 'monospaced.elastic', 'ngImgCrop','ui.tinymce','ngSanitize', 'ngTouch', 'rgkevin.datetimeRangePicker', 'ui.bootstrap', 'angular-momentjs'])
    .config(['$routeProvider', '$locationProvider', '$httpProvider', '$stateProvider', '$urlRouterProvider', '$momentProvider', function ($routeProvider, $locationProvider, $httpProvider, $stateProvider, $urlRouterProvider, $momentProvider) {

        $locationProvider.hashPrefix('!');

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
        $stateProvider.state('services_search', {
            url: '/services/search/:services_search_text',
            templateUrl: '/search_services_tmp',
            controller: 'catalogCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('services_search_page', {
            url: '/services/search/:services_search_text/:page',
            templateUrl: '/search_services_tmp',
            controller: 'catalogCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('people', {
            url: '/people/search/:people_search',
            templateUrl: '/people_tmp',
            controller: 'peopleCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('people_page', {
            url: '/people/search/:people_search/:page',
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
        $stateProvider.state('products_search_page', {
            url: '/products/search/:products_search_text/:page',
            templateUrl: '/search_products_tmp',
            controller: 'catalogCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('products_search', {
            url: '/products/search/:products_search_text',
            templateUrl: '/search_products_tmp',
            controller: 'catalogCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('products_search.state2', {
            url: '/:url_img/:key_img',
            templateUrl: '/show_search_product_photo_tmp',
            controller: 'catalogCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('products_search_page.state2', {
            url: '/:url_img/:key_img',
            templateUrl: '/show_search_product_photo_tmp',
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
            templateUrl: function (){
                var date = new Date();
                var url = "/fleamarketposting_tmp/" + date;
                return url;
            },
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
        $stateProvider.state('events_cat', {
            url: '/events/:id_cat',
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
        $stateProvider.state('chat_by_message', {
            url: '/chat/:id_user_chat/:id_message',
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
        $stateProvider.state('shop', {
            url: '/shop/:id_shop',
            templateUrl: '/shop_tmp',
            controller: 'shopCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('create_shop', {
            url: '/create_shop',
            templateUrl: '/create_shop_tmp',
            controller: 'shopCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('edit_shop', {
            url: '/edit_shop/:edit_id',
            templateUrl: '/edit_shop_tmp',
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
        $stateProvider.state('rates', {
            url: '/rates',
            templateUrl: '/rates',
            controller: 'personCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('rules', {
            url: '/rules',
            templateUrl: '/rules',
            controller: 'personCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('legal_information', {
            url: '/legal_information',
            templateUrl: '/legal_information',
            controller: 'personCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('about', {
            url: '/about',
            templateUrl: '/about',
            controller: 'personCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('help', {
            url: '/help',
            templateUrl: '/help',
            controller: 'personCtrl',
            reloadOnSearch: true,
        });
        $stateProvider.state('agreement', {
            url: '/agreement',
            templateUrl: '/agreement',
            controller: 'personCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('news', {
            url: '/news',
            templateUrl: '/news_tmp',
            controller: 'personCtrl',
            reloadOnSearch: true
        });
        $stateProvider.state('news.state2', {
            url: '/:key_post/:key_post_img',
            templateUrl: function ($stateParams){
                var url = "/show_post_photo_news_tmp";
                return url;
            },
            controller: 'personCtrl',
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
        $stateProvider.state('id.state3', {
            url: '/post/:id_post',
            templateUrl: function ($stateParams){
                var url = "/show_person_post_tmp";
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


        $momentProvider
            .asyncLoading(false)
            .scriptUrl('//cdnjs.cloudflare.com/ajax/libs/moment.js/2.5.1/moment.min.js');

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
