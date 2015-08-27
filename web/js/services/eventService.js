
angular
    .module('service.event', [])
    .factory('eventService', [
        '$http',
        function ($http) {

            var url = '/app_dev.php/v1/';

            return {
                getCity: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_city',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                getEventSections: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_event_sections',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                saveEventService: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'save_event',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                getEvents: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_events',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                getEvent: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_event',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                }
            };
        }]);
