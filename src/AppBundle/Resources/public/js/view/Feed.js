var FeedView = Backbone.View.extend({
    template: _.template( $('#feed').html() ),

    initialize: function() {
        this.$el.on('click', this.showExtendedInfo.bind(this));
    },

    render: function() {
        this.$el.html(this.template(this.model.attributes));
        return this;
    },

    showExtendedInfo: function() {
        var feedViewExtended = new FeedViewExtended;
        feedViewExtended.model = this.model;
        feedViewExtended.render();
    }
});