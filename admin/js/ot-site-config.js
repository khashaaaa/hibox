var SearchProviders = new Backbone.Collection();
var SearchProvidersConfig = Backbone.View.extend({
    "el": $("#global-wrapper")[0],
    "events": {
        "click .ot-save-theme": "saveTheme",
        "click #save-default_item_provider": "saveDefaultProvider",
        "click #save-design_theme": "saveDesignTheme",
        "click .ot_show_deletion_dialog_modal": "confirmDelete",
        "click .ot_add_ip_button": "addIp",
        "click .ot_assign_current_ip_button": "addCurrentIp",
        "click #save-protect_key": "switchIp",
        "click #generate-password": "generatePassword",
        "click #chande-password": "changePassword",
        "click #compile-custom-scss": "compileCustomScss",
        "click #compile-scss": "compileScss",
        "change input.color-input": "customColorChanged",
        "click #delete-custom-scss": "deleteCustomScss",
    },
    initialize: function(){

    },
    saveTheme: function(ev){
        var that = this;
        this.$(ev.target).closest('.ot-save-theme').attr('disabled', 'disabled');

        $.each(Themes.models, function(key, theme){
            if(activeTheme != theme.get('name'))
                return true;
            var availableThemeTemplate = renderTemplate('themes/available-theme-button', {name: theme.get('name')});
            var element = that.$('.ot-save-image-button-wrapper[data-theme="'+theme.get('name')+'"]');
            element.html(availableThemeTemplate);
            element.closest('.thumbnail').removeClass('selected_item').find('h3').css('font-weight', 'normal');
        });

        activeTheme = that.$(ev.target).closest('.ot-save-image-button-wrapper').data('theme');
        $.post('?cmd=SiteConfiguration&do=save', {name: 'current_site_theme', value: activeTheme}, function(){
            that.$(ev.target).closest('.thumbnail').addClass('selected_item');
            that.$(ev.target).closest('.thumbnail').find('h3').css('font-weight', 'bold');
            var currentThemeTemplate = renderTemplate('themes/current-theme-button');
            that.$(ev.target).closest('.ot-save-image-button-wrapper').html(currentThemeTemplate);
        });

        return false;
    },
    saveDefaultProvider: function(ev){
        var button = $('#save-default_item_provider');
        $(button).addClass('disabled').find('.icon-ok').removeClass('icon-ok').addClass('icon-refresh');

        var defaultItemProvider = $('.DefaultItemProvider').val();
        $.post(
            '?cmd=SiteConfiguration&do=saveInstanceOptions',
            {'DefaultItemProvider': defaultItemProvider},
            function (data) {
                if (! data.error) {
                    showMessage(trans.get('Notify_success'));
                } else {
                    showError(data);
                }
                $(button).find('.icon-refresh').removeClass('icon-refresh').addClass('icon-ok');
                $(button).removeClass('disabled');
            }, 'json'
        );

        return false;
    },
    saveDesignTheme: function(ev){
        var button = $('#save-design_theme');
        $(button).addClass('disabled').find('.icon-ok').removeClass('icon-ok').addClass('icon-refresh');

        var theme = $('select[name="design_theme"]').val();
        $.post(
            '?cmd=SiteConfiguration&do=save',
            {'name': 'design_theme', 'pk': 1, 'value': theme},
            function (data) {
                if (! data.error) {
                    showMessage(trans.get('Notify_success'));
                } else {
                    showError(data);
                }
                $(button).find('.icon-refresh').removeClass('icon-refresh').addClass('icon-ok');
                $(button).removeClass('disabled');
            }, 'json'
        );

        return false;
    },
    confirmDelete: function(e) {
        e.preventDefault();
        var target;
        if ($(e.target).hasClass('icon-remove-sign')) {
            target = $(e.target).parent();
        } else {
            target = $(e.target);
        }
        var ip = $(target).attr('ip');
        var action = $(target).attr('action');
        var msg = _.template(trans.get('delete_warning'), {item: escapeData(ip)});

        modalDialog(trans.get('Confirm_needed'), msg, function(){
            var $button = $(target).button('loading');
            $.post(
                action,
                {
                    ip : ip
                },
                function (data) {
                    if (! data.error) {
                        showMessage(trans.get('Ip_is_deleted'));
                        location.reload();
                    } else {
                        showError(data.message);
                        $button.button('reset');
                    }
                }, 'json'
            );
        });
        return false;
    },
    addIp: function(e) {
        e.preventDefault();
        var $button = this.$(e.target).button('loading');
        $.post(
            '?cmd=SiteConfiguration&do=addIp',
            {
                ip : $("#add-ip-value").val()
            },
            function (data) {
                if (! data.error) {
                    showMessage(trans.get('Ip_added'));
                    location.reload();
                } else {
                    $button.button('reset');
                    showError(data.message);
                }
            }, 'json'
        );
        return false;
    },
    addCurrentIp: function(e) {
        e.preventDefault();
        var $button = this.$(e.target).button('loading');
        $.post(
            '?cmd=SiteConfiguration&do=addCurrentIp',
            {},
            function (data) {
                if (! data.error) {
                    showMessage(trans.get('Ip_added'));
                    location.reload();
                } else {
                    $button.button('reset');
                    showError(data.message);
                }
            }, 'json'
        );
        return false;
    },
    switchIp: function(e) {
        e.preventDefault();
        var val = $('#protect_key').val();

        if (val == 1) {
            url = '?cmd=SiteConfiguration&do=SwitchOnIp';
        } else {
            url = '?cmd=SiteConfiguration&do=SwitchOffIp';
        }

        $.post(
            url,
            {},
            function (data) {
                if (! data.error) {
                    showMessage(trans.get('Ok'));
                    location.reload();
                } else {
                    showError(data.message);
                }
            }, 'json'
        );
        return false;
    },
    generatePassword: function(e) {
        e.preventDefault();

        var arr_symbol = ['a', 'b', 'c', 'd', 'e', 'f',
            'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o',
            'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x',
            'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        var word = '';
        for (i = 1; i <= 8; i++) {
            word = word + arr_symbol[getRandomInt(0, (arr_symbol.length - 1))];
        }
        $('input[name="new_password"]').val(word);
    },
    changePassword: function(e) {
        e.preventDefault();
        if (! $('#old_password').val()) {
            showError(trans.get('Old_password_error'));
            return false;
        }
        if (! $('#new_password').val()) {
            showError(trans.get('New_password_error'));
            return false;
        }
        
        var msg = trans.get('Change_password_confirm') + '?';
        modalDialog(trans.get('Confirm_needed'), msg, function(){
            var params = {
                old_password : $('#old_password').val(),
                new_password : $('#new_password').val()
            };
            $.post(
                '?cmd=SiteConfiguration&do=changepassword',
                params,
                function (data) {
                    if (! data.error) {
                        showMessage(trans.get('Password_successfully_changed'));
                        $('#old_password').val('');
                        $('#new_password').val('');
                    } else {
                        showError(data.message);
                    }
                }, 'json'
            );
        });


    },
    compileCustomScss: function(e) {
        e.preventDefault();
        var target = this.$(e.target);
        var $button = target.button('loading');

        $.post(
            '?cmd=SiteConfiguration&do=compileCustomScss',
            {},
            function (data) {
                $button.button('reset');
                if (! data.error) {
                    showMessage(trans.get('Notify_success'));
                	$('#compile-custom-scss').removeClass('btn-primary');
                	$('#compile-custom-scss').removeClass('btn-warning');
                	$('#compile-custom-scss').addClass('btn-success');
                	$('#compile-custom-scss').html(trans.get('Saved'));
                	setTimeout(function(){
                		$('#compile-custom-scss').removeClass('btn-success');
                		$('#compile-custom-scss').addClass('btn-primary');
                		$('#compile-custom-scss').html(trans.get('Save'));
                	}, 5000);
                } else {
                    showError(data.message);
                }
            }, 'json'
        );
    },
    compileScss: function(e) {
        e.preventDefault();
        var target = this.$(e.target);
        var $button = target.button('loading');

        $.post(
            '?cmd=SiteConfiguration&do=compileScss',
            {},
            function (data) {
                $button.button('reset');
                if (! data.error) {
                    showMessage(trans.get('Notify_success'));
                } else {
                    showError(data.message);
                }
            }, 'json'
        );
    },
    customColorChanged: function(ev)
    {
    	$('#compile-custom-scss').removeClass('btn-primary');
    	$('#compile-custom-scss').addClass('btn-warning');
    	var value = $(ev.currentTarget).val();
		var name = $(ev.currentTarget).data('name');
        $.post(
                '?cmd=SiteConfiguration&do=save',
                {'name': name, 'pk': 1, 'value' :  value},
                function (data) {
                    if (! data.error) {
                    } else {
                        showError(data);
                    }
                }, 'json'
            );

    },
    deleteCustomScss: function(e) {
        modalDialog(trans.get('Confirmation'), trans.get('Delete_custom_css_confirm') + '?', function () {
            e.preventDefault();
            var target = this.$(e.target);
            var $button = target.button('loading');

            $.post(
                '?cmd=SiteConfiguration&do=deleteCustomCss',
                {},
                function (data) {
                    $button.button('reset');
                    if (! data.error) {
                        showMessage(trans.get('Notify_success'));
                    } else {
                        showError(data.message);
                    }
                }, 'json'
            );
        });
        return false;
    }
});


var Config = new Backbone.Collection();
var ConfigPage = Backbone.View.extend({
    "el": ".config-wrapper",
    "events": {
        "change input#delete_bg": "enableCompileCustomSCSS",
        "change input#existing_bg": "enableCompileCustomSCSS",
        "change input#uploaded_bg":  "enableCompileCustomSCSS"
    },
    toggleOldTemplateSettingCallback: function (value) {
        var $designTheme = $("#design_theme");
        if (value === "0") {
            $designTheme.removeClass("hidden");
        } else {
            $designTheme.addClass("hidden");
        }
    },
    enableCompileCustomSCSS: function() 
    {
    	$('#compile-custom-scss').removeClass('btn-primary');
    	$('#compile-custom-scss').addClass('btn-warning');
    },
    render: function()
    {
        return this;
    },
    initialize: function(){
    	var self = this;
        this.render();
        $('#custom_scss_background_repeat_container').on('click', 'button.btn-primary', function(){ self.enableCompileCustomSCSS(); });
        $('#custom_scss_background_position_container').on('click', 'button.btn-primary', function(){ self.enableCompileCustomSCSS(); });
        $('#custom_scss_background_attachment_container').on('click', 'button.btn-primary', function(){ self.enableCompileCustomSCSS(); });
    }    
});


$(function(){
    var C = new ConfigPage();
    var S = new SearchProvidersConfig();
});
