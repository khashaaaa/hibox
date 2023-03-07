<div id="dialog-form" title="<?=LangAdmin::get('error')?>!">
    <span id="basketinfo"></span>
</div>

<div class="main"><div class="canvas clrfix" id="overlay-wrapper">
    <h1>Восстановление файлов сайта за ранний период.</h1>
    <? if (count($recoverinfo)!=0) { ?>
      <span>Досупные данные для восстановления:</span>
      <div id="recoverdone" style="display:none; color: #C00;"></div>
      <div id="listfiles">
      	<? foreach( $recoverinfo as $file ) { ?>
        <div id="file" style="display:block; width:95%; height:50px; border:#CCC 1px solid; margin:5px; padding:10px;">
        	Резервная копия за <?=date("j.m.Y H:i:s", $file['date']);?> (<?=$file['name'];?>)<br>
        	<a href="#" onclick="Recover('<?=$file['name'];?>');">Восстановить.</a> </div>
        <? } ?>
      
      </div>
      <div id="loader" style="display:none;">      
   	  	<img src="../../css/i/ajax-loader.gif" width="16" height="11"  />
      </div>
    <? } else { ?>
      <span>У Вам нет данных для восстановления.</span>
    <?  } ?>
   
</div></div>

