<? include (TPL_DIR."header.php");

$title = empty($id) ? 'Добавить товар' : 'Редактировать товар';
$item = isset($goods) ? $goods[0] : array();
$attibutes = !empty($item['properties']) ? unserialize($item['properties']) : array();
if (isset($item['image'])) {
	$file_parts = explode('.', $item['image']);
	$ext = end($file_parts);
	$image = str_replace('.' . $ext, '_100x100.' . $ext, $item['image']);
}
?>

<br/>
<a href="index.php?sid=<?=$GLOBALS['ssid']?>&amp;cmd=my_goods"> << <?=LangAdmin::get('back')?></a><br/>

<h2><?=$title?></h2>
<form action="<?=BASE_DIR;?>index.php?cmd=my_goods&amp;do=save&amp;sid=<?=$GLOBALS['ssid'];?>" method="post">
    <table>
        <tr>
            <td width="30%"><strong><?=LangAdmin::get('product_name')?>:</strong></td>
            <td>
				<input type="text" style="width:70%" name="name" value="<?= (!empty($item)) ? $item['name'] : '';?>"/>
				<input type="hidden" name="id" value="<?= (!empty($item)) ? $item['id'] : '';?>"/>
				<input type="hidden" name="cat" value="<?= (isset($_GET['cat'])) ? (int)$_GET['cat'] : (!empty($item['my_category_id']) ? $item['my_category_id'] : 0);?>"/>
			</td>
        </tr>
        <tr>
            <td><strong><?=LangAdmin::get('description')?>:</strong></td>
            <td><input type="text" style="width:70%" name="description" value="<?= (!empty($item)) ? $item['description'] : '';?>"/></td>
        </tr>
        <tr>
            <td><strong><?=LangAdmin::get('image')?>:</strong></td>
            <td><div id="file-uploader"></div>
				<input type="hidden" name="PictureUrl" />
			</td>
        </tr>
        <tr>
            <td><strong><?=LangAdmin::get('price');?>:</strong></td>
            <td><input type="text" style="width:70%" name="price" value="<?= (!empty($item)) ? $item['price'] : '';?>"/></td>
        </tr>
        <tr>
            <td><strong><?=LangAdmin::get('quantity_of_goods');?>:</strong></td>
            <td><input type="text" style="width:70%" name="amount" value="<?= (!empty($item)) ? $item['amount'] : '';?>"/></td>
        </tr>
        <tr>
            <td valign="bottom"><strong>Атрибуты товара:</strong> <button type="button" id="add_attribute">Добавить</button></td>
            <td id="attributs">
				<?php foreach ($attibutes as $key => $value) : ?>

				<div style="margin-bottom:6px">
					<strong>Атрибут:</strong> <input class="atr_name" name="atr_name[]" value="<?=$key;?>"></input>
					<strong>Значение:</strong> <input class="atr_val" name="atr_val[]" value="<?=$value;?>"></input>
					<img class="del" width="12" height="12" align="middle" style="vertical-align:middle" src="templates/i/del.png">
				</div>
				<?php endforeach; ?>

			</td>
        </tr>
    </table>

    <div class="fbut clrfix">
        <input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all" value="<?=LangAdmin::get('save')?>"/>
    </div>
</form>

<script type="text/javascript">
$(function(){


    function createUploader() {
        var uploader = new qq.FileUploader({
            element:document.getElementById('file-uploader'),
            action:'utils/Uploader.php?resize[]=310&resize[]=160&resize[]=100',
            debug:true,
            template:'<div class="qq-uploader">' +
                '<div class="qq-upload-drop-area"><span></span></div>' +
                '<div class="qq-upload-button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" style="height:23px;padding-top:7px">&nbsp;&nbsp;&nbsp;<?=LangAdmin::get('select_a_picture')?>&nbsp;&nbsp;&nbsp;</div>' +
                '<ul class="qq-upload-list"></ul>' +
                '</div>',
            onComplete:function (id, fileName, responseJSON) {
				var fileUrl = responseJSON.url,
							parts, ext = ( parts = fileUrl.split("/").pop().split(".") ).length > 1 ? parts.pop() : "";
							var newUrl = fileUrl.replace('.'+ext,"_100x100."+ext);
                $('.qq-upload-list').empty().append($('<li></li>').append($('<img />').attr('src', newUrl + '?' + Math.random())));
                $('[name="PictureUrl"]').val(fileUrl);
            }
        });
    }

	$('#add_attribute').click(function(){
		$('<div style="margin-bottom:6px"><strong>Атрибут:</strong> <input class="atr_name" name="atr_name[]"></input> <strong>Значение:</strong> <input class="atr_val" name="atr_val[]"></input> <img width="12" height="12" align="middle" style="vertical-align:middle" src="templates/i/del.png"></div>').fadeIn('slow').appendTo('td#attributs');
	});

	$('img.del').click(function(){
		var idx = $('td#attributs img').index($(this));
		$('td#attributs div:eq('+idx+')').remove();
	});

	createUploader();
	<? if (isset($item['image'])) : ?>

	$('.qq-upload-list').empty().append($('<li></li>').append($('<img />').attr('src', '<?=$image;?>')));
    <? endif; ?>
});
</script>

<? include (TPL_DIR."footer.php"); ?>
