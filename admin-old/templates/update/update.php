<div id="dialog-form" title="<?=LangAdmin::get('error')?>!">
    <span id="basketinfo"></span>
</div>

<div class="main"><div class="canvas clrfix" id="overlay-wrapper">
    <div id="overlay">
    </div>
    <h1> <?=LangAdmin::get('upgrading_the_system')?> </h1>
    <p>
        <?=LangAdmin::get('version_of_your_system')?>: <? print $current_version ? @$current_version->Version[0]->Number : LangAdmin::get('not_recognized')?>
    </p>
    <p>
        <?=LangAdmin::get('version_of_your_lib')?>: <? print @$current_version->Version[0]->LibVersion ? @$current_version->Version[0]->LibVersion : LangAdmin::get('not_recognized')?>
    </p>

    <?if($allow_update != 'Ok'){?>
    <h3><?=Lang::get('update_denied')?></h3>
    <?=html_entity_decode($allow_update)?>
    <?}?>

    <p>
        <?=LangAdmin::get('latest_version_available')?>: <b><?=$versions->Version[0]->Number?></b>
        <br /><br />
        <?=LangAdmin::get('description')?>:<br />
        <?=  nl2br($versions->Version[0]->Description) ?>
    </p>
    <? $k = 0; ?>
    <? $i = 0; foreach($versions->Version as $k=>$v){ ?>
        <? if(!$i){$i++; continue;} ?>
        <? if($v->Number>=@$current_version->Version[0]->Number){ ?>
        <? if(!$k){$k++;
        ?>
            <h4><?=LangAdmin::get('the_other_new_releases')?></h4>
            <?
        } ?>
        <p>
            <?=LangAdmin::get('the_version_number')?>: <b><?=$v->Number?></b>
            <br /><br />
            <?=LangAdmin::get('description')?>:<br />
            <?=  nl2br($v->Description) ?>
        </p>
        <? } ?>
    <? } ?>
    <?if($allow_update == 'Ok'){?>
    <input type="submit" value="<?=LangAdmin::get('update')?>"  class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" id="update" />
    <?}?>
</div></div>

