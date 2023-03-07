<?
	$user = base64_decode($refId);
	$userData = explode('|', $user);
?>
<div class="form-row">
    <label for="parent"><?=Lang::get('login_of_friend')?><br/><small><?=Lang::get('no_matter')?></small></label>
    <input <?=! empty($userData[0]) ? '' : 'id="parent"'?> type="text" <?=! empty($userData[0]) ? '' : 'name="parent"'?> class="form-field" <?=! empty($userData[0]) ? 'disabled' : ''?> value="<?=$userData[0]?>"/><br/>
    <small><?=Lang::get('referal_register_desc')?></small>
    <input id="parent_id" type="hidden" name="parent_id" value="<?=$userData[1];?>"/>
    <? if (! empty($userData[0])) { ?>
    <input id="parent" type="hidden" name="parent" value="<?=$userData[0];?>"/>
    <? } ?>
</div>