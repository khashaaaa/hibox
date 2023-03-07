$.parseUrl = function(){
    var match,
        pl     = /\+/g,  // Regex for replacing addition symbol with a space
        search = /([^&=]+)=?([^&]*)/g,
        decode = function (s) { return decodeURIComponent(s.replace(pl, " ")); },
        query  = window.location.search.substring(1);

    var urlParams = {};
    while (match = search.exec(query))
        urlParams[decode(match[1])] = decode(match[2]);

    return urlParams;
}

jQuery.fn.extend({
    addSortArrows : function(){
        var arrows = {
            asc : '<a href="#" class="apply-sort" direction="desc">&#x25BC;</a>',
            desc: '<a href="#" class="apply-sort" direction="asc">&#x25B2;</a>',
            both: '&#x25C6;'
        }

        var sortId = $(this).attr('sort');

        $(this).css({'white-space': 'nowrap', 'vertical-align': 'middle'});
        if( typeof  sorting[sortId] != 'undefined'){
            $(this).append(arrows[sorting[sortId]]);
        }
        else{
            if(sortId != 'TotalAmount')
                $(this).append(arrows.desc);
            else{
                $('#amount-filter').css({
                    position: 'relative',
                    top: '-8px'
                });
                $(this).append(
                    $('<div>' + arrows.desc + '<br />' + arrows.asc + '</div>')
                        .css('display', 'inline-block')
                );
            }
        }
    }
});

$(function(){
    urlParams = $.parseUrl();
    $('.sort-columns').each(function(){
        $(this).addSortArrows();
    });
    $('.apply-sort').live('click', function(){
        var filterParams = {};
        filterParams['sort['+$(this).closest('th').attr('sort')+']'] = $(this).attr('direction');
        for(var i in urlParams){
            if(i!='sort['+$(this).closest('th').attr('sort')+']')
                filterParams[i] = urlParams[i];
        }

        var form = $('<form></form>').attr('action', 'index.php');
        for ( var key in filterParams ) {
            if (typeof filterParams[key] == 'object'){
                for ( var subKey in filterParams[key] ) {
                    var input = $('<input />').attr({'type': 'hidden', 'name': key+'['+subKey+']'}).val(filterParams[key][subKey]);
                    form.append(input);
                }
                continue;
            }
            var input = $('<input />').attr({'type': 'hidden', 'name': key}).val(filterParams[key]);
            form.append(input);
        }
        $('body').append(form);
        form.submit();
        return false;
    });
});