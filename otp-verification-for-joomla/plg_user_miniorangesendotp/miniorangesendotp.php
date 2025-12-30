<?php
/**
 * @package     Joomla.User
 * @subpackage  plg_user_miniorangesendotp
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access

jimport('joomla.user.helper');
jimport('joomla.plugin.plugin');
jimport('miniorangeotpplugin.utility.commonOtpUtilities');

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
$lang = Factory::getLanguage();
$lang->load('plg_user_miniorangesendotp', JPATH_ADMINISTRATOR);
/**
 * miniOrange OTP Plugin plugin
 */

require_once 'common-elements.php';
require_once 'messages.php';
require_once 'miniorange_logic_interface.php';
require_once 'miniorange_form_handler.php';
require_once 'miniorange_email_logic.php';
require_once 'miniorange_phone_logic.php';
require_once 'miniorange_email_or_phone_logic.php';
require_once 'constants.php';
require_once 'moutility.php';
require_once 'curl.php';  
 
class PlgUserMiniorangesendotp extends CMSPlugin
{

    /*OTP verification During Registration time*/

    public function onUserBeforeSave($oldUser, $isnew, $newuser)
    {
        $columnName                 = array('registration_otp_type','enable_during_registration','mo_default_country_code');
        $customer_details           = commonOtpUtilities::getCustomerDetails($columnName, '#__miniorange_otp_customer', 'loadObjectList', array('id' => 1,));
        $registration_otp_type      = isset($customer_details[0]->registration_otp_type) ? $customer_details[0]->registration_otp_type : 0;
        $enable_during_registration = isset($customer_details[0]->enable_during_registration) ? $customer_details[0]->enable_during_registration : 0;
        $default_country_code       = isset($customer_details[0]->mo_default_country_code) ? $customer_details[0]->mo_default_country_code : 0;

        if ($enable_during_registration != '1') return;

        if (commonOtpUtilities::is_customer_registered()) {
            $errors = NULL;
            if ($this->checkIfVerificationIsComplete()) return $errors;
            $phone_number = NULL;
            foreach ($newuser as $key => $value) {
                if ($key == "username")
                    $username = $value;
                elseif ($key == "email1") 
                    $email = $value;
                elseif ($key == "password1")
                    $password = $value;
                elseif ($key == "profile")
                    $phone_number = $value['phone'];
                else
                    $extra_data[$key] = $value;
            }

            $app   = Factory::getApplication();
            $input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
            $tab   = $input->get('task', '', 'CMD');
            if (isset($tab) && $tab == 'registration.register') {
                $phone_number = str_replace(" ", "", $phone_number);
                $phno = strlen($phone_number);
                $phbr = substr($phone_number, 0, 1);

                if ($phbr != '+') {
                    if (!empty($default_country_code)) {
                        $phone_number = '+'.$default_country_code.$phone_number;
                        $phbr = '+';
                    }
                }

                if ($phone_number != '') {
                    if ($phno <= 4 || $phno >= 18 || $phbr != '+') {
                        $result = commonOtpUtilities::_get_custom_message();
                        $invalid_format = isset($result['mo_custom_phone_invalid_format_message']) ? $result['mo_custom_phone_invalid_format_message'] : '';
                        $app = Factory::getApplication();
                        if(!empty($invalid_format)){
                            $invalid_format = str_replace("##phone##",$phone_number,$invalid_format);
                            $app->enqueueMessage($invalid_format, 'error');
                        }else{
                            $app->enqueueMessage(Text::_('PLG_USER_MINIORANGESENDOTP_PHONE_ERROR_MSG'), 'error');
                        }
                        $app->redirect(Route::_('index.php/component/users/?view=registration&Itemid=101'));
                    }
                }
                $this->startVerificationProcess($registration_otp_type, $username, $email, $errors, $phone_number, $password, $extra_data);
                //MoCurlOTP::mo_send_otp_token('EMAIL',$newuser["email1"],'');
            }
        }
    }

