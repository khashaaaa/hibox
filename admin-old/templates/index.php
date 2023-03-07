<?
include ("header.php");

$types = array("Mixed" => LangAdmin::get('mixed_catmode_descr'), 
    "Internal" => LangAdmin::get('internal_catmode_descr'),
    "External"=> LangAdmin::get('external_catmode_descr'),
    "LeafMixed"=> LangAdmin::get('leafmixed_catmode_descr'), 
    "Predefined" => LangAdmin::get('predefined_catmode_descr'));
?>
     

<div class="col700">
    <div class="tuning">
        <form action="<?=BASE_DIR;?>index.php?sid=<?=$GLOBALS['ssid'];?>&amp;do=savesett&amp;cmd=Control" method="post">
            <? if (count($data->Settings->TranslateTypes->NamedProperty) > 1) { ?>
            <h2 class="mt30"><?=LangAdmin::get('drive_mode')?></h2>
            <small class="ihint"><?=LangAdmin::get('manual_translation_uses_a_manually_compiled_dictionary')?>.<br>
            <?=LangAdmin::get('automatic_translation_using_original_or_custom_categories_taobao')?>.</small>
            <br/>
            <select name="translate_type" id="">
                <?
                foreach($data->Settings->TranslateTypes->NamedProperty as $v){
                    $type = ''.$v->Name;
                    $type_desc = ''.$v->Description;
                    $sel_type = ''.$data->Settings->SelectedTranslateType;
                    $selected = ($type == $sel_type)? 'selected' : '';

                    echo '<option '.$selected.' value="'.$type.'">'.$type_desc.'</option>';
                }
                ?>
            </select>
            <br/>
            <? } ?>

            <h2 class="mt30"><?=LangAdmin::get('selecting_the_operating_mode_categories')?></h2>
            <select name="StructureType" id="structuretype" onchange="onChangeType()">
                <?
                $selected_mode = '';

                foreach($data->Settings->CategoryStructureTypes->NamedProperty as $v)
                {
                    $type = (string)$v->Name;
                    $type_desc = $v->Description;
                    //$type_desc = substr($type_desc, 0, strpos($type_desc, '.'));
                    $sel_type = (string)$data->Settings->SelectedCategoryStructureType;
                    $selected = ($type == $sel_type)? 'selected' : '';
                    if($selected == 'selected') $selected_mode = $type;

                    echo '<option '.$selected.' value="'.$type.'">'.$type_desc.'</option>';
                }
                ?>
            </select><br/>
            <?=LangAdmin::get('description_of_mode')?>:<br/>
            <small class="ihint" id="mode_desc"></small><br/><br/>




<div style="width:470px; float:left;">
            <h2 class="mt30"><?=LangAdmin::get('languages_​​showcase')?></h2>
			
            <ul class="deliveries-list">
                <li class="stay-always"><?=LangAdmin::get('languages_​​spoken')?><br /><small><?=LangAdmin::get('in_order_of_priority')?></small></li>
                <?
                $enabledLangs = array();
                ?>
                
                <? foreach($data->Settings->UsedLanguages->string as $lang){ ?>
                    <?
                    $enabledLangs[] = (string)$lang;
                    $langDescr = '';
                    foreach($instanceOptionsInfo['AvailableLanguages'] as $langSearch){
                        if((string)$lang == $langSearch['Name']){
                            $langDescr = $langSearch['Description'];
                            break;
                        }
                    }
                    ?>
                    <li delivery="<?=(string)$lang?>"><?=(string)$langDescr?></li>
                <? } ?>
                
                <li class="stay-always"><?=LangAdmin::get('system_languages')?></li>
                
                <?
				if (isset($data->Settings->UsedSystemLanguages))
				foreach($data->Settings->UsedSystemLanguages->string as $lang){ ?>
                    <?
                    $enabledSysLangs[] = (string)$lang;
                    $langDescr = '';
                    foreach($instanceOptionsInfo['AvailableLanguages'] as $langSearch){
                        if((string)$lang == $langSearch['Name']){
                            $langDescr = $langSearch['Description'];
                            break;
                        }
                    }
                    ?>
                    <li delivery="<?=(string)$lang?>"><?=(string)$langDescr?></li>
                <? } ?>
                
                
                
                
                <li class="stay"><?=LangAdmin::get('unused_languages')?></li>
                <? foreach($instanceOptionsInfo['AvailableLanguages'] as $lang){ ?>
                    <? if(!in_array($lang['Name'], $enabledLangs)){ ?>
                        <li delivery="<?=$lang['Name']?>"><?=$lang['Description']?></li>
                    <? } ?>
                <? } ?>
            </ul>

            <input type="hidden" name="UsedLanguages" value='<?=json_encode($enabledLangs)?>' />            

            <br/><br/>
            <div class="fbut clrfix">
                    <input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"  value="<?=LangAdmin::get('save')?>"/>
            </div>
        </form>
        <!--  -->
</div>


<div style="width:470px; float:left">
	<h2 class="mt30"><?=LangAdmin::get('available_langs')?></h2>
    <ul class="deliveries-list">    
    <? foreach($available_langs as $lang){ ?>
        <li class="stay-always"><?=$lang['name']?$lang['name']:LangAdmin::get('not_recognized')?> [<strong><?=strtoupper($lang['code'])?></strong>]</li>
    <? } ?>
    </ul>
    
