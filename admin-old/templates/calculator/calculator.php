<? include (TPL_DIR . "header.php"); ?>
<script type="text/javascript" src="js/lang.js"></script>
<script src="js/jquery.cookie.js" type="text/javascript"></script>

<div class="main"><div class="canvas clrfix">

        <div class="col700">
            <div class="tuning">
                <span id="error" style="color:red;font-weight: bold;">
                    <? if(isset($error)) { print $error; } ?>
                </span>
                <h1> <?=LangAdmin::get('calculator');?> </h1>
                <p></p>
            </div>
        </div>
        <div id="tabs">
            <ul>
                <li id="itab1"><a href="#tabs-1"><?=Lang::get('countries_type_delivery')?></a></li>
                <li id="itab2"><a href="#tabs-2"><?=Lang::get('types_delivery')?></a></li>
                <li id="itab3"><a href="#tabs-3"><?=LangAdmin::get('countries');?></a></li>
            </ul>

            <span id="error" style="color:red; font-weight: bold;">
                <? if(isset($error)) { echo $error; } ?>
            </span>
			<div class="addform" id="dialog-form" title="Сохранение">
				<br/><span id="message" >Все изменения сохранены</span>
			</div>

            <div id="tabs-1">
                <br/><h2></h2>
                <div style="width:60%">
					<table style="margin:10px;">
						<tr>
							<th valign="top">
								<?=LangAdmin::get('kind_of_delivery');?>
                            </th>
                        </tr>
                        <tr>
                            <td>
                                <select id="delivery" name="delivery">
									<?php foreach ($delivery as $item) :?>
									<option value="<?=$item['id'];?>"><?=$item['name'];?></option>
									<?php endforeach; ?>
								</select>
                                <br>
							</td>
                        </tr>
                    </table>
                    <table style="margin:10px;">
                        <tr>
							<th>
                                <?=LangAdmin::get('counties_of_delivery')?>
                            </th>
							<th>
                                <?=LangAdmin::get('actions')?>
                            </th>
                        </tr>
                        <?php foreach ($countries as $country) :?>

                        <tr>
                            <td>
                                <label for="country_<?=$country['id']?>">
                                    <input type="checkbox" class="sets" country_id="<?=$country['id']?>" value="<?=$country['id']?>" />
                                    <?=$country['name']?>
                                </label>
                                <br>
							</td>
                            <td><a class="link_edit" href="javascript:;" country_id="<?=$country['id']?>"><?=LangAdmin::get('edit_params_of_formula');?></td>
						</tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="2">
                                 <input type="button" name="save" onclick="javascript:;" value="<?=LangAdmin::get('save')?>"/>
                            </td>
                        </tr>
					</table>

                </div>
            </div>

            <div id="tabs-2">
                <br/><h2></h2>
                <div style="margin:10px; width:60%">

                    <input type="button" id="but_add_delivery" name="but_add_delivery" onclick="location.href='?cmd=calculator&do=add_delivery'" value="<?=LangAdmin::get('add')?>"/>
                    <br/><br/>
                    <table border="1">
                        <tr>
                            <th width="50">ID</th>
                            <th><?=LangAdmin::get('name');?></th>
                            <th width="250"><?=LangAdmin::get('actions');?></th>
                        </tr>
                        <?php foreach ($delivery as $item) : ?>

                        <tr>
                            <td><?=$item['id'];?></td>
                            <td><?=$item['name'];?></td>
                            <td><a href="?cmd=calculator&do=delivery_edit&id=<?=$item['id'];?>"><?=LangAdmin::get('editing');?></a>
                            <a class="del delivery" item="<?=$item['id'];?>"><?=LangAdmin::get('remove');?></a></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>

            <div id="tabs-3">
                <br/><h2></h2>
                <div style="margin:10px; width:60%">

                    <input type="button" id="but_add_country" name="but_add_country" onclick="location.href='?cmd=calculator&do=add_country'" value="<?=LangAdmin::get('add')?>"/>
                    <br/><br/>
                    <table border="1">
                        <tr>
                            <th width="50">ID</th>
                            <th><?=LangAdmin::get('name');?></th>
                            <th width="250"><?=LangAdmin::get('actions');?></th>
                        </tr>
                        <?php foreach ($countries as $country) : ?>

                        <tr>
                            <td><?=$country['id'];?></td>
                            <td><?=$country['name'];?></td>
                            <td><a href="?cmd=calculator&do=country_edit&id=<?=$country['id'];?>"><?=LangAdmin::get('editing');?></a>
                            <a class="del country" item="<?=$country['id'];?>"><?=LangAdmin::get('remove');?></a></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>

        </div>

        <div class="windialog" id="confirm-form" title="<?=LangAdmin::get('confirmation')?>">
            <?=LangAdmin::get('the_record_will_be_removed')?>, <?=LangAdmin::get('proceed')?>?
            <div style="display:none;" class="spinner"></div>
        </div>

