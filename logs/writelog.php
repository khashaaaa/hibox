<?php

function writeservicelog($methodCallInfo){
    Plugins::invokeEvent('onLogMethodCall', array('methodCallInfo' => $methodCallInfo));
}