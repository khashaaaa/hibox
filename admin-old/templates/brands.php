<?
include ("header.php");
$cid = @$_GET['cid'];
?>

<div class="tuning">

    <h1 onclick="$('#recommend').toggle()" style="cursor: pointer;"><?=LangAdmin::get('brands')?></h1>

    <div id="recommend" <? if (@$_COOKIE['HiddenRecomBrands']) { ?> style="display:none"<? }?> >
        <? if (empty($brands)) { ?>
        <p><?=LangAdmin::get('empty')?>!</p>
        <? } else { ?>
        <br/>        
<ul id="sortable">
            <? foreach ($brands as $item) { ?>
            <? $style=((string)$item['IsGlobal']=='true') ? "background-color:red;" : ''; ?>
            <li class="sortlist" style="<?=$style?>" id="rec<?=$item['id']?>" <? if ($item['IsHidden'] == 'true') { ?>
                style="opacity: 0.3;" <? } ?>>
                <table class="nowidth nopadding" style="width:215px">
                    <tr >
                        <td width="20%"><a href="../index.php?p=search&search=&brand=<?=$item['id'];?>"><img
                            src="<?=$item['pictureurl'];?>" alt="" width="40px" height="40px"/></a></td>
                        <td><center><?=$item['Name'];?></center></td>
                        <td width="15"><a
                            href="<?=BASE_DIR;?>index.php?sid=<?=$GLOBALS['ssid'];?>&amp;do=del&amp;cmd=brands&amp;id=<?=$item['id'];?>">
                            <img src="<?=TPL_DIR;?>i/del.png" width="12" height="12" align="middle" style="vertical-align:middle"/></a>
                        </td>
                        <td width="5">&nbsp;</td>
                        <td width="15"><? if ($item['IsHidden'] == 'true') { ?>
                            <a class="show-brand"
                               href="<?=BASE_DIR;?>index.php?sid=<?=$GLOBALS['ssid'];?>&amp;do=show&amp;cmd=brands&amp;id=<?=$item['id'];?>">
                                <img src="<?=TPL_DIR;?>i/unpublish.png" width="12" height="12" align="middle" style="vertical-align:middle" title="<?=LangAdmin::get('show')?>" alt="<?=LangAdmin::get('show')?>"/></a>
                            <? } else { ?>
                            <a class="hide-brand"
                               href="<?=BASE_DIR;?>index.php?sid=<?=$GLOBALS['ssid'];?>&amp;do=hide&amp;cmd=brands&amp;id=<?=$item['id'];?>">
                                <img src="<?=TPL_DIR;?>i/publish.png" width="12" height="12" align="middle" style="vertical-align:middle" title="<?=LangAdmin::get('hide')?>" alt="<?=LangAdmin::get('hide')?>"/></a>
                            <? } ?></td>
                        <td width="5">&nbsp;</td>
                        <td width="15"><a
                            href="<?=BASE_DIR;?>index.php?sid=<?=$GLOBALS['ssid'];?>&amp;do=edit&amp;cmd=brands&amp;id=<?=$item['id'];?>">
                            <img src="<?=TPL_DIR;?>i/edit-icon.png" width="12" height="12" align="middle" style="vertical-align:middle"/></a></td>
                    </tr>
                </table>
            </li>
            <? }?>
        </ul>
        <? } ?>

    </div>

    <div class="clear"></div>
    <br/>

    <h3><?=LangAdmin::get('addition')?></h3>
    <span id="error_brands" style="color:red;font-weight: bold;">
        <? if (isset($error)) {
        print $error;
    } ?>
    </span>
    <?
    if (isset($brandData)) {
        $action = 'save';
        $nameval = "value='{$brandData['Name']}'";
        $descval = $brandData['Description'];
        $extidval = "value='{$brandData['ExternalId']}'";
		
        $pic = "value='{$brandData['PictureUrl']}'";
        $IsNameSearch = @$brandData['IsNameSearch']=='true' ? 'checked' : '';
        $button = LangAdmin::get('save');
		
		$seo_meta_title = "value='{$brandData['seo']['pagetitle']}'";
		$seo_meta_keywords = "value='{$brandData['seo']['seo_keywords']}'";
		$seo_meta_description = "value='{$brandData['seo']['seo_description']}'";
		$seo_seo_title = "value='{$brandData['seo']['seo_title']}'";
    }
    else {
        $action = 'add';
        $nameval = "";
        $descval = "";
        $extidval = "";
        $pic = "";
        $IsNameSearch = '';
        $button = LangAdmin::get('add_brand');
		$seo_meta_title = "";
		$seo_meta_keywords = "";
		$seo_meta_description = "";
		$seo_seo_title = "";
    }
    ?>
    <!--  ===========================================     --->
   
