(function() {

	tinymce.create('tinymce.plugins.RELATED_customMCEPluginName', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			ed.addButton('RELATED_MCECustomButton', {
				title : 'Tag Tip',
				image : url + '/../images/icon.gif',
				onclick : function() {
                    tinyMCE.execCommand('mceReplaceContent', false ,'[tagtip]{$selection}[/tagtip]');
				}
			});
		},


	});

	// Register plugin
	tinymce.PluginManager.add('RELATED_customMCEPlugin', tinymce.plugins.RELATED_customMCEPluginName);
})();