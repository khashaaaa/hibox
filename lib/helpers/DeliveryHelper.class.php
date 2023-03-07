<?php


class DeliveryHelper
{
    /**
     * @param string $deliveryId
     * @param array $deliveryModes
     * @return array
     */
    public static function getDeliveryMode($deliveryId, $deliveryModes)
    {
        // если есть способ доставки, то возвращаем его
        if (key_exists($deliveryId, $deliveryModes)) {
            return $deliveryModes[$deliveryId];
        }

        // если способа доставки нету, то ищем самый дешевый вариант
        $cheapestModeId = false;
        foreach ($deliveryModes as $mode) {
            $cheapestModeId = $cheapestModeId ? $cheapestModeId : $mode['Id'];

            $costCheapest = $deliveryModes[$cheapestModeId]['Cost']['Val'];
            $costCurrent = $mode['Cost']['Val'];
            $cheapestModeId = ($costCurrent < $costCheapest) ? $mode['Id'] : $cheapestModeId;
        }

        return $deliveryModes[$cheapestModeId];
    }
}