<?
    $sid = @$_SESSION['sid'];
    if(isset($GLOBALS['EmptyCats']))
    $EmptyCats = $GLOBALS['EmptyCats'];

?>
<link rel="stylesheet" href="css/EmptyCats.css" type="text/css" media="screen" charset="utf-8">
<script type='text/javascript' src='js/EmptyCats.js'></script>
<div id="attention"><?=LangAdmin::get('DeletedCatsAtte')?> <a href="<?=CFG_TOOLS_URL?>/categories/" target="_blank"><?=LangAdmin::get('DeletedCatsUrl')?></a>.</div><br>

<a id="UlClick" onclick="ShowEmptyCars();"><?=LangAdmin::get('DeletedCats')?>(<? echo count($EmptyCats) ?>):</a>
<div id="ULEmpty" style="display:block; width:100%; height:0px; overflow:hidden;">
  <ul>
<?
      $showempty = '';
	if (is_array($EmptyCats)) {
		$size = count($EmptyCats);
		foreach($EmptyCats as $cat) {
			if ($cat['name']=="") { $cat['name']=LangAdmin::get('NoName'); }
			if ($cat['isparent']=='true') {
				$showempty.="<li id=\"parentempty\">".$cat['name']." (".$cat['id'].") <a href=\"../index.php?p=category&cid=".$cat['id']."\" target=\"_blank\">".LangAdmin::get('DeletedCatsUrlClick')."</a><ul>";
				$tmp_id=$cat['id'];
				foreach($EmptyCats as $subcat) {
					  if ($subcat['parentid']==$tmp_id) {
						  if ($subcat['name']=="") { $subcat['name']="Без названия"; }
						  $showempty.="<li>".$subcat['name']." (".$subcat['id'].") <a href=\"../index.php?p=category&cid=".$subcat['id']."\" target=\"_blank\">".LangAdmin::get('DeletedCatsUrlClick')."</a></li>";
					  }

				}
				$showempty."</ul></li>";
			} else {
				$showempty.="<li id=\"empty\">".$cat['name']." (".$cat['id'].") <a href=\"../index.php?p=category&cid=".$cat['id']."\" target=\"_blank\">".LangAdmin::get('DeletedCatsUrlClick')."</a></li>";
			}

		}
	}
    echo @$showempty;


?>
</ul>
</div>
