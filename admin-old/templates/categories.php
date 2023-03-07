<?
include ("header.php");

$cid = @$_GET['cid'];

$can_edit = ($webui->Settings->SelectedCategoryStructureType == 'Predefined') ? 0 : 1;

$error = (isset($_GET['error'])) ? $_GET['error'] : false;
$error_exist = (isset($_GET['error'])) ? 1 : 0;
$success = (isset($_GET['success'])) ? $_GET['success'] : false;
?>

<?=Lang::loadJSTranslation(array('cant_load_tab', 'save', 'cancel', 'loading', 'saving', 'saved', 'home'))?>


<div class="main"><div class="canvas clrfix">
        <div class="col700">
            <div class="tuning">
            <?  include("emptycategorylist.php"); ?>
            <div style="font-weight: bold; color:red;"><?if(!is_array(@$_SESSION['error'])){?><?=LangAdmin::get(@$_SESSION['error'])?><?}?></div>
            <? if ((string)$webui->Settings->SelectedTranslateType == 'AutoTranslation') { ?>
                <div align="right" class="flr">
                    <a class="linktext" onclick="showaddform(0)"><img src="<?=TPL_DIR;?>i/plus.gif" width="16" height="16" align="middle" style="vertical-align:middle"> <?=LangAdmin::get('add_root_category')?></a>
                    <a class="linktext" onclick="shhidden()"><?=LangAdmin::get('display')?>\<?=LangAdmin::get('hide_invisible')?></a>
                    <a class="linktext" href='index.php?cmd=category&do=importform&sid=<?=$GLOBALS['ssid']?>'><?=LangAdmin::get('imports')?></a>
                    <a class="linktext" href='index.php?cmd=category&do=export&sid=<?=$GLOBALS['ssid']?>'><?=LangAdmin::get('exports')?></a><br>


                    <a class="linktext" href='index.php?cmd=category&do=importFileform&sid=<?=$GLOBALS['ssid']?>'><?=LangAdmin::get('imports_translates')?></a>
                    <a class="linktext" href='index.php?cmd=category&do=exportToTranslate&sid=<?=$GLOBALS['ssid']?>'><?=LangAdmin::get('exports_translates')?></a>
                    <?if(count($usedLanguages)>1){?>
                    <br><br>
                    <form action="index.php" id="set-site-lang-form" method="post">
                        <label for="site-lang"><?=LangAdmin::get('choose_active_language')?>: </label>
                        <select name="site_lang" id="site-lang" onchange="$('#set-site-lang-form').submit();">
                            <?foreach($usedLanguages as $name=>$lang){?>
                            <option value="<?=$name?>" <?=@$_SESSION['active_lang']==$name?'selected':''?>>
                                <?=$lang?>
                            </option>
                            <?}?>
                            <input type="hidden" name="from" value="<?=$_SERVER['REQUEST_URI']?>" />
                        </select>
                    </form>
                    <?}?>
                    <? if ($_SESSION['CatShowMode']=='true') { $checker="checked=\"checked\""; } else { $checker="";} ?>
                    <?=LangAdmin::get('show_stat_cats')?>? <input name="showempty" id="showemptyparam" type="checkbox" <?=$checker?>  value="" onclick="ChangeShowMode()"/><br>
                    <a class="linktext" href='index.php?cmd=category&do=hidedeletedcats&sid=<?=$GLOBALS['ssid']?>'><?=LangAdmin::get('hide_deled_cats')?></a>
                </div>
                <br clear="all"/>
                <? if(!$can_edit) { ?>
                    <br/><br/><strong style="color:red;"><?=LangAdmin::get('attention')?>! <?=LangAdmin::get('edit_in_this_mode_is_not_possible')?>!<br/><br/></strong>
                <? } else {  ?>
                    <small class="stitle red"><?=LangAdmin::get('category')?> [<?=LangAdmin::get('taobao_category')?>]:</small><br/><br/>
                    <div id="incat0">
                    <? $parentid = 0; ?>
                    <?  include("categotylist.php"); ?>
                    </div>
                <? } ?>
            <? } else { ?>
                <h3><?=LangAdmin::get('editing_the_structure_is_available_only_when_auto_mode')?></h3>
            <? } ?>
            </div>
        </div>

