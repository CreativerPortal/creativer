
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
                }
            };
        }]);
