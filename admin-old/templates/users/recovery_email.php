<?
if (isset($code)){
$message = 
Lang::get('go_recovery_link').': <a href="http://'.$code.'">'.Lang::get('recover').'</a>
<br />
<br />
'.Lang::get('best_regards').' '.General::getConfigValue('site_name').'.';
}
else {
$message = 
Lang::get('your_new_login_details').': <a href="http://'.$_SERVER['HTTP_HOST'].'/index.php?p=login.">'.CFG_SITE_NAME.'</a>:<br />
<br />'.
LangAdmin::get('login').': '.$this->escape($user['Login']).
'<br /> '.
LangAdmin::get('password').': '.$newPass.
'<br />
<br />'.
Lang::get('best_regards').' '.General::getConfigValue('site_name').'.';}
?>
