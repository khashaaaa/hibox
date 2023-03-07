<?
include ("templates/header.php");
?>
<div class="main"><div class="canvas clrfix editnews">
    <? if (@$news['id']==='new') { ?>
    <h1><?=LangAdmin::get('add_news')?></h1>
    <? } else { ?>
    <h1><?=LangAdmin::get('editing_a_news')?> (ID: <?=@$news['id']?>)</h1>
    <? } ?>
    <form action="?cmd=news&do=editsave" method="post"  enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?=@$news['id']?>">
    <input type="hidden" name="cms" value="1">
    <p><label><?=LangAdmin::get('title')?>: <input type="edit" name="title" value="<?=@$news['title']?>"></label></p>
    <p><label><?=LangAdmin::get('brief')?>: <textarea name="brief"><?=@$news['brief']?></textarea></label></p>
        <p><label><?=LangAdmin::get('image')?>:

        <div id="fileupload">
                            <div class="row fileupload-buttonbar fileupload-banner">
                                <div class="fileupload fileupload-new" data-provides="fileupload">
                                    <div class="span4 fileupload-preview fileupload-exists thumbnail" style="margin-bottom: 0 !important; display: inline-block; margin-right: 5px; min-height: 50px;">
                                        <img src="<?=! empty($news['image']) ? DigestRepository::getImage($news['image'], "big") : ''?>"/>
                                    </div>
                                    <div class="">
                                        <span class="btn btn-file btn-primary fileinput-button">
                                            <span style="display: <?=(empty($news['image']) ? 'inline-block' : 'none');?>" class="fileupload-new"><?=LangAdmin::get('Upload')?></span>
                                            <span style="display: <?=(! empty($news['image']) ? 'inline-block' : 'none');?>" class="fileupload-exists"><?=LangAdmin::get('Change')?></span>
                                            <input id="uploaded_logo" name="uploaded_logo" type="file" />
                                        </span>
                                    </div>
                                </div>
                                <input type="hidden" name="existing_logo" value="<?=! empty($news['image']) ? $news['image'] : ''?>" />
                            </div>
        </div>
        </label>
        </p><br>

    <p>
        <label>
            <?=LangAdmin::get('language')?>:
            <select name="lang">
                <?
                foreach($webui->Settings->Languages->NamedProperty as $v){
                    $lang = (string)$v->Name;
                    $lang_desc = (string)$v->Description;

                    $selected = '';
                    if(@$news['lang_code'] == $lang){
                        $selected = ' selected';
                    }
                    ?>
                    <option value="<?=$lang?>"<?=$selected?>><?=$lang_desc?></option>
                    <?
                }
                ?>
            </select>
        </label>
    </p>
    <p><input type="submit" value=" <?=LangAdmin::get('save')?> " class="ui-button ui-widget ui-state-default ui-corner-all"></p>
    </form>
    <br clear="all">
</div></div>

<script type="text/javascript">
    $("#dialog-form").dialog({
        autoOpen:false,
        height:315,
        width:350,
        modal:true,
        buttons:{
            "<?=LangAdmin::get('close')?>":function () {
                $(this).dialog("close");
            }
        },
        close:function () {
        }
    });

</script>

<!--- ULOADER-->
<link rel="stylesheet" href="../admin/css/vendor/bootstrap-fileupload.css">
<link rel="stylesheet" href="../admin/css/vendor/jquery.fileupload-ui.css">
<script src="../admin/js/vendor/bootstrap-fileupload.js"></script>

<?
include ("templates/footer.php");
?>
