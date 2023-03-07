<? include (TPL_DIR."header.php"); ?>

<div class="main"><div class="canvas clrfix">
    <div class="windialog" id="dialog-add-filter" title="<?=LangAdmin::get('add')?>" style="display:none;">
        <label>Type</label>
        <select name="type_new_filter">
            <? foreach ($types as $type) { ?>
                <option value="<?=(string)$type?>"><?=(string)$type?></option>
            <? } ?>
        </select><br/><br/>
        <label>Value</label>
        <input type="text" value="" name="new_filter"/>
    </div>

    <div class="windialog" id="category-list" title="<?=LangAdmin::get('categories')?>" style="display:none;">
        <? include(TPL_DIR.'content_filtering/categories.php'); ?>
    </div>

    <div align="right" class="flr">
        <button id="add_new_filter"><?=LangAdmin::get('add_new_filter')?></button>
        <button id="save_all_filters"><?=LangAdmin::get('save_all_filters')?></button>
        <button id="refresh_all_filters"><?=LangAdmin::get('refresh')?></button>
    </div> <br/><br/><br/>
    <form id="form-filters" method="post" action="index.php?cmd=filtering&sid=&do=editfilters">
    <div id="tabs">
        <ul>
            <li id="itab1"><a href="#tabs-1"><?=LangAdmin::get('filter_goods')?></a></li>
            <li id="itab2"><a href="#tabs-2"><?=LangAdmin::get('filter_categories')?></a></li>
            <li id="itab3"><a href="#tabs-3"><?=LangAdmin::get('filter_vendors')?></a></li>
            <li id="itab4"><a href="#tabs-4"><?=LangAdmin::get('filter_search_strings')?></a></li>
            <li id="itab5"><a href="#tabs-5"><?=LangAdmin::get('filter_brands')?></a></li>
        </ul>
        <span id="error" style="color:red;font-weight: bold;">
            <? if(isset($error)) { print $error; } ?>
        </span>
        <div id="tabs-1"><br/>
            <table class="table400">
                <? $type = Filtering::CONTENT_TYPE_ITEM; ?>
                <? if (isset($blacklist[$type])) { ?>
                    <? foreach($blacklist[$type] as $item) { ?>
                        <tr>
                            <td>
                                <a target="_blank" href="<?=$_SERVER['HTTP_HOST'] ?>/index.php?p=item&id=<?=$item['info']['id'] ?>">
                                <img width="50px" height="50px" src="<?=$item['info']['mainpictureurl'] ?>">
                                </a>
                            </td>
                            <td><strong>Id :</strong>
                                <input type="text" value="<?=$item['id'] ?>" name="<?=$type?>[]">
                            </td>
                            <td><strong>Name</strong>  : <?=$item['info']['title'] ?></td>
                            <td><div class="delete_filter" style='cursor: pointer;'>
                                <img src="templates/i/del.png" title="delete"/>
                                </div>
                            </td>
                        </tr>
                    <? } ?>
                <? } else { ?>
                    <tr><td colspan="4"></td></tr>
                <? } ?>
            </table>
        </div>
        <div id="tabs-2"><br/>
            <table class="table400">
                <? $type = Filtering::CONTENT_TYPE_CATEGORY; ?>
                <? if (isset($blacklist[$type])) { ?>
                <? foreach($blacklist[$type] as $item) { ?>
                        <tr>
                            <td><strong>Id :</strong>
                                <input type="text" value="<?=$item['id'] ?>" name="<?=$type?>[]">
                            </td>
                            <td><strong>Name</strong> : <?=$item['info']['name'] ?></td>
                            <td><div class="delete_filter" style='cursor: pointer;'>
                                <img src="templates/i/del.png" title="delete"/>
                                </div>
                            </td>
                        </tr>
                    <? } ?>
                <? } else { ?>
                    <tr><td colspan="3">Не добавлено</td></tr>
                <? } ?>
            </table>
        </div>
        <div id="tabs-3"><br/>
            <table class="table400">
            <? $type = Filtering::CONTENT_TYPE_VENDOR; ?>
            <? if (isset($blacklist[$type])) { ?>
            <? foreach($blacklist[$type] as $item) { ?>
                <tr>
                    <td><strong>Id :</strong>
                        <input type="text" value="<?=$item['id'] ?>" name="<?=$type?>[]">
                    </td>
                    <td><strong>Name</strong> : <?=$item['info']['name'] ?></td>
                    <td><div class="delete_filter" style='cursor: pointer;'>
                        <img src="templates/i/del.png" title="delete"/>
                        </div>
                    </td>
                </tr>
            <? } ?>
            <? } else { ?>
                <tr><td colspan="3">Не добавлено</td></tr>
            <? } ?>
            </table>
        </div>
        <div id="tabs-4"><br/>
            <table class="table400">
            <? $type = Filtering::CONTENT_TYPE_STRING; ?>
            <? if (isset($blacklist[$type])) { ?>
                <? foreach($blacklist[$type] as $item) { ?>
                    <tr>
                    <td>
                        <input type="text" value="<?=$item['info'] ?>" name="<?=$type?>[]">
                    </td>
                    <td><div class="delete_filter" style='cursor: pointer;'>
                        <img src="templates/i/del.png" title="delete"/>
                        </div>
                    </td>
                    </tr>
                <? } ?>
            <? }  else { ?>
                <tr><td colspan="2">Не добавлено</td></tr>
            <? } ?>
            </table>
        </div>
        <div id="tabs-5"><br/>
            <table class="table400">
            <? $type = Filtering::CONTENT_TYPE_BRAND; ?>
            <? if (isset($blacklist[$type])) { ?>
            <? foreach($blacklist[$type] as $item) { ?>
                <tr>
                    <td><strong>Id :</strong>
                        <input type="text" value="<?=$item['id'] ?>" name="<?=$type?>[]">
                    </td>
                    <td><strong>Name</strong> : <?=$item['info']['name'] ?></td>
                    <td><div class="delete_filter" style='cursor: pointer;'>
                        <img src="templates/i/del.png" title="delete"/>
                        </div>
                    </td>
                </tr>
            <? } ?>
            <? }  else { ?>
                <tr><td colspan="2">Не добавлено</td></tr>
            <? } ?>
            </table>
        </div>
    </div>
    </form>

