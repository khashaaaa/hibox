<?php

class OtapiArrayOfInstanceUserRoleInfo1 extends BaseOtapiType{
    /**
     * @return OtapiInstanceUserRoleInfo[]
     */
    public function GetInstanceUserRoleInfo(){
        return isset($this->xmlData->InstanceUserRoleInfo) ? new UnboundedElementsIterator(
                $this->xmlData->InstanceUserRoleInfo,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiInstanceUserRoleInfo'
                )
            ) : array();
    }
}