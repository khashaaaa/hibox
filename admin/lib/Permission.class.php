<?php

class Permission {

    static $roles = array('SuperAdmin', 'SuperOperator', 'SuperManager',
        'ContentManager', 'Operator', 'Financier');
    
    static $current_roles = array();

    static $main_menu_mask = array(
        'adminusers'        => array(1, 0, 0, 0, 0, 0),
        'cmsadmin'          => array(1, 0, 0, 1, 0, 0),
        'cmsadmin_menu'     => array(1, 0, 0, 1, 0, 0),
        'control'           => array(1, 0, 0, 1, 0, 0),
        'siteConfiguration' => array(1, 0, 0, 1, 0, 0),
        'caching'           => array(1, 0, 0, 1, 0, 0),
        'control_case'      => array(1, 0, 0, 0, 0, 0),
        'control_pricing'   => array(1, 0, 0, 0, 0, 0),
        'category'          => array(1, 0, 0, 1, 0, 0),
        'sb_invoice'        => array(1, 0, 0, 0, 0, 0),
        'orders'            => array(1, 1, 1, 0, 1, 0),
        'users'             => array(1, 0, 0, 0, 0, 1),
        'newsletter'        => array(1, 0, 0, 0, 0, 0),
        'news'              => array(1, 0, 0, 1, 0, 0),
        'users'             => array(1, 0, 1, 0, 0, 0),
        'digest'            => array(1, 0, 0, 0, 0, 0),
        'reviews'           => array(1, 0, 0, 0, 0, 0),
        'set'               => array(1, 0, 0, 1, 0, 0),
        'brands'            => array(1, 0, 0, 1, 0, 0),
        'banners'           => array(1, 0, 0, 1, 0, 0),
        'update'            => array(1, 0, 0, 0, 0, 0),
        'referrals'         => array(1, 0, 0, 0, 0, 0),
        'langTranslations'  => array(1, 0, 0, 1, 0, 0),
        'support'           => array(1, 1, 1, 0, 0, 1),
        'calculator'        => array(1, 0, 0, 0, 0, 0),
        'my_categories'     => array(1, 0, 0, 0, 0, 0),
        'my_goods'          => array(1, 0, 0, 0, 0, 0),
        'filtering'         => array(1, 0, 0, 1, 0, 0),
        'order_settings'    => array(1, 0, 0, 0, 0, 0),
        'logout'            => array(1, 1, 1, 1, 1, 1),
    );
    
    static $order_fields_mask = array(
        'creation_time'     => array(1, 1, 1, 0, 1, 0),
        'total_amount'      => array(1, 0, 1, 0, 0, 0),
        'paid'              => array(1, 0, 1, 0, 0, 0),
        'name_of_purchaser' => array(1, 0, 1, 0, 0, 0),
        'name_of_operator'  => array(1, 1, 1, 0, 1, 0),
        'order_status'      => array(1, 1, 1, 0, 1, 0),
        'steps_to_order'    => array(1, 1, 1, 0, 0, 0),
        
        'link_to_user'      => array(1, 0, 1, 0, 0, 0),
        'name_of_purchaser2'=> array(1, 1, 1, 0, 0, 0),
        'account_balance'   => array(1, 1, 1, 0, 0, 0),
        'comment_buyer'     => array(1, 1, 1, 0, 0, 0),
        'name_of_operator'  => array(1, 1, 1, 0, 1, 0),
        'additional_info'   => array(1, 1, 1, 0, 0, 0),
        'can_delete_good'   => array(1, 1, 1, 0, 0, 0),
        
        'orderinfo_itab1'   => array(1, 1, 1, 0, 1, 0),
        'orderinfo_itab2'   => array(1, 1, 1, 0, 0, 0),
        'orderinfo_itab3'   => array(1, 1, 1, 0, 0, 0),
        'orderinfo_itab4'   => array(1, 1, 1, 0, 0, 0),
		'orderinfo_itab5'   => array(1, 1, 1, 0, 0, 0),
        
        /*
         * r_ - статусы товара в заказе, 
         * доступные для просмотра
         */
        'r_status_id_1'     => array(1, 1, 1, 0, 0, 0), // Ожидает оплаты
        'r_status_id_2'     => array(1, 1, 1, 0, 1, 0), // В обработке
        'r_status_id_3'     => array(1, 1, 1, 0, 0, 0), // Подтверждение цены
        'r_status_id_4'     => array(1, 1, 1, 0, 1, 0), // Заказан
        'r_status_id_5'     => array(1, 1, 1, 0, 0, 0), // Провека качества
        'r_status_id_6'     => array(1, 1, 1, 0, 0, 0), // Получен
        'r_status_id_7'     => array(1, 1, 1, 0, 0, 0), // Упаковка
        'r_status_id_8'     => array(1, 1, 1, 0, 0, 0), // Готов к отправке
        'r_status_id_9'     => array(1, 1, 1, 0, 0, 0), // Отправлен
        'r_status_id_10'    => array(1, 1, 1, 0, 0, 0), // Получен
        'r_status_id_11'    => array(1, 1, 1, 0, 0, 0), // Возвращен к продавцу
        'r_status_id_12'    => array(1, 1, 1, 0, 0, 0), // Невозможно поставить
        'r_status_id_13'    => array(1, 1, 1, 0, 0, 0), // Отменен
    );
    
