var FeedsView = Backbone.View.extend({
    el: '#feeds',

    initialize: function() {
        feeds.on('sync', this.render.bind(this));
        authorizationWindow.on('logout', this.empty.bind(this));
    },

    render: function() {
        this.empty();

        if (feeds.length > 0) {
            this.$el.append('<h2>Feeds</h2>');
        }

        for(var i=0; i < feeds.length; i++) {
            var feedView = new FeedView();
            feedView.model = feeds.models[i];
            feedView.render();
            this.$el.append(feedView.$el);
        }

        return this;
    },

    empty: function() {
        this.$el.empty();
    }
});

var feedsView = new FeedsView();