</div></div>

<script>

var tab_number = <? echo (isset($tab_number)) ? $tab_number : 1; ?>;
$("#itab" + tab_number).addClass('ui-tabs-selected').addClass('ui-state-active');
$("#tab-" + tab_number).show();

$(function() {
    $('#tabs').tabs();
    $('.delete_filter').click(function() {
        $(this).parent().parent().remove();
    });
    $('#add_new_filter')
        .button()
        .click(function() {
            $("#dialog-add-filter").dialog("open");
    });
    $('#save_all_filters')
        .button()
        .click(function() {
            $("#form-filters").submit();
    });
    $('#refresh_all_filters')
        .button()
        .click(function() {
            window.location = 'index.php?cmd=filtering';
    });
    $("input[name=new_filter]").click(function() {
        if ($("select[name=type_new_filter] option:selected").val() == '<?=Filtering::CONTENT_TYPE_CATEGORY?>') {
            $("#category-list").dialog("open");
        }

    });
});

$("#dialog-add-filter").dialog({
    autoOpen: false,
    width:250,
    modal: true,
    resizable: false,
    height:200,
    buttons : {
        "<?=LangAdmin::get('add')?>" : function() {
            var new_filter = $("input[name=new_filter]").val();
            var type_new_filter = $("select[name=type_new_filter] option:selected").val();
            var value = $('<input type="hidden" value="' + new_filter +
                '" name=' + type_new_filter + '[]>');
            var form = $("#form-filters");
            form.prepend(value).submit();
        },
        "<?=LangAdmin::get('cancel')?>" : function() {
            $(this).dialog("close");
        }
    }
});

$("#category-list").dialog({
    autoOpen: false,
    width:550,
    modal: true,
    resizable: false,
    height:500,
    buttons : {
        "<?=LangAdmin::get('add')?>" : function() {
            var cid = $('input[name=bind_cat]:checked').val();
            $('input[name=new_filter]').val(cid);
            $(this).dialog("close");
        },
        "<?=LangAdmin::get('cancel')?>" : function() {
            $(this).dialog("close");
        }
    }
});

</script>

<? include (TPL_DIR."footer.php"); ?>