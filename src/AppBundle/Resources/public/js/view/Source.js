var SourceView = Backbone.View.extend({
    template: _.template( $('#source').html() ),

    render: function() {
        this.$el.html(this.template(this.model.attributes));
        return this;
    }
});
