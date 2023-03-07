<?
include ("header.php");
$cid = @$_GET['cid'];
?>

<div class="tuning">
    <form action="" enctype="multipart/form-data" method="POST" class="addform"
          id="dialog-form" title="<?=LangAdmin::get('add')?>">
        <div id="upload_img2"><span></span></div>
        <p class="validateTips"></p>
        <label style="display: inline-block; width: 100px"><?=LangAdmin::get('name')?>*:</label>
        <input type="text" value="" name="desc" id="name" class="text ui-widget-content ui-corner-all"/>
        <br/><br/>
        <label style="display: inline-block; width: 100px"><?=LangAdmin::get('link')?>:</label>
        <input type="text" value="" name="link" id="link" class="text ui-widget-content ui-corner-all"/>
        <br/><br/>
        <label style="display: inline-block; width: 100px"><?=LangAdmin::get('language')?>:</label>
        <select name="language" id="language">
        <?
            foreach($data->Settings->Languages->NamedProperty as $v){
                ?>
                <option value="<?=(string)$v->Name?>"><?=(string)$v->Description?></option>
                <?                
            }
        ?>
        </select>
        <br/><br/>

        <label style="display: inline-block; width: 300px"><?=LangAdmin::get('banner_image')?>*:</label><br />
        <input  type="file" name="qqfile"/>
        <input  type="hidden" name="id" id="id">
        <input type="hidden" name="PictureUrl" width="100px" height="100px"/>
    </form>
    <div id="" style="float:right;">
        <input type="button" value="<?=LangAdmin::get('add')?>..." onclick="NewBanner();"
               class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"><br/><br/>
    </div>
    <p><strong><?=LangAdmin::get('banner_description')?></strong></p>
    <ul id="sort_ban">
        <? if (isset($banners)) foreach ($banners as $banner) { ?>
        <li class="sortlist" id="b<?=$banner['id']?>">
            <table class="valign-top nowidth">
                <tr>
                    <td>
                        <a href="">
                            <? if (@strpos($banner['source'], '.swf') !== false) { ?>
                                <object width="695" height="330">
                                    <param name="movie" value="<?=$banner['source']?>">
                                    <embed src="<?=$banner['source']?>" width="695" height="330">
                                    </embed>
                                </object>
                            <? }  else { ?>
                                <img src="<?=$banner['source']?>" alt="<?=$banner['name']?>" width="120px"/>
                            <? } ?>
                        </a>
                    </td>
                    <td width="70%" style="vertical-align: top;">
                        <span><strong><?=LangAdmin::get('description')?>:</strong>&nbsp;<?=$banner['name']?><br/></span>
                        <span><strong><?=LangAdmin::get('link')?>:</strong>&nbsp;<?=$banner['url']?><br /></span>
                        <span><strong><?=LangAdmin::get('language')?>:</strong>&nbsp;<?=$banner['lang']?></span>
                    </td>
                    <td style="vertical-align: top"><a class="ui-button ui-icon ui-icon-trash" onclick="confirm_delete_banner('<?=$banner['id'];?>');" href="#"></a></td>
                    <td style="vertical-align: top"><a class="ui-button ui-icon ui-icon-pencil" onclick="EditBanner('<?=$banner['id']?>','<?=$banner['name']?>','<?=$banner['url']?>','<?=$banner['source']?>','<?=$banner['lang']?>');"></a></td>
                </tr>
            </table>
        </li>
        <? }?>
    </ul>
    <div id="save1" style="display:none;float:right;">
        <form action="<?=BASE_DIR;?>index.php?sid=<?=$GLOBALS['ssid'];?>&amp;do=saveorder&amp;cmd=banners"
              method="post">
            <input type="hidden" id="ids" name="ids" value=""/>
            <input type="hidden" name="return" value="<?= $_SERVER['REQUEST_URI'] ?>" />
            <button id="submit1save"><?=LangAdmin::get('save')?></button>
        </form>
    </div>
</div>

<?=Plugins::invokeEvent('onRenderBannersPage')?>
<script type="text/javascript">
    $(function () {
        $("#sort_ban").sortable();
        $("#sort_ban").disableSelection();
        createUploader();
        
    });
	function NewBanner() {
		$('#dialog-form').attr('action','index.php?cmd=banners&do=add');
		$("#name").val();
		$("#link").val();
		$('[name="PictureUrl"]').val();
		$("#id").val();
		$('#dialog-form').dialog('open');
	}
	function EditBanner(id,name,url,file,lang) {
		$('#dialog-form').attr('action','index.php?cmd=banners&do=edit');
		$("#name").val(name);
		$("#link").val(url);
		$('[name="PictureUrl"]').val(file);		
		$("#language [value='"+lang+"']").attr("selected", "selected");
		$("#id").val(id);
		$('#dialog-form').dialog('open');
	}
	
	
    $("#sort_ban").sortable({
        change:function (event, ui) {
            $("#save1").show();
        }
    });
    $("#dialog-form").dialog({
        autoOpen:false,
        height:300,
        width:350,
        modal:true,
        buttons:{
            "<?=LangAdmin::get('add')?>":function () {
                this.submit();
            },
            "<?=LangAdmin::get('cancellation')?>":function () {
                $(this).dialog("close");
            }
        },
        close:function () {
        }
    });

    $("#upload_img2").dialog({
        autoOpen:false,
        height:380,
        width:730,
        modal:true,
        close:function () {
        }
    });

    $("#submit1save")
        .button()
        .click(function () {
            var result = $('#sort_ban').sortable('toArray');
            var str = '';
            $.each(result, function (i, value) {
                str += value.substr(1) + ';';

            });
            $('#ids').val(str);
            $("#submit1save").submit();
        });

    function createUploader() {
        var uploader = new qq.FileUploader({
            element:document.getElementById('file-uploader'),
            action:'utils/Upload.php',
            debug:true,
            template:'<div class="qq-uploader">' +
                '<div class="qq-upload-drop-area"><span></span></div>' +
                '<div class="qq-upload-button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"><?=LangAdmin::get('select_a_picture')?></div>' +
                '<ul class="qq-upload-list"></ul>' +
                '</div>',
            onComplete:function (id, fileName, responseJSON) {
                var url = responseJSON.url;
                if (url.indexOf('.swf') + 1) {
                    $("#upload_img2").empty().append('<div></div>');
                    $('#upload_img2 div').html('<object width="695" height="330">' + 
                        '<param name="movie" value="' + url + '">' +
                        '<embed src="' + url + '" width="695" height="330">' +
                        '</embed>' + '</object>');
                } else {
                    $('#upload_img2').empty().append($('<img />').attr('src', url + '?' + Math.random()));
                    
                }
                $('#upload_img2').dialog('open');
                $('[name="PictureUrl"]').val(responseJSON.url);
            }
        });
    }

    function confirm_delete_banner(id) {
        if (confirm("<?=LangAdmin::get('confirm_delete_banner')?>")) {
            delete_banner(id);
        } else {
            return false;
        }
    }

    function delete_banner(id) {
        var url = '<?=BASE_DIR;?>index.php?sid=<?=$GLOBALS['ssid'];?>&do=del&cmd=banners&id=' + id + '&return=<?= urlencode($_SERVER['REQUEST_URI']) ?>';
        window.location.href = url;
    }

</script>

<?
include ("footer.php");
?>