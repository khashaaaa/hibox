<br/>
<h2><?=LangAdmin::get('edit_price_group')?></h2>
<br/>
<form method="post" action="index.php?do=pricing&tab=2&editgroup&id=<?=@$group_info['id']?>" id="edit-form">
    <table>
        <tr>
            <td width="30%"><?=LangAdmin::get('group_name')?></td>
            <td>
                <input type="text" name="name" disabled="disabled" value="<?=@$group_info['name']?>"/>
                <input type='hidden' name='id' value='<?=@$group_info['id']?>'/>
                <input type='hidden' name='name' value='<?=@$group_info['name']?>'/>
            </td>
        </tr>
        <tr>
            <td><?=LangAdmin::get('description')?></td>
            <td>
                <input type="text" name="desc" value="<?=@$group_info['description']?>"/>
            </td>
        </tr>
        <tr>
            <td><?=LangAdmin::get('default_price_group')?></td>
            <td>
                <? $checked = ($group_info['isdefault']=='true') ? ' checked' : ''; ?>
                <input type="checkbox" name="default_group" value="1" <?=$checked?>/>
            </td>
        </tr>
        <tr>
            <td><?=LangAdmin::get('type_of_price_group')?></td>
            <td> 
                <? //var_dump($strategy_list); ?>
                <select name="strategy" id="strategy_select2">
                    <? foreach ($strategy_list as $strategy) { ?>
                        <? $selected = ($strategy['name'] == $group_info['strategytype']) ? ' selected' : ''; ?>
                        <? if ($selected) $desc = $strategy['description']; ?>
                        <option value="<?=$strategy['name']?>" title="<?=$strategy['description']?>" <?=$selected?>><?=$strategy['name']?></option>
                    <? } ?>
                </select><br/>
                <small id="description2">
                    <?=@$desc?>
                </small>
            </td>
        </tr> 
        <tr>
            <td><?=LangAdmin::get('delivery')?></td> 
            <td> 
                <input type="text" name="delivery_all" value="<?=$group_info['settings']['internaldeliveryprice']?>"/>
            </td>
        </tr>
        <tr>
            <td><strong><?=LangAdmin::get('intervals')?></strong></td>
            <td><button class="add_interval_1"><?=LangAdmin::get('add_new_interval')?></button></td>
        </tr>
        <tr>
            <td colspan="2">
                <table id="intervals_edit">
                    <tr style='border-top: 1px dotted #D3D3D3;'>
                        <td width='150px'><?=LangAdmin::get('markup_percentage')?></td>
                        <td><?=LangAdmin::get('min_price')?>, <?=LangAdmin::get('yuan')?></td>
                        <td><?=LangAdmin::get('group_delivery')?>, <?=LangAdmin::get('yuan')?></td>
                        <td></td>
                    </tr>
                    <? foreach (@$group_info['settings']['priceformationintervals'] as $interval) { ?>
                        <tr style='border-top: 1px dotted #D3D3D3;'>
                            <td> 
                                <input type='hidden' name='interval_id[]' value='<?=$interval['id']?>'/>
                                <table>
                                    <tr>
                                        <?php $margin = ((float)$interval['marginpercent'])>0 ?((float)$interval['marginpercent']-1)*100 : '0'; 
                                        //($interval['marginpercent'] != '')  ? ($interval['marginpercent']-1)*100 : 0;  ?>
                                        <? $checked = ($interval['marginpercent'] != '') ? ' checked' : ''; ?>
                                        <td><input type='radio' name='margin_type[][<?=$interval['id']?>]' value='persent' <?=$checked?>/></td>
                                        <td>
                                            <?=LangAdmin::get('margin_in_persent')?><br/>
                                            <input type='text' name='margin[]' value='<?=$margin?>'/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <? $margin_fixed = ($interval['marginfixed'] != '')  ? $interval['marginfixed'] : 0;  ?>
                                        <? $checked = ($interval['marginfixed'] != '') ? ' checked' : ''; ?>
                                        <td><input type='radio' name='margin_type[][<?=$interval['id']?>]' value='fixed' <?=$checked?>/></td>
                                        <td>
                                            <?=LangAdmin::get('fixed_margin')?><br/>
                                            <input type='text' name='margin_fixed[]' value='<?=$margin_fixed?>'/>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td>
                                <input type='text' name='limit[]' value='<?=@$interval['minimumlimit']?>'/>
                            </td>
                            <td>
                                <input type='text' name='delivery[]' value='<?=@$interval['internaldeliveryprice']?>'/>
                            </td>
                            <td>
                                <span class='delete_interval' style='cursor: pointer;' onClick='$(this).parent().parent().remove()'>
                                    <img src='templates/i/del.png' width='12' height='12' align='middle' style='vertical-align:middle'/>
                                </span>
                            </td>
                        </tr>
                    <? } ?>
                </table>
            </td>
        </tr>
    </table>
    <input type="hidden" name="editgroup" value=""/>
</form>
<button class="save_edit"><?=LangAdmin::get('save')?></button>
<button class="cancel_edit"><?=LangAdmin::get('cancellation')?></button>

<script>
    
$('select[name=strategy]').change(function() {
    var selected = $('#strategy_select2 option:selected').val();
    var desc = strategy_list[selected];
    $('#description2').html(desc);
});

$('button[type!=submit]').click(function(){
    return false;
});

$('.add_interval_1')
    .button()
    .click(function () {
        <? 
        $text = file_get_contents(TPL_DIR.'pricing/interval.php');
        $text = str_replace("\n", "", $text);
        ?>
        var content = "<?=$text ?>";
        $('#intervals_edit').append(content);
        $(this).hide();
});
</script>

<br clear="all"/>

