<?php include (TPL_DIR . "header.php"); ?>

<div class="main">
	<div class="canvas clrfix">
		<h1> Мои категории </h1>
		<p></p>

	<?php if (@$categories) : ?>
		<table>
			<tr>
				<th width="70">ID</th>
				<th width="70">PID</th>
				<th><?=LangAdmin::get('name');?></th>
				<th><?=LangAdmin::get('description');?></th>
				<th><?=LangAdmin::get('actions');?></th>
			</tr>
		<?php $this->_htmlTree(); ?>
		</table>
	<?php else : ?>
		<h2 style="border-bottom:none;"><?=LangAdmin::get('not_found_categories')?></h2>
	<?php endif; ?>
		<input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all" value="<?=LangAdmin::get('add_a_category')?>" onclick="location.href='?cmd=my_categories&do=add_category'"/>
	</div>
</div>

<div class="windialog" id="confirm-form" title="<?=LangAdmin::get('confirmation')?>">
	<?=LangAdmin::get('the_record_will_be_removed')?>, <?=LangAdmin::get('proceed')?>?
	<div style="display:none;" class="spinner"></div>
</div>

<script type="text/javascript">
$(function(){
	var target = '';

    $('#confirm-form').dialog({
       autoOpen: false,
       modal: true,
       buttons : {
           "<?=LangAdmin::get('yes')?>" : function() {
			var id = target.attr('item');
               $('.spinner').show();
               $.ajax({
                    url:'index.php?cmd=my_categories&do=del_category&id='+id,
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
