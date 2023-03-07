<?

$sid = @$_SESSION['sid'];
$i=0;
if(isset($GLOBALS['cats']))
    $cats = $GLOBALS['cats'];

?>

<? if (is_array($cats)) { $size = count($cats); foreach($cats as $cat) {
        $i++;
        if (!isset($parentid)) $parentid = 0;
        $cat['parentid'] = $parentid;
        ?>
    <div id="cat<?=$cat['id']?>" <? if ($cat['ishidden']=='true') { ?>class="hidden2"<? if (!@$_COOKIE['HiddenState']) {?> style="display:none"<?} } ?>>
      <div class="folder">
          
        <? if ($cat['isparent'] == 'false') { ?>
            <span  class="cname <? if ($cat['ishidden']=='true') { ?> hidden<? } ?>" id="cname_<?=$cat['id']?>">
                <input type="radio" name="bind_cat" value="<?=$cat['id']?>"/>
                <img src="<?=TPL_DIR;?>i/folder2.jpg" width="21" height="21" alt="0" title="" align="middle" id="spinner_<?=$cat['id']?>" />
                <a class="linktext"><b><?=$cat['name']?></b><? if ($cat['externalid']){?> [<?=$cat['externalid']?>]<?}?></a>
        <? } else { ?>
            <span class="cname <? if ($cat['ishidden']=='true') { ?> hidden<? } ?>" id="cname_<?=$cat['id']?>" >
                <input type="radio" name="bind_cat" value="<?=$cat['id']?>"/>
                <img src="<?=TPL_DIR;?>i/folder.jpg" width="21" height="21" alt="1" title="" align="middle" id="spinner_<?=$cat['id']?>" />
                <a onclick="subcat('<?=$cat['id']?>','<?=$cat['parentid']?>')" class="linktext"><b><?=$cat['name']?></b><? if ($cat['externalid']){?> [<?=$cat['externalid']?>]<?}?></a>
        <? } ?>
            
        </span>

      </div>
      <div id="incat<?=$cat['id']?>" class="" style="display:none;margin-left:30px;"></div>
    </div>

<? } } ?>