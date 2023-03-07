function ajaxfilemanager(field_name, url, type, win)
{
    var ajaxfilemanagerurl = "/js/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php";
    switch (type) {
        case "image":
            break;
        case "media":
            break;
        case "flash":
            break;
        case "file":
            break;
        default:
            return false;
    }
    tinyMCE.activeEditor.windowManager.open({
        url: "/js/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php",
        width: 782,
        height: 440,
        inline : "yes",
        close_previous : "no"
    },{
        window : win,
        input : field_name
    });
}
