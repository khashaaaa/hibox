
<li data-route="referral" <? if($url->GetCmd() == 'referral') {?>class="active"<?}?>>
    <a href="<?=$url->AssignClearCmd('referral')?>"><i class="icon icon-black icon-plus"></i><span class="hidden-tablet"> <?=LangAdmin::get('Referral_system')?></span></a>
</li>
