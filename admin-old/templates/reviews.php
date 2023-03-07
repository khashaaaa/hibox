<?
include ("header.php");

$pages = array(10, 20, 50, 100);

?>
<div class="main"><div class="canvas clrfix">
    <h3 style="color:red;"><?=LangAdmin::get('Use_new_admin_panel_to_control_reviews')?></h3>
    <a href="/admin/?cmd=reviews&do=default" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-hover" role="button" aria-disabled="false"><span class="ui-button-text"><?=LangAdmin::get('go_to_new_admin')?></span></a>
    <!---
	<? if(in_array('Seo2', General::$enabledFeatures)){
	$url = 'item?';
} else {
	$url = 'index.php?p=item&';
} ?>
	<? if ($status) { ?>
    <h1> <?=LangAdmin::get('reviews')?> </h1>
    <div style="float:right;">
        <form id="perPager" action="index.php" method="get">
            <input type="hidden" value="reviews" name="cmd"/>
            <select name="ps" id="pages">
                <? foreach ($pages as $perpagecount) { ?>
                    <? $selected = ($perpagecount == $perPage) ? ' selected' : ''; ?>
                        <option value="<?=$perpagecount?>" <?=$selected?>><?=$perpagecount?></option>
                    <? } ?>
            </select> 
        </form>
        
    </div>
    <p>
    </p>
    <table>
        <tr>
            <th>ID</th>
            <th><?=LangAdmin::get('comment_buyer_to_the_product')?></th>
			<th><?=LangAdmin::get('creation_time')?></th>
			<th><?=LangAdmin::get('created_by')?></th>
			<th><?=LangAdmin::get('actions')?></th>
		</tr>
        <? if(is_array($comments)) foreach($comments as $comment) { ?>
            <tr>
            <td><?=$comment['review_id']?></td>
				<td><?=htmlspecialchars($comment['text'])?></td>
				<td><?=$comment['created']?></td>
				<td><?=$comment['name']?></td>
				<td>
            	<a href="../<?=$url?>id=<?=$comment['item_id']?>&reviewtab" target="_blank"><?=LangAdmin::get('go')?></a>&nbsp;&nbsp;&nbsp;
				<a  onclick="return confirm('<?=LangAdmin::get('want_to_accept_comment')?>?\r\n&quot;<?=$comment['text']?>&quot;');" href="?cmd=reviews&do=accept&id=<?=$comment['review_id']?>"><?=LangAdmin::get('accept')?></a>&nbsp;&nbsp;&nbsp;
				<a onclick="return confirm('<?=LangAdmin::get('want_to_delete_comment')?>?\r\n&quot;<?=$comment['text']?>&quot;');" href="?cmd=reviews&do=del&id=<?=$comment['review_id']?>"><?=LangAdmin::get('remove')?></a>&nbsp;&nbsp;&nbsp;
            &nbsp;</td>
            </tr>
        <? } ?>
    </table>
<!--    <input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all" value="--><?//=LangAdmin::get('add_news')?><!--" onclick="location.href='?cmd=news&do=add'"/>-->
	<!-- .pager --><!--
    <? if (! empty($pagination['pages'])) { ?>
        
        <div class="pagination">
            <?if($pagination['pages'][0]>1){?>
                <a class="curved" href="<?=$pageurl?>&p=1">1</a> ...
            <?}?>

            <? foreach ($pagination['pages'] as $i) { ?>
                <? if ($pagination['last'] == 1) break; ?>
                <? if ($pagination['current'] == $i) { ?>
                    <span class="active curved"><?=$i?></span>
                <? } else { ?>
                    <a class="curved" href="<?=$pageurl?>&p=<?=$i?>"><?=$i?></a>
                <? } ?>
            <? } ?>
            <?if($i<$pagination['last']){?>
                ... <a class="curved" href="<?=$pageurl?>&p=<?=$pagination['last']?>"><?=$pagination['last']?></a>
            <?}?>
        </div>
    <?}?>
	<!-- /.pager -->

	<? } else { ?>
    <p><?=LangAdmin::get('error_connecting_to_database')?>.</p>
    <p><?=LangAdmin::get('check_configcustom_for_correct_db_accesses')?>.</p>
    <p><?=LangAdmin::get('example')?>:</p>
    <pre>
    define('DB_HOST', 'localhost');
    define('DB_USER', 'otbox');
    define('DB_PASS', '*******');
    define('DB_BASE', 'otbox');
    </pre>
<? } ?>
    
</div></div>
<script type="text/javascript" src="js/reviews.js"></script>
<?
include ("footer.php");
?>

