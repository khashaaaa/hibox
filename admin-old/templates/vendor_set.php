<div class="tuning">

    <h1><?=LangAdmin::get('vendors_set')?></h1>

    <?if (@$_SESSION['error']) { ?>
    <style>
        #error {
            background-color: #CCCCCC;
            color: red;
            display: block;
            margin: -3px 0 0;
            padding: 5px;
        }
    </style>
    <div id="error"><?=$_SESSION['error']?></div>
    <? }?>

    <div>
        <? if (!isset($vendors) && empty($vendors)) { ?>
        <p><?=LangAdmin::get('empty')?>!</p>
        <? } else { ?>
        <br/>
        <ul id="sortable-vendors">
            <? foreach ($vendors as $item) { ?>
            <li class="sortlist" id="<?=$item['id']?>">
                <table class="nowidth nopadding" style="width:215px">
                    <tr>
                        <td width="20%"><a href="../index.php?p=vendor&id=<?=$item['id'];?>"><img
                            src="<?=$item['image_path'];?>" alt=""/></a></td>
                        <td>
                            <center><?=$item['vendor_name'];?>(<?=$item['Name']?>)</center>
                        </td>
                        <td width="15"><a
                            href="<?=BASE_DIR;?>index.php?sid=<?=$GLOBALS['ssid'];?>&amp;do=deleteVendor&amp;cmd=Set&amp;id=<?=$item['id'];?>">
                            <img src="<?=TPL_DIR;?>i/del.png" width="12" height="12" align="middle"
                                 style="vertical-align:middle"/></a>
                        </td>
                    </tr>
                </table>
            </li>
            <? }?>
        </ul>
        <? } ?>

    </div>

    <div class="clear"></div>
    <br/>

    <h3><?=LangAdmin::get('addition')?></h3>
    <?
    $action = 'add';
    $nameval = "";
    $pic = "";
    $button = LangAdmin::get('add_vendor');
    ?>
    <div id="save-vendors-order" style="display: none;">
        <form action="<?=BASE_DIR;?>index.php?sid=<?=$GLOBALS['ssid'];?>&amp;do=saveorder&amp;cmd=Set"
              method="post">
            <input type="hidden" name="ids" value="" id="vendors-set-ids"/>
            <input type="hidden" name="type" value="Best"/>
            <input type="hidden" name="isVendor" value="1"/>
            <input type="hidden" name="return" value='index.php?cmd=set&active_tab=tabs-4'/>
            <input type="submit" name="submit" value="<?=LangAdmin::get('save_citations')?>" class="order-save" />
        </form>
    </div>
    <div class="clear"></div>
    <form id="vendors-set-form" enctype="multipart/form-data" action="<?=BASE_DIR;?>index.php?sid=<?=$GLOBALS['ssid'];?>&amp;do=addVendor&amp;cmd=set"
          method="post">
        <table>
            <tr>
                <td>
                    <?=LangAdmin::get('vendor_id')?>:
                    <br/>
                    <small><?=LangAdmin::get('vendor_add_tips')?></small>
                </td>
                <td><input type="text" name="Name" class="text ui-widget-content ui-corner-all"/></td>
            </tr>
            <tr>
                <td>
                    <?=LangAdmin::get('vendor_name')?>:
                </td>
                <td><input type="text" name="VendorName" class="text ui-widget-content ui-corner-all"/></td>
            </tr>
            <tr>
                <td><?=LangAdmin::get('image')?>:</td>
                <td>
                    <input  type="file" name="qqfile"/>
                </td>
            </tr>
        </table>
        <button id="submit1"><?=$button?></button>
        <? if (isset($vendorData)) { ?>
        <input type="hidden" name="Id" value="<?=$vendorData['id']?>"/>
        <? } ?>
    </form>
</div>
<script>

    function createUploader() {
        var uploader = new qq.FileUploader({
            element:document.getElementById('file-uploader'),
            action:'utils/Upload.php?resize=1',
            debug:true,
            template:'<div class="qq-uploader">' +
                '<div class="qq-upload-drop-area"><span></span></div>' +
                '<div class="qq-upload-button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" style="height:23px;padding-top:7px">&nbsp;&nbsp;&nbsp;<?=LangAdmin::get('select_a_picture')?>&nbsp;&nbsp;&nbsp;</div>' +
                '<ul class="qq-upload-list"></ul>' +
                '</div>',
            onComplete:function (id, fileName, responseJSON) {
                $('.qq-upload-list').empty().append($('<li></li>').append($('<img />').attr('src', responseJSON.url + '?' + Math.random())));
                $('[name="PictureUrl"]').val(responseJSON.url);
            }
        });
    }
    $(function () {
        $("#sortable-vendors").sortable({
            change:function (event, ui) {
                $("#save-vendors-order").show();
            }
        });
        $('#submit1').button();
        $("#sortable-vendors").disableSelection();

        $('.order-save').click(function () {
            $('#vendors-set-ids').val($('#sortable-vendors').sortable('toArray').join(';'));
            return true;
        }).button();

        //createUploader();
    <?
    if (isset($brandData)) {
        ?>
        $('.qq-upload-list').empty().append($('<li></li>').append($('<img />').attr('src', '<?=$brandData['PictureUrl']?>')));
        <?
    }
    ?>
    });
</script>

