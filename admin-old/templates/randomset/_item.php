<table class="valign-top nowidth">
<tr>
    <td width="50"><a href="index.php?p=item&id=<?=$item['id'];?>"><img src="<?=$item['mainpictureurl'];?>_40x40.jpg" alt="" width="40px" height="40px"/></a></td>
    <td width="600">
        <a target="_blank" href="<?=$item['externalitemurl'];?>" ><?=$item['title'];?></a>
    </td>
    
    <td width="10">
        <a href="#" class="edit-name" rel="<?=$item['id'];?>">
            <?=LangAdmin::get('amend_the_title')?>
        </a>
    </td>
    <td width="10">
        <a href="#" class="edit-descr" rel="<?=$item['id'];?>">
            <?=LangAdmin::get('change_the_description_of')?>
        </a>
    </td>
        
    <td width="10"><a href="<?=BASE_DIR;?>index.php?sid=<?=$GLOBALS['ssid'];?>&amp;do=del&amp;catid=<?=$catid?>&amp;cmd=Randomset&amp;id=<?=$item['id'];?>">
        <img src="<?=TPL_DIR;?>i/del.png" width="12" height="12" align="middle" style="vertical-align:middle"/>
    </a></td>
</tr>
</table>