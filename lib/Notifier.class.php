<?php

OTBase::import('system.lib.helpers.EmailHelper');
OTBase::import('system.lib.helpers.TextHelper');

class Notifier
{
    public static function generalNotification($template, $title, $data, $predefinedEmail = '')
    {
        Lang::getTranslations('', General::getConfigValue('admin_letter_lang', 'ru'));
        $mailParams = self::getMailParams();
        $bodyBlock = EmailHelper::getInstance();
        $bodyBlock->setTemplate($template);
        $bodyBlock->setData($data);
        $text = $bodyBlock->generate();
        if (! $predefinedEmail) {
            if (! is_array($mailParams['mailTo'])) {
                General::mail_utf8($mailParams['mailTo'], $mailParams['nameFrom'], $mailParams['mailFrom'], $title, $text);
            } else {
                foreach ($mailParams['mailTo'] as $email) {
                    General::mail_utf8($email, $mailParams['nameFrom'], $mailParams['mailFrom'], $title, $text);
                }
            }
        } else {
            General::mail_utf8($predefinedEmail, $mailParams['nameFrom'], $mailParams['mailFrom'], $title, $text);
        }
        Lang::getTranslations('', Session::getActiveLang());
    }

    public static function generalUserNotification($emails,$template,$title,$data){
        $mailParams = self::getMailParams();
        if(General::getConfigValue($template)){
            $text = self::prepareTextMessage(General::getConfigValue($template), $data);
        }
        else{
            $bodyBlock = EmailHelper::getInstance();
            $bodyBlock->setTemplate($template);
            $bodyBlock->setData($data);
            $text = $bodyBlock->generate();
        }
        $emails = str_replace(" ", "", $emails);
        $emails = explode(';', $emails);
        foreach ($emails as $email) {
            if(!file_exists(CFG_APP_ROOT . '/cache/mails/'))
                @mkdir(CFG_APP_ROOT . '/cache/mails/', 0777);
            @file_put_contents(CFG_APP_ROOT . '/cache/mails/' . time() . '_' . $template . '_' . $email . '.txt', $text);
            General::mail_utf8($email, $mailParams['nameFrom'], $mailParams['mailFrom'], $title, nl2br($text));
        }
    }

    /*
     * Сообщение админу о том что пытаются оплатить заказа наличными со страницы заказа
     */
    public static function notifyAdminOnPaymentInCash($order, $user, $paymentSum)
    {
        Lang::getTranslations('', General::getConfigValue('admin_letter_lang', 'ru'));
        $mailParams = self::getMailParams();

        $currencySign = (string) isset($order['CurrencySign']) ? $order['CurrencySign'] : $order['currencySign'];
        $createDateTime = (string) isset($order['CreatedDateTime']) ? $order['CreatedDateTime'] : $order['createdDateTime'];
        $params = array(
            'currency' => $currencySign,
            'username' => $user['login'],
            'orderId' => (string) $order['id'],
            'first_name' => isset($user['firstname']) ? $user['firstname'] : $user['firstName'],
            'last_name' => isset($user['lastname']) ? $user['lastname'] : $user['lastName'],
            'middle_name' => isset($user['middlename']) ? $user['middlename'] : $user['middleName'],
            'payment_sum' => TextHelper::formatPrice($paymentSum),
            'payment_sum_text' => TextHelper::formatPrice((float) $paymentSum, $currencySign),
            'date' => date('Y-m-d H:i:s', strtotime($createDateTime)),
        );

        if (! General::getConfigValue('email_order_pay_cash')) {
            $params['user'] = $user;

            $bodyBlock = EmailHelper::getInstance();
            $bodyBlock->setTemplate('payment_in_cash_order');
            $bodyBlock->setData($params);
            $text = $bodyBlock->generate();

            unset( $params['user']);
        } else {
            $text = General::getConfigValue('email_order_pay_cash');
        }
        $text = self::prepareTextMessage($text, $params);
        foreach ($mailParams['mailTo'] as $email) {
            General::mail_utf8($email, $mailParams['nameFrom'], $mailParams['mailFrom'], Lang::get('payment_in_cash'), $text);
        }
        Lang::getTranslations('', Session::getActiveLang());
    }

