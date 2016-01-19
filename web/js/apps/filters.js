app.filter('filterByTags', function () {
    return function(text, limit) {

        var changedString = String(text).replace(/<[^>]+>/gm, '');
        var length = changedString.length;

        return changedString.length > limit ? changedString.substr(0, limit - 1) : changedString;
    }
}).filter('trustAsResourceUrl', ['$sce', function($sce) {
    return function(val) {
        if(val.indexOf('watch?v=') + 1) {
            return $sce.trustAsResourceUrl(val.replace("watch?v=", "embed/")+"?modestbranding=1&showinfo=0&color=white&theme=light");
        }else if(val.indexOf('video') + 1) {
            return $sce.trustAsResourceUrl(val.replace("video", "play/embed"));
        }else if(val.indexOf('view') + 1){
            return $sce.trustAsResourceUrl(val.replace("view", "embed"));
        }else if(val.indexOf('vimeo.com') + 1){
            return $sce.trustAsResourceUrl(val.replace("vimeo.com", "player.vimeo.com/video"));
        }
    };
}]).filter('bytes', function() {
    return function(bytes, precision) {
        if (isNaN(parseFloat(bytes)) || !isFinite(bytes)) return '-';
        if (typeof precision === 'undefined') precision = 1;
        var units = ['bytes', 'kB', 'MB', 'GB', 'TB', 'PB'];
        var number = Math.floor(Math.log(bytes) / Math.log(1024));
        return (bytes / Math.pow(1024, Math.floor(number))).toFixed(precision) +  ' ' + units[number];
    };
}).filter('imagey', function() {
    var IMG_URL_REGEX = /(href=['"]?)?https?:\/\/(?:[0-9a-z\-]+\.)+[a-z]{2,6}\/(?:[^'"]+)\.(?:jpe?g|gif|png)/g

    function proxify(href) {
        var prefix, suffix, encodedHref

        if (href && href.substring(0, 5) == 'http:') {
            prefix = "https://images2-focus-opensocial.googleusercontent.com/gadgets/proxy?url="
            suffix = "&container=focus&gadget=a&no_expand=1&resize_h=0&rewriteMime=image%2F*"
            encodedHref = encodeURIComponent(href)
            return prefix + encodedHref + suffix
        } else {
            return href
        }
    }

    return function(input) {
        if (!input) { return input }

        // If we have an href, skip the link, else swap it out for an image tag
        return input.replace(IMG_URL_REGEX, function(match, href) {
            return (href) ? match : "<img width='300' src=\"" + proxify(match) + "\">"
        })
    }
}).filter('highlight', function($sce) {
    return function(text, phrase) {
        if (phrase) text = text.replace(new RegExp('('+phrase+')', 'gi'),
            '<span class="highlighted">$1</span>')

        return $sce.trustAsHtml(text)
    }
})