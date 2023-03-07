<?php

interface IRule
{
    public function test($value);
    public function getMessage();
}