    /*
     * Перечисление всевозможных статусов товаров в заказе
     * w_ - статусы товара в заказе, доступные для записи
     */
    static $order_lines_status_mask = array(
        'w_status_id_1'     => array(1, 1, 1, 0, 0, 0), // Ожидает оплаты
        'w_status_id_2'     => array(1, 1, 1, 0, 1, 0), // В обработке
        'w_status_id_3'     => array(1, 1, 1, 0, 1, 0), // Подтверждение цены
        'w_status_id_4'     => array(1, 1, 1, 0, 1, 0), // Заказан
        'w_status_id_5'     => array(1, 1, 1, 0, 0, 0), // Провека качества
        'w_status_id_6'     => array(1, 1, 1, 0, 1, 0), // Получен
        'w_status_id_7'     => array(1, 1, 1, 0, 0, 0), // Упаковка
        'w_status_id_8'     => array(1, 1, 1, 0, 0, 0), // Готов к отправке
        'w_status_id_9'     => array(1, 1, 1, 0, 0, 0), // Отправлен
        'w_status_id_10'    => array(1, 1, 1, 0, 0, 0), // Получен
        'w_status_id_11'    => array(1, 1, 1, 0, 0, 0), // Возвращен к продавцу
        'w_status_id_12'    => array(1, 1, 1, 0, 1, 0), // Невозможно поставить
        'w_status_id_13'    => array(1, 1, 1, 0, 0, 0), // Отменен
    );
    
    
    /*
     * Перечисление всевозможных статусов заказов
     * статусы доступны в фильтре и на странице списка заказов
     */
    static $order_status_mask = array(
        'status_id_10'       => array(1, 0, 1, 0, 1, 0), // Ожидает оплаты
        'status_id_20'       => array(1, 1, 1, 0, 0, 0), // Оплачен
        'status_id_30'       => array(1, 1, 1, 0, 1, 0), // В обработке
        'status_id_40'       => array(1, 1, 1, 0, 1, 0), // Завершен
        'status_id_50'       => array(1, 1, 1, 0, 0, 0), // Отменен
        'status_id_-1'       => array(1, 1, 1, 0, 1, 0), // Кроме 'Ожидает оплаты'
    );

