(function($) {
    $.fn.alphanumeric = function(p) {
        p = $.extend({
            ichars: "!@#$%^&*()+=[]\\\';,/{}|\":<>?~`.-_№ ",
            nchars: "",
            allow: ""
        }, p);
        return this.each(function() {
            if (p.nocaps) p.nchars += "ABCDEFGHIJKLMNOPQRSTUVWXYZАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ";
            if (p.allcaps) p.nchars += "abcdefghijklmnopqrstuvwxyzабвгдеёжзийклмнопрстуфхцчшщъыьэюя";
            allow = p.allow;
            s = p.allow.split('');
            for (i = 0; i < s.length; i++)
                if (p.ichars.indexOf(s[i]) != -1) s[i] = "\\" + s[i];
            p.allow = s.join('|');
            var reg = new RegExp(p.allow, 'gi');
            var ch = p.ichars + p.nchars;
            ch = ch.replace(reg, '');

            var pattern = '';
            for (i = 0; i < p.ichars.length; i++) {
                if(false == allow.indexOf(p.ichars[i]) + 1) {
                    pattern += "\\" + p.ichars[i];
                }
            }
                
            for (i = 0; i < p.nchars.length; i++) {
                if(false == allow.indexOf(p.nchars[i]) + 1) {
                    pattern += p.nchars[i];
                }
            }
            
            $(this).keypress(function (e) {
                if (!e.charCode) k = String.fromCharCode(e.which);
                else k = String.fromCharCode(e.charCode);
                if (ch.indexOf(k) != -1
                    && !(e.ctrlKey && (
                        k == 'v'
                        || k == 'c'
                        || k == 'x'
                        || k == 'a'
                        || k == 'V'
                        || k == 'C'
                        || k == 'X'
                        || k == 'A'
                    ))) e.preventDefault();
            });
            $(this).bind('paste', function (e) {
                var element = this;
                setTimeout(function () {
                    var text = $(element).val();
                    text = text.replace(new RegExp('[' + pattern + ']', 'gi'), '');
                    $(element).val(text);
                }, 100);
            });
            $(this).bind('contextmenu', function() {
                return false
            });
        });
    };
    $.fn.numeric = function(p) {
        var az = "abcdefghijklmnopqrstuvwxyzабвгдеёжзийклмнопрстуфхцчшщъыьэюя";
        az += az.toUpperCase();
        p = $.extend({
            nchars: az
        }, p);
        return this.each(function() {
            $(this).alphanumeric(p);
        });
    };
    $.fn.alpha = function(p) {
        var nm = "1234567890";
        p = $.extend({
            nchars: nm
        }, p);
        return this.each(function() {
            $(this).alphanumeric(p);
        });
    };
})(jQuery);