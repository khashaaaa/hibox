<style>
.red b{
	color:#F00; 
}
.purple b{
	color:#90F; 
}
</style>
<?
$sid = @$_SESSION['sid'];

$i=0;

if(isset($GLOBALS['cats']))
    $cats = $GLOBALS['cats'];

?>

<?
if (is_array($cats)) {
    $size = count($cats);
    foreach ($cats as $cat) {
        $i++;
        if (!isset($parentid)) $parentid = 0;
        $cat['parentid'] = $parentid;
		if ($cat['deletestatus']=="Deleted") { $stle="red"; }
		if ($cat['deletestatus']=="None") { $stle=""; }
		if ($cat['deletestatus']=="ParentOfDeleted")  { $stle="purple"; }
		if ($cat['deletestatus']=="ParentOfHiddenDeleted") { $stle="#FF5C5C"; }
?>
    <div id="cat<?=$cat['id']?>" <? if ($cat['ishidden']=='true') { ?>class="hidden2"<? if (!@$_COOKIE['HiddenState']) {?> style="display:none"<?} } ?>>
      <div class="folder">
        <? if (($cat['isparent'] == 'false') or ($cat['deletestatus']=="Deleted")) { ?>
            <span class="cname <? if ($cat['ishidden']=='true') { ?> hidden<? } ?>" id="cname_<?=$cat['id']?>">
                <img src="<?=TPL_DIR;?>i/folder2.jpg" width="21" height="21" alt="0" title="" align="middle" id="spinner_<?=$cat['id']?>" />
        <? } else { ?>
            <span class="cname <? if ($cat['ishidden']=='true') { ?> hidden<? } ?>" id="cname_<?=$cat['id']?>" onclick="subcat('<?=$cat['id']?>','<?=$cat['parentid']?>')">
                <img src="<?=TPL_DIR;?>i/folder.jpg" width="21" height="21" alt="1" title="" align="middle" id="spinner_<?=$cat['id']?>" />
        <? } ?>
                <?/*<a><? if ($cat['externalid'] != $cat['id']){?><b>(<?=$cat['id']?>)</b><? if ($cat['externalid']){?> <b>[<?=$cat['externalid']?>]</b><?}?><?}else{?><b>[(<?=$cat['id']?>)]</b><?}?> <?=$cat['name']?></a>*/?>
                <a class="linktext" style="color: <?=@$stle?>"><b><?=$cat['name']?></b><?if ($cat['approxweight']){?> (прим. вес: <?=(float)$cat['approxweight']?> кг.)<?}?> <? if ($cat['externalid']){?> [<?=$cat['externalid']?>]<?}?></a>
            </span>
        <input type="hidden" class="edfld" id="editf_<?=$cat['id']?>" value="<?=$cat['name']?>">
        <input type="hidden" class="edfld" id="edit2f_<?=$cat['id']?>" value="<?=$cat['externalid']?>">
        <input type="hidden" class="edfld" id="edit2_apprw_<?=$cat['id']?>" value="<?=$cat['approxweight']?>" />
        <input type="hidden" class="edfld" id="edit2al_<?=$cat['id']?>" value="<?=@$cat['alias']?>" />

        <input type="hidden" class="edfld" id="edit2st_<?=$cat['id']?>" value="<?=@$cat['seo']['pagetitle']?>">
        <input type="hidden" class="edfld" id="edit2sk_<?=$cat['id']?>" value="<?=@$cat['seo']['seo_keywords']?>">
        <input type="hidden" class="edfld" id="edit2sd_<?=$cat['id']?>" value="<?=@$cat['seo']['seo_description']?>">
        <input type="hidden" class="edfld" id="edit2sp_<?=$cat['id']?>" value="<?=@$cat['seo']['seo_title']?>">

          <div style="float:right; cursor:pointer;" class="windialog"> 
            <img src="<?=TPL_DIR;?>i/uparrow.png" alt="<?=LangAdmin::get('up')?>" title="<?=LangAdmin::get('up')?>" align="middle" class="buttons" onclick="order('<?=$cat['id']?>','<?=$cat['parentid']?>','<?=$i?>','up')">
            
            <img src="<?=TPL_DIR;?>i/downarrow.png" alt="<?=LangAdmin::get('down')?>" title="<?=LangAdmin::get('down')?>" align="middle" class="buttons" onclick="order('<?=$cat['id']?>','<?=$cat['parentid']?>','<?=$i?>','down')">
            
            <? if($cat['ishidden']=='true') { ?>
                <img alt="0" id="vis_<?=$cat['id']?>" src="<?=TPL_DIR;?>i/unpublish.png" title="<?=LangAdmin::get('show')?>" align="middle"  class="buttons" onclick="change_visible('<?=$cat['id']?>','<?=$cat['parentid']?>')">
            <? } else { ?>
                <img alt="1" id="vis_<?=$cat['id']?>" src="<?=TPL_DIR;?>i/publish.png" title="<?=LangAdmin::get('hide')?>" align="middle"  class="buttons" onclick="change_visible('<?=$cat['id']?>','<?=$cat['parentid']?>')">
            <? } ?>
            
            <img src="<?=TPL_DIR;?>i/edit-icon.png" alt="<?=LangAdmin::get('edit')?>" title="<?=LangAdmin::get('edit')?>" align="middle" class="buttons" onclick="showedit('<?=$cat['id']?>','<?=$cat['parentid']?>')">
            <img class="buttons" onclick="showaddform('<?=$cat['id']?>', '<?=($cat['isparent'] == 'false' ? $cat['parentid'] : $cat['id'])?>')" src="<?=TPL_DIR;?>i/plus.gif" width="16" height="16" align="middle" title="<?=LangAdmin::get('add_a_subcategory')?>">
            <img class="buttons" onclick="showdelform('<?=$cat['id']?>', '<?=$cat['parentid']?>')" src="<?=TPL_DIR;?>i/del.png" width="14" height="14" align="middle" title="<?=LangAdmin::get('drop_category')?>">

            <img class="buttons" onclick="show_edit_search_filter('<?=$cat['id']?>')" src="<?=TPL_DIR;?>i/edit-icon.png" width="14" height="14" align="middle" title="<?=LangAdmin::get('edit_filters_category')?>" alt="" />

            <a class="buttons" target="_blank" href="../<?=UrlGenerator::generateFullCatUrl('subcategory',$cat,'','')?>"><img src="<?=TPL_DIR;?>i/preview.png" alt="<?=LangAdmin::get('review')?>" title="<?=LangAdmin::get('review')?>" align="middle"/></a>
              <a class="buttons edit-seo-text" target="_blank" href="#" cid="<?=$cat['id']?>">
                  <img src="<?=TPL_DIR;?>i/text.png"
                       height="20"
                       alt="<?=LangAdmin::get('seo_text_edit')?>"
                       title="<?=LangAdmin::get('seo_text_edit')?>" align="middle"/>
              </a>
              <?if(in_array('Seo2', General::$enabledFeatures)){?>
            <?}?>
          </div>
      </div>
      <div id="incat<?=$cat['id']?>" class="" style="display:none;margin-left:30px;"></div>
    </div>

<? } } ?>