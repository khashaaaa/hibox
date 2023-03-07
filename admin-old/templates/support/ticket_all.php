
<form action="<?=BASE_DIR;?>index.php?cmd=support#tabs-2" method="post" enctype="multipart/form-data" >
	<table class="filter" width=100%>
        <tr>
            <td style="width:150px; text-align:right">
                <label style="width: 100px; display: inline-block">ID</label>
            </td>
            <td>
                <input id="ticket_id" style="width: 250px;" name="ticket_id"type="text" value="<?=@$_SESSION['arSubFilter']['ticket_id']?>">
            </td>
        </tr>	
		<tr>
			<td style="width:150px; text-align:right">
				<label style="width: 100px; display: inline-block"><?=LangAdmin::get('date_from')?></label>
			</td>
			<td>
				<input id="fromdate_all" style="width: 250px;" name="ticket_date_from"type="text" value="<?=@$_SESSION['arSubFilter']['ticket_date_from']?>">
			</td>
		</tr>
		<tr>
			<td style="width:150px; text-align:right">
				<label style="width: 100px; display: inline-block"><?=LangAdmin::get('date_to')?></label>
			</td>
			<td>
				<input id="todate_all" style="width: 250px;" name="ticket_date_to"type="text" value="<?=@$_SESSION['arSubFilter']['ticket_date_to']?>">
			</td>
		</tr>
		<tr>
			<td style="width:150px; text-align:right">
				<label style="width: 150px; display: inline-block"><?=LangAdmin::get('ticket_new_and_need_answer')?></label>
			</td>
			<td>
				<input name="ticket_new"type="checkbox" value="1" <?=@$_SESSION['arSubFilter']['ticket_new']?'checked':''?>>
			</td>
            
		</tr>
        <tr>
            <td colspan="2">
				<span style="font-size:10px; color:#666;">При переходе на другую вкладку рекомендуем сбросить фильтры, чтобы избежать приминения этого фильтра к другому списку тикетов.</span>
			</td>
        </tr>        
	</table>
	<input name="filter" class="ui-button ui-widget ui-state-default ui-corner-all" type="submit" value="<?=LangAdmin::get('apply_filters')?>">
	<input name="clearFilter" class="ui-button ui-widget ui-state-default ui-corner-all" type="submit" value="<?=LangAdmin::get('reset_filters')?>">
</form>
<br>
<? if ($count): ?>
<div class="grid_16">
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th><?=LangAdmin::get('ticket_date')?></th>
            <th><?=LangAdmin::get('user_name')?></th>
			<th><?=LangAdmin::get('order_number')?></th>
			<th><?=LangAdmin::get('ticket_category')?></th>
			<th><?=LangAdmin::get('ticket_subject')?></th>
            <th><?=LangAdmin::get('ticket_all_messages')?></th>
            <th><?=LangAdmin::get('ticket_unread_messages')?></th>
            <th></th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <td colspan="6" class="pagination">
                <? $curpage = $page; ?>
                <? $maxpage = ceil($count / $perpage); ?>

                <? for ($i = 1; $i <= $maxpage; $i++) { ?>
                <? if ($curpage == ($i-1)*$perpage) { ?>
                    <span class="active curved"><?=$i?></span>
                    <? } else { ?>
                    <a class="curved" href="<?=$pageurl?>&from=<?=($i-1)*$perpage?>#tabs-2"><?=$i?></a>
                    <? } ?>
                <? } ?>
            </td>
        </tr>
        </tfoot>
        <tbody>
        
        <? for ($i = $from; $i <= $from + $perpage; $i++): ?> 
            
            <? if (!isset($ticketsE[$i])) break; ?>
            <? $ticket = $ticketsE[$i]; ?>
			<tr id="ticket<?=$ticket['id']?>">
                <td><?=$ticket['id']?></td>
				<td><?=$ticket['createddate']?></td>
                <td><?=$ticket['userLogin']?$this->escape($ticket['userLogin']):LangAdmin::get('user_was_delete')?></td>
                <td>
                    <? if ($ticket['orderid']) { ?>
                        <a href="index.php?cmd=orders&do=orderinfo&id=<?=@$ticket['orderid']?>" target="_blank">
                        <?=@$ticket['orderid']?>
                        </a>
                    <? } ?>
                </td>
				<td><?=$ticket['category']?></td>
				<td><?=htmlspecialchars($ticket['subject'])?></td>
				<td><?=$ticket['msgcount']?></td>
                
                <td <? if($ticket['newmsgcount']>0) {?>style="font-weight: bold;"<?} ?>>
                <? if ($ticket['userLogin']) { ?>
					<?=$ticket['newmsgcount']?>
					<?=$ticket['notAnswered']?' (<span class="required">'.LangAdmin::get('ticket_not_answered').'</span>)':''?>
                <? } ?>    
				</td>
                <td>
                <? if ($ticket['user']) { ?>
                    <form target="_blank" action="../index.php">
                        <input type="hidden" name="sid" value="<?=$_SESSION['sid']?>" />
                        <input type="hidden" name="admin" value="On" />
                        <input type="hidden" name="p" value="support" />
                        <input type="hidden" name="mode" value="chat" />
                        <input type="hidden" name="id" value="Ticket-<?=$ticket['id']?>" />
                        <input type="hidden" name="userid" value="<?=$ticket['user']?>" />
                        <input type="submit" value="<?=LangAdmin::get('view_chat')?>" />
                    </form>
                <? } ?>
                </td>
            </tr>
        <? endfor; ?>
        </tbody>
    </table>
</div>

<? endif; ?>
