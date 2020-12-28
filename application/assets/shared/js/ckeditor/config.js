/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.extra_allowed_content_rules = {
    '*'        : { attributes: ['class', 'data-*', 'id', 'lang', 'style', 'aria-*'], classes: ['*'], styles: ['border', 'border-*', 'color', 'font-size', 'font-variant', 'font-weight', 'text-align', 'text-align-last', 'text-transform']},
	div        : { attributes: ['class', 'data-*', 'id', 'lang', 'style'], classes: ['*'], styles: ['background', 'background-color', 'background-image', 'margin', 'margin-*', 'max-height', 'max-width', 'min-width', 'min-height', 'padding', 'padding-*', 'width'] },
	span       : { attributes: ['class', 'data-*', 'id', 'lang', 'style', 'aria-hidden'], classes: ['*'] },
	article    : { attributes: ['class', 'data-*', 'id', 'lang', 'style'], classes: ['*'] },
	nav        : { attributes: ['class', 'data-*', 'id', 'lang', 'style'], classes: ['*'] },
	section    : { attributes: ['class', 'data-*', 'id', 'lang', 'style'], classes: ['*'], styles: ['background', 'background-color', 'background-image', 'margin', 'margin-*', 'max-height', 'max-width', 'min-width', 'min-height', 'padding', 'padding-*', 'width'] },
	p          : { attributes: ['class', 'data-*', 'id', 'lang', 'style'], classes: ['*'], styles: ['font-size', 'font-weight', 'line-height', 'margin', 'margin-*', 'padding', 'padding-*'] },
	h1         : { attributes: ['class', 'data-*', 'id', 'lang', 'style'], classes: ['*'], styles: ['margin', 'margin-*'] },
	h2         : { attributes: ['class', 'data-*', 'id', 'lang', 'style'], classes: ['*'], styles: ['margin', 'margin-*'] },
	h3         : { attributes: ['class', 'data-*', 'id', 'lang', 'style'], classes: ['*'], styles: ['margin', 'margin-*'] },
	h4         : { attributes: ['class', 'data-*', 'id', 'lang', 'style'], classes: ['*'], styles: ['margin', 'margin-*'] },
	h5         : { attributes: ['class', 'data-*', 'id', 'lang', 'style'], classes: ['*'], styles: ['margin', 'margin-*'] },
	h6         : { attributes: ['class', 'data-*', 'id', 'lang', 'style'], classes: ['*'], styles: ['margin', 'margin-*'] },
	ul         : { attributes: ['class', 'data-*', 'id', 'lang', 'style'], classes: ['*'], styles: ['margin', 'margin-*', 'max-height', 'max-width', 'min-height', 'min-width'] },
	li         : { attributes: ['class', 'data-*', 'id', 'lang', 'style'], classes: ['*'] },
	dl         : { attributes: ['class', 'data-*', 'id', 'lang', 'style'], classes: ['*'] },
	dt         : { attributes: ['class', 'data-*', 'id', 'lang', 'style'], classes: ['*'] },
	dd         : { attributes: ['class', 'data-*', 'id', 'lang', 'style'], classes: ['*'] },
	blockquote : { attributes: ['class', 'data-*', 'id', 'lang', 'style'], classes: ['*'] },
	a          : { attributes: ['class', 'data-*', 'id', 'lang', 'style'], classes: ['*'] },
	cite       : { attributes: ['class', 'data-*', 'id', 'lang', 'style'], classes: ['*'] },
	strong     : { attributes: ['class', 'data-*', 'id', 'lang', 'style'], classes: ['*'] },
	img        : { attributes: ['class', 'data-*', 'id', 'lang', 'style'], classes: ['*'] },
	figure     : { attributes: ['class', 'data-*', 'id', 'lang', 'style'], classes: ['*'] },
	figcaption : { attributes: ['class', 'data-*', 'id', 'lang', 'style'], classes: ['*'] },
	hr         : { attributes: ['class', 'data-*', 'id',         'style'], classes: ['*'], styles: ['border', 'border-*', 'margin', 'margin-*', 'max-width', 'min-width', 'width'] },
	button     : { attributes: ['class', 'data-*', 'id', 'lang', 'style'], classes: ['*'] },
	iframe     : {
		attributes: ['class', 'data-*', 'id', 'lang', 'style', 'src', 'width', 'height'],
		classes: ['*']
	},
    video      : { attributes: ['autoplay', 'controls', 'class', 'data-*', 'height', 'id', 'lang', 'loop', 'muted', 'poster', 'preload', 'src', 'style', 'width'], classes: ['*'] },
    source     : { attributes: ['class', 'data-*', 'id', 'lang', 'media', 'sizes', 'src', 'srcset', 'type'], classes: ['*'] },
    track      : { attributes: ['class', 'data-*', 'default', 'id', 'kind', 'label', 'lang', 'src', 'srclang'], classes: ['*'] }
};

