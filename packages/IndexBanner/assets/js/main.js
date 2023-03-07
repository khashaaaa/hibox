/**
 * Created by ErgalievDK on 24/06/2019.
 */

$(document).ready(function (event) {
    $(".actionBannerDelete").click(function(event){
        event.preventDefault();
        var banner_id = parseInt($(this).attr('data-id'));
        var parrent_hide = $(this).parent("span").parent("div").parent("div").parent("div");

        $.post(
            '/admin/?cmd=PluginsUtil&do=view&plugin=IndexBanner&action=delete',
            {id: banner_id},
            function(data){
                parrent_hide.hide(500);
            }
        );
    });
});