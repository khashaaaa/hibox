var applyButton = $('#applyButton').html();
var xhrFilter;
function initFilterSearch(el) {
        if (typeof(xhrFilter)!='undefined') {
            xhrFilter.abort();
        }
        $(".helpBox").each(function() {
            $(this).remove();
        });
        $(el).parent().append(applyButton);
        $('.microPreLoader').hide();
        $('.search-filter-form-send').click(function() {
            $("#isAjaxForm").each(function() {
                $(this).remove();
            });
            $('#filterform').submit();        
        });
        $("#SearchProvider").is("input")
        if ($("#SearchProvider").is("input") && $("#SearchMethod").is("input")) {
            $('.microPreLoader').show();
            //Только если поиск по определенному провайдеру
            xhrFilter = $.get(
                $('#filterform').attr('action'),
                $('#filterform').serialize(),
                function (data) {
                    $('.microPreLoader').hide();
                    if (data.Success != 'Ok') {
                        $('#error-dialog').dialog({
                            title: 'Error',
                            buttons:{}
                        });
                        $('#error-dialog').html(data.message).dialog('open');
                    } else {
                        $(".helpBox a").append(' (' + data.Count + ')');
                    }
                }, 'json'
            );
            
        }
}

$(function(){
    $('.microPreLoader').hide();
    $('#applyButton').remove();
    $('.js-radio-filters').change(function() {
        initFilterSearch(this);        
        return false;
    });
    
    var isAllTypesChecked = $('[name="filters[StuffStatus]"]:first').attr('checked');
    var countFilters = $('li.opening input:checked').length;
    if (typeof activeBrandFilters !== 'undefined' && activeBrandFilters) {
        var filterName = $('#brandSearch').attr('filter-name');
        countFilters = countFilters + activeBrandFilters.length;
        for(var i in activeBrandFilters) {
            for(var j in searchPropsBrand) {
                if (searchPropsBrand[j].Id == i) {
                    $('#filterform').prepend(
                        $('<input type="hidden" id="' + searchPropsBrand[j].Id + '" name="filters[20000][' + searchPropsBrand[j].Id + ']" value="1"/>')
                    );
                    $('#clear-filter').prepend(
                        $('<li class="item">' +
                            '<a href="'+ searchPropsBrand[j].Id +'" class="i-new i-delete i delete go"></a><div class="cat">'+ filterName +'</div><div class="itemcat">'+ searchPropsBrand[j].Name +'</div>' +
                            '</li>')
                    );
                }
            }
        }
    }    
    if((countFilters > 0 && !isAllTypesChecked) || (countFilters > 1)){
        $('#active-search-prop').show();
    }

    if (typeof searchPropsBrand !== 'undefined') {
        for(var i in searchPropsBrand) {
            searchPropsBrand[i].NameLowCase = searchPropsBrand[i].Name.toLowerCase();
        }
    }
    
    $('.js-radio-filters:checked').each(function(){
        var title = $(this).data('parent-filter-name'),
            subtitle = $(this).data('filter-name');

            $('#clear-filter').prepend(
                $('<li class="item">' +
                    '<a href="'+$(this).attr('id')+'" class="i-new i-delete i delete go"></a><div class="cat">'+ title +'</div><div class="itemcat">'+ subtitle +'</div>' +
                   '</li>')
            );
    });
    
        
    $('.i.delete').on('click', function(){
        if(!$(this).hasClass('delete-brand-fiter')){
            var id=$(this).attr('href');
            $('#'+id).attr('name', '');
            $('#filterform').submit();
            return false;
        }
    });
    
    var ul = $('#brandSearch').parent();
    $('#brandSearch').keyup(function(e) {
        if ((e.keyCode != 40) && (e.keyCode != 38) && (e.keyCode != 13)) {
            ul.find('.item').remove();
            var input = $(this).val().toLowerCase();
            var checked;
            if (input.length > 1) {
                for(var i in searchPropsBrand){
                    if (searchPropsBrand[i].NameLowCase.indexOf(input) != -1) {
                        checked = '';
                        for(var j in activeBrandFilters) {
                            if (searchPropsBrand[i].Id == j) {
                                checked = 'checked';
                                $('#'+searchPropsBrand[i].Id).attr('name', '');
                            }
                        }
                        ul.append('<li class="item"><label for="ch' + searchPropsBrand[i].Id + '"><input class="js-radio-filters" type="' + typeOfInput +
                        '" id="ch' + searchPropsBrand[i].Id + '" name="filters[20000][' + searchPropsBrand[i].Id + ']" value="1" data-filter-name="' +
                        searchPropsBrand[i].Name + '" data-parent-filter-name="" ' + checked + ' />' + searchPropsBrand[i].Name + '</label></li>');                        
                    }
                }
                ul.find('.js-radio-filters').change(function() {
                    initFilterSearch(this);        
                    return false;
                });
            }             
        }
    });    
});