CKEDITOR.dtd.$removeEmpty.span = 0;

CKEDITOR.editorConfig = function(c) {
	// Define changes to default configuration here. For example:
	// c.language = 'fr';
	// c.uiColor = '#AADC6E';

    c.scayt_disableOptionsStorage = 'all';
    c.scayt_sLang = 'en_GB';

    c.extraPlugins = 'simplebox,textselection,uploadbutton';

    c.toolbar =
        [
            ['Source'],
            ['Format', 'Font', 'FontSize'],
            ['Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat'],
            ['TextColor','BGColor'],
            ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'],
            ['NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'Indent', 'Outdent'],
            ['UploadButton', 'Image', 'Table', 'Simplebox'],
            ['Link', 'Unlink', 'Anchor', 'SpecialChar'],
            ['Maximize', 'ShowBlocks']
        ];

    $.ajax({url:'/admin/media/ajax_get_fonts/', async: false}).success(
        function(results)
        {
            if (results.length > 0)
            {
                var font_names = '';
                for (var i = 0; i < results.length; i++)
                    font_names += results[i]+';';

                c.font_names = font_names;
            }
        }
    );

	c.extraAllowedContent = CKEDITOR.extra_allowed_content_rules;

    c.filebrowserImageBrowseUrl = 'dummy';

    c.scayt_autoStartup = true;

    c.contentsCss = [
        '/engine/shared/js/ckeditor/contents.css',
        '/engine/shared/js/ckeditor/plugins/widget/dev/assets/simplebox/contents.css'
    ];
};

CKEDITOR.on('dialogDefinition',
    function(e) {
        // Take the dialog name and its definition from the event data
        var dialogName       = e.data.name;
        var dialogDefinition = e.data.definition;
		var infoTab, browseServer;

        // Check if the definition is from the image dialog
        if (dialogName == 'image') {
            infoTab      = dialogDefinition.getContents('info');
            browseServer = infoTab.get('browse');
			// Remove the 'Link' Tab from the Image Dialog, as NOT REQUIRED
			dialogDefinition.removeContents('Link');

            browseServer.onClick = function(e) {
                // Launch the image browser
                ImageBrowser.run(
                    function(url) {
                        CKEDITOR.dialog.getCurrent().setValueOf('info', 'txtUrl', url);
                    }
                );
            }
        }

        // Check if the definition is from the link dialog
        if (dialogName == 'link') {
            infoTab  = dialogDefinition.getContents('info');
            var linkType = infoTab.get('linkType');
			browseServer = infoTab.get('browse');
			browseServer.hidden = false;

			// Load the LinksBrowser -=> will provide a list with Documents / Internal Pages to be set as LINKS
			browseServer.onClick = function(e) {
				// Launch the image browser
				LinksBrowser.run(
					function(url) {
						CKEDITOR.dialog.getCurrent().setValueOf('info', 'url', url);
					}
				);
			}
        }
    }
);

CKEDITOR.on('instanceReady', function(ev)
{
	var writer = ev.editor.dataProcessor.writer;
	writer.setRules('div',     { indent: true });
	writer.setRules('section', { indent: true });
	writer.setRules('article', { indent: true });
	writer.setRules('ul',      { indent: true });
	writer.setRules('ol',      { indent: true });
	writer.setRules('li',      { indent: true });
	writer.setRules('dl',      { indent: true });
	writer.setRules('dt',      { indent: true });
	writer.setRules('dd',      { indent: true });
});

