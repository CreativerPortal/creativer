
angular
    .module('service.baraholka', [])
    .factory('baraholkaService', [
        '$http',
        function ($http) {

            var url = '/v1/';

            return {
                getDataBaraholka: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_data_baraholka',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },

                createPostBaraholka: function (data) {
                    return $http({
                        method: 'POST',
                        url: 'create_post_baraholka',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },

                getPostsByCategory: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_posts_by_category',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },

                getPostById: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_post_by_id',
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
                        url: url + 'save_post_comment',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },

                checkPostCategory: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'id_check_post_category',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },

                checkCategoryBaraholka: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'check_category_baraholka',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },

                editTitle: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'edit_title',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },

                editCity: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'edit_city',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },

                editFullDescription: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'edit_full_description',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },

                editDescription: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'edit_description',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },

                editPrice: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'edit_price',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },

                editAuction: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'edit_auction',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },

                removeImageBaraholka: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'remove_image_baraholka',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },

                mainImageBaraholka: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'main_image_baraholka',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },

                searchPostsBaraholkaByText: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'search_posts_baraholka_by_text',
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
                        url: url + 'remove_comment_baraholka',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },

                deletePostBaraholka: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'delete_post_baraholka',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                }

            };
        }]);