    public static function notifyAdminWithdrawMoney($user, $availableSum, $withdrawSum, $comment)
    {
        Lang::getTranslations('', General::getConfigValue('admin_letter_lang', 'ru'));
        if (! General::getConfigValue('email_money_withdrawal')) {
            $bodyBlock = EmailHelper::getInstance();
            $bodyBlock->setTemplate('email_money_withdrawal');
            $bodyBlock->setData(array('username' => $user['login'],'first_name' => $user['firstname'],'last_name' => $user['lastname'],'middle_name' => $user['middlename'],'withdraw_sum' => TextHelper::formatPrice($withdrawSum),'available_sum' => TextHelper::formatPrice($availableSum),'currency' => !empty($user['currencysign']) ? (string)$user['currencysign'] : '','comment' => $comment ));
            $text = $bodyBlock->generate();
        } else {
            $text = General::getConfigValue('email_money_withdrawal');
            $text = self::prepareTextMessage($text, array('username' => $user['login'],'first_name' => $user['firstname'],'last_name' => $user['lastname'],'middle_name' => $user['middlename'],'withdraw_sum' => TextHelper::formatPrice($withdrawSum),'available_sum' => TextHelper::formatPrice($availableSum),'currency' => !empty($user['currencysign']) ? (string)$user['currencysign'] : '','comment' => $comment,
            ));
        }
        $mailParams = self::getMailParams();
        foreach ($mailParams['mailTo'] as $email) {
            General::mail_utf8($email, $mailParams['nameFrom'], $mailParams['mailFrom'], Lang::get('Withdrawals'), $text);
        }
        Lang::getTranslations('', Session::getActiveLang());
    }

    public static function notifyUserOnTicketAnswer($data)
    {
        if (empty($data['email'])) {
            return false;
        }
        $mailParams = self::getMailParams();
        if (! General::getConfigValue('email_ticket_answer')) {
            $bodyBlock = EmailHelper::getInstance();
            $bodyBlock->setTemplate('ticket_answer');
            $bodyBlock->setData(array('ticket_id' => (string)$data['ticket_id'], 'txt_message' => (string)$data['txt_message']));
            $mailblock = $bodyBlock->generate();
            if (!isset($_SESSION['mail_as_text'])) {
                General::mail_utf8($data['email'], $mailParams['nameFrom'], $mailParams['mailFrom'],Lang::get('new_ticket_answer'), $mailblock);
            } else {
                General::mail_utf8_txt($data['email'], $mailParams['nameFrom'], $mailParams['mailFrom'],Lang::get('new_ticket_answer'), $mailblock);
            }
        } else {
            $mailblock = General::getConfigValue('email_ticket_answer');
            $mailblock = str_replace(array('{{ticketid}}', '{{txtmessage}}'), array($data['ticket_id'], $data['txt_message']), $mailblock);
            if (!isset($_SESSION['mail_as_text'])) {
                General::mail_utf8($data['email'], $mailParams['nameFrom'], $mailParams['mailFrom'],Lang::get('new_ticket_answer'), $mailblock,true);
            } else {
                General::mail_utf8_txt($data['email'], $mailParams['nameFrom'], $mailParams['mailFrom'],Lang::get('new_ticket_answer'), $mailblock,true);
            }
        }
    }

    private static function prepareTextMessage($text, array $params)
    {
        foreach ($params as $key => $value) {
            $text = str_replace('{{' . $key . '}}', $value, $text);
        }
        return $text;
    }

    public static function getMailParams($check = false)
    {
        if ($check) {
            return array(
                'mailTo' => General::getConfigValue('notification_email', false),
                'mailFrom' => General::getConfigValue('notification_send_from', false),
                'nameFrom' => General::getConfigValue('notification_send_from_name', false),
            );
        } else {
            $mailTo = array();
            $arrayOfMails = General::getConfigValue('notification_email', '');
            $arrayOfMails = str_replace(" ", "", $arrayOfMails);
            $arrayOfMails = explode(';', $arrayOfMails);
            return array(
                'mailTo' => $arrayOfMails,
                'mailFrom' => General::getConfigValue('notification_send_from', 'noreply@' . preg_replace('/^www\./','',$_SERVER['HTTP_HOST'])),
                'nameFrom' => General::getConfigValue('notification_send_from_name', preg_replace('/^www\./','',$_SERVER['HTTP_HOST'])),
            );
        }

    }
}
