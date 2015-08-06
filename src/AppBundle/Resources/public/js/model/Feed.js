var Feed = Backbone.Model.extend({
    defaults: {
        id: '',
        title: '',
        content: ''
    }
});

var FeedCollection = Backbone.Collection.extend({
    model: Feed
});

var feeds = new FeedCollection();