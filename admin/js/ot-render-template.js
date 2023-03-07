function renderInlineEditableElement(templateName, templateData) {
    if (! renderInlineEditableElement.tmpl_cache) {
        renderInlineEditableElement.tmpl_cache = {};
    }

    if (! renderInlineEditableElement.tmpl_cache[templateName]) {
        var templateDir = 'templates/inline_elements';
        var templateUrl = templateDir + '/' + templateName + '.html';

        var templateString = '';
        $.ajax({
            url: templateUrl,
            method: 'GET',
            async: false,
            success: function(data) {
                templateString = data;
            }
        });

        renderInlineEditableElement.tmpl_cache[templateName] = _.template(templateString);
    }

    return renderInlineEditableElement.tmpl_cache[templateName](templateData);
}

function renderTemplate(templateName, templateData) {
    if (! renderTemplate.tmpl_cache) {
        renderTemplate.tmpl_cache = {};
    }

    if (! renderTemplate.tmpl_cache[templateName]) {
        var templateDir = 'templates';
        var templateUrl = templateDir + '/' + templateName + '.html';

        var templateString = '';
        $.ajax({
            url: templateUrl,
            method: 'GET',
            async: false,
            success: function(data) {
                templateString = data;
            }
        });

        renderTemplate.tmpl_cache[templateName] = _.template(templateString);
    }

    return renderTemplate.tmpl_cache[templateName](templateData);
}