<?php
class NotFoundException extends Exception
{
    const ADMIN_SESSION_EMPTY = 1;
    
    public function getErrorCode()
    {
        return 'NotFound';
    }
}
