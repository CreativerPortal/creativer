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
}).directive('editPain', function () {
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
            if(scope.post){
                function read() {
                    var html = element.html();
                    html = html.replace(/\<(?!img src="img\/blank.gif"|br).*?\>/g, "");
                    scope.post.text = html;
                    scope.editTextPost(scope.post.id, html);
                }
                element.bind("blur change", function() {
                    scope.$apply(read);
                });
                element.bind("blur", function(){
                    // var html = element.html();
                    // html = html.replace(/\<(?!img|br).*?\>/g, "");
                    // scope.post.text = html;
                })
            }
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
                var contenteditable = element.parent().parent().find("[contenteditable]")[0];
                if(contenteditable)
                    contenteditable.removeAttribute("contenteditable");
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
                if(document.querySelectorAll("[remove_video_post]")[0]){
                    var remove_video = document.querySelectorAll("[remove_video_post]");
                    for(var key in remove_video){
                        remove_video[key].innerHTML = "";
                        remove_video[key].removeAttribute("remove_video_post");
                    }
                }
                if(document.querySelectorAll("[remove_document_post]")[0]){
                    var remove_document = document.querySelectorAll("[remove_document_post]");
                    for(var key in remove_document){
                        remove_document[key].innerHTML = "";
                        remove_document[key].removeAttribute("remove_document_post");
                    }
                }

                var text = element.parent().parent().find("p")[0];
                var attache = angular.element(element.parent().parent()[0].querySelector('.attacher'))[0];
                var progress = angular.element(element.parent().parent()[0].querySelector('.progress__wrapper'))[0];
                var images = element.parent().parent().find("img_post");
                var videos = element.parent().parent().find("video_post");
                var documents = element.parent().parent().find("document_post");
                var videoPostAdd = element.parent().parent().find("video_post_add")[0];


                attache.setAttribute('attache-post', '');
                text.setAttribute('contenteditable', '');
                progress.setAttribute('progress-wrapper', 'editUploaderPost');
                videoPostAdd.setAttribute('video_post_add', '');
                $compile(attache)(scope);
                $compile(text)(scope);
                $compile(progress)(scope);
                $compile(videoPostAdd)(scope);

                for(var key in images){
                    if(!isNaN(key)){
                        images[key].setAttribute('remove_img_post', '');
                        $compile(images[key])(scope);
                    }
                }
                for(var key in videos){
                    if(!isNaN(key)){
                        videos[key].setAttribute('remove_video_post', '');
                        $compile(videos[key])(scope);
                    }
                }
                for(var key in documents){
                    if(!isNaN(key)){
                        documents[key].setAttribute('remove_document_post', '');
                        $compile(documents[key])(scope);
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
        "<ul class='margin-top_20 padding-left_0 margin-left_30'>" +
        "<li>" +
        "<label for='editUploaderPost'>" +
        "Изображение" +
        "</label>" +
        "</li>" +
        "<li ng-click='addVideo(post)'><label>Видеозапись</label></li>" +
        "<li><label>Документ</label></li>" +
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
}).directive('removeVideoPost', function(){
    return{
        restrict: "A",
        scope: true,
        template: "<span class='glyphicon glyphicon-remove close_image_post' ng-click='removeVideoPost(id_video,id_post)'></span>",
        link: function(scope, element, attrs){
            scope.id_video = attrs.idVideo;
            scope.id_post = scope.post.id;
        }
    }
}).directive('videoPostAdd', function(){
    return{
        restrict: "A",
        scope: true,
        template: "<span class='text-blue text-bold' ng-show='post.videos_add.length'>Добавить видео</span>" +
        "<div class='row col-sm-12 padding-right_0 margin-top_5 margin-bottom_5' ng-repeat='(k,v) in post.videos_add track by $index'>" +
        "<input type='text' class='text_video' ng-model='post.videos_add[k]' placeholder='Cсылка на Youtube, Rutube, Vimeo или др.'>" +
        "</div>",
        link: function(scope, element, attrs){
            scope.id_document = attrs.idDocument;
            scope.id_post = scope.post.id;
        }
    }
}).directive('removeDocumentPost', function(){
    return{
        restrict: "A",
        scope: true,
        template: "<span class='glyphicon glyphicon-remove right text-gray pointer' ng-click='removeDocumentPost(id_document,id_post)'></span>",
        link: function(scope, element, attrs){
            scope.id_document = attrs.idDocument;
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
            element.bind("click", function(event) {
                if(document.querySelectorAll("[attache-post]")[0]){
                    document.querySelectorAll("[attache-post]")[0].innerHTML = '';
                    document.querySelectorAll("[attache-post]")[0].removeAttribute("attache-post");
                }

                var contenteditable = element.parent().parent().find("[contenteditable]")[0];
                if(contenteditable)
                    contenteditable.removeAttribute("contenteditable");
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

                if(document.querySelectorAll("[remove_video_post]")[0]){
                    var remove_video = document.querySelectorAll("[remove_video_post]");
                    for(var key in remove_video){
                        if(!isNaN(key)) {
                            remove_video[key].innerHTML = "";
                            remove_video[key].removeAttribute("remove_video_post");
                        }
                    }
                }

                if(document.querySelectorAll("[remove_document_post]")[0]){
                    var remove_document = document.querySelectorAll("[remove_document_post]");
                    for(var key in remove_document){
                        if(!isNaN(key)) {
                            remove_document[key].innerHTML = "";
                            remove_document[key].removeAttribute("remove_document_post");
                        }
                    }
                }

                if(document.querySelectorAll("[video_post_add]")[0]){
                    var video_post_add_remove = document.querySelectorAll("[video_post_add]")[0];
                    video_post_add_remove.removeAttribute("video_post_add");
                    video_post_add_remove.innerHTML = "";
                }

                scope.$parent.$parent.edits = false;
                scope.$apply();
            })
        }
    }
}).directive('parseDescription', function () {
    return {
        restrict: 'A',
        require: 'ngModel',
        replace: true,
        scope: {
            props: '=parseDescription',
            ngModel: '=ngModel'
        },
        link: function compile(scope, element, attrs, controller) {
            scope.$watch('ngModel', function (value) {
                if(value){
                    var html = value.replace(/<[^>]+>|&nbsp;|&laquo;|&amp;|&raquo;|&ndash;/g,'').slice(0,60)+" ...";
                    element.html(html);
                }
            });
        }
    };
}).directive('shopImages', function () {
    return {
        link: function compile(scope, element, attrs, controller) {
            $(".gridder").gridderExpander({
                scroll: true,
                scrollOffset: 60,
                scrollTo: "panel", // panel or listitem
                animationSpeed: 400,
                animationEasing: "easeInOutExpo",
                showNav: true,
                nextText: "<i class=\"glyphicon glyphicon-chevron-right\"></i>",
                prevText: "<i class=\"glyphicon glyphicon-chevron-left\"></i>",
                closeText: "<i class=\"glyphicon glyphicon-remove\"></i>",
                onStart: function(){
                    console.log("Gridder Inititialized");
                },
                onContent: function(){
                    $("#hasSelectedItem").removeClass('hidden_shop', 1000, "easeOutSine");
                },
                onClosed: function(){
                    setTimeout(function(){
                        $("#hasSelectedItem").addClass('hidden_shop', 1000, "easeOutSine");
                    })
                }
            });
        }
    };
}).directive('shopMap', function () {
    return {
        restrict: 'A',
        require: 'ngModel',
        replace: true,
        scope: {
            ngModel: '=ngModel'
        },
        link: function compile(scope, element, attrs, controller) {
            scope.$watch('ngModel', function (value) {
                geocoder = new google.maps.Geocoder();
                var mapOptions = {
                    center: new google.maps.LatLng(53.884107, 27.719879),
                    zoom: 18,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                var map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);

                if(value){
                    var address = "Минск " + value[0].address;
                }else{
                    var address = "Минск";
                }

                geocoder.geocode({'address': address}, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        map.setCenter(results[0].geometry.location);
                        new google.maps.Marker({map: map, position: results[0].geometry.location});
                    } else {
                        console.log("Адрес не найден: " + status);
                    }
                });
            })
        }
    };
}).directive('disabledLink', function() {
    return {
        restrict: 'A',
        scope: {
            enabled: '=disabledLink'
        },
        link: function(scope, element, attrs) {
            element.bind('click', function(event) {
                if(!scope.enabled) {
                    event.preventDefault();
                }
            });
        }
    };
}).directive('keypressEvents', function ($document, $rootScope) {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {
                $document.bind('keydown', function (e) {
                    if(e.keyCode == 39){
                        var tr = document.querySelectorAll("[next_photo]")[0]
                        if(tr){
                            var next = tr.getAttribute("next");
                            window.location = next;
                        }
                    }else if(e.keyCode == 37){
                        var tr = document.querySelectorAll("[prev_photo]")[0]
                        if(tr){
                            var prev = tr.getAttribute("prev");
                            window.location = prev;
                        }
                    }
                    e.stopPropagation();
                    $rootScope.$broadcast('keypress', e, String.fromCharCode(e));
                });
            }
        }
}).directive('links', function ($document, $rootScope) {
        return {
            restrict: 'A',
            require: 'ngModel',
            replace: true,
            scope: {
                ngModel: '=ngModel'
            },
            link: function (scope, element, attrs) {
                //scope.$watch('ngModel', function (value) {
                //
                //    value = new String(value);
                //
                //
                //    console.log(value.match(/(?:(?:http|https):\/\/)?(?:www.)?facebook.com\/(?:(?:\w)*#!\/)?(?:pages\/)?(?:[?\w\-]*\/)?(?:profile.php\?id=(?=\d.*))?([\w\-]*)?/ig));
                //
                //    window.vkAsyncInit = function () {
                //        VK.init({
                //            apiId: 5176512
                //        });
                //        VK.Api.call('users.get', {user_ids: 'slaqyui'}, function (r) {
                //            if (r.response) {
                //                alert('Привет, ' + r.response[0].first_name);
                //            }
                //        });
                //    };
                //
                //    setTimeout(function () {
                //        var el = document.createElement("script");
                //        el.type = "text/javascript";
                //        el.src = "//vk.com/js/api/openapi.js";
                //        el.async = true;
                //        document.getElementById("vk_api_transport").appendChild(el);
                //    }, 0);
                //
                //})
            }
        }
});