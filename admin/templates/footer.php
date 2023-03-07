            </div><!-- /.ot_content -->
        </div><!-- /.span10-->

    </div><!--/fluid-row-->

    <div id="underground"></div><!-- extra element for pushing footer -->

</section><!-- /#wrapper-->

<? require TPL_ABSOLUTE_PATH . 'global_modals.php'; ?>

<!-- smtp -->
<?= $smtpSettings; ?>
<!-- /smtp -->

<!-- global footer -->
<footer id="footer">
    <?=General::viewFetch('footer_content', array(
        'path' => TPL_PATH,
        'vars' => array()
    ));?>
</footer>

<a href="#top" rel="go_to_top" title="<?=LangAdmin::get('Up')?>"><i class="icon-long-arrow-up"></i></a>

<? AssetsMin::jsBegin(); ?>
    <script>
        var tinyMceContentCss = '';
        <? if (General::getConfigValue('is_old_platform') || General::getConfigValue('design_theme') === General::$baseTheme) { ?>
            tinyMceContentCss += '../css/vendor/bootstrap.min.css,../css/style.css';
        <? } ?>
        <? if (! General::getConfigValue('is_old_platform')) { ?>
            tinyMceContentCss = tinyMceContentCss.length ? tinyMceContentCss + ',' : tinyMceContentCss;
            tinyMceContentCss += '<?= AssetsMin::getCollectedCssFile('general.css', General::getThemeWebDir() . '/config') ?>';
        <? } ?>

        var activeAdminLang = '<?= Session::getActiveAdminLang() ?>';
        var rtlDir = <?= General::getConfigValue('rtl', false) ? 'true' : 'false' ?>; <!-- bool -->
    </script>
<? $strScript = AssetsMin::jsEnd(); ?>
<? AssetsMin::registerJs($strScript); ?>

<!-- Translations -->
<script src="<?=$PageUrl->getTranslationsUrl();?>"></script>

<?=AssetsMin::printJsFilesGroup('admin_general.js', '/admin/cfg')?>

<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE8+ -->
<!--[if gte IE 8]><script src="js/vendor/jQuery-File-Upload-master/js/cors/jquery.xdr-transport.js?<?=CFG_ADMIN_VERSION;?>"></script><![endif]-->

<?=ErrorHandler::showRegisteredErrorsWithPNotify()?>

    <? if (Session::checkErrors()) { ?>
        <? AssetsMin::jsBegin(); ?>
        <script>
            $(function(){
                var ErrorMessage = "<?=preg_replace('#\s+#si', ' ', addslashes(Session::getErrorDescription()))?>";
                showError(ErrorMessage);
            });
        </script>
        <? $strScript = AssetsMin::jsEnd(); ?>
        <? AssetsMin::registerJs($strScript); ?>
    <? } ?>

    <? if (Session::getMessage()) { ?>
        <? AssetsMin::jsBegin(); ?>
        <script>
            $(function(){
                var infoMessage = "<?=htmlspecialchars(Session::getMessage());?>";
                if (infoMessage) {
                    showMessage(infoMessage);
                }
            });
        </script>
        <? $strScript = AssetsMin::jsEnd(); ?>
        <? AssetsMin::registerJs($strScript); ?>
    <? } ?>
    <? if (Reports::hasUnPayedBills()) { ?>
        <? AssetsMin::jsBegin(); ?>
        <script>
            $(function(){
                showStickyMessage('<?=LangAdmin::get('You_have_unpayd_bills')?> <br> <a href="<?=$PageUrl->AssignCmdAndDo('Reports', 'billing')?>"> <?=LangAdmin::get('Go_to')?></a>');
            });
        </script>
        <? $strScript = AssetsMin::jsEnd(); ?>
        <? AssetsMin::registerJs($strScript); ?>
    <? } ?>

<? if (General::isSellFree(Session::getActiveAdminLang())) { ?>
    <a href="#selfreeModal" role="button" class="sellfree-banner" data-toggle="modal"><?=LangAdmin::get('Find_out_the_advantages_of_the_paid_version')?></a>
    <div id="selfreeModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="selfreeModalLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="selfreeModalLabel"><?=LangAdmin::get('Advantages_of_the_paid_version')?></h3>
        </div>
        <div class="modal-body">
            <p><?=LangAdmin::get('Advantages_of_the_paid_version_text')?></p>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true"><?=LangAdmin::get('Close')?></button>
        </div>
    </div>
<? } ?>

<? AssetsMin::registerJsFile('/js/tiny_mce/tinymce.min.js', array('minify' => false)); ?>
<? AssetsMin::registerJsFile('/js/tiny_mce/jquery.tinymce.min.js', array('minify' => false)); ?>

<!--Подключение js-файлов собранных по скриптам/шаблонам :: с инклюдера-->
<? foreach(ScriptIncluder::GetCustomScripts() as $script) { ?>
    <script src="<?=$script . '?' . CFG_ADMIN_VERSION;?>"></script>
<? } ?>
<!--Подключение js-файлов собранных по скриптам/шаблонам-->
<?=AssetsMin::printJsFiles()?>
<!--Подключение js блоков собранных по скриптам/шаблонам-->
<?=AssetsMin::printJs()?>

<?=Plugins::runEvent('onAdminAfterRenderFooter');?>

<? if (! empty($debugLog)) { ?>
    <?= $debugLog ?>
<? } ?>

</body>
</html>
