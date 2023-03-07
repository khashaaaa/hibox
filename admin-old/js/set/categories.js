$(function(){
    $('#set-categories').empty();
    $.each(categories, function(categoryName, categoryCount){
        var categoryTitle = categoryName;
        if(categoryTitle == '0')
            categoryTitle = trans.home;
        $('#set-categories').append(
            $('<li></li>')
                .addClass('ui-state-default')
                .addClass('mb5')
                .append(
                    $('<div></div>')
                        .html(categoryTitle + ' ('+ categoryCount +')')
                        .attr({
                            categoryId: categoryName,
                            'class':    'apply-category'
                        })
            )
        );
        $('[categoryId="'+activeCategory+'"]').css({'font-weight': 'bold'});
    });
});