<?php

interface ICache{
    public  function AddCacheEl($key,$life_time = 21600,$value);
    public  function DelCacheEl($key);
    public  function GetCacheEl($key);
}

?>