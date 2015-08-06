var Registration = Backbone.View.extend({
    el: '#registration',

    selectors: {
        email: '#email',
        password: '#password',
        repeatPassword: '#repeat_password'
    },

    events: {
        'click #register': 'register'
    },

    initialize: function() {
        this.email = this.$el.find(this.selectors.email);
        this.password = this.$el.find(this.selectors.password);
        this.repeatPassword = this.$el.find(this.selectors.repeatPassword);

        this.on('success', this.success);
        this.on('fail', this.fail);
    },

    register: function() {
        var email = this.email.val();
        var password = this.password.val();
        var repeatPassword = this.repeatPassword.val();
        var user = new User();
        user.register(email, password, repeatPassword);
    },

    success: function() {
        this.$el.hide();
    },

    fail: function() {
        this.email.addClass('fail');
        this.password.addClass('fail');
        this.repeatPassword.addClass('fail');
    }
});

var registrationWindow = new Registration();