<!-- confirm deleting modal window -->
<div class="modal hide fade ot_deletion_dialog_modal">
    <button type="button" class="close" data-dismiss="modal">×</button>
    <div class="modal-body">
        <p><?=LangAdmin::get('Remove_confirmation')?> <strong id="item_for_delete"></strong></p>
    </div>
    <div class="modal-footer">
        <a href="javascript:void(0)" class="btn btn-primary pull-left" id="confirm"><?=LangAdmin::get('Delete')?></a>
        <a href="javascript:void(0)" class="btn pull-right" data-dismiss="modal"><?=LangAdmin::get('Cancel')?></a>
    </div>
</div>


<div class="modal hide fade confirmDialog confirmDialog-tpl">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3></h3>
    </div>
    <div class="modal-body"></div>
    <div class="modal-footer">
        <a href="javascript:void(0)" class="btn btn-primary pull-left"
            id="confirm"><?=LangAdmin::get('Yes')?></a>
        <a href="javascript:void(0)" class="btn pull-right" data-dismiss="modal"
            id="cancelBtn"><?=LangAdmin::get('Cancel')?></a>
    </div>
</div>


<div class="modal hide fade choose_region">
    <button type="button" class="close" data-dismiss="modal">×</button>
    <div class="modal-body">
        <ul id="regions"></ul>
    </div>
    <div class="modal-footer">
        <a href="javascript:void(0)" class="btn pull-right" data-dismiss="modal"><?=LangAdmin::get('Cancel')?></a>
    </div>
</div>

<div class="modal hide fade splitItemQuantity">
    <button type="button" class="close" data-dismiss="modal">×</button>
    <div class="modal-body">
        <?=LangAdmin::get('Split_item_suggestion')?><br/>
        <input type="text" name="split" value="1" />
    </div>
    <div class="modal-footer">
        <a href="javascript:void(0)" class="btn btn-primary pull-left" id="confirm"><?=LangAdmin::get('Split')?></a>
        <a href="javascript:void(0)" class="btn pull-right" data-dismiss="modal"><?=LangAdmin::get('Cancel')?></a>
    </div>
</div>


<!-- feedback-support form modal window -->
<div class="modal hide fade ot_modal_dialog_window">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3><?=LangAdmin::get('Support_form')?></h3>
    </div>
    <div class="modal-body"><p></p></div>
    <div class="modal-footer">
        <a href="javascript:void(0)" class="btn btn-primary pull-left"><?=LangAdmin::get('Submit')?></a>
        <a href="javascript:void(0)" class="btn pull-right" data-dismiss="modal"><?=LangAdmin::get('Close')?></a>
    </div>
</div>

        <div id="infoMessageModal" class="modal hide fade">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3><?=LangAdmin::get('InfoMessage')?></h3>
            </div>
            <div class="modal-body">
                <p id="infoMessageBody"></p>
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0)" class="btn" data-dismiss="modal" aria-hidden="true"><?=LangAdmin::get('Close')?></a>
            </div>
        </div>

        <div id="errorMessageModal" class="modal hide fade">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3><?=LangAdmin::get('Error')?></h3>
            </div>
            <div class="modal-body">
                <p id="errorMessageBody"></p>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal" aria-hidden="true"><?=LangAdmin::get('Close')?></a>
            </div>
        </div>

<div class="modal hide fade loginDialog" style="z-index: 100000;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3><?=LangAdmin::get('Session_expired')?></h3>
    </div>
    <div class="modal-body">
<div class="modalLogin">
    <form class="form-horizontal ot_form ot_auth_form" method="POST" action="<?=$PageUrl->AssignCmdAndDo('Login', 'loginAjax')?>">
        <div class="control-group">
            <label class="control-label" for="ot_auth_login"><?=LangAdmin::get('Login')?></label>
            <div class="controls">
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-user"></i></span>
                    <input class="input-medium" id="ot_auth_login" type="text" name="login" required="" autofocus="autofocus" value="<?= isset($login) ? $login : ''?>"/>
                </div>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="ot_auth_password"><?=LangAdmin::get('Password')?></label>
            <div class="controls">
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-lock"></i></span>
                    <input class="input-medium" id="ot_auth_password" type="password" name="password" required="" value="<?= isset($password) ? $password : ''?>"/>
                </div>
            </div>
        </div>

        <? if (General::getConfigValue('auth_capcha_admin')){ ?>
        <div class="control-group">
            <div class="controls">
                <div class="input-prepend">
                    <img id="siimage" data-src="../lib/securimage/securimage_show.php?sid=<?php echo md5(uniqid()) ?>" style="border: 1px solid #000; margin-right: 15px" alt="CAPTCHA Image" align="left">
                </div>
            </div>
        </div>
        <div class="control-group">
            <div class="controls">
                <div class="input-prepend">
                    <?php echo @$_SESSION['ctform']['captcha_error'] ?>
                    <input type="text" name="ct_captcha" class="captchainp" size="12" maxlength="8" />
                </div>
            </div>
        </div>
        <? } ?>
    </form>
</div>
</div>
    <div class="modal-footer">
        <a href="javascript:void(0)" class="btn btn-primary pull-left"
            id="confirm"><?=LangAdmin::get('Authorization')?></a>
        <a href="javascript:void(0)" class="btn pull-right" data-dismiss="modal"
            id="cancelBtn"><?=LangAdmin::get('Cancel')?></a>
    </div>
</div>