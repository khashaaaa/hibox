<ul class="nav nav-pills">
    <li <? if($PageUrl->GetAction() == 'orders'){ ?>class="active"<?} ?>><a href="<?=$PageUrl->AssignDo('orders')?>"><?=LangAdmin::get('General')?></a></li>
    <li <? if($PageUrl->GetAction() == 'ordersProfile'){ ?>class="active"<?} ?>><a href="<?=$PageUrl->AssignDo('ordersProfile')?>"><?=LangAdmin::get('Orders_profile')?></a></li>
    <li <? if($PageUrl->GetAction() == 'bank'){ ?>class="active"<?} ?>><a href="<?=$PageUrl->AssignDo('bank')?>"><?=LangAdmin::get('Quittance_to_bank')?></a></li>
</ul>