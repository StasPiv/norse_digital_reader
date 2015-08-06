var User = Backbone.Model.extend({
    urlRoot: '/api/user/',
    defaults: {
        email: '',
        id: ''
    },
    auth: function(email, password) {
        this.callApi('auth/', {email: email, password: password}, 'POST', function(data) {
            authorizationWindow.trigger(data.result ? 'success' : 'fail');
        });
    },
    register: function(email, password, repeatPassword) {
        this.callApi('register/', {email: email, password: password, repeat: repeatPassword}, 'POST',
            function(data) {
                registrationWindow.trigger(data.result ? 'success' : 'fail');
            });
    },
    logout: function() {
        this.callApi('logout/', null, 'POST');
    },
    callApi: function(apiCall, params, method, callback) {
        $.ajax({
            url: this.urlRoot + apiCall,
            method: method,
            data: params,
            dataType: 'json',
            success: function(data) {
                if (typeof callback === 'function') {
                    callback(data);
                }
            }
        });
    }
});