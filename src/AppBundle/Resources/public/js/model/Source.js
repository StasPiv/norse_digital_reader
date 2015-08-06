var Source = Backbone.Model.extend({
    urlRoot: '/api/source/',
    defaults: {
        id: '',
        source: '',
        content: ''
    },
    initialize: function() {
        var feeds = new FeedCollection();
        feeds.url = '/api/feeds/' + this.get('id');
        this.set('feeds', feeds);
    },
    fetchFeeds: function() {
        this.get('feeds').fetch();
    },
    add: function(source, type) {
        this.set('source', source) ;
        var that = this;
        this.callApi('add/', {source: source, type: type}, 'POST', function(data) {
            that.set('id', data.sourceId);
            sources.add(that);
        });
    },
    remove: function(sourceId) {
        if (this.id != '') {
            sourceId = this.id;
        }
        var that = this;
        this.callApi('remove/' + sourceId, null, 'DELETE', function(data) {
            if (data.result) {
                that.set('id', null);
            }
        });
    },
    update: function(sourceId) {
        if (this.id != '') {
            sourceId = this.id;
        }
        this.callApi('update/' + sourceId, null, 'PUT');
    },
    callApi: function(apiCall, params, method, callback) {
        $.ajax({
            url: this.urlRoot + apiCall,
            method: method,
            data: params,
            dataType: 'json',
            success: function(data) {
                if (typeof callback == 'function') {
                    callback(data);
                }
            }
        });
    }
});

var SourceCollection = Backbone.Collection.extend({
    model: Source,
    url: '/api/sources/0'
});

var sources = new SourceCollection;
sources.fetch();