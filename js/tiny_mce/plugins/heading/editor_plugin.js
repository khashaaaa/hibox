/**
 *
 * @author WSL.RU
 * @copyright Copyright (c) 2006-2009. All rights reserved.
 *
 */

(function() {

	tinymce.create('tinymce.plugins.heading', {
		init : function(ed, url) {
		// adding buttons
			for (var i =1; i<=4;i++) {
				ed.addButton('h'+i, { title : ed.getLang('advanced.h'+i,'h'+i)+' (Ctrl+'+i+')', image : url+'/img/h'+i+'.gif', cmd: 'mceHeading'+i }); 
			}

			ed.addCommand('mceHeading1', function() {
                ed.selection.setContent('<h1 class="editor">'+ed.selection.getContent()+'</h1>');
			});
            ed.addCommand('mceHeading2', function() {
                ed.selection.setContent('<h2 class="editor">'+ed.selection.getContent()+'</h2>');
            });
            ed.addCommand('mceHeading3', function() {
                ed.selection.setContent('<h3 class="editor">'+ed.selection.getContent()+'</h3>');
            });
            ed.addCommand('mceHeading4', function() {
                ed.selection.setContent('<h4 class="editor">'+ed.selection.getContent()+'</h4>');
            });

            ed.onNodeChange.add( function(ed, cm, n) {cm.setActive('h1', n.nodeName.toLowerCase() == 'h1');});
            ed.onNodeChange.add( function(ed, cm, n) {cm.setActive('h2', n.nodeName.toLowerCase() == 'h2');});
            ed.onNodeChange.add( function(ed, cm, n) {cm.setActive('h3', n.nodeName.toLowerCase() == 'h3');});
            ed.onNodeChange.add( function(ed, cm, n) {cm.setActive('h4', n.nodeName.toLowerCase() == 'h4');});

        },

		getInfo : function() {
			return {
				longname :  'Heading plugin',
				author :    'WSL.RU / Andrey G, ggoodd',
				authorurl : 'http://wsl.ru',
				infourl :   'mailto:ggoodd@gmail.com',
				version :   '1.3'
			};
		}
	});

	
	tinymce.PluginManager.add('heading', tinymce.plugins.heading);

})();

