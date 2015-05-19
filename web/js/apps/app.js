var app = angular.module('app', ['ngRoute', 'app.ctr.person', 'app.ctr.album', 'monospaced.elastic', 'ngAnimate'])
    .config(['$routeProvider', function ($routeProvider) {

        $routeProvider.when('/create_album', {
            templateUrl: '/create_album',
            controller: 'personCtrl',
            reloadOnSearch: true
        });
        $routeProvider.when('/', {
            templateUrl: '/person_tmp',
            templateUrl: function ($stateParams){
                var url = "/person_tmp/" + $stateParams.id;
                return url;
            },
            controller: 'personCtrl',
            reloadOnSearch: true
        });
        $routeProvider.when('/favorit', {
            templateUrl: '/favorit_tmp',
            controller: 'personCtrl',
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
            templateUrl: '/album_tmp',
            controller: 'albumCtrl',
            reloadOnSearch: true
        });
        $routeProvider.otherwise({
            redirectTo: '/'
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
               parent.focus();
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
}).directive('ngThumb', ['$window', function($window) {
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
}]).directive('height__auto', function() {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {
                var height = element.height();
                console.log("qqqqqqqqqqqq" + height);
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

