<?php
/**
 * Класс для работы с разделом Пристрой (выставление пользователями своих товаров на продажу)
 **/
class PristroyRepository extends Repository
{
    const STATUS_ON_MODERATION  = __LINE__;
    const STATUS_REJECTED       = __LINE__;
    const STATUS_APPROVED       = __LINE__;
    const STATUS_REMOVED        = __LINE__;
    const STATUS_SOLD           = __LINE__;

    protected $statuses = array(
        'on_moderation' => self::STATUS_ON_MODERATION,
        'rejected'      => self::STATUS_REJECTED,
        'approved'      => self::STATUS_APPROVED,
        'removed'       => self::STATUS_REMOVED,
        'sold'          => self::STATUS_SOLD,
    );

    protected $table = 'pristroy_products';

    public function addProduct($title, $description, $images, $price, $currency, $quantity, $user_id, $user_login, $item_id, $tao_id, $config_id, $config_text, $fullinfo)
    {
        $data = array(
            'title'         => $this->cms->escape($title),
            'description'   => $this->cms->escape($description),
            'images'        => $this->cms->escape($images),
            'price'         => (float) $price,
            'currency'      => (string) $currency,
            'quantity'      => (int) $quantity,
            'status'        => self::STATUS_ON_MODERATION,
            'user_id'       => (int) $user_id,
            'user_login'    => $this->cms->escape($user_login),
            'item_id'       => (int)$item_id,
            'item_tao_id'   => (string)$tao_id,
            'config_id'     => $this->cms->escape($config_id),
            'config_text'   => $this->cms->escape($config_text),
            'fullinfo'      => $this->cms->escape($fullinfo),
        );

        $sql = 'INSERT INTO `'.$this->table.'` (`'.implode('`,`', array_keys($data)).'`, `updated_at`)
        VALUES ("'.implode('","', $data).'", NOW())';

        $this->cms->query($sql, array($this->table));

        return (int) $this->cms->insertedId();
    }

    public function approveProduct($id)
    {
        $id = is_array($id) ? array_map('intval', $id) : array((int)$id);
        if (! empty($id)) {
            $sql = 'UPDATE `' . $this->table . '` SET ';
            $sql .= " `status`= '".self::STATUS_APPROVED."', ";
            $sql .= " `reject_reason`= '' ";
            $sql .= ' WHERE `id` IN ("' . implode('", "', $id) . '")';
            return (bool) $this->cms->query($sql, array($this->table));
        }
        return false;
    }

    public function getList($offset = 0, $limit = false, $status = self::STATUS_APPROVED, $prepareData = true, $sort = false)
    {
        $limit = $limit > 0 ? (int)$limit : 20;
        $status_condition = '';
        if (! is_null($status) && in_array($status, $this->statuses)) {
            $status_condition = ' WHERE `status` = '. (int)$status;
        }
        
        $sqlSort = '`created_at` asc';
        if ($sort) {
            switch ($sort) {
                case 'Price:Asc':
                    $sqlSort = ' `price` asc';
                    break;
                case 'Price:Desc':
                    $sqlSort = ' `price` desc';
                    break;
                case 'Latest:Asc':
                    $sqlSort = ' `created_at` desc';
                case 'Default':
                default:
                    break;
            }
        }
        
        $sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM `' . $this->table . '` ' . $status_condition;
        if ($sqlSort) {
            $sql .= ' ORDER BY ' . $sqlSort . ' ';
        }
        $sql .= ' LIMIT ' . (int)$offset . ', ' . (int)$limit;

        $result = array();
        $result['data'] = $this->cms->queryMakeArray($sql, array($this->table));
        $result['rows'] = $this->cms->getFoundRows();

        if ($prepareData) {
            $result['data'] = $this->prepareData($result['data']);
        }

        return $result;
    }

    public function getListByItemIds(array $ids)
    {
        $ids = array_map('intval', $ids);
        $sql = 'SELECT * FROM `'.$this->table.'` WHERE `item_id` IN ("'.implode('","', $ids).'") ORDER BY `id`';
        $raw = $this->cms->queryMakeArray($sql, array($this->table));
        $result = array();
        foreach ($raw as $row) {
            $result[$row['item_id']] = $row;
        }
        return $result;
    }

