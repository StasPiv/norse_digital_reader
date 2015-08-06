var FeedsView = Backbone.View.extend({
    el: '#feeds',

    initialize: function() {
        feeds.on('sync', this.render.bind(this));
    },

    render: function() {
        this.$el.empty();

        for(var i=0; i < feeds.length; i++) {
            var feedView = new FeedView();
            feedView.model = feeds.models[i];
            feedView.render();
            this.$el.append(feedView.$el);
        }

        return this;
    }
});

var feedsView = new FeedsView();