</div></div><!-- /.main -->

<div id="dialog-confirm" title="Удалить категорию и все её подкатегории?">
    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Удалить категорию и все её подкатегории без возможности восстановления?</p>
</div>

<div id="dialog-error" title="<?=LangAdmin::get('error_exist')?>">
    <div id="error_was" style="font-weight: bold; color:red;"><?=$error?></div>
    <div id="success_was" style="font-weight: bold; color:green;"></div>
</div>

<div class="windialog" id="dialog-form" title="<?=LangAdmin::get('add_a_category')?>">
    <p class="validateTips"></p>
    <b><?=LangAdmin::get('name')?>*:</b><br/>
    <input type="text" value="" id="name" class="text ui-widget-content ui-corner-all" />
    <br><br>
    <b><?=LangAdmin::get('parent_category')?>:</b> <br/>
    <input type="text" id="parentid" readonly value="<?=@$pcid?>" class="text ui-widget-content ui-corner-all" />
    <br><br>
    <b><?=LangAdmin::get('taobao_category')?>:</b><br/>
    <input type="text" value="" id="categoryId" class="text ui-widget-content ui-corner-all" />
    <img id="addspinner" src="<?=TPL_DIR;?>i/spinner.gif">
    <? if($useMetrologist){ ?>
    <br><br>
    <b><?=LangAdmin::get('approximate_weight')?>:</b><br/>
    <input type="text" value="" id="approximate_weight_add" class="text ui-widget-content ui-corner-all" />
    <? } ?>
</div>

<div class="windialog" id="dialog-form2" title="<?=LangAdmin::get('edit_category')?>">
    <p class="validateTips"></p>
    <b><?=LangAdmin::get('name')?>:</b><br/>
    <input type="text" value="" id="ename" class="text ui-widget-content ui-corner-all" />
    <br><br>
    <b><?=LangAdmin::get('to_change_the_parent_category')?> (<span id="eparentid2"><?=@$pcid?></span>) <?=LangAdmin::get('enter_the_name_of_the_new_parent_category_or')?> 0, <?=LangAdmin::get('to_make_it_the_root')?>:</b><br/>
    <input type="text" id="eparentid" value="" class="text ui-widget-content ui-corner-all" />
    <input type="hidden" id="eparentid3" value="" />
    <br><br>
    <b><?=LangAdmin::get('taobao_category')?>:</b><br/>
    <input type="text" value="" id="ecategoryId" class="text ui-widget-content ui-corner-all" />
    <? if($useMetrologist){ ?>
    <br><br>
    <b><?=LangAdmin::get('approximate_weight')?>:</b><br/>
    <input type="text" value="" id="approximate_weight" class="text ui-widget-content ui-corner-all" />
    <? } ?>

    <? if(in_array('Seo2', General::$enabledFeatures)){ ?>
    <br><br>
    <b><?=LangAdmin::get('Alias')?>:</b><br/>
    <input type="text" value="" id="alias" class="text ui-widget-content ui-corner-all" />
    <br><br>
    <b><?=LangAdmin::get('Meta title')?>:</b><br/>
    <input type="text" value="" id="meta_title" class="text ui-widget-content ui-corner-all" />
    <br><br>
    <b><?=LangAdmin::get('Meta keywords')?>:</b><br/>
    <input type="text" value="" id="meta_keywords" class="text ui-widget-content ui-corner-all" />
    <br><br>
    <b><?=LangAdmin::get('Meta description')?>:</b><br/>
    <input type="text" value="" id="meta_description" class="text ui-widget-content ui-corner-all" />
    <br><br>
    <b>Константа для заголовка (префикс и суффикс разделяются символами ||):</b><br/>
    <input type="text" value="" id="seo_title" class="text ui-widget-content ui-corner-all" />
    <br><br>
    <? } ?>

    <img id="addspinner2" src="<?=TPL_DIR;?>i/spinner.gif">
</div>

