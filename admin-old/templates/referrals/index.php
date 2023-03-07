<? include (TPL_DIR . "header.php"); ?>

<?
function showReferralsList($list, $level, $status){
    $request = new RequestWrapper();
    $statuses = array(
        '-'
    , 'Участник'
    , 'Лидер'
    , 'Босс'
    , 'Президент'
    );
    if(isset($list['list'])){
        foreach($list['list'] as $r){?>
        <tr <?if($status == 1 && $level > 2){?>style="color: gray"<?}?>>
            <td><?=$r['idx']?></td>
            <td><?=$level?></td>
            <td><a href="index.php?cmd=referrals&pid=<?=$r['idx']?>"><?=$this->escape($r['login'])?></a></td>
            <td><?=$statuses[$r['status']]?></td>
            <td><?=$r['comission']?></td>
            <td><?=$r['balance']?></td>
            <td><?=$r['presents_from_children']?></td>
            <td>
                <?if($r['parent_id'] == $request->get('pid') && $r['present_exists_to_parent']){?>
                <button class="send_present" item="<?=$r['idx']?>">
                    <?=$r['present_exists_to_parent']?>
                </button> (<?=$r['present_exists_to_parent_sent']?>)
                <?}else{?>
                <?=$r['present_exists_to_parent']?> (<?=$r['present_exists_to_parent_sent']?>)
                <?}?>
            </td>
        </tr>
        <?}
        if(isset($list['children']))
            showReferralsList($list['children'], $level+1, $status);
    }
}
?>

<div class="main">
	<div class="canvas clrfix">
		<h1> Бонусная программа </h1>
		<a href="<?php echo BASE_DIR; ?>index.php?sid=<?=$GLOBALS['ssid'];?>&amp;cmd=referrals">Корень</a>
		<?php foreach ($chain as $key => $item): ?>
		-> <a href="<?php echo BASE_DIR; ?>index.php?sid=<?=$GLOBALS['ssid'];?>&amp;cmd=referrals&pid=<?php echo $key; ?>"><?php echo $item ?></a>
		<?php endforeach; ?>

		<hr>

	<? if ($status) : ?>
		<table>
			<tr>
				<th>ID</th>
				<th>Уровень</th>
				<th>Логин</th>
				<th>Статус</th>
                <th>Комиссия</th>
                <th>Баланс</th>
                <th>Подарки для пользователя</th>
                <th>Подарки от пользователя (отправлено)</th>
			</tr>
            <?=showReferralsList($referrals,  2, $currentUserStatus)?>
		</table>
	<? else : ?>

	<? endif; ?>

	</div>
</div>

<div class="windialog" id="confirm-form" title="<?=LangAdmin::get('confirmation')?>">
	Подтверждаете отправление подарка?
	<div style="display:none;" class="spinner"></div>
</div>

<script type="text/javascript">
$(function(){
	var target = '';

	$('.send_present').button().click(function() {
		target = $(this);
		$('#confirm-form').dialog('open');
    });

    $('#confirm-form').dialog({
       autoOpen: false,
       modal: true,
       buttons : {
           "<?=LangAdmin::get('yes')?>" : function() {
			var id = target.attr('item');
               $('.spinner').show();
               $.ajax({
                    url:'index.php?cmd=referrals&do=sendPresent&id='+id,
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
});
</script>

<? include (TPL_DIR . "footer.php");