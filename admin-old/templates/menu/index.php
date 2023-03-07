<?
include (dirname(dirname(__FILE__))."/header.php");

?>
<script type="text/javascript" src="js/lang.js"></script>

<div class="main"><div class="canvas clrfix">
        
        <div class="col700">
            <div class="tuning">
                <span id="error" style="color:red;font-weight: bold;">
                    <? if(isset($error)) { print $error; } ?>
                </span>
                <h1> <?=LangAdmin::get('menu_manager')?> </h1>
                <p></p>
                <form id="choose-lang">
                    <input type="hidden" name="lang" value="" />
                    <input type="hidden" name="cmd" value="cmsadmin" />
                    <input type="hidden" name="do" value="menu" />
                </form>
                <? foreach($langs as $l){ ?>
                    <? if($l['lang_code']==$current_lang) {$active = 'active'; $current_lang_full = $l['lang_name'];} else $active = ''; ?>
                    <input type="button" lang="<?=  htmlspecialchars($l['lang_code'])?>" value="<?=$l['lang_name']?>" class="choose-lang-btn <?=$active?>" />
                <? } ?>
                <br />
                
                <br/><br/>
                <h2 class="mt30"><?=LangAdmin::get('top_menu')?></h2>

                <? if(!$all_docs && !$top_menu){ ?>
                <b><?=LangAdmin::get('no_docs')?> <?=$current_lang_full?></b>
                <? }else{  ?>
                <ul class="top-menu-list deliveries-list">
                    <li class="stay-always"><?=LangAdmin::get('used_docs')?></li>
                    <?
                    $enabledDels = array();
                    ?>
                    <? if(is_array($top_menu)) foreach($top_menu as $d){ ?>
                        <?
                        $enabledDels[] = $d;
                        if(is_array($all_docs)){
                            foreach($all_docs as $d1){
                                if($d1['id'] == $d){
                                    break;
                                }
                            }
                        }
                        ?>
                        <li doc="<?=$d?>"><?=$d1['title']?></li>
                    <? } ?>
                            
                    <li class="stay"><?=LangAdmin::get('unused_docs')?></li>
                    <? if(is_array($all_docs)) foreach($all_docs as $d){ ?>
                        <? if(!in_array($d['id'], $enabledDels)){ ?>
                            <li doc="<?=$d['id']?>"><?=$d['title']?></li>
                        <? } ?>
                    <? } ?>
                </ul>
                
                
                <br />
                <form action="index.php?cmd=cmsadmin&do=menusave" method="post">
                    <input type="hidden" name="TopMenu" value='<?=$top_menu_json?>' />
                    <div class="fbut clrfix">	
                        <input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all" value="<?=LangAdmin::get('save')?>"/>
                    </div>
                </form>
                <? } ?>


                <br/><br/>
                <h2 class="mt30"><?=LangAdmin::get('left_menu')?></h2>
                <p>(<?=LangAdmin::get('left_menu_manual')?>)</p>

                <? if(!$all_docs && !$left_menu){ ?>
                <b><?=LangAdmin::get('no_docs')?> <?=$current_lang_full?></b>
                <? }else{  ?>
                <ul class="left-menu-list deliveries-list">
                    <li class="stay-always"><?=LangAdmin::get('used_docs')?></li>
                    <?
                    $enabledDels = array();
                    ?>
                    <? if(is_array($left_menu)) foreach($left_menu as $d){ ?>
                    <?
                    $enabledDels[] = $d;
                    if(is_array($all_docs)){
                        foreach($all_docs as $d1){
                            if($d1['id'] == $d){
                                break;
                            }
                        }
                    }
                    ?>
                    <li doc="<?=$d?>"><?=$d1['title']?></li>
                    <? } ?>

                    <li class="stay"><?=LangAdmin::get('unused_docs')?></li>
                    <? if(is_array($all_docs)) foreach($all_docs as $d){ ?>
                    <? if(!in_array($d['id'], $enabledDels)){ ?>
                        <li doc="<?=$d['id']?>"><?=$d['title']?></li>
                        <? } ?>
                    <? } ?>
                </ul>


                <br />
                <form action="index.php?cmd=cmsadmin&do=menusave" method="post">
                    <input type="hidden" name="LeftMenu" value='<?=$left_menu_json?>' />
                    <div class="fbut clrfix">
                        <input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all" value="<?=LangAdmin::get('save')?>"/>
                    </div>
                </form>
                <? } ?>

            </div>
        </div>
        
</div></div><!-- /.main -->

<script type="text/javascript" src="js/jquery.combobox.js"></script>
<script type="text/javascript" src="js/json.js"></script>

<script type="text/javascript">
$(function(){
    $(".top-menu-list").sortable({
        items: 'li:not(.stay-always)',
        cancel: ".stay",
        stop:function (event, ui) {
            var dels = [];
            $('.top-menu-list li:gt(0)').each(function(){
                if( !$(this).hasClass('stay') ){
                    dels[dels.length] = $(this).attr('doc');
                }
                else{
                    return false;
                }
            });
            $('[name="TopMenu"]').val(JSON.stringify(dels));
            console.log(JSON.stringify(dels));
        }
    });
    $(".top-menu-list li").disableSelection();

    $(".left-menu-list").sortable({
        items: 'li:not(.stay-always)',
        cancel: ".stay",
        stop:function (event, ui) {
            var dels = [];
            $('.left-menu-list li:gt(0)').each(function(){
                if( !$(this).hasClass('stay') ){
                    dels[dels.length] = $(this).attr('doc');
                }
                else{
                    return false;
                }
            });
            $('[name="LeftMenu"]').val(JSON.stringify(dels));
            console.log(JSON.stringify(dels));
        }
    });
    $(".left-menu-list li").disableSelection();

});
</script>

<?
include (dirname(dirname(__FILE__))."/footer.php");
?>
