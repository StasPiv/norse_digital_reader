var SourceView = Backbone.View.extend({
    template: _.template( $('#source').html() ),

    initialize: function() {
        this.$el.on('click', this.fetchFeeds.bind(this));
    },

    render: function() {
        this.$el.html(this.template(this.model.attributes));
        return this;
    },

    fetchFeeds: function() {
        feeds.url = '/api/feeds/' + this.model.id;
        feeds.fetch();
    }
});
