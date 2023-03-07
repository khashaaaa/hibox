<?
include ("templates/header.php");
?>

<script type='text/javascript' src='js/bpopup.js'></script>

<link rel="stylesheet" href="css/digest.css" type="text/css" />
<div id="load_ajax" style="position:absolute; top:48%; left:48%; width:100px; height:100px; background:#FFF; display:none; z-index:999" align="center">
<img src="../../css/i/ajax-loader.gif" width="50" height="50"/>
</div>

<div class="main"><div class="canvas clrfix editnews">
<div  id="select_cat_error"></div>

        <?
        $cms = new CMS();
        if ($post['id'] === 'new') {
            ?>
            <h1><?= LangAdmin::get('add') ?></h1>
        <? } else { ?>
            <h1><?= LangAdmin::get('edit') ?> (ID: <?=$post['id']?>)</h1>
            <? $date = $post['created']; ?>
        <? } ?>
        <form action="?cmd=digest&do=editsave" method="post" id="feditor" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?=isset($post['id'])? $post['id']: ''?>">
            <input type="hidden" name="cms" value="1">
            <p><label><?= LangAdmin::get('date') ?>: <input autocomplete="off" type="edit" name="date" id="date" value="<?=isset($date)? $date->format('d.m.Y') : ''?>"></label></p>
            <p><label><?= LangAdmin::get('title') ?>: <input type="edit" name="title" id="title" value="<?=isset($post['title'])? $post['title']: ''?>"></label></p>
            <p><label>
            <?= LangAdmin::get('image') ?>:

            <div id="fileupload">
                            <div class="row fileupload-buttonbar fileupload-banner">
                                <div class="fileupload fileupload-new" data-provides="fileupload">
                                    <div class="span4 fileupload-preview fileupload-exists thumbnail" style="margin-bottom: 0 !important; display: inline-block; margin-right: 5px; min-height: 50px;">
                                        <img src="<?=! empty($post['image']) ? DigestRepository::getImage($post['image'], "big") : ''?>"/>
                                    </div>
                                    <div class="">
                                        <span class="btn btn-file btn-primary fileinput-button">
                                            <span style="display: <?=(empty($post['image']) ? 'inline-block' : 'none');?>" class="fileupload-new"><?=LangAdmin::get('Upload')?></span>
                                            <span style="display: <?=(! empty($post['image']) ? 'inline-block' : 'none');?>" class="fileupload-exists"><?=LangAdmin::get('Change')?></span>
                                            <input id="uploaded_logo" name="uploaded_logo" type="file" />
                                        </span>
                                    </div>
                                </div>
                                <input type="hidden" name="existing_logo" value="<?=! empty($post['image']) ? $post['image'] : ''?>" />
                            </div>
            </div>
            </label>
            </p><br>
            <p>
                <label><?= LangAdmin::get('category') ?>:</label>
                <select name="category" id="category">
                    <option value="" data-cat="false"><?= LangAdmin::get('without_cat') ?></option>
                    <?
                    foreach ($cats as $num => $category) {
                       $selected = "";
                       if (isset($post['category_id']) && ($post['category_id']==$category['id'])) {
                           $selected = 'selected="selected"';
                    }
                    ?>
                       <option value="<?=$category['id']?>" <?= $selected ?> ><?= $category['title'] ?></option>
                    <? } ?>
                </select>
                <input type="button" value="<?= LangAdmin::get('add_a_category') ?>" id="createAddCat">
                <input type="button" value="<?= LangAdmin::get('del_a_category') ?>" id="delDigestCat">
                <input type="button" value="<?= LangAdmin::get('edit_a_category') ?>" id="editDigestCat">

            </p>
            <p>
                <a href="#" onclick="showPrompt()"><?= LangAdmin::get('insert_products') ?></a>
                <!--- <input type="hidden" name="goods" id="goods" alt="">
                <input type="button" id="insert_item" onclick="getItems(document.getElementById('goods').value)" value="<?= LangAdmin::get('get_goods') ?>"> -->
            <div id="msg"></div>
            </p>
            <p><label><?= LangAdmin::get('content') ?>:<br> <textarea name="content" id="edit_content"><?=isset($post['content'])? $post['content']: ''?></textarea></label></p>


            <p>
                <label>
                    <?= LangAdmin::get('language') ?>:
                    <select name="lang" id="postlang" onchange="loadCategoriesByLang();">
                        <?
                        foreach ($webui->Settings->Languages->NamedProperty as $v) {
                            $lang = (string) $v->Name;
                            $lang_desc = (string) $v->Description;

                            $selected = '';
                            if (@$post['lang_code'] == $lang) {
                                $selected = ' selected';
                            }
                            ?>
                            <option value="<?= $lang ?>"<?= $selected ?>><?= $lang_desc ?></option>
                            <?
                        }
                        ?>
                    </select>
                </label>
            </p>
            <p><input type="submit" value=" <?= LangAdmin::get('save') ?> " onclick="return CheckSubmit(this.form)" class="ui-button ui-widget ui-state-default ui-corner-all"></p>
        </form>
        <br clear="all">
    </div></div>



