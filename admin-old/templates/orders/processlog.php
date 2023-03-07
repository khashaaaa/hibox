<div class="grid_16">
<? if(count($processlog)) {?>
<table>
    <thead>
        <tr>
            <th><?=LangAdmin::get('date')?> <?=LangAdmin::get('time')?></th>
            <th><?=LangAdmin::get('status')?></th>
        </tr>
    </thead>

    <tbody>
        <? if (is_array($processlog)) foreach($processlog as $process) { ?>
        <tr>
            <td><? echo $process['logdate'].' '. $process['logtime']; ?></td>
            <td><?=$process['prevvalue']?> --> <?=$process['newvalue']?></td>
        </tr>
        <? } ?>
    </tbody>
</table>
<? } else { ?>
    <br/><strong><?=LangAdmin::get('operations_on_the_order_found')?>!</strong><br/>
<? } ?>
</div>
 <br clear="all"/>
