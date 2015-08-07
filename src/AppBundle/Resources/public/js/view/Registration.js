var Registration = Backbone.View.extend({
    el: '#registration',

    selectors: {
        email: '#register_email',
        password: '#register_password',
        repeatPassword: '#repeat_password',
        passwordWeight: '.password_weight'
    },

    events: {
        'click #register': 'register',
        'keyup #register_password': 'getPasswordWeight'
    },

    initialize: function() {
        this.email = this.$el.find(this.selectors.email);
        this.password = this.$el.find(this.selectors.password);
        this.repeatPassword = this.$el.find(this.selectors.repeatPassword);
        this.passwordWeight = this.$el.find(this.selectors.passwordWeight);

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
        loginState = true;
        authorizationWindow.trigger('success');
    },

    fail: function() {
        this.email.addClass('fail');
        this.password.addClass('fail');
        this.repeatPassword.addClass('fail');
    },

    getPasswordWeight: function(e) {
        var password = e.target.value;
        var passwordWeight = this.passwordWeight;
        $.get('/api/get_password_weight/' + password, function(weight) {
            passwordWeight.removeClass('good').removeClass('bad').removeClass('middle');
            var classWeight;
            switch (weight) {
                case 1:
                    classWeight = 'bad';
                    break;
                case 2:
                    classWeight = 'middle';
                    break;
                case 3:
                    classWeight = 'good';
                    break;
            }
            passwordWeight.addClass(classWeight);
        });
    }
});

var registrationWindow = new Registration();