<div class="windialog" id="dialog-form-edit_filter" title="<?=LangAdmin::get('edit_filters_category')?>">
    <div id="search_filter_list">List filter</div>
    <img id="search_filter_spinner" src="<?=TPL_DIR;?>i/spinner.gif" alt="load" title="Loading" />
</div>


<script language="javascript" type="text/javascript" src="../js/tiny_mce/ajaxfilemanager.js"></script>
<script language="javascript" type="text/javascript" src="../js/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="js/category.js"></script>
<script>

var is_sortable = true;
var ed_pcid = 0;
var editid = '';
var dcid = 0;
var dpcid = 0;

function ChangeShowMode()
{
    if ($("#showemptyparam").is(":checked")) {
        //alert('Отмечено');
        window.location = 'index.php?sid=<?=$GLOBALS['ssid']?>&cmd=category&showmode=true';

    } else {
        //alert('Не отмечена');
        window.location = 'index.php?sid=<?=$GLOBALS['ssid']?>&cmd=category&showmode=false';

    }
}

function showdelform(cid, pcid)
{
    dcid = cid;
    dpcid = pcid;
    $("#dialog-confirm").dialog("open");
}

$(function()
{
    // a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
    //$( "#dialog:ui-dialog" ).dialog( "destroy" );
    $( "#dialog-confirm" ).dialog({
        autoOpen: false,
        resizable: false,
        height:240,
        modal: true,
        buttons: {
            "Удалить": function() {
                var server_url = 'index.php?cmd=category&do=remove&pcid='+dpcid+'&cid='+dcid+'&sid=<?=$GLOBALS['ssid']?>';
                $(jq('spinner_'+dcid)).attr('src', "<?=TPL_DIR;?>i/spinner.gif");
                //alert(server_url);
                $.ajax({
                    url: server_url,
                    success: function(data) {
                        if (data == 'RELOGIN') location.href='index.php';
                        $(jq('incat'+dpcid)).html(data);
                    },
                    error: function() {
                    }
                });
                $( this ).dialog( "close" );
            },
            "Отмена": function() {
                $( this ).dialog( "close" );
            }
        }
    });

        $("#dialog-error").dialog({
        autoOpen: false,
        resizable: false,
        height:240,
        modal: true,
        buttons: {
            "Ok": function() {
                $(this).dialog("close");
            }
        }
    });

        $("#dialog-error:ui-dialog").dialog("destroy");

        if (<?=$error_exist?>) {
            colsole.log('dialog-error');
            //$("#dialog-error").dialog("open");
            //$("#dialog-confirm").dialog("open");
        }

    function updateTips( t ) {
        tips
            .text( t )
            .addClass( "ui-state-highlight" );
        setTimeout(function() {
            tips.removeClass( "ui-state-highlight", 1500 );
        }, 500 );
    }

    function checkLength( o, n, min, max ) {
        if ( o.val().length > max || o.val().length < min ) {
            o.addClass( "ui-state-error" );
            updateTips( "Length of " + n + " must be between " +
                min + " and " + max + "." );
            return false;
        } else {
            return true;
        }
    }

    function checkRegexp( o, regexp, n ) {
        if ( !( regexp.test( o.val() ) ) ) {
            o.addClass( "ui-state-error" );
            updateTips( n );
            return false;
        } else {
            return true;
        }
    }

    var name = $( "#name" ),
        allFields = $( [] ).add( name ),
        tips = $( ".validateTips" );

    $( "#dialog-form" ).dialog({
        autoOpen: false,
        height: 360,
        width: 350,
        modal: true,
        buttons: {
            "<?=LangAdmin::get('add_a_category')?>": function() {
                var bValid = true;
                allFields.removeClass( "ui-state-error" );
                bValid = bValid && checkLength( name, "name", 1, 1000 );
                if ( bValid ) add();
            },
            Cancel: function() {
                $( this ).dialog( "close" );
            }
        },
        close: function() {
            allFields.val( "" ).removeClass( "ui-state-error" );
        }
    });

    var ename = $( "#ename" ),
        eallFields = $( [] ).add( ename ),
        tips = $( ".validateTips" );

    $( "#dialog-form2" ).dialog({
        autoOpen: false,
        height: 450,
        width: 350,
        modal: true,
        buttons: {
            "<?=LangAdmin::get('save')?>": function() {
                var bValid = true;
                eallFields.removeClass( "ui-state-error" );
                bValid = bValid && checkLength( ename, "ename", 1, 1000 );
                if ( bValid ) edit();
            },
            Cancel: function() {
                $( this ).dialog( "close" );
            }
        },
        close: function() {
            eallFields.val( "" ).removeClass( "ui-state-error" );
        }
    });

    $( "#dialog-form-edit_filter" ).dialog({
        autoOpen: false,
        height: 450,
        width: 500,
        modal: true,
        buttons: {
            Cancel: function() {
                $( this ).dialog( "close" );
            }
        },
        close: function() {
            eallFields.val( "" ).removeClass( "ui-state-error" );
        }
    });
});

