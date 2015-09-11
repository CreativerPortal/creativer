angular.module('service.personal', [])
    .factory('personalService', [
        '$http',
        function ($http) {

            var url = '/app_dev.php/v1/';

            return {
                getUser: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_user',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
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
                addFavorits: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'add_favorits',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                removeFavorits: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'remove_favorits',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                updateAvatar: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'update_avatar',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                removePost: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'remove_post',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                saveField: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'save_field',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                savePost: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'save_post',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                saveComment: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'save_comment',
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
                        url: url + 'remove_comment',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                finishUpload: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'finish_upload',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        data: data
                    })
                },
                getAllCategories: function () {
                    return $http({
                        method: 'POST',
                        url: url + 'get_all_categories',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                },
                getAllCategoriesWithAlbumCategories: function () {
                    return $http({
                        method: 'POST',
                        url: url + 'get_all_categories_with_album_categories',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                },
                editTextPost: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'edit_text_post',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        data: data
                    })
                },
                removeImgPost: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'remove_img_post',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        data: data
                    })
                },
                sendFeedBack: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'feedback',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        data: data
                    })
                }
            };
        }]);
