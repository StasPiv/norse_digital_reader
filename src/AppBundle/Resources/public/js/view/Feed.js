var FeedView = Backbone.View.extend({
    template: _.template( $('#feed').html() ),

    initialize: function() {
        this.$el.on('click', this.showExtendedInfoAndColorize.bind(this));
    },

    render: function() {
        this.$el.html(this.template(this.model.attributes));
        return this;
    },

    showExtendedInfoAndColorize: function() {
        var feedViewExtended = new FeedViewExtended;
        feedViewExtended.model = this.model;
        feedViewExtended.render();

        feedsView.$el.find('div').removeClass('selected');
        this.$el.addClass('selected');
    }
});