function add()
{
    var parentid = $( "#parentid" ).val();
    var categoryId = $( "#categoryId" ).val();
    var name = $( "#name" ).val();

    var getApproximateWeight = '';
    if($( "#approximate_weight_add").length){
        var approximateWeight = $( "#approximate_weight_add" ).val();
        getApproximateWeight = '&approximate_weight='+approximateWeight;
    }

    var server_url = 'index.php?cmd=category'+getApproximateWeight+'&do=add2&parentid='+parentid+'&categoryId='+categoryId+'&pcid='+ed_pcid+'&sid=<?=$GLOBALS['ssid']?>&name='+name;
    $('#addspinner').show();
    $.ajax({
        url: server_url,
        success: function(data) {
            if (data == 'RELOGIN') location.href='index.php';
            $( "#dialog-form" ).dialog('close');
            $(jq('incat'+parentid)).html(data);
        },
        error: function() {
            //
        }
    });
}

function edit()
{
    var parentid = $( "#eparentid3" ).val();
    var parentid2 = '';
    if (!parentid) parentid2 = $( "#eparentid" ).val();
    var categoryId = $( "#ecategoryId" ).val();
    var alias = $( "#alias" ).val();
    var name = $( "#ename" ).val();
    var getApproximateWeight = '';
    if($( "#approximate_weight").length){
        var approximateWeight = $( "#approximate_weight" ).val();
        getApproximateWeight = '&approximate_weight='+approximateWeight;
    }

    var seo_title = $( "#seo_title" ).val();
    var meta_title = $( "#meta_title" ).val();
    var meta_keywords = $( "#meta_keywords" ).val();
    var meta_description = $( "#meta_description" ).val();

    var server_url = 'index.php?cmd=category'+getApproximateWeight+'&seo_title='+seo_title+'&meta_keywords='+meta_keywords+'&meta_description='+meta_description+'&meta_title='+meta_title+'&do=save&alias='+alias+'&parentid2='+parentid2+'&parentid='+parentid+'&categoryId='+categoryId+'&cid='+editid+'&pcid='+ed_pcid+'&sid=<?=$GLOBALS['ssid']?>&name='+name;
    $('#addspinner2').show();
    $.ajax({
        url: server_url,
        success: function(data) {
            if (data == 'RELOGIN') location.href='index.php';
            if (data == 'REFRESH') location.reload();
            $( "#dialog-form2" ).dialog('close');
            $(jq('incat'+ed_pcid)).html(data);
        },
        error: function() {

        }
    });
}


