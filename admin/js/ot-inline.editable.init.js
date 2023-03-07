$.fn.editable.defaults.mode = 'inline';
$(document).ready( function () {

    var XEditableFieldView = Backbone.View.extend({
        prepareXEditablePossibleValues: function(){
            var values = [];
            try{
                for(var i in this.model.get('valuesList')){
                    var key = this.model.get('name')+'_value:'+this.model.get('valuesList')[i];
                    var text = '';
                    if(trans.get(key) == key)
                        text = trans.get(this.model.get('name')+'_value:*');
                    else
                        text = trans.get(key);

                    values.push({
                        value: this.model.get('valuesList')[i],
                        text: text
                    });
                }

                var parameters = this.model.attributes.parameters;
                if (parameters.valuesList) {
                    values = [];
                    for (var i in parameters.valuesList) {
                        values.push({
                            value: i,
                            text: parameters.valuesList[i]
                        });
                    }
                }
            }
            catch(e){
            }
            return values;
        },
        prepareXEditableInitParameters: function(){
            var model = this.model;
            var parameters = this.model.attributes;
            parameters.source = this.prepareXEditablePossibleValues();

            if (parameters.value === '' && parameters.type === 'select') {
                parameters.value = 0;
            }
            if (parameters.value === 'checked') {
                parameters.value = 1;
            }

            parameters.success = function(response, value)
            {
                if (typeof response === 'string' && response != '') {
                    response = JSON.parse(response);
                }
                if (response.reload == 1) {
                    var timeout = response.timeout || 0;
                    setTimeout('window.location.reload();', timeout);
                    if (response.message) {
                        showMessage(response.message);
                    }
                }
                if (! response.error) {
                    var dependsElement = $('.ot_show_depends[data-depends="'+model.get('name')+'"]');
                    if(dependsElement.length){
                        var showIf = dependsElement.attr('data-show-cond');
                        if(showIf == value)
                            dependsElement.show();
                        else
                            dependsElement.hide();
                    }
                } else {
                    showError(response.message);
                    var dependsElement = $('.ot_show_depends[data-depends="'+model.get('name')+'"]');
                    dependsElement.show();
                    if (response.newValue) {
                        var editable = $(this).data('editable');
                        $(editable.input.$input).val(response.newValue);
                    }
                    return false;
                }

                if (parameters.parameters.callbackSuccess) {
                    var callbackSuccess = new Function(parameters.parameters.callbackSuccess[0], parameters.parameters.callbackSuccess[1]);
                    callbackSuccess(response, value, $(this).data('editable'));
                }
            }
            if(parameters.processSubmit == true){
                parameters.url = function(scope, params){
                    if(window[parameters.submitHandler])
                        window[parameters.submitHandler](scope,params);
                };
            }

            return parameters;
        },
        render: function()
        {
            var self = this;

            var preparedParameters = this.prepareXEditableInitParameters();

            var randId = preparedParameters.name + '_' + Date.now();
            var wrapperSelector = preparedParameters.name + '-wrapper';
            var renderedTemplate = renderInlineEditableElement(this.model.get('type'), preparedParameters);
            var wrappedHtml = $('<div/>').attr('id', randId).addClass(wrapperSelector).html(renderedTemplate);
            $('[data-field="' + self.model.get('name') + '"]:first').replaceWith(wrappedHtml);

            if (preparedParameters.parameters.langVersions) {
                var langVersionsHtml = renderInlineEditableElement('languages', preparedParameters);
                var langVersionsHtmlWrapper = $('#configLangVersions_' + self.model.get('name'));
                if (langVersionsHtmlWrapper.length) {
                    langVersionsHtmlWrapper.html(langVersionsHtml);
                }
            }

            if (typeof self.model.get('parameters').editable !== 'undefined' && !self.model.get('parameters').editable) {
                return this;
            }

            $('#' + randId + ' .ot_inline_editable[data-name="' + self.model.get('name') + '"]')
                .editable(preparedParameters)
                .on('shown', function() {
                    var editable = $(this).data('editable');
                    editable.setValue(preparedParameters.value);
                    editable.input.$input.val(preparedParameters.value);
                    editable.input.$input.closest('form').parents('div:first').on('save', function(e, params){
                        preparedParameters.value = params.newValue;
                        if (params.newValue !== '') {
                            var key = self.model.get('displayValue');
                            if (key) {
                                editable.$element.text(trans.get(key));
                            }
                        }
                    });
                })
                .on('hidden', function(e, reason) {
                    if (e.currentTarget.innerHTML.length > 40 ) {
                        $(e.currentTarget).html(e.currentTarget.innerHTML.substr(0, 40) + ' ...');
                    }
                    if (reason === 'cancel' || reason === 'nochange') {
                        //auto-open next editable
                        //$(this).closest('tr').next().find('.editable').editable('show');
                        var editable = $(this).data('editable');
                        var key = self.model.get('displayValue');
                        if (key) {
                            editable.$element.text(trans.get(key));
                        }
                    }
                });

            return this;
        }
    });
    _.each(InlineFields.models, function(field){
        var view = new XEditableFieldView({
            model: field
        });
        view.render();
    });
    var ConfigsPage = Backbone.View.extend({
        "el": $(".inline_editable_form"),
        "events": {
            "click .js-remove_config": "removeConfig"
        },
        removeConfig: function(e) {
            var action = '?cmd=SiteConfiguration&do=removeConfig';
            var configName = $(e.currentTarget).data('config');
            var configLang = $(e.currentTarget).data('config-lang');

            var msg = trans.get('Remove_confirmation');
            modalDialog(trans.get('Confirm_needed'), msg, function(){
                $.post(
                    action,
                    {
                        configName : configName,
                        configLang : configLang,
                    },
                    function (data) {
                        if (! data.error) {
                            showMessage(trans.get('Data_removed_successfully'));
                            location.reload();
                        } else {
                            showError(data.message);
                        }
                    }, 'json'
                );
            });

        },
    });
    var ConfigsPageObj = new ConfigsPage();
} );