<div id="popup"><a class="bClose">X</a>
    <center><?= LangAdmin::get('add_a_category') ?></center>
        <form id="add_category" action="" method="post">
                <label><?= LangAdmin::get('category_title') ?>:</label>
                <input required="required" type="text" id="addcat_title" />
                                <br/>
                                <br/>
                <label><?= LangAdmin::get('category_desc') ?>:</label>
                <textarea id="addcat_desc" required="required"></textarea>
                                <br/>
                                <br/>
                <label><?= LangAdmin::get('language') ?>: </label>
                <select name="" id="addcat_lang">
                <?
                foreach ($webui->Settings->Languages->NamedProperty as $v) {
                    $lang = (string) $v->Name;
                    $lang_desc = (string) $v->Description;
                    $selected = '';
                    if (@$post['lang_code'] == $lang) {
                        $selected = ' selected';
                    }
                    ?>
                    <option value="<?= $lang ?>"<?= $selected ?>><?= $lang_desc ?></option>
                    <?
                }
                ?>
            </select>
            <input type="hidden" name="addcategory" value="1" />
            <br/><br/>
            <div align="center" id="addcat"></div>
        <input type="submit" value="<?=LangAdmin::get('add') ?>" onclick=" addCategory(); return false;"/>
</form>
</div>

<div id="popupedit"><a class="bClose">X</a>
    <center><?= LangAdmin::get('edit_a_category') ?></center>
        <form id="edit_category" action="" method="post">
                <label><?= LangAdmin::get('category_title') ?>:</label>
                <input required="required" type="text" id="editcat_title" />
                                <br/>
                                <br/>
                <label><?= LangAdmin::get('category_desc') ?>:</label>
                <textarea id="editcat_desc" required="required"></textarea>
                                <br/>
                                <br/>
                <label><?= LangAdmin::get('language') ?>: </label>
                <select name="" id="editcat_lang">
                <?
                foreach ($webui->Settings->Languages->NamedProperty as $v) {
                    $lang = (string) $v->Name;
                    $lang_desc = (string) $v->Description;
                    $selected = '';
                    if (@$post['lang_code'] == $lang) {
                        $selected = ' selected';
                    }
                    ?>
                    <option value="<?= $lang ?>"<?= $selected ?>><?= $lang_desc ?></option>
                    <?
                }
                ?>
            </select>
            <input type="hidden" name="editcategory" id="editcategory_id"value="" />
            <br/><br/>
            <div align="center" id="addcat"></div>
        <input type="submit" value="<?=LangAdmin::get('edit') ?>" onclick=" editCategory(); return false;"/>
</form>
</div>


<script language="javascript" type="text/javascript">
var enter_id = '<?=LangAdmin::get('enter_id')?>';
var Digest_cats = <?=json_encode($cats)?>;
</script>
<script language="javascript" type="text/javascript" src="../js/tiny_mce/ajaxfilemanager.js"></script>
<script language="javascript" type="text/javascript" src="../js/tiny_mce/tiny_mce.js"></script>
<script type='text/javascript' src='js/digest.js'></script>
<?=Lang::loadJSTranslation(array('select_cat_error', 'saved'))?>

<!--- ULOADER-->
<link rel="stylesheet" href="../admin/css/vendor/bootstrap-fileupload.css">
<link rel="stylesheet" href="../admin/css/vendor/jquery.fileupload-ui.css">
<script src="../admin/js/vendor/bootstrap-fileupload.js"></script>





<?
include ("templates/footer.php");
?>