<link rel="stylesheet" href="css/brands.show.css" type="text/css">
<script language="javascript" type="text/javascript" src="../js/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    editBlock();
});
function editBlock()
{
	tinyMCE.init({
		mode : "exact",
		elements : "Description",
		theme : "advanced",
		plugins : "table,heading,advhr,advimage,advlink,paste,fullscreen,noneditable,contextmenu,inlinepopups,emotions,media,insertdatetime,nonbreaking",
		theme_advanced_buttons1_add_before : "newdocument,separator",
        theme_advanced_buttons1_add_before : "fontselect,fontsizeselect,h1,h2,h3,h4,h5,h6,separator",
        theme_advanced_buttons1_add : "fontselect,fontsizeselect",
		theme_advanced_buttons2_add : "separator,forecolor,backcolor,liststyle,separator,insertdate,inserttime",
		theme_advanced_buttons2_add_before: "cut,copy,paste,pastetext,pasteword,separator",
		theme_advanced_buttons3_add_before : "tablecontrols,separator",
		theme_advanced_buttons3_add : "flash,advhr,emotions,media,nonbreaking,separator,fullscreen,mybutton",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		extended_valid_elements : "hr[class|width|size|noshade],ul[class=listUL],ol[class=listOL],script[language|type|src], object[width|height|classid|codebase|embed|param],param[name|value],embed[param|src|type|width|height|flashvars|wmode], iframe[src|style|width|height|scrolling|marginwidth|marginheight|frameborder]",
        media_strict: false,
		file_browser_callback : "ajaxfilemanager",
		paste_use_dialog : false,
		theme_advanced_resizing : true,
		theme_advanced_resize_horizontal : true,
		apply_source_formatting : true,
		force_br_newlines : false,
		force_p_newlines : true,
		relative_urls : true,
        content_css : "css/style_editor.css", //+ new Date().getTime(),
        content_css : "css/style.css",
        init_instance_callback : "myCustomInitInstance",
        //theme_advanced_styles : "Popup Image=thickbox"
        
		
		
		
	});
}



</script>
<script>
  function SetBrand(nme , ide , description) {	
     document.getElementById('Name').value = nme;
     document.getElementById('ExternalId').value = ide;
     document.getElementById('Description').value = description;	
     $("#showera").css('display','none');
  }
  

$(document).ready(function () {

 
	   
$('#Name').keyup(function(e) { 
    if ((e.keyCode != 40) && (e.keyCode != 38) && (e.keyCode != 13)) {
      counter = $(this).val().length; 
	  if(counter >= 1){  			  
  		$.post('index.php?cmd=Brands&do=GetBrandShort&sid=<?=$GLOBALS['ssid']?>', { nme: $("#Name").val() }, function(data){ 
 		   if(data != 0){   
  		     $("#showera").html(data); 
  	         $("#showera").fadeIn(400);   
           }else { 
	         $("#showera").css('display','none'); 
           } 
  
       });   
      } 
	}
}); 

$("#Name").keydown(function(e){
	var ind=$("#scr li").index($(".seleted")); // 38-up, 40-down
    if (e.keyCode == 40) {			
		if (ind > -1){		   
			$('#scr li:eq('+ind+')').removeClass('seleted');
		}
		if (ind == $("#scr li:last").index()){		   
			ind=-1;
		}
		ind++;
		$('#scr li:eq('+ind+')').addClass('seleted');			 
		         	        
        return false;
    }
    if (e.keyCode == 38) { 
	    if (ind == 0){	
		    $('#scr li:eq('+ind+')').removeClass('seleted');	   
			ind=$("#scr li:last").index()+1;			
		}
		if (ind > 0 ){		   
			$('#scr li:eq('+ind+')').removeClass('seleted');
		}
		ind--;
		$('#scr li:eq('+ind+')').addClass('seleted');	
		
        return false;
    }
	
	
	if (e.keyCode == 13) {	    
		$('#scr li:eq('+ind+') a').click();	    		
        return false;
    }
	
	
	
});
  
$('#Name').focus(function() { 
	if($(this).val() == 'Введите название'){ 
		$(this).val(''); 
		$(this).css('color','#000000'); 
	} 
}); 

  
$('#Name').focusout(function() { 
	if($(this).val() == ''){ 
		$(this).val('Введите название'); $(this).css('color','#000'); 
	} 

}); 

}); 
</script>

    <form id="form1" action="<?=BASE_DIR;?>index.php?sid=<?=$GLOBALS['ssid'];?>&amp;do=<?=$action?>&amp;cmd=Brands"
          method="post">
