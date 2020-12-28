CKEDITOR.dialog.add('column_editor', function(editor) {
    return {
        title: 'Edit Column',
        minWidth: 300,
        minHeight: 100,
        contents: [
            {
                id: 'info',
                elements: [
                    {
                        id    : 'background_color',
                        type  : 'text',
                        label : 'Background colour',
                        width : '100px',
                        onShow: function() {
                            this.setValue($selected_simplebox_column[0].style.backgroundColor);
                        }
                    },
                    {
                        id    : 'light_text',
                        type  : 'checkbox',
                        label : 'Use light text for this background',
                        width : '100px',
                        onShow: function() {
                            this.setValue($selected_simplebox_column.hasClass('simplebox-column-light_text'));
                        }
                    },
                    {
                        id    : 'width',
                        type  : 'text',
                        label : 'Desktop width',
                        width : '100px',
                        onShow: function() {
                            this.setValue($selected_simplebox_column[0].style.width.replace(/[^0-9.]/g, ''));
                        }
                    },
                    {
                        id    : 'unit',
                        type  : 'select',
                        label : 'Unit',
                        items : [['none', ''], ['%', '%'], ['px', 'px']],
                        width : '75px',
                        onShow: function() {
                            this.setValue($selected_simplebox_column[0].style.width.replace(/[0-9.]/g, ''));
                        }
                    },
                    {
                        id    : 'max_width',
                        type  : 'text',
                        label : 'Maximum width',
                        width : '100px',
                        onShow: function() {
                            this.setValue($selected_simplebox_column[0].style.maxWidth.replace(/[^0-9.]/g, ''));
                        }
                    },
                    {
                        id    : 'max_width_unit',
                        type  : 'select',
                        label : 'Unit',
                        items : [['none', ''], ['%', '%'], ['px', 'px']],
                        width : '75px',
                        onShow: function() {
                            this.setValue($selected_simplebox_column[0].style.maxWidth.replace(/[0-9.]/g, ''));
                        }
                    }/*,
                    {
                        id    : 'mobile_width',
                        type  : 'text',
                        label : 'Mobile width',
                        width : '100px'
                    },
                    {
                        id    : 'mobile_unit',
                        type  : 'select',
                        label : 'Unit',
                        items : [['none', ''], ['%', '%'], ['px', 'px']],
                        width : '75px'
                    }*/
                ]
            }
        ],
        onShow: function(data) {
            this.getContentElement('info', 'light_text').setValue($selected_simplebox_column.hasClass('simplebox-column-light_text'));
        },
        onOk: function(data) {
            var background_color = this.getContentElement('info', 'background_color').getValue();
            var light_text       = this.getContentElement('info', 'light_text').getValue();
            var width            = this.getContentElement('info', 'width').getValue();
            var unit             = this.getContentElement('info', 'unit').getValue();
            var max_width        = this.getContentElement('info', 'max_width').getValue();
            var max_width_unit   = this.getContentElement('info', 'max_width_unit').getValue();

            // var mobile_width = this.getContentElement('info', 'mobile_width').getValue();
            // var mobile_unit  = this.getContentElement('info', 'mobile_unit' ).getValue();

            // If a width has not already been set add an equal width distribution among the existing columns in the block
            if (width) {
                if (!$selected_simplebox_column.is('[style*="width"]')) {
                    var $columns = $selected_simplebox_column.parents('.simplebox-columns').find('.simplebox-column');
                    $columns.css('width', (100 / $columns.length)+'%');
                }
            }

            $selected_simplebox_column.css('width', width + unit);
            $selected_simplebox_column.css('maxWidth', max_width + max_width_unit);

            if (background_color) {
                $selected_simplebox_column.addClass('simplebox-column-custom_background').css('background-color', background_color);
            }
            if (light_text) {
                $selected_simplebox_column.addClass('simplebox-column-light_text');
            }
        }
    };
});
