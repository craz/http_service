/**
 * The abbr dialog definition.
 *
 * Created out of the CKEditor Plugin SDK:
 * http://docs.ckeditor.com/#!/guide/plugin_sdk_sample_1
 */

// Our dialog definition.
CKEDITOR.dialog.add( 'abbrDialog', function( editor ) {
	return {

		// Basic properties of the dialog window: title, minimum size.
		title: 'Свойства кнопки',
		minWidth: 400,
		minHeight: 200,

		// Dialog window contents definition.
		contents: [
			{
				// Definition of the Basic Settings dialog tab (page).
				id: 'tab-basic',
				label: 'Основные свойства',

				// The tab contents.
				elements: [
					{
						type: 'text',
						id: 'title',
						label: 'Текст',

						// Validation checking whether the field is not empty.
						validate: CKEDITOR.dialog.validate.notEmpty( "Введите текст" )
					},
					{
						type: 'text',
						id: 'url',
						label: 'Ссылка',

						// Validation checking whether the field is not empty.
						validate: CKEDITOR.dialog.validate.notEmpty( "Введите ссылку" )
					},
				]
			},
		],

		// This method is invoked once a user clicks the OK button, confirming the dialog.
		onOk: function() {

			// The context of this function is the dialog object itself.
			// http://docs.ckeditor.com/#!/api/CKEDITOR.dialog
			var dialog = this;

			var url = dialog.getValueOf( 'tab-basic', 'url' );
			var title = dialog.getValueOf( 'tab-basic', 'title' );

			// Creates a new <abbr> element.
			var abbr = editor.document.createElement('div');
			abbr.setAttribute( 'class', 'link-wrapper' );

			var lnk = editor.document.createElement('a');
			lnk.setAttribute('href', url);
			lnk.setText(title);

			abbr.append(lnk);
			
			editor.insertElement( abbr );
		}
	};
});