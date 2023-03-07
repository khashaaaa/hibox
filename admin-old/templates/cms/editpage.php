<?
include ("templates/header.php");

?>
<div class="main">
    <div class="canvas clrfix editpage">
	<? if (@$page['id']==='new') { ?>
            <h1><?=LangAdmin::get('bookmark_this_page')?></h1>
	<? } else { ?>
            <h1><?=LangAdmin::get('editing_a_page')?> (ID: <?=@$page['id']?>)</h1>
	<? } ?>
	<form action="?cmd=cmsadmin&do=editsave" method="post">
            <input type="hidden" name="id" value="<?=@$page['id']?>">
            <input type="hidden" name="cms" value="1">
            <p><label><?=LangAdmin::get('alias')?>: <input type="edit" name="alias" value="<?=@$page['alias']?>"></label></p>
            <p><label><?=LangAdmin::get('title')?>: <input type="edit" name="title" value="<?=@$page['title']?>"></label></p>

            <? if (in_array('Seo1', General::$enabledFeatures)) { ?>
            <p><label><?=LangAdmin::get('Title')?>: <input type="edit" name="pagetitle" value="<?=@$page['pagetitle']?>"></label></p>
            <p><label><?=LangAdmin::get('Meta keywords')?>: <input type="edit" name="seo_keywords" value="<?=@$page['seo_keywords']?>"></label></p>
            <p><label><?=LangAdmin::get('Meta description')?>: <input type="edit" name="seo_description" value="<?=@$page['seo_description']?>"></label></p>
            <? } else { ?>
            <input type="hidden" name="pagetitle" value="" />
            <input type="hidden" name="seo_keywords" value="" />
            <input type="hidden" name="seo_description" value="" />
            <? } ?>

            <p>
                <label>
                    <?=LangAdmin::get('language')?>:
                    <select name="lang">
                        <?
                        foreach($webui->Settings->Languages->NamedProperty as $v){
                            $lang = (string)$v->Name;
                            $lang_desc = (string)$v->Description;

                            $selected = '';
                            if(@$page['lang_code'] == $lang){
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
            <? if (isset($_GET['id_parent'])) { ?>
                <input name="parent" value="<?=$_GET['id_parent']?>" type="hidden" />
            <? } ?>
            <p><label><?=LangAdmin::get('is_service_page')?>: <input type="checkbox" name="is_service" <? if (@$page['is_service']) echo 'checked="checked" '?>/></label></p>
            <p><input type="submit" value=" <?=LangAdmin::get('save')?> " class="ui-button ui-widget ui-state-default ui-corner-all"></p>
        </form>
	<br clear="all">
    </div>
</div>

<?
include ("templates/footer.php");
?>