</div></div>

<script type="text/javascript" src="js/jquery.combobox.js"></script>
<script type="text/javascript">
$(function(){
	var target = '';

	$("#tabs").tabs({
		cache: true,
		cookie: { expires: 30 },
		ajaxOptions:{
			error: function( xhr, status, index, anchor ) {
				$( anchor.hash ).html("<p>No data</p>");
			}
		}
	});

	var setCountries = function() {
		var elem = $('select[name=delivery]'),
			delivery = elem.val();
        $.post(
            'index.php',
            {'cmd':'calculator', 'do':'getCountryByDelivery','id':delivery},
            function(data) {
				$('div#tabs').mask("Waiting...");
				if (data.success) {
				    $.each($('input:checkbox'), function() {
						var check = false,
							country=$(this).val();

						$.each(data.data, function(){
							var obg = $(this)[0];
							check = check || (obg.delivery_id==delivery && obg.country_id==country && obg.is_active==1);
							return !check;
						});

					  if (check) {
						  $(this).attr('checked', 'checked')
					  } else {
						  $(this).removeAttr('checked');
					  };
					});
                } else {
					alert(data.message)
				}
				$('div#tabs').unmask();
            },
            'json');
	}

    $('select[name=delivery]').change(function() {
		setCountries();
    });

	$('input:button[name=save]').click(function(){
		var mass	 = $('input:checkbox.sets'),
			delivery = $('select[name=delivery]').val(),
			arr		 = new Array();
		$.each(mass, function() {
			var item = new Object();
				item.id = $(this).attr('country_id');
				item.is_active = $(this).attr('checked');
			arr.push(item);
		});
		$.post(
			'index.php',
			{'cmd':'calculator', 'do':'setCountryByDelivery', 'countries':arr, 'delivery':delivery},
			function(data){
				if(data.success==true) {
					$("#dialog-form").dialog("open");
				};
			},
			'json');
	});

    $('#confirm-form').dialog({
       autoOpen: false,
       modal: true,
       buttons : {
           "<?=LangAdmin::get('yes')?>" : function() {
				var id = target.attr('item'),
					name = target.hasClass("country"),
					obj = name ? 'countries' : 'delivery';
               $('.spinner').show();
               $.ajax({
                    url:'index.php?cmd=calculator&do=delete&tbl='+obj+'&id='+id,
                    success:function (data) {
                        if (data != 'Ok') {
                            $('#error').html('<?=LangAdmin::get('there_was_an_error')?> ' + data);
                        }
						$('#confirm-form').dialog('close');
						window.location.reload();
                    },
                    error:function () {
                        $('#error').html('<?=LangAdmin::get('there_was_an_error')?> ');
                    }
                });
           },
           "<?=LangAdmin::get('no');?>" : function() { $(this).dialog("close"); }
       }
    });

    $('.del').button().click(function() {
		target = $(this);
		$('#confirm-form').dialog('open');
    });

	$( "#dialog-form" ).dialog({
		autoOpen: false,
		width:210,
		height:160,
		modal: true,
		buttons: {
			"Закрыть": function() {
				$(this).dialog("close");
			}
		}
	});

    $('a.link_edit').click(function(){
        var country_id = $(this).attr('country_id'),
            delivery_id = $('select[name=delivery]').val();
            if (country_id && delivery_id) {
                window.location.search = '?cmd=calculator&do=delivery_for_country_edit&country_id='+country_id+'&delivery_id='+delivery_id;
            }
     });

	setCountries();

});
</script>


<? include (TPL_DIR . "footer.php"); ?>

