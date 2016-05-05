
angular
    .module('service.shop', [])
    .factory('shopService', [
        '$http',
        function ($http) {

            var url = '/v1/';

            return {

                getShopById: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_shop_by_id',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },

                getCtegoriesShops: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_ctegories_shops',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },

                getShopsByCategory: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_shops_by_category',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },

                removeShop: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'remove_shop',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },

                removeImageShop: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'remove_image_shop',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },

                mainImageShop: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'main_image_shop',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },

                editName: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'edit_name_shop',
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
                        url: url + 'edit_description_shop',
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
                        url: url + 'edit_full_description_shop',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },

                editSite: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'edit_site_shop',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },

                editAddress: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'edit_address_shop',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },

                editTelephone: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'edit_telephone_shop',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },

                editEmail: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'edit_email_shop',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },

                editWorkingTime: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'edit_working_time_shop',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },

                editCategories: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'edit_categories',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                }
            };
        }]);
