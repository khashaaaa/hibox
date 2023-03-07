
<style type="text/css">
.spoiler-wrap {
    border: 1px solid #aaa;
    border-radius: 5px;
    margin: 12px 6px;
}
.spoiler-head {
    padding: 2px 18px;
    cursor: pointer;
    background: url('templates/i/spoiler-icon1.png') no-repeat 12px 8px;
}
.unfolded { background: url('templates/i/spoiler-icon2.png') no-repeat 12px 8px; }
.spoiler-body {
    margin: 5px;
    display: none;
}
</style>

<?
if (isset($searchprops) && $searchprops) {
    foreach ($searchprops as $pid => $pvalue) {
?>
        <div class="spoiler-wrap">
            <input type="text" value="<?= $pvalue['name'] ?>" id="vItemPropertyName<?= $pid ?>" />
            <input type="button" value="<?=LangAdmin::get('change')?>" id="ItemPropertyName<?= $pid ?>" onclick="update_search_filter('<?= $pid ?>', 'ItemPropertyName');" />
            <div class="spoiler-head" style="float: left;">&nbsp;</div>
            <div class="spoiler-body">
                <? foreach ($pvalue['values'] as $value) { ?>
                <input type="text" value="<?= $value['name'] ?>" id="vItemPropertyValueName<?= $value['id'] ?>" />
                <input type="button" value="<?=LangAdmin::get('change')?>" id="ItemPropertyValueName<?= $value['id'] ?>" onclick="update_search_filter('<?= $value['id'] ?>', 'ItemPropertyValueName');" /><br />
                <? } ?>
            </div>
        </div>
<?
    }
} else {
?>
<div><?=LangAdmin::get('no_filter_for_category')?></div>
<? } ?>

<script type="text/javascript">(function($){
var SBobj = '';
$('.spoiler-body').hide();
$('.spoiler-show').show();
$('.spoiler-head').click(function(){
    $(this).toggleClass('unfolded');
    SBobj = $(this).next();
    if ($(SBobj).css('display') == 'none') $(SBobj).show(300); else $(SBobj).hide(300);
    })
})(jQuery);
</script>