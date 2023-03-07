<?
include ("header.php");
?>
	
    <style>
		input[type=checkbox] {
			width:auto;
		}
		.time_select {
			width:auto !important;
		}
	</style>

    <h1><?=LangAdmin::get('Caching_settings')?></h1>
    <?
					//Стандпртнык значения чтобы не было ощибок если не заданы
					if(!isset($site_conf['caching_time_hours']))  { $site_conf['caching_time_hours']='12'; $firstlaunch="<br>".LangAdmin::get('CautionLaunch'); } 
					if(!isset($site_conf['caching_time_minutes']))  $site_conf['caching_time_minutes']='30';
					
					if(!isset($site_conf['caching_lvl1']))  $site_conf['caching_lvl1']='0';
					if(!isset($site_conf['caching_lvl2']))  $site_conf['caching_lvl2']='0';
					if(!isset($site_conf['caching_lvl3']))  $site_conf['caching_lvl3']='0';
					
					?>
    <div class="canvas clrfix">
     <div style=" width:930px; height:50px; border:#F00 3px solid; background:#CCC;  padding:10px; font-size:14px; font-weight:700;"><?=LangAdmin::get('CautionUse'); if (isset($firstlaunch)) echo $firstlaunch; ?> </div>
        <form action="<?=BASE_DIR;?>index.php?do=saveSettings&amp;cmd=caching" method="post">
            <div class="grid_16">
            	<p>
                    
                	<span style="margin-right:40px;"><?=LangAdmin::get('Caching_day_time')?>:</span>
                    <strong><?=LangAdmin::get('Hours')?></strong> : 
                    <strong><?=LangAdmin::get('Minutes')?></strong> / 
                    <select class="time_select" id="caching[time-hours]" name="caching[time-hours]">
                        <?php for($i=0;$i<24;$i++): ?>
                            <?php $name = ($i<10)? '0'.$i : $i; ?>
                            <option value="<?php echo $i; ?>"
								<?php  if($site_conf['caching_time_hours']==$i) echo 'selected="selected"'; ?> 
							>
								<?php echo $name; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                    :
                    <select class="time_select" id="caching[time-minutes]" name="caching[time-minutes]">
                        <?php for($i=0;$i<60;$i++): ?>
                            <?php $name = ($i<10)? '0'.$i : $i; ?>
                            <option value="<?php echo $i; ?>" 
								<?php  if($site_conf['caching_time_minutes']==$i) echo 'selected="selected"'; ?> 
							>
                                <?php echo $name; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </p>
            </div>
            <div class="grid_3">
            	<p><?=LangAdmin::get('list_to_cache')?></p>
            </div>
            <div class="grid_3">
                <input id="caching[lvl1]" type="checkbox" name="caching[lvl1]"
                	<?php if($site_conf['caching_lvl1'] == 1){ echo 'checked="checked"'; } ?>  />
                <label for="caching[lvl1]"><?=LangAdmin::get('Main_pages')?></label>	
            </div>
            <div class="grid_3">
            	<input id="caching[lvl2]" type="checkbox" name="caching[lvl2]" 
                	<?php if($site_conf['caching_lvl2'] == 1){ echo 'checked="checked"'; } ?>  />   
                <label for="caching[lvl2]"><?=LangAdmin::get('Second_level_pages')?></label>   	
            </div>
            <div class="grid_3">
            	<input id="caching[lvl3]" type="checkbox" name="caching[lvl3]" 
                	<?php if($site_conf['caching_lvl3'] == 1){ echo 'checked="checked"'; } ?>  />
                <label for="caching[lvl3]"><?=LangAdmin::get('Third_level_pages')?></label>    	
            </div>
             
            <div class="grid_16">
            	<input type="submit" value="<?=LangAdmin::get('save')?>" />
            </div>
        </form>
    </div>


<?
include ("footer.php");
?>