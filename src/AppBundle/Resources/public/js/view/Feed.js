var FeedView = Backbone.View.extend({
    template: _.template( $('#feed').html() ),

    render: function() {
        this.$el.html(this.template(this.model.attributes));
        return this;
    }
});