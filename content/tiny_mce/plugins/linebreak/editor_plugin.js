/**
 * $Id: editor_plugin.js,v 1.1 2010/02/10 17:56:54 lutz Exp $
 *
 * @author Lutz Ißler
 * @copyright Copyright ©2007-2008, Lutz Ißler, All rights reserved.
 */

(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('linebreak');

	tinymce.create('tinymce.plugins.LinebreakPlugin', {
		init : function(ed, url) {
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
			ed.addCommand('mceLinebreak', function() {
				h = '<br />';
				tinyMCE.activeEditor.execCommand('mceInsertContent', false, h);
			});

			// Register linebreak button
			ed.addButton('linebreak', {
				title : 'linebreak.desc',
				cmd : 'mceLinebreak',
				image : url + '/img/linebreak.gif'
			});

		},

		getInfo : function() {
			return {
				longname : 'Linebreak',
				author : 'Lutz Ißler',
				authorurl : 'http://www.lutzissler.net/',
				infourl : 'http://www.lutzissler.net/',
				version : '0.1'
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('linebreak', tinymce.plugins.LinebreakPlugin);
})();
