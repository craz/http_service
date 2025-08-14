/**
 * Basic sample plugin inserting abbreviation elements into CKEditor editing area.
 *
 * Created out of the CKEditor Plugin SDK:
 * http://docs.ckeditor.com/#!/guide/plugin_sdk_sample_1
 */

// Register the plugin within the editor.
CKEDITOR.plugins.add( 'banner', {

	// Register the icons.
	icons: 'banner',

	// The plugin initialization logic goes inside this method.
	init: function( editor ) {

		// Define an editor command that opens our dialog.
		editor.addCommand( 'abbr',  {
			exec : function()
			{
				editor.insertHtml('<banner></banner>');
			}
		});

		// Create a toolbar button that executes the above command.
		editor.ui.addButton( 'Banner', {

			// The text part of the button (if available) and tooptip.
			label: 'Баннер',

			// The command to execute on click.
			command: 'abbr',

			// The button placement in the toolbar (toolbar group name).
			toolbar: 'insert'
		});
	},

	afterInit : function( editor )
    {  
      function createFakeElement(realElement) {
         var fakeElement = editor.createFakeParserElement(realElement, 'banner', 'hr', false),
            fakeStyle = '';
         fakeStyle = fakeElement.attributes.style = fakeStyle + 'width:100%;';
         fakeStyle = fakeElement.attributes.style = fakeStyle + 'height:20px;';
         fakeStyle = fakeElement.attributes.style = fakeStyle + 'background:#ccc;';
         return fakeElement;
      }

      var dataProcessor = editor.dataProcessor;
      var dataFilter = dataProcessor && dataProcessor.dataFilter;
      if (dataFilter) {
         dataFilter.addRules({
            elements: {
               'banner': function(element) { 
                   return createFakeElement(element); 
                 }
            }
         }, 10);
      }
    },

    requires : [ 'fakeobjects' ]
});