<table>
            <tr>
                <td><?=LangAdmin::get('the_brand_name')?>:</td>
                <td><input type="text" name="Name" <?=$nameval?> id="Name" value="Введите название" class="text ui-widget-content ui-corner-all"/>
                <div style="position:relative;">
                <div id="showera"></div></div></td>
            </tr>
            <tr>
                <td><?=LangAdmin::get('image')?>:</td>
                <td>
                    <div id="file-uploader"></div>
                    <input type="hidden" name="PictureUrl" <?=$pic?>/>
                </td>
            </tr>
            <tr>
                <td><?=LangAdmin::get('description')?>:</td>
                <td><textarea name="Description" id="Description"
                              class="text ui-widget-content ui-corner-all"> <?=$descval?></textarea></td>
            </tr>
            <tr>
                <td><?=LangAdmin::get('search_by_brand_name')?></td>
                <td><input type="checkbox" name="IsNameSearch" <?=$IsNameSearch?>
                           class="text ui-widget-content ui-corner-all"/></td>
            </tr>
            <tr>
                <td><?=LangAdmin::get('external')?> ID:</td>
                <td><input type="text" name="ExternalId" id="ExternalId" <?=$extidval?>
                           class="text ui-widget-content ui-corner-all"/></td>
            </tr>
            <? if ((in_array('Seo2', General::$enabledFeatures)) and ($action<>'add')) { ?>
    		<tr>
                <td><?=LangAdmin::get('Meta title')?>:</td>
    			<td><input type="text" <?=$seo_meta_title?> id="meta_title" name="meta_title" class="text ui-widget-content ui-corner-all" /></td>
            </tr>
   			<tr>
                <td><?=LangAdmin::get('Meta keywords')?>:</td>
    			<td><input type="text" <?=$seo_meta_keywords?>  id="meta_keywords" name="meta_keywords" class="text ui-widget-content ui-corner-all" /></td>
            </tr>
    		<tr>
                <td><?=LangAdmin::get('Meta description')?>:</td>
    			<td><input type="text"  <?=$seo_meta_description?> id="meta_description" name="meta_description" class="text ui-widget-content ui-corner-all" /></td>
    		</tr>
            <tr>
                <td>Константа для заголовка (префикс и суффикс разделяются символами ||):</td>
    			<td><input type="text" <?=$seo_seo_title?> id="seo_title" name="seo_title" class="text ui-widget-content ui-corner-all" /></td>
    		</tr>
    		<? } ?>
            
            
            
        </table>
        <button id="submit1"><?=$button?></button>
        <? if (isset($brandData)) { ?>
        <input type="hidden" name="Id" value="<?=$brandData['id']?>"/>
        <? } ?>
    </form>
    
    
    <!--  ===========================================     --->
