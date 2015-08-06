var SourceViewAdd = Backbone.View.extend({
   el: '#source_add',

   events: {
      'click .cancel': 'hideForm',
      'click .add': 'addSource'
   },

   hideForm: function() {
      this.$el.hide();
   },

   addSource: function() {
      var type = this.$el.find('[name="type"]:checked').val();
      var source = this.$el.find('[name="source"]').val();

      var sourceModel = new Source();
      sourceModel.add(source, type);

      sources.fetch();
      this.$el.hide();
   }
});

var sourceAdd = new SourceViewAdd();
