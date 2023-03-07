<form action="<?=BASE_DIR;?>index.php?cmd=support#tabs-1" method="post" enctype="multipart/form-data" >
	<table class="filter" width=100%>		
		<tr>
			<td style="width:150px; text-align:right">
				<label style="width: 150px; display: inline-block"><?=LangAdmin::get('order_number')?></label>
			</td>
			<td>
				<select style="width: 250px;" name="ticket_order_number">
					<?foreach ($arOrderID as $key=>$orderID){?>
					<option value="<?=$key==0?$key:$orderID?>" <?=($key==0&&!$_SESSION['arSubFilter']['ticket_order_number'])||$_SESSION['arSubFilter']['ticket_order_number']===$orderID?'selected':''?>><?=(defined('CFG_PREFIX_REPLACE_ORD')?str_replace('ORD', CFG_PREFIX_REPLACE_ORD, $orderID):(string)$orderID)?></option>
					<?}?>
				</select>
			</td>
            
		</tr>	
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
				<input id="fromdate" style="width: 250px;" name="ticket_date_from"type="text" value="<?=@$_SESSION['arSubFilter']['ticket_date_from']?>">
			</td>
		</tr>
		<tr>
			<td style="width:150px; text-align:right">
				<label style="width: 100px; display: inline-block"><?=LangAdmin::get('date_to')?></label>
			</td>
			<td>
				<input id="todate" style="width: 250px;" name="ticket_date_to"type="text" value="<?=@$_SESSION['arSubFilter']['ticket_date_to']?>">
			</td>
		</tr>
        <tr>
            <td colspan="2">
				<span style="font-size:10px; color:#666;"><?=LangAdmin::get('ticket_help')?></span>
			</td>
        </tr>	
	</table>
	<input name="filter" class="ui-button ui-widget ui-state-default ui-corner-all" type="submit" value="<?=LangAdmin::get('apply_filters')?>">
	<input name="clearFilter" class="ui-button ui-widget ui-state-default ui-corner-all" type="submit" value="<?=LangAdmin::get('reset_filters')?>">
</form>
<br>



 
<? if ($count_t2): ?>	
	
	<? for ($i = $from_t2; $i <= $from_t2 + $perpage_t2-1; $i++) { ?>			
    	<? if (!isset($ordersIDs[$i])) break; ?>
    	<? $order = $ordersIDs[$i]; ?>	
        <? //Найдем есть ли не отвеченные
			foreach ($tickets as $ticket){
				if ($order==$ticket['orderid']) {
					if ($ticket['userLogin']) {
						$ticket['notAnswered']? $need_answer = true : $need_answer = false;						
					} else {
						$need_answer = false;
					}					
					break;
				}
			}
		?>
        	
<div style="display:block; width:100%; border:#CCC 1px solid;"><a style="color: #060; font-weight:700; text-decoration:underline; cursor:pointer" onclick="ChangeView('<?=$order?>');"><?=(defined('CFG_PREFIX_REPLACE_ORD')?str_replace('ORD', CFG_PREFIX_REPLACE_ORD, $order):(string)$order)?> (показать)</a><br> <a href="index.php?cmd=orders&do=orderinfo&id=<?=$order?>" target="_blank"><?=LangAdmin::get('go_to_ticket')?></a></div>
<div id="<?=$order?>" style=" <?=$need_answer? '' : 'display:none;'?>">
    <table>
        <thead>
        <tr>
        	<th>ID</th>
            <th><?=LangAdmin::get('ticket_date')?></th>
            <th><?=LangAdmin::get('user_name')?></th>			
			<th><?=LangAdmin::get('ticket_category')?></th>
			<th><?=LangAdmin::get('ticket_subject')?></th>
            <th><?=LangAdmin::get('ticket_all_messages')?></th>
            <th><?=LangAdmin::get('ticket_unread_messages')?></th>
            <th></th>
        </tr>
        </thead>
        <tfoot>
        
        </tfoot>
        <tbody>
			       
        <? foreach ($tickets as $ticket){ ?>
            <? if ($order==$ticket['orderid']) {?>
			<tr id="ticket<?=$ticket['id']?>">
				<td><?=$ticket['id']?></td>
				<td><?=$ticket['createddate']?></td>
                <td><?=$ticket['userLogin']?$this->escape($ticket['userLogin']):LangAdmin::get('user_was_delete')?></td>
                
				<td><?=$ticket['category']?></td>
				<td><?=htmlspecialchars($ticket['subject'])?></td>
				<td><?=$ticket['msgcount']?></td>
                <td  <? if($ticket['newmsgcount']>0) {?>style="font-weight: bold;"<?} ?>>
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
       		<? } ?>
        <? } ?>
        </tbody>
    </table>    
</div>
<? } ?>
<table>
		<tr>
        	<td colspan="6" class="pagination">
                <? $curpage = $page_t2; ?>
                <? $maxpage = ceil($count_t2 / $perpage_t2); ?>
                <? if ($maxpage > 1) { ?>
                <? for ($i = 1; $i <= $maxpage; $i++) { ?>
                <? if ($curpage == ($i-1)*$perpage_t2) { ?>
                    <span class="active curved"><?=$i?></span>
                    <? } else { ?>
                    <a class="curved" href="<?=$pageurl?>&from_t2=<?=($i-1)*$perpage_t2?>#tabs-1"><?=$i?></a>
                    <? } ?>
                <? } ?>
                <? } ?>
            </td>
        </tr>
</table>
<? endif; ?>
