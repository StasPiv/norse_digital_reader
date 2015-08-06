var SourcesView = Backbone.View.extend({
    el: '#sources',

    selectors: {
        content: '.content',
        add_source: '.add_source',
        remove_source: '.remove_source'

    },

    events: {
        'click .remove_source': 'removeCurrent',
        'click .add_source': 'showForm'
    },

    initialize: function() {
        sources.on('sync', this.render.bind(this));
        this.content = this.$el.find(this.selectors.content);
        this.add = this.$el.find(this.selectors.add_source);
        this.remove = this.$el.find(this.selectors.remove_source);
    },

    render: function() {
        this.content.empty();

        for(var i=0; i < sources.length; i++) {
            var sourceView = new SourceView();
            sourceView.model = sources.models[i];
            sourceView.render();
            this.content.append(sourceView.$el);
            if (i == 0) {
                sourceView.fetchFeedsAndSetCurrent();
            }
        }
        this.remove.hide();

        return this;
    },

    removeCurrent: function() {
        this.currentModel.remove();
        sources.fetch();
        feeds.fetch();
    },

    setCurrentModel: function(model) {
        this.currentModel = model;
        this.remove.show();
    },

    showForm: function() {
        sourceAdd.$el.show();
    }
});

var sourcesView = new SourcesView();