    /*
     * Фильтрация меню в соответсвии с ролью оператора
     */
    static function filter_menu($menu) {
        if (!defined('BUY_IN_CHINA')) return $menu;
        
        $filtered_menu = array();
        $used_cmd = array();
        $current_roles = Permission::get_current_roles();

        foreach ($menu as $item) {
            $cmd    = (isset($item['cmd']) && $item['cmd']) ? $item['cmd'] : 'control';
            $action = isset($item['do']) ? '_'.$item['do'] : '';
            
            foreach ($current_roles as $role) {

                $role_index = array_search($role['Name'], Permission::$roles);
                if ($role_index === false) continue;

                if (!isset(Permission::$main_menu_mask[$cmd . $action][$role_index])) continue;

                if (Permission::$main_menu_mask[$cmd . $action][$role_index] &&
                        !in_array($cmd . $action, $used_cmd)) {
                   $filtered_menu[] =  $item;
                   $used_cmd[] = $cmd . $action;
                }
            }
        }

        foreach ($menu as $item) {
            if (isset($item['cmd']) && $item['cmd']=='logout' && 
                    !in_array('logout', $used_cmd)) {
                $filtered_menu[] = $item;
                $used_cmd[] = 'logout';
            }
        }

        return $filtered_menu;
    }
    
    
    /*
     * Фильтрация статусов товаров в заказе 
     * в соответсвии с ролью оператора
     */
    static function filter_order_lines_status($statuses) {
        if (!defined('BUY_IN_CHINA')) return $statuses;
        
        $filtered = array();
        $current_roles = Permission::get_current_roles();

        foreach ($statuses as $status) {
            foreach ($current_roles as $role) {

                $role_index = array_search($role['Name'], Permission::$roles);

                if ($role_index === false) continue;
                if (!isset(Permission::$order_lines_status_mask['w_status_id_'.$status['id']][$role_index])) continue;

                if (Permission::$order_lines_status_mask['w_status_id_'.$status['id']][$role_index]) {
                   $filtered[] =  $status;
                }
            }
        }

        return $filtered;
    }
    
    
    /*
     * Фильтрация статусов заказов 
     * в соответсвии с ролью оператора
     */
    static function filter_order_status($statuses) {
        if (!defined('BUY_IN_CHINA')) return $statuses;
        
        $filtered = array();
        $current_roles = Permission::get_current_roles();

        foreach ($statuses as $status) {
            foreach ($current_roles as $role) {

                $role_index = array_search($role['Name'], Permission::$roles);

                if ($role_index === false) continue;
                if (!isset(Permission::$order_status_mask['status_id_'.$status['id']][$role_index])) continue;

                if (Permission::$order_status_mask['status_id_'.$status['id']][$role_index]) {
                   $filtered[] =  $status;
                }
            }
        }

        return $filtered;
    }
    
    
    /*
     * Фильтрация заказов 
     * в соответсвии с ролью оператора
     */
    static function filter_orders($orders) {
        if (!defined('BUY_IN_CHINA')) return $orders;
        
        $filtered = array();
        $current_roles = Permission::get_current_roles();

        foreach ($orders as $order) {
            foreach ($current_roles as $role) {

                $role_index = array_search($role['Name'], Permission::$roles);

                if ($role_index === false) continue;
                if (isset(Permission::$order_status_mask['status_id_'.$order['statusid']]))
                {
                    if (!isset(Permission::$order_status_mask['status_id_'.$order['statusid']][$role_index])) continue;
                    if (Permission::$order_status_mask['status_id_'.$order['statusid']][$role_index]) {
                        $filtered[] =  $order;
                    }
                } else {
                    $filtered[] = $order;
                }
            }
        }

        return $filtered;
    }
    
 
    static function default_cmd () {
        if (!defined('BUY_IN_CHINA')) return 'Orders';
        
        if (!isset($_SESSION['sid'])) {
            return 'control';
        }

        $current_roles = Permission::get_current_roles();
        if (Permission::is_availible_cmd($current_roles, 'control')) {
            return 'control';
        }
        
        $cmd = '';
        foreach ($current_roles as $role) {

            $role_index = array_search($role['Name'], Permission::$roles);
            if ($role_index === false) continue;
            
            foreach (Permission::$main_menu_mask as $cmd => $settings) {
                if (isset($settings[$role_index]) && $settings[$role_index]) {
                    return $cmd;
                }
            }
        }
        return 'control';
    }

    
    /*
     * Определение доступности команды для оператора
     */
    static function is_availible_cmd ($current_roles, $cmd) {
        if (!defined('BUY_IN_CHINA')) return TRUE;
        
        foreach ($current_roles as $role) {
            
            $role_index = array_search($role['Name'], Permission::$roles);
            if ($role_index === false) continue;

            if (!isset(Permission::$main_menu_mask[$cmd][$role_index])) continue;

            if (Permission::$main_menu_mask[$cmd][$role_index]) {
               return TRUE;
            }
        }
        return FALSE;
    }
    
    
     /*
     * Определение доступности команды для оператора
     */
    static function can_show_cmd ($cmd) {
        if (!defined('BUY_IN_CHINA')) return TRUE;
        
        if (!isset($_SESSION['sid'])) return FALSE;
        
        $current_roles = Permission::get_current_roles();
        return Permission::is_availible_cmd($current_roles, strtolower($cmd));
    }
    
    
     /*
     * Определение доступности отображения поля заказа для оператора
     */
    static function show_order_field ($field) {
        if (!defined('BUY_IN_CHINA')) return TRUE;
        
        $current_roles = Permission::get_current_roles();
        
        foreach ($current_roles as $role) {
            $role_index = array_search($role['Name'], Permission::$roles);
            if ($role_index === false) continue;
            if (!isset(Permission::$order_fields_mask[$field][$role_index])) continue;

            return Permission::$order_fields_mask[$field][$role_index];
        }
        return FALSE;
    }
    
    
    static function get_current_roles () {
        global $otapilib;
        
        if (!isset($_SESSION['current_roles'])) {
            $current_roles = $otapilib->GetInstanceUserRoleList($_SESSION['sid']);
            $_SESSION['current_roles'] = $current_roles;
        } else {
            $current_roles = $_SESSION['current_roles'];
        }
        
        return $current_roles;
    }

}
