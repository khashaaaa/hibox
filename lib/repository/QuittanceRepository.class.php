<?php

class QuittanceRepository extends SiteConfigurationRepository
{
    public function updateQuittance(array $params)
    {
        foreach ($params as $key => $value) {
            $this->Set($key, $value);
        }
    }
}
