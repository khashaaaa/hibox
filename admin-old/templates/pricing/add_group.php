<br/>
<h2><?=LangAdmin::get('add_price_group')?></h2>
<br/>

<form method="post" action="index.php?do=pricing&addgroup">
    <table>
        <tr>
            <td width="30%"><?=LangAdmin::get('group_name')?></td>
            <td>
                <input type="text" name="name" value=""/>
            </td>
        </tr>
        <tr>
            <td><?=LangAdmin::get('description')?></td>
            <td>
                <input type="text" name="desc" value=""/>
            </td>
        </tr>
        <tr>
            <td><?=LangAdmin::get('default_price_group')?></td>
            <td>
                <input type="checkbox" name="default_group" value="1" />
            </td>
        </tr>
        <tr>
            <td><?=LangAdmin::get('type_of_price_group')?></td>
            <td>
                <select name="strategy" id="strategy_select">
                    <? foreach ($strategy_list as $strategy) { ?>
                        <option value="<?=$strategy['name']?>" title="<?=$strategy['description']?>"><?=$strategy['name']?></option>
                    <? } ?>
                </select><br/>
                <small id="description">
                    <?=$strategy_list[0]['description']?>
                </small>
            </td>
        </tr> 
        <tr>
            <td><?=LangAdmin::get('delivery')?></td> 
            <td> 
                <input type="text" name="delivery_all" value=""/>
            </td>
        </tr>
        <tr>
            <td><?=LangAdmin::get('intervals')?></td>
            <td><button class="add_interval"><?=LangAdmin::get('add_new_interval')?></button></td>
        </tr>
        
        <tr>
            <td colspan="2">
                <table id="intervals">
                    <tr style='border-top: 1px dotted #D3D3D3;'>
                        <td width='150px'><?=LangAdmin::get('markup_percentage')?></td>
                        <td><?=LangAdmin::get('min_price')?>, <?=LangAdmin::get('yuan')?></td>
                        <td><?=LangAdmin::get('group_delivery')?>, <?=LangAdmin::get('yuan')?></td>
                        <td></td>
                    </tr>
                    <? include (TPL_DIR.'pricing/interval.php'); ?>
                </table>
            </td>
        </tr>
    </table>
    <input type="submit" value="<?=LangAdmin::get('save')?>"/>
</form>
<script>
 
$('button[type!=submit]').click(function(){
    return false;
});

var strategy_list = Array();
<? foreach ($strategy_list as $strategy) { ?>
    strategy_list['<?=$strategy['name']?>'] = '<?=$strategy['description']?>';
<? } ?>
    
$('select[name=strategy]').change(function() {
    var selected = $('#strategy_select option:selected').val();
    var desc = strategy_list[selected];
    $('#description').html(desc);
});

$('.add_interval')
    .button()
    .click(function () {
        <? 
        $text = file_get_contents(TPL_DIR.'pricing/interval.php');
        $text = str_replace("\n", "", $text);
        ?>
        var content = "<?=$text ?>";
        $('#intervals').append(content);
        $(this).hide();
});
</script>

<br clear="all"/>

