var Authorization = Backbone.View.extend({
    el: '#authorization',

    selectors: {
        email: '#email',
        password: '#password'
    },

    events: {
        'click #enter': 'auth'
    },

    initialize: function() {
        this.email = this.$el.find(this.selectors.email);
        this.password = this.$el.find(this.selectors.password);

        this.on('success', this.success);
        this.on('fail', this.fail);
    },

    auth: function() {
        var email = this.email.val();
        var password = this.password.val();
        var user = new User();
        user.auth(email, password);
    },

    success: function() {
        this.$el.hide();
    },

    fail: function() {
        this.email.addClass('fail');
        this.password.addClass('fail');
    }
});

var authorizationWindow = new Authorization();
