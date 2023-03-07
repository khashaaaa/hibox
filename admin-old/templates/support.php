<? include (TPL_DIR . "header.php"); ?>
<script>
	$(function() {
		$( "#fromdate" ).datepicker();
		$( "#todate" ).datepicker();
		$( "#fromdate_all" ).datepicker();
		$( "#todate_all" ).datepicker();
	});
</script>
<?
//Создаем массив заказов и обычных
$ordersIDs = array();
$ticketsE = array();

foreach ($tickets['content'] as $ticket){
  if ($ticket['orderid']!='') {
  	  $ordersIDs[] = $ticket['orderid'];
  } else {
	  $ticketsE[] = $ticket;
  }  
}

$ordersIDs = @array_unique($ordersIDs);

$tmp = array();
foreach ($ordersIDs as $IDs){
  if (isset($IDs)) 
  	  $tmp[] = $IDs;
}
$ordersIDs = $tmp;

$pageurl = 'index.php?cmd=Support';
$page = (isset($_SESSION['admin_support_page'])) ? $_SESSION['admin_support_page'] : 0;
$page = (isset($_GET['from'])) ? $_GET['from'] : $page;
if ((isset($_GET['from']))) $_SESSION['admin_support_page'] = $_GET['from'];
//$count = (int)$tickets['totalcount'];
$count = count($ticketsE);

$page_t2 = (isset($_SESSION['admin_support_page_t2'])) ? $_SESSION['admin_support_page_t2'] : 0;
$page_t2 = (isset($_GET['from_t2'])) ? $_GET['from_t2'] : $page_t2;
if ((isset($_GET['from_t2']))) $_SESSION['admin_support_page_t2'] = $_GET['from_t2'];
$count_t2 = count($ordersIDs);


$arOrderID = $tickets['filterOrderList'];
$tickets = $tickets['content'];
if (!isset($_SESSION['arSubFilter']['ticket_user_id'])) $_SESSION['arSubFilter']['ticket_user_id']=false;
?>

<div id="dialog-form" title="<?=LangAdmin::get('message')?>">
    <span id="info"></span>
</div>
<? if (isset($success)): ?>
    <strong style="color:green;"><?=$success;?></strong>
<? endif; ?>


<link rel="stylesheet" href="css/EmptyCats.css" type="text/css" media="screen" charset="utf-8">

<? $newLink = 'http://' . HOST_NAME . '/admin/?cmd=support'; ?>

<div class="main"><div class="canvas clrfix">
    <div id="attention">
        <?=LangAdmin::get('redirect_to_new_admin')?>
        <a href="<?=$newLink?>"><?=LangAdmin::get('redirect_to_link')?></a>
    </div>
</div></div>
<br/><br/>


<h2><?=LangAdmin::get('support_requests')?></h2>
<!---
<?if (defined ('ADVANCED_SUPPORT_INTERFACE')){?>
<form action="<?=BASE_DIR;?>index.php?cmd=support" method="post" enctype="multipart/form-data" >
	<table class="filter" width=100%>
		<tr>
			<td style="width:150px; text-align:right">
				<label style="width: 150px; display: inline-block"><?=LangAdmin::get('user_name')?></label>
			</td>
			<td>
				<select style="width: 250px;" name="ticket_user_id">
				<?foreach ($arUserInfo as $userInfo){?>
				<option value="<?=$userInfo['Id']?>" <?=$_SESSION['arSubFilter']['ticket_user_id']==$userInfo['Id']?'selected':''?>><?=$this->escape($userInfo['Login'])?></option>
				<?}?>
				</select>
			</td>
		</tr>
		<tr>
			<td style="width:150px; text-align:right">
				<label style="width: 150px; display: inline-block"><?=LangAdmin::get('order_number')?></label>
			</td>
			<td>
				<select style="width: 250px;" name="ticket_order_number">
					<?foreach ($arOrderID as $key=>$orderID){?>
					<option value="<?=$key==0?$key:$orderID?>" <?=($key==0&&!$_SESSION['arSubFilter']['ticket_order_number'])||$_SESSION['arSubFilter']['ticket_order_number']===$orderID?'selected':''?>><?=$orderID?></option>
					<?}?>
				</select>
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
			<td style="width:150px; text-align:right">
				<label style="width: 150px; display: inline-block"><?=LangAdmin::get('ticket_new_and_need_answer')?></label>
			</td>
			<td>
				<input name="ticket_new"type="checkbox" value="1" <?=@$_SESSION['arSubFilter']['ticket_new']?'checked':''?>>
			</td>
		</tr>
	</table>
	<input name="filter" class="ui-button ui-widget ui-state-default ui-corner-all" type="submit" value="<?=LangAdmin::get('apply_filters')?>">
	<input name="clearFilter" class="ui-button ui-widget ui-state-default ui-corner-all" type="submit" value="<?=LangAdmin::get('reset_filters')?>">
</form>
<br>
<?}?>
--->
<div class="main"><div class="canvas clrfix">

<div id="tabs">
        <ul>
            <li id="itab1"><a href="#tabs-1"><?=LangAdmin::get('order_ticket')?></a></li>
            <li id="itab2"><a href="#tabs-2"><?=LangAdmin::get('all_ticket')?></a></li>            
        </ul>        
        <div id="tabs-1"><? include(TPL_DIR.'support/ticket_orders.php'); ?></div>
        <div id="tabs-2"><? include(TPL_DIR.'support/ticket_all.php'); ?></div>
        
    </div>
        
</div></div>


<script>

var tab_number = <? echo (isset($tab_number)) ? $tab_number : 1; ?>;
$("#itab" + tab_number).addClass('ui-tabs-selected').addClass('ui-state-active');
$("#tab-" + tab_number).show();

$(function() {
    $('#tabs').tabs();
    //$("#dialog-confirm:ui-dialog").dialog("destroy");
    //$("#dialog-add-category:ui-dialog").dialog("destroy");
	
});

function ChangeView(orederID) {
	if ($("#" + orederID).css("display")=='none') {
		$("#" + orederID).show(200);
	} else {
		$("#" + orederID).hide(200);
	}
	
}
    
</script>

<?
include (TPL_DIR . "footer.php");
?>	