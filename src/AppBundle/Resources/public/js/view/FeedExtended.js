var FeedViewExtended = Backbone.View.extend({
    el: '#feed_full',

    template: _.template( $('#feed_extended').html() ),

    initialize: function() {
        feeds.on('sync', this.empty.bind(this));
    },

    render: function() {
        this.$el.html(this.template(this.model.attributes));
        return this;
    },

    empty: function() {
        this.$el.empty();
    }
});
