/**
 * Copyright (c) 2014-2016, CKSource - Frederico Knabben. All rights reserved.
 * Licensed under the terms of the MIT License (see LICENSE.md).
 *
 * Based on Simple CKEditor Widget (Part 2).
 * http://docs.ckeditor.com/#!/guide/widget_sdk_tutorial_2
 */

// Register the plugin within the editor.
CKEDITOR.plugins.add('simplebox', {
    // This plugin requires the Widgets System defined in the 'widget' plugin.
    requires: 'widget',

    // Register the icon used for the toolbar button. It must be the same
    // as the name of the widget.
    icons: 'simplebox',

    // The plugin initialization logic goes inside this method.
    init: function(editor) {
        var toolbox = '<div class="simplebox-content-toolbar">' +
            '<button type="button">' +
            '<img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" width="12" height="12" />' +
            '</button>' +
            '</div>';

        for (var i in CKEDITOR.instances) {
            (function(i){
                CKEDITOR.instances[i].on('contentDom', function() {
                    this.document.on('click', function(ev)
                    {
                        var target = ev.data.$.target;

                        // Add toolboxes to the editable columns
                        $(target).parents('body').find('.simplebox-content').each(function()
                        {
                            if ($(this).find('.simplebox-content-toolbar').length == 0) {
                                $(this).append(toolbox);
                            }
                        });

                        // If a toolbox was clicked on...
                        if ($(target).parents('.simplebox-content-toolbar').length) {
                            // Take note of the column and open the dialogue box
                            $selected_simplebox_column = $(target).parents('.simplebox-column');
                            editor.execCommand('column_editor');
                        }
                    });
                });

                CKEDITOR.instances[i].on('blur', function(data) {
                    console.log(data);
                    $(editor.document).find('.simplebox-content-toolbar').remove();
                });
            })(i);
        }


        // Register the editing dialogs
        CKEDITOR.dialog.add('simplebox',     this.path + 'dialogs/simplebox.js');
        CKEDITOR.dialog.add('column_editor', this.path + 'dialogs/column_editor.js');

        editor.addCommand('column_editor', new CKEDITOR.dialogCommand('column_editor'));


        // Register the simplebox widget.
        editor.widgets.add('simplebox', {
            // Allow all HTML elements, classes, and styles that this widget requires.
            // Read more about the Advanced Content Filter here:
            // * http://docs.ckeditor.com/#!/guide/dev_advanced_content_filter
            // * http://docs.ckeditor.com/#!/guide/plugin_sdk_integration_with_acf

            // Minimum HTML which is required by this widget to work.
            requiredContent: 'div(simplebox)',

            // Define two nested editable areas.
            editables: {
                title: {
                    // Define CSS selector used for finding the element inside widget element.
                    selector: '.simplebox-title'
                    // Define content allowed in this nested editable. Its content will be
                    // filtered accordingly and the toolbar will be adjusted when this editable
                    // is focused.
                },
                content: {
                    selector: '.simplebox-content'
                },
                column2: {
                    selector: '.simplebox-column-2 .simplebox-content'
                },
                column3: {
                    selector: '.simplebox-column-3 .simplebox-content'
                },
                column4: {
                    selector: '.simplebox-column-4 .simplebox-content'
                },
                column5: {
                    selector: '.simplebox-column-5 .simplebox-content'
                },
                column6: {
                    selector: '.simplebox-column-6 .simplebox-content'
                },
                backgroundImage: {
                    selector: '.simplebox-background_image'
                }
            },



            // Define the template of a new Simple Box widget.
            // The template will be used when creating new instances of the Simple Box widget.
            template:
                '<div class="simplebox">' +
                    '<div class="simplebox-title">Above text</div>' +
                    '<div class="simplebox-background_image">Insert background image</div>' +
                    '<div class="simplebox-columns">' +
                        '<div class="simplebox-column">' +
                            '<div class="simplebox-content">' + toolbox +
                                '<p>Content...</p>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>',

            ib_toolbar: '<div><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" width="16" height="16" /></div>',

            // Define the label for a widget toolbar button which will be automatically
            // created by the Widgets System. This button will insert a new widget instance
            // created from the template defined above, or will edit selected widget
            // (see second part of this tutorial to learn about editing widgets).
            //
            // Note: In order to be able to translate your widget you should use the
            // editor.lang.simplebox.* property. A string was used directly here to simplify this tutorial.
            button: 'Add columns',

            // Set the widget dialog window name. This enables the automatic widget-dialog binding.
            // This dialog window will be opened when creating a new widget or editing an existing one.
            dialog: 'simplebox',

            // Check the elements that need to be converted to widgets.
            //
            // Note: The "element" argument is an instance of http://docs.ckeditor.com/#!/api/CKEDITOR.htmlParser.element
            // so it is not a real DOM element yet. This is caused by the fact that upcasting is performed
            // during data processing which is done on DOM represented by JavaScript objects.
            upcast: function( element ) {
                // Return "true" (that element needs to converted to a Simple Box widget)
                // for all <div> elements with a "simplebox" class.
                return element.name == 'div' && element.hasClass( 'simplebox' );
            },

            // When a widget is being initialized, we need to read the data ("align" and "width")
            // from DOM and set it by using the widget.setData() method.
            // More code which needs to be executed when DOM is available may go here.
            init: function() {
                var width = this.element.getStyle('width');
                if (width)
                    this.setData('width', width);

                if (this.element.hasClass('align-left'))
                    this.setData('align', 'left');
                if (this.element.hasClass('align-right'))
                    this.setData('align', 'right');
                if (this.element.hasClass('align-center'))
                    this.setData('align', 'center');

                var max_width = this.element.getStyle('max_width');
                if (max_width) {
                    this.setData('max_width', max_width);
                }

            },

            // Listen on the widget#data event which is fired every time the widget data changes
            // and updates the widget's view.
            // Data may be changed by using the widget.setData() method, which we use in the
            // Simple Box dialog window.
            data: function() {
                // Check whether "width" widget data is set and remove or set "width" CSS style.
                // The style is set on widget main element (div.simplebox).
                if ( this.data.width == '' )
                    this.element.removeStyle('width');
                else
                    this.element.setStyle('width', this.data.width);

                var max_width = isNaN(this.data.max_width) ? this.data.max_width : parseInt(this.data.max_width);
                $(this.element.$).find('.simplebox-columns').css('max-width', max_width);


                // Brutally remove all align classes and set a new one if "align" widget data is set.
                this.element.removeClass('align-left');
                this.element.removeClass('align-right');
                this.element.removeClass('align-center');
                if (this.data.align)
                    this.element.addClass('align-' + this.data.align );
            }
        });
    }
});