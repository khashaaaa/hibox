<?php

class AdminLog extends Log {
    protected $notificationUrl;
    protected $beginTime;
    protected $loadingTime;

    protected $filePath;
    protected $backupPath;

    public function __construct($request = null){
        $this->request = $request ? $request : new RequestWrapper();
        $this->filePath = dirname(__FILE__) . '/log.admin.dat';
        $this->backupPath = dirname(__FILE__) . '/log.admin.ready.dat';
        $this->notificationUrl = CFG_SUPPORT_URL . '/log_analyzer/on_ready_admin_log';

        $this->Create();
    }

    public function CompleteClose(){
        $this->Stop();
        $this->Write();
        if($this->Size() > 10){
            $this->Release();
        }
    }
}