<?php
class ErrorUtil extends GeneralUtil {
    protected $_cache = false;
    protected $_life_time = 3600;
    protected $_template = '';
    protected $_template_path = '';

    /**
     * @param ServiceException $e
     * @return string
     */
    public function showErrorWithPNotify($e){
        $this->_template_path = 'error/';
        $this->_template = 'pnotify';

        $code = ($e instanceof ServiceException) ? $e->getErrorCode() : $e->getCode();

        $this->tpl->assign('message', $code == 500 ? 'Service Internal Error' : $e->getMessage());
        $this->tpl->assign('code', $code);

        return $this->fetchTemplateWithoutHeaderAndFooter();
    }
}