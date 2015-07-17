
angular
    .module('service.album', [])
    .factory('albumService', [
        '$http',
        function ($http) {

            var url = '/app_dev.php/v1/';

            return {
                getUserByAlbumId: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_user_by_album_id',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                getAlbumById: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_album_by_id',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                getAlbumComments: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_album_comments',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                deleteImage: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'delete_image',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                saveImageComment: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'save_image_comments',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                like: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'like',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                imagePreviews: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'image_previews',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                }
            };
        }]);
