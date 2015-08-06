var Authorization = Backbone.View.extend({
    el: '#authorization',

    selectors: {
        email: '#email',
        password: '#password',
        loginWindow: '.login',
        logoutWindow: '.logout'
    },

    events: {
        'click #enter': 'auth',
        'click #logout': 'logout',
        'click #show_register': 'showRegister'
    },

    initialize: function() {
        this.email = this.$el.find(this.selectors.email);
        this.password = this.$el.find(this.selectors.password);
        this.loginWindow = this.$el.find(this.selectors.loginWindow);
        this.logoutWindow = this.$el.find(this.selectors.logoutWindow);

        this.showDependsOnLoginState();

        this.on('success', this.showDependsOnLoginState);
        this.on('fail', this.fail);

        this.on('logout', this.showDependsOnLoginState);
    },

    showDependsOnLoginState: function() {
        if (loginState) {
            this.loginWindow.hide();
            this.logoutWindow.show();
        } else {
            this.logoutWindow.hide();
            this.loginWindow.show();
        }
        sources.fetch();
        this.$el.show();
    },

    auth: function() {
        var email = this.email.val();
        var password = this.password.val();
        var user = new User();
        user.auth(email, password);
    },

    logout: function() {
        var user = new User();
        user.logout();
    },

    success: function() {
        this.$el.hide();
    },

    fail: function() {
        this.email.addClass('fail');
        this.password.addClass('fail');
    },

    showRegister: function() {
        this.$el.hide();
        registrationWindow.$el.show();
    }
});

var authorizationWindow = new Authorization();