    function checkIfVerificationIsComplete()
    {
        $session = Factory::getSession();
        $formvalidation = $session->get('formvalidation');
        if (isset($formvalidation) && $formvalidation == 'success') {
            $this->unsetOTPSessionVariables();
            return TRUE;
        }
        return FALSE;
    }

    public static function unsetOTPSessionVariables()
    {
        $session = Factory::getSession();
        $formvalidation = $session->get('formvalidation');
        $test = $session->get('test');
        $form = $session->set('formvalidation', 'Done');
        unset($test);
        unset($form);
    }

    public static function startVerificationProcess($_otp_type, $username, $email, $errors, $phone_number, $password, $extra_data ,$resend=0)
    {
        $default_country_code = commonOtpUtilities::__getDBValuesWOArray('#__miniorange_otp_customer');
        $default_country_code = $default_country_code['mo_default_country_code'] ?? null;
        /**
         * $_otp_type = 1 => OTP over Email method
         * $_otp_type = 2 => OTP over SMS method
         * $_otp_type = 3 => OTP over Email or SMS method
         * $_otp_type = 4 => OTP over Email and SMS method
         */
        if (empty($phone_number) && ($_otp_type == 2 || $_otp_type == 3 || $_otp_type == 4))
        {
            $app = Factory::getApplication();
            $app->enqueueMessage(Text::_('PLG_USER_MINIORANGESENDOTP_PHONE_REQUIRED'), 'error');
            $app->redirect(Route::_('index.php/component/users/?view=registration&Itemid=101'));
        } 
        else if (!empty($phone_number) && ($_otp_type == 2 || $_otp_type == 3 || $_otp_type == 4))
        {
            $phone_number = str_replace(" ", "", $phone_number);
            $phno = strlen($phone_number);
            $phbr = substr($phone_number, 0, 1);
            if ($phbr != '+') 
            {
                if (!empty($default_country_code))
                {
                    $phone_number = '+'.$default_country_code.$phone_number;
                    $phbr = '+';
                }
            }

            if ($phone_number == '' || $phno <= 4 || $phno >= 18 || $phbr != '+' )
            {
                $result = commonOtpUtilities::_get_custom_message();
                $invalid_format = isset($result['mo_custom_phone_invalid_format_message']) ? $result['mo_custom_phone_invalid_format_message'] : '';
                $app = Factory::getApplication();
                if (!empty($invalid_format))
                {
                    $msg = str_replace("##phone##",$phone_number,$invalid_format);

                } else
                {
                    $msg = Text::_('PLG_USER_MINIORANGESENDOTP_PHONE_ERROR_MSG');
                }
                $app->enqueueMessage($msg, 'error');
                $app->redirect(Route::_('index.php/component/users/?view=registration&Itemid=101'));
            }
        }

        $session = Factory::getSession();
        switch ($_otp_type){
            case '1':
                $otp_method = 'email';
                $session->set('otp_method',$otp_method);
                miniorange_site_challenge_otp($username, $email, $errors,$otp_method,  $phone_number, $password, $extra_data,false,$resend);
                break;
            case '2':
                $otp_method = 'phone';
                $session->set('otp_method',$otp_method);
                miniorange_site_challenge_otp($username, $email, $errors,$otp_method,  $phone_number, $password, $extra_data,false,$resend);
                break;
            case '3':
                $otp_method = 'otp_over_email_or_sms';
                $session->set('otp_method',$otp_method);
                miniorange_site_challenge_otp($username, $email, $errors,$otp_method,  $phone_number, $password, $extra_data,false,$resend);
                break;
            case '4':
                $otp_method = 'otp_over_email_and_sms';
                $session->set('otp_method',$otp_method);
                miniorange_site_challenge_otp($username, $email, $errors,$otp_method,  $phone_number, $password, $extra_data,false,$resend);
                break;
        }
    }
}