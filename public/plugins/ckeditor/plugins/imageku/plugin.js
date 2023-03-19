CKEDITOR.plugins.add( 'imageku', {
    init: function( editor ) {
        editor.addCommand( 'insertImageku', {
            exec: function( editor ) {
                imageUpload();
            }
        });
        editor.ui.addButton( 'Image Upload', {
            label: 'Insert Image',
            command: 'insertImageku',
            toolbar: 'insert',
			icon: this.path + 'icons/imageku.png'
        });
    }
});