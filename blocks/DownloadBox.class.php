<?php

OTBase::import('system.lib.Validation.*');
OTBase::import('system.lib.Validation.Rules.*');
OTBase::import('system.lib.dispatcher.classes.*');

class DownloadBox extends GenerateBlock
{
    protected $_cache = false;
    protected $_life_time = 3600;
    protected $_template = 'download_box_form';
    protected $_template_path = '/forms/';

    public function __construct()
    {
        parent::__construct(true);
    }

    protected function setVars()
    {
        $errors = array();
        if ($this->request->getMethod() == 'POST') {
            $validator = new Validator(array(
                'fio' => trim($this->request->getValue('fio')),
                'email' => trim($this->request->getValue('email')),
            ));
            $validator->addRule(new NotEmptyString(), 'fio', Lang::get('Name_cannot_be_empty'));
            $validator->addRule(new NotEmptyString(), 'email', Lang::get('Email_cannot_be_empty'));
            $data = $validator->getData();
            $this->tpl->assign('data', $data);
            if (! $this->checkCaptcha()) {
                Session::setError(Lang::get('incorrect_code'), 'incorrect_code');
            } else {
                if (! $validator->validate()) {
                    $errors = $validator->getErrors();
                } else {
                    $this->addClaim($data);
                    $this->sendMail($data);
                    $path = CFG_TOOLS_URL . '/update_rep/info/info.php?phpVersion=' . phpversion();
                    $versions = simplexml_load_string(trim(file_get_contents($path)));
                    $version = $versions->Version[0]->Number;
                    $link = CFG_TOOLS_URL . '/update_rep/info/download.php?version=' . $version;
                    header('Location: '.$link);
                }
            }
        }
        $this->tpl->assign('errors', $errors);
    }

    private function checkCaptcha()
    {
        require_once dirname(dirname(__FILE__)).'/lib/securimage/securimage.php';
        $secureImage = new Securimage();
        return $secureImage->check($this->request->getValue('ct_captcha'));
    }

    private function sendMail($data)
    {
        $subject = 'Скачивание коробки с демо-сайта';
        $text = '<b>Контакты заинтересованного лица</b>:<br><br>';
        $text .= '<table cellpadding="3" cellspacing="0" border="1">';
        foreach ($this->request->getAll() as $attr => $value) {
            if ($attr == 'ct_captcha') continue;
            $attr = str_replace('_', ' ', $attr);
            if (empty($value)) $value = '-';
            $text .= '<tr><td>'.htmlentities($attr, ENT_QUOTES).'</td><td>'.htmlentities($value, ENT_QUOTES).'</td></tr>';
        }
        $text .= '</table>';

        $to = "info@otcommerce.com";
        $headers  = "Content-type: text/html; charset=utf-8 \r\n";
        $headers .= "From: [http://demo.opentao.net] <no-reply@demo.opentao.net>\r\n";
        $res = mail($to, $subject, $text, $headers);
    }

    private function addClaim($data)
    {
        $userClaim = new stdClass();

        $userClaim->Comment = '';
        $userClaim->Email = @$data['email'];
        $userClaim->Name = @$data['email'];
//        $userClaim->Phone = @$data['phone'];
        $userClaim->Skype = @$data['skype'];
        $userClaim->Source = 'Demo';

        try {

            $accountManager = OTWebClient::getService('CRMIntegrator');

            $accountManager->Client->AddClaim(
                array(
                    'login' => $accountManager->getLogin(),
                    'password' => $accountManager->getPassword(),
                    'userClaim' => $userClaim
                )
            );

        } catch (SoapFault $e) {
//            echo $e->getMessage();
        }
    }
}
