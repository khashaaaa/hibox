<?
include ("templates/header.php");

?>
<div class="main"><div class="canvas clrfix editpage">
    <? if (!@$translation['id']) { ?>
        <h1><?=LangAdmin::get('add_a_translation')?></h1>
    <? } else { ?>
        <h1><?=LangAdmin::get('editing_translation')?> (<?=LangAdmin::get('key')?>: <?=@$trans[0]['key']?>)</h1>
    <? } ?>
    <? if (@$isSearchTranslation) { ?>
        <p style="color:#F00"><?=LangAdmin::get('Search_providers_translations_alert')?></p>
    <? } ?>
    <form action="?cmd=langTranslations&do=saveTranslation" method="post">

    <p><label class="l270"><?=LangAdmin::get('key')?>: <input type="edit" name="key" value="<?=@$trans[0]['key']?>"></label></p>
    
    <?
    $lc_trans = array();
    if(is_array(@$trans)){
        foreach($trans as $t){
            $lc_trans[$t['lang_code']] = $t['translation'];
        }
    }
    if(@is_array($langs)){
        foreach($langs as $l){
            ?>
            <p>
                <label class="l270"><?=LangAdmin::get('transfer_to')?> <b><?=$l['lang_name']?></b>: 
                    <input type="edit" name="translations[<?=$l['lang_code']?>]" value="<?=@$lc_trans[$l['lang_code']]?>">
                </label>
            </p>
            <?
        }
    }
    ?>

    <p><input type="submit" value=" <?=LangAdmin::get('save')?> " class="ui-button ui-widget ui-state-default ui-corner-all"></p>
    </form>
    <br clear="all">
</div></div>

<?
include ("templates/footer.php");
?>