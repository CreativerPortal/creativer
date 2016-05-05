
angular
    .module('service.album', [])
    .factory('albumService', [
        '$http',
        function ($http) {

            var url = '/v1/';

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
                getLikesByAlbumId: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_likes_by_album_id',
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
                },
                editTextImage: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'edit_text_image',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                editDescriptionAlbum: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'edit_description_album',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                editNameAlbum: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'edit_name_album',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                editCategoriesAlbum: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'edit_categories_album',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                deleteAlbum: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'delete_album',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                removeImage: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'remove_image',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                removeComment: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'remove_comment_album',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                mainImage: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'main_image',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                setViewed: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'set_viewed',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                }
            };
        }]);
