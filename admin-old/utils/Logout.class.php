<?php

class Logout
{
    public function defaultAction(){
        unset($_SESSION['sid']);
        unset($_SESSION['current_roles']);
        header('Location: index.php?cmd=login');
    }

}