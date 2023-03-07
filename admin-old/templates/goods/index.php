<?php include (TPL_DIR . "header.php"); ?>

<div class="main">
	<div class="canvas clrfix">
		<h1><?=LangAdmin::get('my_goods');?></h1>
		<p></p>
		<select name="categiries" id="categories">
			<option value="0" disabled="disabled" selected="selected"><?=LangAdmin::get('select_category');?></option>
			<?php foreach ($categories as $category) : ?>
			<option value="<?=$category['id'];?>"<? if ($category['id']==$category_id):?> selected<?endif;?>><?=$category['name'];?></option>
			<?php endforeach; ?>
		</select>
		<p></p>
		<?php if (@$goods) : ?>
		<table>
			<tr>
				<th width="70">ID</th>
				<th><?=LangAdmin::get('name');?></th>
				<th><?=LangAdmin::get('description_of_goods');?></th>
				<th><?=LangAdmin::get('quantity_of_goods');?></th>
				<th><?=LangAdmin::get('cost_of_goods');?></th>
				<th><?=LangAdmin::get('actions');?></th>
			</tr>
			<?php foreach ($goods as $good) :?>

			<tr>
				<td width="70"><?=$good['id'];?></td>
				<td><?=$good['name'];?></td>
				<td><?=$good['description'];?></td>
				<td><?=$good['amount'];?></td>
				<td><?=$good['price'];?></td>
				<td>
					<a href="?cmd=my_goods&do=edit_good&id=<?=$good['id']?>"><?=LangAdmin::get('edit')?></a>
					<a class="del" item="<?=$good['id'];?>"><?=LangAdmin::get('remove')?></a>
				</td>
			</tr>
			<?php endforeach; ?>
		</table>
		<?php else : ?>
		<h2 style="border-bottom:none;"><?=LangAdmin::get('no_goods')?></h2>
		<?php endif; ?>
		<input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all" value="<?=LangAdmin::get('add_goods')?>"/>
	</div>

        <div class="windialog" id="confirm-form" title="<?=LangAdmin::get('confirmation')?>">
            <?=LangAdmin::get('the_record_will_be_removed')?>, <?=LangAdmin::get('proceed')?>?
            <div style="display:none;" class="spinner"></div>
        </div>

</div>

<script type="text/javascript">
$(function(){
	var target = '';

    $('select[name=categiries]').change(function() {
		$('select[name=categiries]').css('color','black');
		window.location.search = '?cmd=my_goods&cat='+$(this).val();
    });

    $('input[type=submit]').click(function() {
		var category = $('select[name=categiries]').val();
		if (category>0) {
			window.location.search = '?cmd=my_goods&do=add_goods&cat='+category;
		} else {
			$('select[name=categiries]').css('color','red');
			$('select[name=categiries] option').css('color','black')
		}
    });

    $('#confirm-form').dialog({
       autoOpen: false,
       modal: true,
       buttons : {
           "<?=LangAdmin::get('yes')?>" : function() {
			var id = target.attr('item');
               $('.spinner').show();
               $.ajax({
                    url:'index.php?cmd=my_goods&do=del_good&id='+id,
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
});
</script>

<?php include (TPL_DIR . "footer.php"); ?>