function showedit(cid, pcid)
{
    ed_pcid = pcid;
    editid = cid;
    $("#approximate_weight" ).val($(jq('edit2_apprw_'+cid)).val());

    $("#ecategoryId" ).val($(jq('edit2f_'+cid)).val());
    $("#alias" ).val($(jq('edit2al_'+cid)).val());

    $("#meta_title" ).val($(jq('edit2st_'+cid)).val());
    $("#meta_keywords" ).val($(jq('edit2sk_'+cid)).val());
    $("#meta_description" ).val($(jq('edit2sd_'+cid)).val());
    $("#seo_title" ).val($(jq('edit2sp_'+cid)).val());

    $("#eparentid").val("");
    $("#eparentid2").html(pcid);
    $("#eparentid3").val("");
    $("#ename").val($(jq('editf_'+cid)).val());
    $('#addspinner2').hide();
    $(".validateTips").html("");
    $("#dialog-form2").dialog("open");
    $("#eparentid" ).autocomplete({
        minLength: 2,
        source: 'index.php?cmd=category&do=gethint&sid=<?=$GLOBALS['ssid']?>',
        select: function( event, ui ) {
                $( "#eparentid" ).val( ui.item.label );
                $( "#eparentid3" ).val( ui.item.value );
                return false;
            }

    }).data( "autocomplete" )._renderItem = function( ul, item ) {
        if (item.path)
        {
            return $( "<li></li>" )
                .data( "item.autocomplete", item )
                .append( "<a><span class='mini'>" + item.path + "</span><b>" + item.label + "</b></a>" )
                .appendTo( ul );
        } else {
            return $( "<li></li>" )
                .data( "item.autocomplete", item )
                .append( "<a><b>" + item.label + "</b></a>" )
                .appendTo( ul );
        }
    };
}

function show_edit_search_filter(cid)
{
    editid = cid;
    ecategoryId = $(jq('edit2f_'+cid)).val();
    $('#search_filter_list').hide();
    $('#search_filter_spinner').show();
    $("#dialog-form-edit_filter").dialog("open");

    $.post('index.php?cmd=category&do=getSearchSelect&sid=<?=$GLOBALS['ssid']?>',
        {'category_id': ecategoryId}, function(data) {
            $('#search_filter_list').html(data);
            $('#search_filter_spinner').hide();
            $('#search_filter_list').show();
    });
}

var update_filter_activate = false;
function update_search_filter(category_id, type)
{
    if (!update_filter_activate) {
        update_filter_activate = true;
        $('#'+type+category_id).val('Load...');
        text = $('#v'+type+category_id).val();
        $.post('index.php?cmd=category&do=updateSearchFilter&sid=<?=$GLOBALS['ssid']?>',
            {'category_id': category_id, 'text': text, 'type': type}, function(data) {
                if (data == '1') {
                    update_filter_activate = false;
                    $('#'+type+category_id).val('<?=LangAdmin::get('change')?>');
                }
        });
    }
}


function showaddform(pcid, pcid2)
{
    ed_pcid = pcid2;
    $('#addspinner').hide();
    $("#categoryId" ).val('');
    $("#parentid").val(pcid);
    $(".validateTips").html("");
    $("#dialog-form").dialog("open");
}

if (is_sortable)
{
    $(function() {
        $( "#incat0" ).sortable({
            stop: function(event, ui) {
                var data = $("#incat0").sortable('toArray');
                order2(ui.item[0].id, this.id, data, ui.position.top - ui.originalPosition.top);
            },
            placeholder: "ui-state-highlight",
        });
        //$( "#incat0" ).disableSelection();
    });
}

function jq(myid)
{
    return '#' + myid.replace(/(:|\.)/g,'\\$1');
}

var shstat = <?=$hiddenstat?>;
function shhidden()
{
    if (shstat == 1)
    {
        $('.hidden2').each(function(){
            $(this).hide();
        });
        shstat = 0;
    } else {
        $('.hidden2').each(function(){
            $(this).show();
        });
        shstat = 1;
    }
    $.ajax({
        url: 'index.php?cmd=category&do=savestat&stat='+shstat+'&sid=<?=$GLOBALS['ssid']?>'
    });
}
//shhidden();

