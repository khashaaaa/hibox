<?
include ("header.php");

?>
<div class="main"><div class="canvas clrfix">

<? if ($status) { ?>
    <h1> <?=LangAdmin::get('digest')?> </h1>
    <p>
    </p>
    <input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all" value="<?=LangAdmin::get('add')?>" onclick="location.href='?cmd=digest&do=add'"/>
    <table>
        <tr>
            <th>ID</th>
            <th><?=LangAdmin::get('title')?></th>
            <th><?=LangAdmin::get('category')?></th>
            <th><?=LangAdmin::get('image')?></th>
            <th><?=LangAdmin::get('language')?></th>
            <th><?=LangAdmin::get('actions')?></th>
        </tr>
        
        <?if(is_array($digest)) foreach($digest as $dig) { ?>
            <tr>
            <td><?=$dig['id']?></td>
            <td><?=$dig['title']?></td>
            <td><a href='#' alt="<?=$dig['cat_desc']?>"><?=$dig['cat_title']?></a></td>
            <td><img src="<?=DigestRepository::getImage($dig['image'], "thumb")?>" width="100"/></td>
            <td><? if(!@$dig['lang_id']) { print LangAdmin::get('not_selected'); } else { print $dig['lang_name']; }?></td>
            <td>
            <a href="../index.php?p=post&id=<?=$dig['id']?>" target="_blank"><?=LangAdmin::get('show')?></a>&nbsp;&nbsp;&nbsp;
            <a href="?cmd=digest&do=edit&id=<?=$dig['id']?>"><?=LangAdmin::get('change')?></a>&nbsp;&nbsp;&nbsp;
            <a href="?cmd=digest&do=del&id=<?=$dig['id']?>"><?=LangAdmin::get('remove')?></a>&nbsp;&nbsp;</td>
            </tr>
        <? } ?>
    </table>
    <table>
		<tr>
        	<td colspan="6" class="pagination">
                <? $curpage = $page+1; ?>
                <? $maxpage = ceil($CountPosts / $page_count); ?>
				<? if ($maxpage!=1) { ?>
                	<? for ($i = 1; $i <= $maxpage; $i++) { ?>
                	<? if ($curpage == $i) { ?>
                    	<span class="active curved"><?=$i?></span>
                    	<? } else { ?>
                    	<a class="curved" href="?cmd=digest&page=<?=$i?>"><?=$i?></a>
                    	<? } ?>
                	<? } ?>
                <? } ?>   
            </td>
        </tr>
	</table>
    <input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all" value="<?=LangAdmin::get('add')?>" onclick="location.href='?cmd=digest&do=add'"/>
    
    
    
<? } else { ?>
    <p><?=LangAdmin::get('error_connecting_to_database')?>.</p>
    <p><?=LangAdmin::get('check_configcustom_for_correct_db_accesses')?>.</p>
<? } ?>
    
</div></div>

<?
include ("footer.php");
?>