    public function getListByUserId($user_id, $exclude_items = null, $status = self::STATUS_APPROVED, $prepareData = true)
    {
        $exclude_condition = '';
        if (! is_null($exclude_items)) {
            $exclude_items = is_array($exclude_items) ? array_map('intval', $exclude_items) : array((int)$exclude_items);
            $exclude_condition = ' AND `id` NOT IN ("' . implode('", "', $exclude_items) . '")';
        }
        $status_condition = '';

        if (! is_null($status)) {
            if ($status!='All') {
                $status_condition = ' AND `status` = ' . (int)$status;
            }
        }

        $sql = 'SELECT * FROM `'.$this->table.'` WHERE `user_id` = ' . (int)$user_id . $exclude_condition . $status_condition;

        $result = $this->cms->queryMakeArray($sql, array($this->table));

        if ($prepareData) {
            $result = $this->prepareData($result);
        }

        return $result;
    }

    public function getListForAdmin($offset = 0, $limit = false, $prepareData = true, $filter = array())
    {
        $limit = $limit > 0 ? (int)$limit : 20;

        if ($filter && array_key_exists('status', $filter)) {
            $statusesToFilter = $filter['status'];
        }
        else {
            //use default statuses
            $statusesToFilter = array(
                self::STATUS_ON_MODERATION,
                self::STATUS_REJECTED,
                self::STATUS_APPROVED,
            );
        }

        $sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM `'.$this->table.'` ';
        $sql .= 'WHERE `status` IN ('. implode(', ', array_map('intval', $statusesToFilter)) . ') ';
        if ($filter && array_key_exists('userId',$filter) && is_numeric($filter['userId'])) {
            $sql .= ' AND `user_id` = ' . (int)$filter['userId']  .' ';
        }
        $sql .= 'ORDER BY `created_at` DESC LIMIT ' . (int)$offset . ', ' . (int)$limit;

        $result = array();
        $result['data'] = $this->cms->queryMakeArray($sql, array($this->table));
        $result['rows'] = $this->cms->getFoundRows();

        if ($prepareData) {
            $result['data'] = $this->prepareData($result['data']);
        }

        return $result;
    }

    public function getProduct($id, $prepareData = true)
    {
        $result = null;

        $sql = 'SELECT * FROM `'.$this->table.'` WHERE `id` = '.(int)$id;
        $result = $this->cms->queryMakeArray($sql, array($this->table));

        if (! empty($result[0])) {
            $result = $result[0];
        }
        if (! empty($result) && $prepareData) {
            $result = $this->prepareData($result, true);
        }
        return $result;
    }

    public function getStatuses()
    {
        return $this->statuses;
    }

    public function isProductApproved(array $item)
    {
        return !empty($item['status']) && $item['status'] == self::STATUS_APPROVED;
    }

    public function removeProduct($id)
    {
        $result = false;
        if ($id > 0) {
            $sql = 'DELETE FROM `'.$this->table.'` WHERE id = ' . (int)$id;
            $res = (bool) $this->cms->query($sql, array($this->table));
        }
        return $result;
    }

    public function rejectProduct($params)
    {
        $result = true;
        foreach ($params as $id => $rejectReason) {
            $sql = 'UPDATE `' . $this->table . '` SET ';
            $sql .= " `status`= '".self::STATUS_REJECTED."', ";
            $sql .= " `reject_reason`= '".(!is_null($rejectReason) ? $this->cms->escape($rejectReason): null)."' ";
            $sql .= ' WHERE `id` = ' . (int) $id;

            $result = $result && (bool) $this->cms->query($sql, array($this->table));

        }
        return $result;
    }

