/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	//config.language = 'fr';
	// config.uiColor = '#AADC6E';

	config.filebrowserBrowseUrl = '/tools/ckeditor/kcfinder-2.54/browse.php?type=files';
	config.filebrowserImageBrowseUrl = '/tools/ckeditor/kcfinder-2.54/browse.php?type=images';
	config.filebrowserFlashBrowseUrl = '/tools/ckeditor/kcfinder-2.54/browse.php?type=flash';
	config.filebrowserUploadUrl = '/tools/ckeditor/kcfinder-2.54/upload.php?type=files';
	config.filebrowserImageUploadUrl = '/tools/ckeditor/kcfinder-2.54/upload.php?type=images';
	config.filebrowserFlashUploadUrl = '/tools/ckeditor/kcfinder-2.54/upload.php?type=flash';

	config.extraPlugins = 'colorbutton,gallery';

	config.allowedContent = true;
	config.FillEmptyBlocks = false;
	config.enterMode = CKEDITOR.ENTER_BR;
};