function subcat(cid, parent)
{
    //var incat = document.getElementById('incat'+cid);
    //alert(incat.innerHTML);
    var incat = $(jq('incat'+cid));
    if (incat.html() != '')
    {
        if (incat.css('display') == 'none')
        {
            incat.show('blind');
        } else {
            incat.hide('blind');
        }
    } else {
        //alert(cid);
        var spinner = $(jq('spinner_'+cid));
        if (spinner.attr('alt') == 'Wait') return;
        spinner.attr('src', "<?=TPL_DIR;?>i/spinner.gif");
        spinner.attr('alt', "Wait");
        $.ajax({
                url: 'index.php?cmd=category&do=get&catid='+cid+'&sid=<?=$GLOBALS['ssid']?>',
                success: function(data) {
                    if (data == 'RELOGIN') location.href='index.php';
                    $(jq('incat'+cid)).html(data);
                    spinner.attr('src', "<?=TPL_DIR;?>i/folder.jpg");
                    $(jq('incat'+cid)).show('blind');
                    if (is_sortable)
                    {
                        $(jq('incat'+cid)).sortable({
                            stop: function(event, ui) {
                                var data = $(jq('incat'+cid)).sortable('toArray');
                                order2(ui.item[0].id, this.id, data, ui.position.top - ui.originalPosition.top);
                            },
                            placeholder: "ui-state-highlight",
                        });
                    }
                    //$(jq('incat'+cid)).disableSelection();
                },
                error: function() {

                }
            });
    }
}

function order2(cid, pcid, data, direct)
{
    length = data.length;
    for(var i = 0; i < length; i++)
    {
        if (data[i] == cid) break;
    }
    var server_url = 'index.php?cmd=category&do=order2&pcid='+pcid+'&cid='+cid+'&direct='+direct+'&sid=<?=$GLOBALS['ssid']?>&pos='+i;
    //alert(server_url);
    $.ajax({
        url: server_url,
        success: function(data) {
            if (data == 'RELOGIN') location.href='index.php';
            //$(jq(pcid)).html(data);
        },
        error: function() {
        }
    });
}

function order(cid, pcid, i, direct)
{
    var server_url = 'index.php?cmd=category&do=order&cid='+cid+'&pcid='+pcid+'&sid=<?=$GLOBALS['ssid']?>&i='+i;
    if(direct == 'down')
    {
        server_url += '&down';
    }
    $(jq('spinner_'+cid)).attr('src', "<?=TPL_DIR;?>i/spinner.gif");
    //document.getElementById('spinner_'+cid).src="<?=TPL_DIR;?>i/spinner.gif";
    $.ajax({
        url: server_url,
        success: function(data) {
            if (data == 'RELOGIN') location.href='index.php';
            $(jq('incat'+pcid)).html(data);
        },
        error: function() {

        }
    });
}

function change_visible(cid, pcid)
{
    if (document.getElementById('vis_'+cid).alt == 1)
    {
        ishidden = 'false';
    } else {
        ishidden = 'true';
    }
    var server_url = 'index.php?cmd=category&do=visible&cid='+
                        cid+'&pcid='+pcid+'&sid=<?=$GLOBALS['ssid']?>&ishidden='+ishidden;
    $(jq('spinner_'+cid)).attr('src', "<?=TPL_DIR;?>i/spinner.gif");
    $.ajax({
        url: server_url,
        success: function(data) {
            if (data == 'RELOGIN') location.href='index.php';
            else
            {
                var cs = document.getElementById('vis_'+cid).alt;
                if (cs == 1)
                {
                    document.getElementById('cname_'+cid).className = "cname hidden";
                    document.getElementById('vis_'+cid).title = "<?=LangAdmin::get('show')?>";
                    document.getElementById('vis_'+cid).alt = 0;
                    document.getElementById('vis_'+cid).src = "<?=TPL_DIR;?>i/unpublish.png";
                    document.getElementById('cat'+cid).className = "hidden2";
                    if (shstat == 0)
                    {
                        document.getElementById('cat'+cid).style.display = 'none';
                    }
                } else {
                    document.getElementById('cname_'+cid).className = "cname";
                    document.getElementById('vis_'+cid).title = "<?=LangAdmin::get('hide')?>";
                    document.getElementById('vis_'+cid).alt = 1;
                    document.getElementById('vis_'+cid).src = "<?=TPL_DIR;?>i/publish.png";
                    document.getElementById('cat'+cid).className = "";
                }
                var spinner = $(jq('spinner_'+cid));
                if (spinner.attr('alt') == 0)
                {
                    spinner.attr('src', "<?=TPL_DIR;?>i/folder2.jpg");
                } else {
                    spinner.attr('src', "<?=TPL_DIR;?>i/folder.jpg");
                }
            }
        },
        error: function() {

        }
    });
}
</script>
<?
include ("footer.php");
?>