<?php

class SendDebugInfo extends GenerateBlock
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'send_debug_info'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/debug/'; //- путь к шаблону

    public function setVars()
    {
        try {
            if (! Session::get('sid')) {
                throw new NotFoundException('admin_session_not_found', NotFoundException::ADMIN_SESSION_EMPTY);
            }

            $debugInfo = unserialize($this->request->post('logs'));
            if (empty($debugInfo)) {
                throw new NotFoundException('debug_file_not_found');
            }

            $lang = Session::get('active_lang') == 'ru' ? 'ru' : 'en';

            $c = new Curl(CFG_SUPPORT_URL.'/'.$lang.'/test_site_speed/', 60, true, 10);
            $c->setPost($debugInfo, false);
            $c->connect();

            if ($c->getHttpStatus() != 200)
                throw new Exception('test_speed_service_connection_error');

            $json = json_decode($c->getWebPage());

            if(!$json->success)
                throw new Exception('test_speed_service_error');

            header('Location: '.CFG_SUPPORT_URL.'/'.$lang.'/test_site_speed/result/'.$json->test_id);
            die;
        } catch (NotFoundException $e) {
            $this->tpl->assign('error', $e->getMessage());
        } catch (Exception $e) {
            $this->tpl->assign('error', $e->getMessage());
        }
    }
}
