var SourceView = Backbone.View.extend({
    template: _.template( $('#source').html() ),

    initialize: function() {
        this.$el.on('click', this.fetchFeedsAndSetCurrentAndColorize.bind(this));
    },

    render: function() {
        this.$el.html(this.template(this.model.attributes));
        return this;
    },

    fetchFeedsAndSetCurrentAndColorize: function() {
        feeds.url = '/api/feeds/' + this.model.id;
        feeds.fetch();
        sourcesView.$el.find('div').removeClass('selected');

        this.$el.addClass('selected');
        sourcesView.setCurrentModel(this.model);
    }
});
