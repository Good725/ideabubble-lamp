function Layer(type, width, height, x, y)
{
    this.type             = type;
    this.fixed_width      = false;
    this.fixed_height     = false;
    this.width            = width;
    this.height           = height;
    this.x                = (typeof x == 'undefined') ? 0 : x;
    this.y                = (typeof y == 'undefined') ? 0 : y;
    this.src              = null;
    this.scale            = 1;
    this.text             = '';
    this.text_align       = 'center';
    this.text_styles      = {'font-family':'arial','color':'#000000','font-size':'35pt'};
    this.text_width       = 0;
    this.border_color     = '#000000';
    this.rounded          = true;
    this.background_color = '';
}

Layer.prototype.set_type             = function(value) { this.type              = value; };
Layer.prototype.set_fixed_width      = function(value) { this.fixed_width       = value; this.set_text_dimensions(); };
Layer.prototype.set_fixed_height     = function(value) { this.fixed_height      = value; this.set_text_dimensions(); };
Layer.prototype.set_width            = function(value) { this.width             = value; };
Layer.prototype.set_height           = function(value) { this.height            = value; };
Layer.prototype.set_x                = function(value) { this.x                 = value; };
Layer.prototype.set_y                = function(value) { this.y                 = value; };
Layer.prototype.set_src              = function(value) { this.src               = value; };
Layer.prototype.set_scale            = function(value) { this.scale             = value; };
Layer.prototype.set_text             = function(value) { this.text              = value; this.set_text_dimensions(); };
Layer.prototype.set_text_align       = function(value) { this.text_align        = value; };
Layer.prototype.set_text_styles      = function(value) { this.text_styles       = value; this.set_text_dimensions(); };
Layer.prototype.set_padding          = function(value) { this.padding           = value; this.set_text_dimensions(); };
Layer.prototype.set_line_height      = function(value) { this.line_height       = value; };
Layer.prototype.set_text_width       = function(value) { this.text_width        = value; };
Layer.prototype.set_border_color     = function(value) { this.border_color      = value; };
Layer.prototype.set_border_width     = function(value) { this.border_width      = value; };
Layer.prototype.set_rounded          = function(value) { this.rounded           = value; };
Layer.prototype.set_background_color = function(value) { this.background_color  = value; };

Layer.prototype.set_text_style       = function(label, value) { this.text_styles[label] = value; this.set_text_dimensions();};

Layer.prototype.get_type        = function() { return this.type;        };
Layer.prototype.get_width       = function() { return this.width;       };
Layer.prototype.get_height      = function() { return this.height;      };
Layer.prototype.get_x           = function() { return this.x;           };
Layer.prototype.get_y           = function() { return this.y;           };
Layer.prototype.get_scale       = function() { return this.scale;       };
Layer.prototype.get_text        = function() { return this.text;        };
Layer.prototype.get_text_align  = function() { return this.text_align;  };
Layer.prototype.get_text_styles = function() { return this.text_styles; };
Layer.prototype.get_padding     = function() { return this.padding;     };
Layer.prototype.get_line_height = function() { return this.line_height; };
Layer.prototype.get_text_width  = function() { return this.text_width;  };

Layer.prototype.set_text_dimensions = function()
{
    // Line breaks are unsupported. This is necessary to calculate the width and height
    // of lines, so they can be individually placed.
    var wrap_open = '<div class="accessible-hide" style="';
    for (var key in this.text_styles)
    {
        wrap_open += key+':'+this.text_styles[key]+';';
    }
    wrap_open += '">';
    var wrap_close = '</div>';

    var span  = $(wrap_open + this.text + wrap_close);
    var span2 = $(wrap_open + ((this.text).replace('\n','<br />')) + wrap_close);
    $('#sign_builder_wrapper').after(span).after(span2);

    var lines        = this.text.split(/\r\n|\r|\n/).length;
    this.text_width  = span2.width();
    this.line_height = span.height();
    this.padding     = this.line_height * 0.2;
    if ( ! this.fixed_width)
    {
        this.width  = 2 * this.padding + this.text_width;
    }
    if ( ! this.fixed_height)
    {
        this.height = 2 * this.padding + this.line_height * lines;
    }
    span.remove();
    span2.remove();
};
