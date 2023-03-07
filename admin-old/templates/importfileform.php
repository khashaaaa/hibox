<?
include ("header.php");
$cid = @$_GET['cid'];
?>

<div class="main"><div class="canvas clrfix">
        <div class="col700">
            <div class="tuning">
                <h2> <?=LangAdmin::get('import_categories')?> </h2>
                <strong><?=LangAdmin::get('import_file_requirement_csv')?></strong><br/><br/>
                <form id="form1" action="<?=BASE_DIR;?>index.php?sid=<?=$GLOBALS['ssid'];?>&amp;do=importToTranslate&amp;cmd=category" method="post" enctype="multipart/form-data">
                    <label><?=LangAdmin::get('select_the_file')?>:</label>
                    <input type="file" name="userfile" accept="text/html"  onchange="check_file()" /><br/><br/>
                    <!-- <input type="submit" value="Send File"/>  -->
                </form>
                <button id="submit1"><?=LangAdmin::get('imports')?></button>
                <button id="submit2"><?=LangAdmin::get('cancellation')?></button>
            </div>
        </div>
<script>
$( "#submit1" )
    .button()
    .click(function() {
            $("#form1").submit();
});
$( "#submit2" )
    .button()
    .click(function() {
            location.href = 'index.php?cmd=category';
});

function check_file() {
    var fileName = $('input[name=userfile]').val();
    if (fileName == "") {
        alert("Не выбран файл!");
    }
    else if (fileName.split(".")[1] != "csv") {
        alert("Не верное расширение файла " + fileName.split(".")[1] + ". Требуемое расширение: .csv!");
        $('input[name=userfile]').val('');
        $('input[name=userfile]').click();
    }
}
</script>
</div></div><!-- /.main -->
<?
include ("footer.php");
?>