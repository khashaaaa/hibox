<?php

class OTCaptcha implements WidgetInterface
{
    private static $captchaRendered = false;
    private static $captchaRender = '';

    const GOOGLE_RECAPTCHA_VALIDATE_URL = 'https://www.google.com/recaptcha/api/siteverify';

    public static function getWidget(array $options = [])
    {
        if (self::isCaptchaUnavailable()) return false;

        if (General::getConfigValue('google_recaptcha')) {
            return self::renderGoogleReCaptcha();
        } else {
            return self::renderGDCaptcha();
        }
    }

    public static function validate($fields)
    {
        if (self::isCaptchaUnavailable()) return true;

        if (General::getConfigValue('google_recaptcha')) {
            return self::validateGoogleReCaptcha($fields);
        } else {
            return self::validateGDCaptcha($fields);
        }
    }

    private static function renderGoogleReCaptcha()
    {
        if (! self::$captchaRendered) {
            $strScript = '<script>
                          var renderGoogleReCaptcha = function() {
                              var sitekey = "' . General::getConfigValue("google_recaptcha_key_public") . '";
                              $(".googleReCaptcha").each(function() {
                                  grecaptcha.render($(this).attr("id"), {
                                      "sitekey" : sitekey,
                                  });
                              });
                          };
                          </script>';
            AssetsMin::registerJs($strScript);
            AssetsMin::registerJs('<script src="https://www.google.com/recaptcha/api.js?onload=renderGoogleReCaptcha&render=explicit&hl=' . Session::getActiveLang() . '" async defer></script>');

            self::$captchaRendered = true;
        }

        return General::viewFetch('/widget/google-captcha', array(
            'path' => CFG_VIEW_ROOT,
            'vars' => array(
                    'id' => 'googleReCaptcha_' . rand(10000, 99999)   // уникальный Id для гугл капчи
                )
            )
        );
    }

    private static function validateGoogleReCaptcha($fields)
    {
        if (empty($fields['g-recaptcha-response'])) {
            throw new Exception(Lang::get('pass_security_check'));
        }

        $post_data = array (
            'secret' => General::getConfigValue('google_recaptcha_key_secret'),
            'response' => $fields['g-recaptcha-response'] ?: '',
        );

        $Curl = new Curl(self::GOOGLE_RECAPTCHA_VALIDATE_URL);
        $Curl->setPost($post_data, false);
        $Curl->connect();
        $googleAnswer = json_decode($Curl->getWebPage(), true);

        if (isset($googleAnswer['success']) && $googleAnswer['success'] == true) {
            return true;
        }

        throw new Exception(Lang::get('pass_security_check'));
    }

    private static function renderGDCaptcha()
    {
        if (! self::$captchaRendered) {
            $strScript = '<script>
                              $(".gd-captchaRefresh").on("click", function() {
                                   var id = Math.random();
                                   $(".captcha .captcha-img").attr("src", "./lib/securimage/securimage_show.php?sid=" + id);
                                   $(".gd-captchaRefresh").blur();
                                   
                                   return false;
                              });
                              
                              var captchaRefresh = function() {
                                  $("input.gdcaptcha").val("");
                                  $(".gd-captchaRefresh").click();
                              };
                          </script>';
            AssetsMin::registerJs($strScript);

            self::$captchaRender = General::viewFetch('/widget/gd-captcha', array(
                    'path' => CFG_VIEW_ROOT,
                    'vars' => array(
                        'id' => 'gdcaptcha_' . rand(10000, 99999)   // уникальный Id для GD капчи
                    )
                )
            );

            self::$captchaRendered = true;
        }

        return self::$captchaRender;
    }

    private static function validateGDCaptcha($fields)
    {
        if (empty($fields['ct_captcha'])) {
            throw new Exception(Lang::get('not_entered_code'));
        }

        $captchapath = dirname(dirname(__FILE__)).'/securimage/securimage.php';
        require_once $captchapath;
        $securimage = new Securimage();

        if ($securimage->check($fields['ct_captcha']) == true) {
            return true;
        }

        throw new Exception(Lang::get('pass_security_check'));
    }

    public static function isCaptchaUnavailable()
    {
        return defined('CFG_NO_CAPTCHA');
    }
}