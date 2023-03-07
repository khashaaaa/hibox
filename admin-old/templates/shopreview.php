<?
include ("header.php");

?>
<link rel="stylesheet" href="css/shopreview.css" type="text/css" />
<script type="text/javascript">
function edit(param,answer)
{
	$("#set_answer").show();
	$("#data_answer").val(answer);
	$("#id").val(param);
}
</script>
<div id="set_answer">
  <form action="index.php?cmd=shopreview&do=answer" method="post">
    <label><?=LangAdmin::get('shopreview_answer_set')?>:</label><br>
    <textarea name="txt" id="data_answer" cols="60" rows="4"></textarea><br>
    <input name="id" id="id" type="hidden" value="" />
    <input name="" type="submit" value="<?=LangAdmin::get('save')?>" />
    <input name="" type="button" value="<?=LangAdmin::get('cancel')?>" onclick="$('#set_answer').hide();"/>
  </form>
</div>

<div class="main"><div class="canvas clrfix">

	<? if ($status) { ?>
    <h1> <?=LangAdmin::get('shopreview')?> </h1>
    <p>
    </p>
    <table>
        <tr>            
            <th width="600"><?=LangAdmin::get('shopreview_comments')?></th>
		  <th><?=LangAdmin::get('creation_time')?></th>
			<th><?=LangAdmin::get('created_by')?></th>
			<th><?=LangAdmin::get('actions')?></th>
		</tr>
        <? if(is_array($reviews)) foreach($reviews as $comment) { ?>
            <tr>            
		    <td style="<?=$comment['accepted'] ? '' : 'border-left:#F00 2px solid;'?>">
			<div class="small_txt"><?=LangAdmin::get('shopreview_comment')?>:</div><?=$this->escape($comment['text'])?><br>
            <div class="small_txt"><?=LangAdmin::get('shopreview_answer')?>:</div><?=$this->escape($comment['answer'])?><br>
            </td>
				<td><?=$comment['created']?></td>
				<td><?=$comment['name']?></td>
				<td> 
                <a onclick="edit('<?=$comment['review_id']?>','<?=$comment['answer']?>');" href="#">Ответить.</a>&nbsp;&nbsp;&nbsp;           	
				<? if (!$comment['accepted']) { ?>
                <a  onclick="return confirm('<?=LangAdmin::get('want_to_accept')?>');" href="?cmd=shopreview&do=accept&id=<?=$comment['review_id']?>"><?=LangAdmin::get('accept')?></a>&nbsp;&nbsp;&nbsp;
                <?  } ?>
				<a onclick="return confirm('<?=LangAdmin::get('want_to_delete_comment')?>');" href="?cmd=shopreview&do=del&id=<?=$comment['review_id']?>"><?=LangAdmin::get('remove')?></a>&nbsp;&nbsp;&nbsp;
            &nbsp;</td>
            </tr>
        <? } ?>
    </table>
<!--    <input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all" value="--><?//=LangAdmin::get('add_news')?><!--" onclick="location.href='?cmd=news&do=add'"/>-->
	
	<!-- .pager -->
	<?if ($count != 0) { ?>
		<? $curpage = floor($from/$perpage)+1; ?>
		<? $maxpage = ceil($count/$perpage); ?> 
		<table><tfoot><tr>
		<td class="pagination">       

		<? if ($curpage > 3) { ?>
			<a href="<?=$pageurl?>&from=0" class="curved">1</a>
		<? } ?>

		<? if ($curpage > 4) { ?>
			<span class="curved">...</span>
		<? } ?>

		<? for ($i = max(1, $curpage - 2); $i<=min($maxpage, $curpage+2); $i++) { ?>
			<? if ($curpage == $i) { ?>
				<span class="active curved"><?=$i?></span>
			<? } else { ?>
				<a href="<?=$pageurl?>&from=<?=(($i-1)*$perpage)?>" class="curved"><?=$i?></a>
			<? } ?>
		<? } ?>

		<? if ($curpage < $maxpage - 3) { ?>
			<span class="curved">...</span>
		<? } ?>

		<? if ($curpage < $maxpage - 2) { ?>
			<a href="<?=$pageurl?>&from=<?=(($maxpage-1)*$perpage)?>" class="curved"><?=$maxpage?></a>
		<? } ?>
		</td>
		</tr></tfoot></table>
		<!-- /.pager -->
	<? } ?>
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

<?
include ("footer.php");
?>

