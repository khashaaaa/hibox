
<ul class="breadcrumb">
    <li><a href="."><i class="icon-home"></i></a> <span class="divider">›</span></li>
    <li><a href="promo/seo">'.LangAdmin::get('Seo').'</a> <span class="divider">›</span></li>
    <li><a href="promo/mailing/list">'.LangAdmin::get('mailing').'</a> <span class="divider">›</span></li>
    <li class="active">'.LangAdmin::get('mailing_list').'</li>
</ul>
<!--/.breadcrumb-->

<? include('inc/sub_nav_promo.php'); ?>

<div class="ot_sub_sub_nav">
    <ul class="nav nav-pills">
        <li class="active"><a href="promo/mailing/list">'.LangAdmin::get('mailing_list').'</a></li>
        <li><a href="promo/mailing/subscribers">'.LangAdmin::get('subscribers').'</a></li>
        <li><a href="promo/mailing/config">'.LangAdmin::get('config').'</a></li>
    </ul>
</div>

<h1>
    '.LangAdmin::get('mailing_list').'
    <a href="promo/mailing/crud" autocomplete="off" data-loading-text="'.LangAdmin::get('add_mailing').'" class="btn btn-tiny btn-primary btn_preloader weight-normal offset-left3" title="'.Lang::get('add_mailing').'">'.Lang::get('add_mailing').'</a>
</h1>

<div class="row-fluid offset-top1">

    <div class="span6">

        <div class="text-right">
            <select class="input-mini">
                <option value="10" selected="selected">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="all">'.LangAdmin::get('All').'</option>
            </select>
        </div>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th class="span5">'.LangAdmin::get('mailing_name').'</th>
                    <th class="span2 text-center">'.LangAdmin::get('mailing_msg_count').' </th>
                    <th class="span1">'.LangAdmin::get('Actions').'</th>
                </tr>
            </thead>

            <tbody>

                <tr>
                    <td>Сезонные скидки на всю одежду</td>
                    <td class="text-center"><span class="text-success" title="'.LangAdmin::get('Letters_sent').'">220</span> / <span class="text-error" title="Писем в очереди">3479</span></td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button data-toggle="dropdown" class="btn btn-mini dropdown-toggle"><i class="icon-cog"></i> <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li><a href="promo/mailing/crud" title="'.LangAdmin::get('Edit_mailing').'"><i class="icon-pencil"></i> Редактировать</a></li>
                                <li><a href="#" title="'.LangAdmin::get('Remove_mailing').'" class="ot_show_deletion_dialog_modal"><i class="icon-remove"></i> Удалить</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Опять двадцать пять — скидка 25 % на весь ассортимент мужских носков!</td>
                    <td class="text-center">122</td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button data-toggle="dropdown" class="btn btn-mini dropdown-toggle"><i class="icon-cog"></i> <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li><a href="promo/mailing/crud" title="'.LangAdmin::get('Edit_mailing').'"><i class="icon-pencil"></i> Редактировать</a></li>
                                <li><a href="#" title="'.LangAdmin::get('Remove_mailing').'" class="ot_show_deletion_dialog_modal"><i class="icon-remove"></i> Удалить</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>На сайте появился новый функционал — пристрой</td>
                    <td class="text-center">80</td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button data-toggle="dropdown" class="btn btn-mini dropdown-toggle"><i class="icon-cog"></i> <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li><a href="promo/mailing/crud" title="'.LangAdmin::get('Edit_mailing').'"><i class="icon-pencil"></i> Редактировать</a></li>
                                <li><a href="#" title="'.LangAdmin::get('Remove_mailing').'" class="ot_show_deletion_dialog_modal"><i class="icon-remove"></i> Удалить</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>

            </tbody>
    </table>

    </div>
</div>

<? include('inc/pager.php'); ?>
