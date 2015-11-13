
angular
    .module('service.event', [])
    .factory('eventService', [
        '$http',
        function ($http) {

            var url = '/v1/';

            return {
                getCityAndSections: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_city_snd_sections',
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
                reviewedEvent: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'reviewed_event',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                searchEvents: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'search_events',
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
                        url: url + 'remove_comment_event',
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
                },
                getDatapicker: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'get_datapicker',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                eventAttend: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'event_attend',
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
                        url: url + 'save_event_comment',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                saveEditEvent: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'save_edit_event',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                },
                deleteEvent: function (data) {
                    return $http({
                        method: 'POST',
                        url: url + 'delete_event',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: data
                    });
                }
            };
        }]);
