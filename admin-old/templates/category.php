<?
include ("header.php");

$cid = @$_GET['cid'];

?>

<div class="main"><div class="canvas clrfix">
        
    <div class="col700">
        <div class="tuning">
          <form action="<?=BASE_DIR;?>index.php?sid=<?=$GLOBALS['ssid'];?>&amp;do=add&amp;cmd=category&amp;cid=<?=@$cat_info['id']?>" method="post">  
              <table class="padd">
              <tr><td><?=LangAdmin::get('name')?>:</td><td> <input type="text" name="name" size="60" value="" /></td></tr>
              <tr style="display:none"><td><?=LangAdmin::get('parent_category')?>:</td><td> 
                <input type="text" name="parentid" value="<?=@$pcid?>" />
              </td></tr>
              <tr><td><?=LangAdmin::get('taobao_category')?>:</td><td> <input type="text" name="categoryId" value="" /></td></tr>
              <tr><td><?=LangAdmin::get('visibility')?>:</td><td> <input type="checkbox" checked="checked" name="isvisible" /></td></tr>
              </table>
              <div class="fbut clrfix">	
                  <input type="submit" value=" <?=LangAdmin::get('save')?> "/>
              </div>
          </form>
        </div>    
    </div>


</div></div><!-- /.main -->
<?
include ("footer.php");
?>