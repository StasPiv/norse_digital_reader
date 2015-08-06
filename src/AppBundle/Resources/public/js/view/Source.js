var SourceView = Backbone.View.extend({
    template: _.template( $('#source').html() ),

    initialize: function() {
        this.$el.on('click', this.fetchFeedsAndSetCurrent.bind(this));
    },

    render: function() {
        this.$el.html(this.template(this.model.attributes));
        return this;
    },

    fetchFeedsAndSetCurrent: function() {
        feeds.url = '/api/feeds/' + this.model.id;
        feeds.fetch();
        sourcesView.setCurrentModel(this.model);
    }
});
