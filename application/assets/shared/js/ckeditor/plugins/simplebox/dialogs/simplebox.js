CKEDITOR.dialog.add('simplebox', function(editor) {
    return {
        title: 'Edit Block',
        minWidth: 200,
        minHeight: 100,
        contents: [
            {
                id: 'info',
                elements: [
                    {
                        id: 'columns',
                        type: 'select',
                        label: 'Number of columns',
                        items: [[1, ''], [2, 2], [3, 3], [4, 4], [5, 5], [6, 6]],
                        width: '75px',
                        setup: function(widget) {
                            this.setValue(widget.data.columns);
                        },
                        commit: function( widget ) {
                            widget.setData('columns', this.getValue());

                            var no_of_columns = this.getValue() || 1;
                            var column        = widget.element.$.getElementsByClassName('simplebox-column')[0];
                            var clone;

                            var columns_html  = '';
                            for (var i = 0; i < no_of_columns; i++) {
                                clone = column.cloneNode(true);
                                clone.className = clone.className.concat(' simplebox-column-').concat(i+1);

                                columns_html += clone.outerHTML;
                            }
                            widget.element.$.getElementsByClassName('simplebox-columns')[0].innerHTML = columns_html;
                        }
                    },
                    {
                        id: 'vertical_align',
                        type: 'select',
                        label: 'Vertical alignment',
                        items: [['middle', ''], ['top', 'top'], ['bottom', 'bottom']],
                        width: '100px',
                        setup: function(widget) {
                            this.setValue(widget.data.columns);
                        },
                        commit: function( widget ) {
                            if (this.getValue()) {
                                widget.element.$.className += ' simplebox-align-'+this.getValue();
                            }
                        }
                    },
                    {
                        id: 'classes',
                        type: 'text',
                        label: 'Classes',
                        width: '100px',
                        setup: function(widget) {
                            this.setValue(widget.data.box_classes);
                        },
                        commit: function(widget) {
                            widget.setData('box_classes', this.getValue());

                            widget.element.$.className += ' ' + this.getValue();
                        }
                    },
                    {
                        id: 'max_width',
                        type: 'text',
                        label: 'Maximum width',
                        width: '100px',
                        setup: function(widget) {
                            this.setValue(widget.data.max_width);
                        },
                        commit: function(widget) {
                            var value = isNaN(this.getValue()) ? this.getValue() : parseInt(this.getValue());
                            var $element = $(widget.element.$);

                            $element.find('.simplebox-columns').css('max-width', value);
                        }
                    },
                    {
                        id: 'is_diagonal',
                        type: 'checkbox',
                        label: 'Diagonal content',
                        setup: function(widget) {
                            this.setValue(widget.data.is_diagonal);
                        },
                        commit: function(widget) {
                            if (this.getValue()) {
                                widget.element.$.className += ' diagonal';
                            }
                        }
                    },
                    {
                        id: 'has_background',
                        type: 'checkbox',
                        label: 'Give this block a background image',
                        setup: function(widget) {
                            this.setValue(widget.data.has_background);
                        },
                        commit: function(widget) {
                            if (!this.getValue()) {
                                $(widget.element.$).find('.simplebox-background_image').remove();
                            }
                        }
                    },
                    {
                        id: 'has_shadows',
                        type: 'checkbox',
                        label: 'Add shadows to the columns',
                        setup: function(widget) {
                            this.setValue(widget.data.has_background);
                        },
                        commit: function(widget) {
                            if (this.getValue()) {
                                widget.element.$.className += ' simplebox-shadowed';
                            }
                        }
                    }
                ]
            }
        ]
    };
});