</div>

<div style="clear:both"></div>
<br><br>
<div style="width:800px;">
            <h2 class="mt30"><?=LangAdmin::get('Order_of_search_providers')?></h2>
			<p><?=LangAdmin::get('need_to_clear_cache')?></p>
            <ul class="methods-list" id="sortMethods">
                <li class="stay used"><?=LangAdmin::get('Search_providers')?><br /><small><?=LangAdmin::get('in_order_of_priority')?></small></li>
                                
                <? foreach($usedSearchSettings as $method){ ?>                    
                    <li method="<?=$method?>"><?=Lang::get($method.'_Flag')?></li>
                <? } ?>
                
                <li class="stay unUsed"><?=LangAdmin::get('unused_search_providers')?></li>
                <? if (is_array($unUsedSearchSettings)) {?>
                    <? foreach($unUsedSearchSettings as $method){ ?>                    
                        <li method="<?=$method?>"><?=Lang::get($method.'_Flag')?></li>
                    <? } ?>
                <? } ?> 
            </ul>
            <form action="<?=BASE_DIR;?>index.php?sid=<?=$GLOBALS['ssid'];?>&amp;do=saveSearchMethods&amp;cmd=Control" method="post">

            <input type="hidden" name="usedMethods" id="usedMethods" value='' />            
            <input type="hidden" name="unUsedMethods" id="unUsedMethods" value='' /> 
            

            <br/><br/>
            <div class="fbut clrfix">
                    <input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"  value="<?=LangAdmin::get('save')?>"/>
            </div>
            </form>
        <!--  -->
</div>




        <br/><br/>
        <h2 class="mt30"><?=LangAdmin::get('caching')?></h2>
        <small class="ihint"><?=LangAdmin::get('some_pages_keshiruyutya')?>. <?=LangAdmin::get('it_is_possible_to_clear_cached_pages')?>.</small><br/>
        <form action="index.php?do=clearCache" method="post">
        <div class="fbut clrfix">
                <input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"  value="<?=LangAdmin::get('clear_the_cache')?>"/>
        </div>
        </form>
        <!--  -->

        <br/><br/>
        <h2 class="mt30"><?=LangAdmin::get('change_password')?></h2>
        <a href="<?=BASE_DIR;?>index.php?sid=<?=$GLOBALS['ssid'];?>&amp;cmd=Control&amp;do=changeOperatorPassword">
            <input type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"  value="<?=LangAdmin::get('change_password')?>" />
        </a>
    </div>    
</div>

<script type="text/javascript" src="js/json.js"></script>
<script type="text/javascript" src="js/jquery.combobox.js"></script>

<script type="text/javascript">
$(function(){
    $(".deliveries-list").sortable({
        items: 'li:not(.stay-always)',
        cancel: ".stay",
        stop:function (event, ui) {
            var dels = [];
            $('.deliveries-list li:gt(0)').each(function(){
                if( !$(this).hasClass('stay') ){
                    dels[dels.length] = $(this).attr('delivery');
                }
                else{
                    return false;
                }
            });
            $('[name="UsedLanguages"]').val(JSON.stringify(dels));					
            console.log(JSON.stringify(dels));
        }
    });
    $(".deliveries-list li").disableSelection();
	
	$(".methods-list").sortable({
        items: 'li:not(.stay-always)',
        cancel: ".stay",
        stop:function (event, ui) {
			var centralPos = $('.unUsed').index();
            var used = [];
			var noUsed = [];
            $('.methods-list li').each(function(){
                if (! $(this).hasClass('stay') ){					
					if ($(this).index() < centralPos) {
						used[used.length] = $(this).attr('method');
					} else {
						noUsed[noUsed.length] = $(this).attr('method');
					}                    
                }
                else{
                    return;
                }
            });
            $('#usedMethods').val(JSON.stringify(used));
			$('#unUsedMethods').val(JSON.stringify(noUsed));
            console.log(JSON.stringify(used));
			console.log(JSON.stringify(noUsed));
        }
    });

    $('.combolist').combobox();
    $('.region-select').live('click', function(){
        $('[name="ExternalDeliveryRegionId"]').val($(this).attr('regid'));
        $('[name="ExternalDeliveryRegionName"]').val($(this).attr('regname'));
        $('#delivery-choosed').html('<?=LangAdmin::get('selected_region')?>: '+$(this).attr('regid')+' '+$(this).attr('regname'));
        $( "#dialog-form" ).dialog( "close" );
        return false;
    });
    /*
    $("#regions").treeview({
            url: "index.php?do=regions"
    })
    */
    $( "#dialog-form" ).dialog({
            autoOpen: false,
            height: 315,
            width: 350,
            modal: true,
            buttons: {
                "<?=LangAdmin::get('cancellation')?>": function() {
                        $( this ).dialog( "close" );
                }
            },
            close: function() {
            }
    });        
});
</script>

<script>

$('.combolist').combobox();

var types = Array();
<? foreach($types as $type=>$desc) { $desc = str_replace(array(chr(10), chr(13)), '', $desc); ?>
    types['<?=$type?>'] = '<?=$desc?>';
<? }?>

function onChangeType()
{
    var type = $('#structuretype').val();
    $('#mode_desc').html(types[type]);
}
onChangeType();
</script>

<?
include ("footer.php");
?>
	
	