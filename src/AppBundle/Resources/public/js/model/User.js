var User = Backbone.Model.extend({
    urlRoot: '/api/user/',
    defaults: {
        email: '',
        id: ''
    },
    auth: function(email, password) {
        this.callApi('auth/', {email: email, password: password}, 'POST');
    },
    register: function(email, password, repeatPassword) {
        this.callApi('register/', {email: email, password: password, repeat: repeatPassword}, 'POST');
    },
    logout: function() {
        this.callApi('logout/', null, 'POST');
    },
    callApi: function(apiCall, params, method) {
        $.ajax({
            url: this.urlRoot + apiCall,
            method: method,
            data: params,
            dataType: 'json'
        });
    }
});