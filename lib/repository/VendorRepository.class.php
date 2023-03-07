<?php

class VendorRepository extends Repository
{

    private static $registry = array();

	/**
	 * Добавление данных в локальную базу о продавце
	 * @param string $vendorId
	 * @param string $imageUrl        url изображения продавца локально
	 * @param string $VendorName      Введенное название продавца
	 */
    public function addVendorImage($vendorId, $imageUrl, $VendorName)
    {
        $imageUrl = $this->cms->escape($imageUrl);
        $vendorId = $this->cms->escape($vendorId);
		$VendorName = $this->cms->escape($VendorName);
        return (bool) $this->cms->queryMakeArray('
                INSERT INTO `site_vendors_images`
                SET `vendor_id` = "' . $vendorId . '"
                   ,`vendor_name` = "' . $VendorName . '"
                   ,`image_path` = "' . $imageUrl . '"
            ', array('site_vendors_images'));
    }

	/**
	 * Редактирование данных о продавце в локальной базе
	 * @param string $vendorId
	 * @param string $imageUrl
	 * @param string $VendorName
	 */
	public function editVendorImage($vendorId, $VendorName, $imageUrl)
	{
	$vendorId = $this->cms->escape($vendorId);
	$imageUrl = $this->cms->escape($imageUrl);
	$VendorName = $this->cms->escape($VendorName);
	return $res = $this->cms->query('
                UPDATE `site_vendors_images`
                SET `vendor_name` = "' . $VendorName . '"
                   ,`image_path` = "' . $imageUrl . '"
                WHERE `vendor_id` = "' . $vendorId . '"');
	}

	 /**
	 * Удаление данных о продавце из локальной базы
	 * @param string $vendorId
	 */
	public function deleteVendorImage($vendorId)
	{
	$vendorId = $this->cms->escape($vendorId);
	return $res = $this->cms->query('DELETE FROM `site_vendors_images`
							   WHERE `vendor_id` = "' . $vendorId . '"');
	}

    /**
     * Вернет массив [$id, $needRedirect], если $needRedirect = true,
     * то $id будет содержать Url для редиректа
     *
     * @param $alias
     * @return array
     */
    public function parseVendorIdFromAlias($alias)
    {
        if (!General::IsFeatureEnabled('Seo2')) {
            return [$alias, false];
        }

        $alias = rawurldecode($alias);
        $id = $this->getVendorIdByAlias($alias);
        if (empty($id)) {
            $redirectUrl = UrlGenerator::generateContentUrl('404');
            return [$redirectUrl, true];
        }
        return [$id, false];
    }

    private function getVendors()
    {
        $result = $this->cms->queryMakeArray('SELECT * FROM `site_vendors_images`', array('site_vendors_images'));
        $vendors = array();
        foreach ($result as $item) {
            $vendors[$item['vendor_id']][] = $item;
        }
        return $vendors;
    }

    public function GetVendorInfo($vendorId)
    {
        $vendorIdSafe = $this->cms->escape($vendorId);
        if (!isset(self::$registry['vendors'])) {
            self::$registry['vendors'] = $this->getVendors();
        }
        return isset(self::$registry['vendors'][$vendorIdSafe]) ? self::$registry['vendors'][$vendorIdSafe] : false;
    }

    public function getVendorIdByAlias($alias)
    {
        if (!isset(self::$registry['vendors'])) {
            self::$registry['vendors'] = $this->getVendors();
        }
        if (!isset(self::$registry['vendorsByAlias'])) {
            $vendorsByAlias = [];
            foreach (self::$registry['vendors'] as $vendor) {
                $vendorsByAlias[$vendor[0]['alias']] = $vendor[0];
            }
            self::$registry['vendorsByAlias'] = $vendorsByAlias;
        }

        $alias = $this->cms->escape($alias);
        return isset(self::$registry['vendorsByAlias'][$alias]['vendor_id']) ? self::$registry['vendorsByAlias'][$alias]['vendor_id'] : false;
    }
}
