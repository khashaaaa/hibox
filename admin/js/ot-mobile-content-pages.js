var Contents = new Backbone.Collection();
var ContentsPage = Backbone.View.extend(
    {
        "el": ".contents-wrapper",
        "events": {
            "click li.edit-page": "editPage",
            "click li.delete-page": "removeContentPage",

        },
        render: function()
        {
            return this;
        },
        initialize: function()
        {

        },
        editPage: function(e)
        {
            var tr = $(e.currentTarget).closest('tr');
            var pageId = $(tr).attr('id');
            document.location.href = 'index.php?cmd=contents&do=editMobileContentPage&menuItemId='+pageId;
            return false;
        },
        removeContentPage: function(e)
        {
            var tr = $(e.currentTarget).closest('tr');
            console.log(tr);
            var pageId = $(tr).attr('id');

            modalDialog(trans.get('Confirm_needed'), trans.get('contents::Really_remove_this_page'), function() {
                $.post('?cmd=contents&do=deleteContentPage', {'id' : pageId}, function (data) {
                    if (data.result == 'ok') {
                        $(tr).remove();
                    }
                }, 'json');
            });
        },
    });

$(function()
{
    var P = new ContentsPage();
});
