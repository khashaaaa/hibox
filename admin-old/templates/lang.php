<?
include ("header.php");

?>
<script type="text/javascript" src="js/lang.js"></script>

<div class="main"><div class="canvas clrfix">
        
        <div class="col700">
            <div class="tuning">
                <span id="error" style="color:red;font-weight: bold;">
                    <? if(isset($error)) { print $error; } ?>
                </span>
                <h1> <?=LangAdmin::get('translations')?> </h1>
                <p></p>
                <form id="choose-lang">
                    <input type="hidden" name="lang" value="" />
                    <input type="hidden" name="cmd" value="langTranslations" />
                </form>
                <form id="save-translation" action="index.php?cmd=langTranslations&do=saveTranslation">
                    <input type="hidden" name="key" value="" />
                    <input type="hidden" name="page" value="" />
                    <input type="hidden" name="translation" value="" />
                </form>
                <? foreach($langs as $l){ ?>
                    <? if($l['lang_code']==$current_lang) $active = 'active'; else $active = ''; ?>
                    <input type="button" lang="<?=  htmlspecialchars($l['lang_code'])?>" value="<?=$l['lang_name']?>" class="choose-lang-btn <?=$active?>" />
                <? } ?>

                <br /><br />
                <input type="button" class="ui-button ui-widget ui-state-default ui-corner-all" value="<?=LangAdmin::get('add_a_translation')?>" onclick="location.href='?cmd=langTranslations&do=addTranslation'"/>
                <br /><br />

                <table>
                    <tr>
                        <th class="l-th1"><?=LangAdmin::get('key')?></th>
                        <th class="l-th2"><?=LangAdmin::get('translation')?></th>
                        <th class="l-th3"><?=LangAdmin::get('actions')?></th>
                    </tr>
                    <? if($translations) foreach($translations as $trans) { ?>
                        <tr>
                        <td><?=$trans['key']?></td>
                        <td><?=  htmlspecialchars($trans['translation'])?></td>
                        <td>
                        <a href="?cmd=langTranslations&do=edit&key=<?=$trans['key']?>"><?=LangAdmin::get('change')?></a>&nbsp;&nbsp;&nbsp;
                        <a href="?cmd=langTranslations&do=del&id=<?=$trans['id']?>" style="display: none;"><?=LangAdmin::get('remove')?></a>&nbsp;&nbsp;&nbsp;&nbsp;
                        </td>
                        </tr>
                    <? } ?>
                </table>
                <input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all" value="<?=LangAdmin::get('add_a_translation')?>" onclick="location.href='?cmd=langTranslations&do=addTranslation'"/>
                <p>
                </p>
            </div>
        </div>
        
</div></div><!-- /.main -->

<script type="text/javascript" src="js/jquery.combobox.js"></script>


<?
include ("footer.php");
?>
