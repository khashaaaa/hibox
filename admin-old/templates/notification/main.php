<? include (TPL_DIR."header.php"); ?>

<? if (!defined('NOTIFICATION_LOGIN')) { ?>
    <strong><?=LangAdmin::get('set_notification_login')?></strong><br/><br/>
<? } elseif (!defined('NOTIFICATION_PASS')) { ?>
    <strong><?=LangAdmin::get('set_notification_pass')?></strong><br/><br/>
<? } else { ?>
    <? if (isset($_GET['success'])) { ?>
        <strong><font color="green"><?=LangAdmin::get('notify_success')?></font></strong><br/><br/>
    <? } ?>
    <? if (isset($_GET['error'])) { ?>
        <strong><font color="red"><? echo LangAdmin::get('notify_error').': '.$_GET['error']; ?></font></strong><br/><br/>
    <? } ?>
    <?= $onrendernotificationform;?>
<? } ?>

<? include (TPL_DIR."footer.php"); ?>	