    public function updateProduct($id, $title = null, $description = null, $price = null, $quantity = null,  $status = null, $images = null, $reject_reason = null)
    {
        $result = false;

        $data = array(
            'title'         => !is_null($title)         ? $this->cms->escape($title)        : null,
            'description'   => !is_null($description)   ? $this->cms->escape($description)  : null,
            'images'        => !is_null($images)        ? $this->cms->escape($images)       : null,
            'price'         => !is_null($price)         ? (float) $price                    : null,
            'quantity'      => !is_null($quantity)      ? (int) $quantity                   : null,
            'status'        => !is_null($status)        ? (int) $status                     : null,
            'reject_reason' => !is_null($reject_reason) ? $this->cms->escape($reject_reason): null,
        );

        $data = array_filter($data, function ($a) {
            return !is_null($a);
        });

        if (! empty($data)) {
            $sql = 'UPDATE `' . $this->table . '` SET ';
            foreach ($data as $field => $value) {
                $sql .= '`' . $field . '` = "' . $value . '",';
            }
            $sql .= '`updated_at` = NOW()';
            $sql .= ' WHERE `id` = ' . (int) $id;

            $result = (bool) $this->cms->query($sql, array($this->table));
        }
        return $result;
    }

    public function updateProductStatus($id, $status)
    {
        $result = false;
        $id = is_array($id) ? array_map('intval', $id) : array((int)$id);
        if (! empty($id) && in_array($status, $this->statuses)) {
            $sql = 'UPDATE `'.$this->table.'` SET `status` = ' . (int) $status . ' WHERE `id` IN ("' . implode('", "', $id) . '")';
            $result = (bool) $this->cms->query($sql, array($this->table));
        }
        return $result;
    }

    public function updateStatusByItemId($itemId, $status)
    {
        $result = false;
        if ($itemId > 0 && in_array($status, $this->statuses)) {
            $sql = 'UPDATE `'.$this->table.'` SET `status` = ' . (int) $status . ' WHERE item_id = ' . (int)$itemId;
            $result = (bool) $this->cms->query($sql, array($this->table));
        }
        return $result;
    }

    private function getItemConfig($config_id, $item)
    {
        $result = array();
        if (! empty($item['item_with_config'][$config_id])) {
            $config = $item['item_with_config'][$config_id]['config'];
            $configs = $item['configurations'];
            foreach ($config as $key => $val) {
                $result[$key] = $configs[$key];
                $result[$key]['values'] = array_filter($configs[$key]['values'], function ($a) use ($val) {
                    return $a["id"] == $val;
                });
                $result[$key]['values'] = reset($result[$key]['values']);
            }
        }
        return $result;
    }

    private function prepareData(array $data, $singleItem = false)
    {
        if (! $singleItem) {
            foreach ($data as &$product) {
                $product['images'] = json_decode($product['images'], true);
                $product['default_image'] = !empty($product['images'][0]) ? $product['images'][0] : '';
                $product['uploaded_image'] = !empty($product['images'][1]) ? $product['images'][1] : '';
                $product['fullinfo'] = json_decode($product['fullinfo'], true);

                if (! empty($product['currency'])) {
                    $currency = $product['currency'];
                } else {
                    $currency = (isset($product['fullinfo']) && isset($product['fullinfo']['Item']['currencysign'])) ? $product['fullinfo']['Item']['currencysign'] : '';
                }
                $product['display_price'] = General::getHtmlPrice(array('Val' => htmlspecialchars($product['price']), 'Sign' => htmlspecialchars($currency)));
            }
        } else {
            $data['images'] = json_decode($data['images'], true);
            $data['default_image'] = !empty($data['images'][0]) ? $data['images'][0] : '';
            $data['uploaded_image'] = !empty($data['images'][1]) ? $data['images'][1] : '';
            $data['fullinfo'] = json_decode($data['fullinfo'], true);
            $currency = ! empty($data['currency']) ? $data['currency'] : '';
            if (! empty($data['fullinfo']['Item'])) {            
                $data['config'] = $this->getItemConfig($data['config_id'], $data['fullinfo']['Item']);
                if (empty($currency)) {
                    $currency = $data['fullinfo']['Item']['currencysign'];
                }
            }
            $data['display_price'] = General::getHtmlPrice(array('Val' => htmlspecialchars($data['price']), 'Sign' => htmlspecialchars($currency)));
        }

        return $data;
    }
}
