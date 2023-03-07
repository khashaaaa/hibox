<? if ($ticketid==-1) {  ?>
<h2><?=LangAdmin::get('create_dialog')?></h2>
<div class="userform newmessage">
<form action="<?=$_SERVER['REQUEST_URI']?>" method="post">        
        <input id="Subject" type="hidden" name="Subject" value="<?=$subjectChat?>" />      
        <input id="SalesId" type="hidden" name="SalesId" value="<?=$order_info['salesorderinfo']['id']?>" />
		<input id="CategoryId" type="hidden" name="CategoryId" value="<?=$categoryIdChat?>" /> 
       
            <label for="Text"><?=Lang::get('message_text')?>:</label><br>
            <textarea id="Text" name="Text" style="width:100%; height:100px;">       </textarea><br><br>
        
            <input type="submit" name="send" value="<?=Lang::get('send_message')?>" class="btn_office" />
        
    </form>
</div>
<? } else { ?>
					<? foreach($chat['TicketMessageList'] as $msg){ ?>
                    <div style="width:100%; margin-top:10px; border-top:#666 1px solid;">
                            <div style="background-color:#CCC;">
                                    <div class="date flr"><?=$msg['CreatedDate']?></div>
                                    <div class="name">
                                        <? 
                                        if($msg['Direction']=='In'){                                             
                                            print Lang::get('user');
                                        }
                                        else{
                                            print Lang::get('operator'); 
                                        }
                                        ?>
                                    </div>
                            </div>
                            <div class="content">
                                    <?=nl2br(htmlspecialchars($msg['Text']))?>
                            </div>
                    </div>
                    <? } ?>
                    <br><br>
                    <h2><?=Lang::get('new_message2')?></h2>
					
                        <form action="<?=$_SERVER['REQUEST_URI']?>" method="post">				
						<label for="Text"><?=Lang::get('message_text')?>:</label><br>
						<textarea id="Text" name="Text" style="width:100%; height:100px;">       </textarea><br><br>
						<input type="submit" name="send" value="<?=Lang::get('send_message')?>" class="btn_office" />
						</form>

<? } ?>