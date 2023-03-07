<br/>
<div id="recommend" class="block-container">
    <? if (empty($set1)) {?>
        <h3><?=LangAdmin::get('empty')?>!<h3>
    <? } else { ?>
    <h3><?=LangAdmin::get('list_of_products_in_the_compilation')?></h3>
    <ul id="sortable" class="sortable">
        <? foreach ($set1 as $item): ?>
            <li class="sortlist" id="rec<?=$item['id']?>">
                <? require TPL_DIR."randomset/_item.php"; ?>
            </li> 
        <? endforeach; ?>
    </ul>
    <? } ?>
    <br/>
    <div id="save1" style="display:none;float:right;">
        <form action="<?=BASE_DIR;?>index.php?sid=<?=$GLOBALS['ssid'];?>&amp;do=saveorder&amp;cmd=randomset" method="post">
            <input type="hidden" name="ids" value=""/>
            <input type="hidden" name="catid" value="<?=$catid?>"/>
            <input type="hidden" name="return" value="<?= $_SERVER['REQUEST_URI'] ?>" />
            <button type="submit" name="submit" value="save"><?=LangAdmin::get('save_citations')?></button>
        </form>
    </div>

    <br/>
    <h3><?=LangAdmin::get('addition')?></h3>
    <small class="ihint"><?=LangAdmin::get('add_item_to_set_title')?></small>
    <form id="form1" action="<?=BASE_DIR;?>index.php?sid=<?=$GLOBALS['ssid'];?>&amp;do=add&amp;cmd=randomset" method="post">
        <input type="text" name="itemid" value="" class="text ui-widget-content ui-corner-all"/><br><br>
        <input type="hidden" name="catid" value="<?=$catid?>"/>
        <button type="submit" name="submit" value="add"><?=LangAdmin::get('add_an_item')?></button>
    </form>
    <br/><br/>
    <form id="form1del" action="<?=BASE_DIR;?>index.php?sid=<?=$GLOBALS['ssid'];?>&amp;do=delall&amp;cmd=randomset" method="post">
        <input type="hidden" name="catid" value="<?=$catid?>"/>
        <button type="submit" name="submit" value="delete-all"><?=LangAdmin::get('clear_all')?></button>
    </form>
</div>