
angular
    .module('service.personal', [])
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

                finishUpload: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'finish_upload',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        data: data
                    })
                }

            };
        }]);
