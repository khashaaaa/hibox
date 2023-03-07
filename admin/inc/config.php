<?

$menu = array(
    '/' => 'Tradehub System. Главная',
    '/agent' => 'Заказы. Роль Агента. Расчетный центр Tradehub System',
    	'/agent/' => 'Заказы. Роль Агента. Расчетный центр Tradehub System',
    	'/agent/orders' => 'Заказы. Роль Агента. Расчетный центр Tradehub System',
        '/agent/payment' => 'Авансы. Роль Агента. Расчетный центр Tradehub System',
    '/logist' => 'Заказы. Роль Логиста. Расчетный центр Tradehub System',
        '/logist/orders' => 'Заказы. Общая сводка. Роль Логиста. Расчетный центр Tradehub System',
            '/logist/orders/agents' => 'Заказы по агентам. Роль Логиста. Расчетный центр Tradehub System',
                '/logist/orders/agents/daomart' => 'Агент Петер. Заказы по агентам. Роль Логиста. Расчетный центр Tradehub System',
        '/logist/payment' => 'Авансы. Общая сводка. Роль Логиста. Расчетный центр Tradehub System',
            '/logist/payment/agents' => 'Авансы по агентам. Роль Логиста. Расчетный центр Tradehub System',
                '/logist/payment/agents/daomart' => 'Агент Петер. Авансы по агентам. Роль Логиста. Расчетный центр Tradehub System',

    '/orders-tpl' => 'Заказы. Шаблон. Расчетный центр Tradehub System',
    '/payment-tpl' => 'Авансы. Шаблон. Расчетный центр Tradehub System',
    '/403' => 'Доступ запрещен. Системная страница. Расчетный центр Tradehub System',
);

$path = str_replace('\\', '/', dirname(dirname(__FILE__)));
$diff = str_replace($_SERVER['DOCUMENT_ROOT'],'', $path);

$uri = trim(str_replace($diff, '', $_SERVER["REQUEST_URI"]), '/');
