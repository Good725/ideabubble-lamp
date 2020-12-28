(function( $ ) {
    $.widget( "custom.combobox", {
        _create: function() {
            this.wrapper = $( "<span>" )
                .addClass( "custom-combobox" )
                .insertAfter( this.element );
            this.element.hide();
            this.element.addClass('has-custom-combobox');
            this._createAutocomplete();
            this._createShowAllButton();
            this.element.on('change', function()
            {
                if (this.selectedIndex >= 0 && this.options.length > this.selectedIndex) {
                    $(this).find('\+ .custom-combobox .custom-combobox-input').val(this[this.selectedIndex].innerHTML);
                }
            });

            // Update labels that pointed to the select list to point to the autocomplete input
            if (this.element[0] && this.element[0].id) {
                $('label[for="'+this.element[0].id+'"]').attr('for', this.element[0].id + '-input');
            }
        },
        _createAutocomplete: function()
        {
            // If this is in a .form-select, get the ID of the .form-select. Give it an ID, if it does not have one.
            // This ID is needed, so that the dropdown can be positioned relative to it.
            var wrapper_id = '';
            var $fselect   = this.element.parents('.form-select');

            if ($fselect.length) {
                if (!$fselect.attr('id') && this.element.attr('id')) {
                    $fselect.attr('id', this.element.attr('id')+'-combobox-wrapper')
                }
                wrapper_id = $fselect.attr('id');

                if ($fselect.find('select').hasClass('form-input')) {
                    this.wrapper.addClass('form-input');
                }
            }

            var original_select = this.element;
            var selected = this.element.children( ":selected" );
            var value = selected.val() ? selected.text() : "";
            var wrapper = this.wrapper;
            this.input = $( "<input>" )
                .appendTo( this.wrapper )
                .val( value )
                .attr( "title", "" )
                .attr('placeholder', this.element.attr('data-placeholder'))
                .attr('data-combobox-prepend', this.element.attr('data-combobox-prepend'))
                .addClass( "custom-combobox-input" )
                .autocomplete({
                    appendTo: wrapper_id ? '#'+wrapper_id : null,
                    minLength: 0,
                    source: $.proxy( this, "_source" ),
                    select: function() {
                        $(this).trigger('change');
                        $(original_select).trigger('change');
                    },
                    change: function() {
                        $(this).trigger('change');

                        if (!$(original_select).hasClass('ib-combobox-new')) {
                            $(original_select).trigger('change');
                        }
                    },
                    open: function(event, ui) {
                        /* Bold portions of results that match searched text */
                        var ac_data = $(this).data('ui-autocomplete');

                        if (ac_data.term.trim()) {
                            // Encode entities in search term (& -> &amp; etc.)
                            var term = $('<span>').text(ac_data.term.trim()).html();

                            // Regex to find the search term, but only find instances outside of HTML elements
                            var re = new RegExp('('+term+')(?!([^<]+)?>)', 'gi');

                            // Replace the instances of the search term with a bolded version of itself
                            ac_data.menu.element.find('li').each(function() {
                                this.innerHTML = this.innerHTML.replace(re, function(str) {
                                    return '<b class="ui-autocomplete-highlight">'+str+'</b>';
                                });
                            });
                        }


                        /* Prepend HTML */
                        var prepend_selector = $(event.target).attr('data-combobox-prepend');

                        if (prepend_selector) {
                            wrapper.find('~ .ui-autocomplete').prepend('<li class="ui-autocomplete-prepend">'+$(prepend_selector).html()+'</li>');
                        }
                    }
                })
                .keyup(function() {
                    if (this.value == '') {
                        $(original_select).val('').trigger('change');
                    }
                })
                .attr( 'id', this.element.attr( 'id' )+'-input' );

            if ($(this.element).hasClass('ib-combobox-new')) {
                this.input.attr('name', this.element.attr('name').replace('_id', '')+'_new');
            }

			if (this.element.hasClass('validate[required]')) {
				this.element.removeClass('validate[required]');
				this.input.addClass('validate[required]');
			}

            // Clear text button
            this.wrapper.append('<button type="button" class="btn-link custom-combobox-clear"><span class="fa fa-remove-circle icon-remove-circle"></span></button>');

            this.wrapper.find('.custom-combobox-clear').on('click', function() {
                $(this).parent().find('.custom-combobox-input').val('');
                $(this).parent().prev('select').val('').trigger('change');
            });

			// If the select list has a popover, ensure the combobox has the same popover
			if (this.element.hasClass('popinit'))
			{
				this.input
					.addClass('popinit')
					.attr('data-trigger', this.element.data('trigger'))
					.attr('data-content', this.element.data('content'))
					.attr('data-original-title', this.element.data('original-title'));
			}
			else
			{
				this.input.tooltip({tooltipClass: "ui-state-highlight"});
			}

            this._on( this.input, {
                autocompleteselect: function( event, ui ) {
                    ui.item.option.selected = true;
                    this._trigger( "select", event, {
                        item: ui.item.option
                    });
					this.input.trigger('change');
                },
                autocompletechange: "_removeIfInvalid"
            });

            this.input.on('focus', function() {
                $(this).trigger('keydown');
            })
        },
        _createShowAllButton: function() {
            var input = this.input,
                wasOpen = false;
            $( "<a>" )
                .attr( "tabIndex", -1 )
                // .attr( "title", "Show all" )
                .tooltip()
                .appendTo( this.wrapper )
                .button({
                    text: false
                })
                .removeClass( "ui-corner-all" )
				.html('<span class="icon-caret-down"></span>')
                .addClass( "custom-combobox-toggle" )
                .mousedown(function() {
                    wasOpen = input.autocomplete( "widget" ).is( ":visible" );
                })
                .click(function() {
                    input.focus();
// Close if already visible
                    if ( wasOpen ) {
                        return;
                    }
// Pass empty string as value to search for, displaying all results
                    input.autocomplete( "search", "" );
                });
        },
        _source: function( request, response ) {
            var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
            response( this.element.children( "option:not(:disabled)" ).map(function() {
                var text = $( this ).text();
                if ( this.value && ( !request.term || matcher.test(text) ) )
                    return {
                        label: text,
                        value: text,
                        option: this
                    };
            }) );
        },
        _removeIfInvalid: function( event, ui ) {
// Selected an item, nothing to do
            if ( ui.item ) {
                return;
            }
// Search for a match (case-insensitive)
            var value = this.input.val(),
                valueLowerCase = value.toLowerCase(),
                valid = false;
            this.element.children( "option:not(:disabled)" ).each(function() {
                if ( $( this ).text().toLowerCase() === valueLowerCase ) {
                    this.selected = valid = true;
                    return false;
                }
            });
// Found a match, nothing to do
            if ( valid ) {

                // Use to clear the selection
                this._trigger("change", event, {
                    item: null
                });

                return;
            }
// Remove invalid value (unless ib-combobox-new class is used)
            if (!$(this.element).hasClass('ib-combobox-new')) {
                this.input.val( "" ).attr( "title", value + " didn't match any item" );
            }

			if (typeof $.tooltip != 'undefined') {
				this.input.tooltip('open');
			}

            this.element.val( "" );
            this._delay(function() {
                this.input.tooltip( "close" ).attr( "title", "" );
            }, 2500 );
            this.input.autocomplete( "instance" ).term = "";

            // Use to clear Selection
            this._trigger("change", event, {
                item: null
            });
        },
        _destroy: function() {
            this.wrapper.remove();
            this.element.show();
        }
    });
})( jQuery );
$(document).ready(function()
{
    $('.ib-combobox').combobox();
});
