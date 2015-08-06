var SourcesView = Backbone.View.extend({
    el: '#sources',

    initialize: function() {
        sources.on('sync', this.render.bind(this));
    },

    render: function() {
        this.$el.empty();

        for(var i=0; i < sources.length; i++) {
            var sourceView = new SourceView();
            sourceView.model = sources.models[i];
            sourceView.render();
            this.$el.append(sourceView.$el);
        }

        return this;
    }
});

new SourcesView();