</div>
<script>
    $("#submit1")
        .button()
        .click(function () {
            $("#form1").submit();
        });
    $("#submit2")
        .button()
        .click(function () {
            $("#form2").submit();
        });

    $("#submit1del")
        .button()
        .click(function () {
            $("#form1del").submit();
        });
    $("#submit2del")
        .button()
        .click(function () {
            $("#form2del").submit();
        });

    $("#submit1save")
        .button()
        .click(function () {
            var result = $('#sortable').sortable('toArray');
            var str = '';
            $.each(result, function (i, value) {
                str += value.substr(3) + ';';
                //alert(str);
            });
            $('#rec_ids').val(str);
            $("#submit1save").submit();
        });

    $("#submit2save")
        .button()
        .click(function () {
            var result = $('#sortable2').sortable('toArray');
            var str = '';
            $.each(result, function (i, value) {
                str += value.substr(3) + ';';
                //alert(str);
            });
            $('#pop_ids').val(str);
            $("#submit2save").submit();
        });

    $("#sortable").sortable({
        change:function (event, ui) {
            $("#save1").show();
        }
    });

    $("#sortable2").sortable({
        change:function (event, ui) {
            $("#save2").show();
        }
    });

    var shstat = new Array(<?=$hidden_popular?>,<?=$hidden_recom?>);

    function save_state(block) {
        if (block == 'pop') {
            if (shstat[0] == 1) {
                $('#popular').hide();
                shstat[0] = 0;
            } else {
                $('#popular').show();
                shstat[0] = 1;
            }
        }

        if (block == 'rec') {
            if (shstat[1] == 1) {
                $('#recommend').hide();
                shstat[1] = 0;
            } else {
                $('#recommend').show();
                shstat[1] = 1;
            }
        }
        $.ajax({
            url:'index.php?cmd=set&do=savestat&statp=' + shstat[0] + '&statr=' + shstat[1] + '&sid=<?=$GLOBALS['ssid']?>'
        });
    }

    function createUploader() {
        var uploader = new qq.FileUploader({
            element:document.getElementById('file-uploader'),
            action:'utils/Upload.php?resize=1',
            debug:true,
            template:'<div class="qq-uploader">' +
                '<div class="qq-upload-drop-area"><span></span></div>' +
                '<div class="qq-upload-button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" style="height:23px;padding-top:7px">&nbsp;&nbsp;&nbsp;<?=LangAdmin::get('select_a_picture')?>&nbsp;&nbsp;&nbsp;</div>' +
                '<ul class="qq-upload-list"></ul>' +
                '</div>',
            onComplete:function (id, fileName, responseJSON) {
                $('.qq-upload-list').empty().append($('<li></li>').append($('<img />').attr('src', responseJSON.url + '?' + Math.random())));
                $('[name="PictureUrl"]').val(responseJSON.url);
            }
        });
    }
    $(function () {
        $('.hide-brand').live('click', function () {
            var brandBlock = $(this);
            $.post($(this).attr('href'), {ajax:1}, function (data) {
                if (data.success) {
                    brandBlock.closest('li').css({
                        'opacity':'0.3'
                    });
                    brandBlock.find('img').attr('src', '<?=TPL_DIR;?>i/unpublish.png');
                    var url = brandBlock.attr('href');
                    url = url.replace(/do\=hide/, 'do=show');
                    brandBlock.attr('href', url);
                    brandBlock.removeClass('hide-brand');
                    brandBlock.addClass('show-brand');
                }
                else {
                    alert(data.error);
                }
            }, 'json');
            return false;
        });

        $('.show-brand').live('click', function () {
            var brandBlock = $(this);
            $.post($(this).attr('href'), {ajax:1}, function (data) {
                if (data.success) {
                    brandBlock.closest('li').css({
                        'opacity':'1'
                    });
                    brandBlock.find('img').attr('src', '<?=TPL_DIR;?>i/publish.png');
                    var url = brandBlock.attr('href');
                    url = url.replace(/do\=show/, 'do=hide');
                    brandBlock.attr('href', url);
                    brandBlock.addClass('hide-brand');
                    brandBlock.removeClass('show-brand');
                }
                else {
                    alert(data.error);
                }
            }, 'json');
            return false;
        });

        $("#sortable").sortable();
        $("#sortable").disableSelection();

        $("#sortable2").sortable();
        $("#sortable2").disableSelection();
        createUploader();
    <?
    if (isset($brandData)) {
        ?>
        $('.qq-upload-list').empty().append($('<li></li>').append($('<img />').attr('src', '<?=$brandData['PictureUrl']?>')));
        <?
    }
    ?>
    });
</script>

<?
include ("footer.php");
?>