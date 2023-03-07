<!-- site language -->
<div class="btn-group pull-right">
    <a class="btn dropdown-toggle offset-top05" data-toggle="dropdown" href="#" title="<?=LangAdmin::get('Language_select_for_edit')?>">

        <? $activeLang = false; ?>
        <? if($AvailableLanguages) foreach($AvailableLanguages as $key => $lang){ ?>
            <? if((string)$key == Session::get('active_lang_siteconfiguration')){ ?>
                <? $activeLang = (string)$key; ?>
                <?=(string)$lang?>
            <? } ?>
        <? } ?>
        <? if(!$activeLang){ ?>
            <?=LangAdmin::get('All_languages_versions')?>
        <? } ?>

        <span class="caret"></span>
    </a>
    <ul class="dropdown-menu">
        <li>
            <a data-value="" href="<?=$PageUrl->SetPageLangUrl('')?>">
                <?=LangAdmin::get('All_languages_versions')?>
            </a>
        </li>
        <? if($AvailableLanguages) foreach($AvailableLanguages as $key => $lang){ ?>
            <li>
                <a data-value="<?=(string)$key?>" href="<?=$PageUrl->SetPageLangUrl((string)$key)?>">
                    <?=(string)$lang?>
                </a>
            </li>
        <? } ?>
    </ul>
</div>

<? AssetsMin::jsBegin(); ?>
<script>
    $(function(){
        //showError('Test Error', 'TestError');
    });
</script>
<? $strScript = AssetsMin::jsEnd(); ?>
<? AssetsMin::registerJs($strScript); ?>
<!-- /site language -->
