<?php
/**
 * @package     Joomla.User
 * @subpackage  plg_user_miniorangesendotp
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');
jimport('miniorangeotpplugin.utility.commonOtpUtilities');

class MocURLOTP
{


    public static function mo_send_otp_token($auth_type, $email = '', $phone = '')
    {
        
        require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR .
            'com_joomlaotp' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'mo_otp_utility.php';

        $url = MoOTPConstants::HOSTNAME . '/moas/api/auth/challenge';

        $columnName = array('customer_key','api_key');
        $customer_details = commonOtpUtilities::getCustomerDetails($columnName, '#__miniorange_otp_customer', 'loadObjectList', array('id' => 1,));

        $customerKey = !MoUtility::isBlank($customer_details[0]->customer_key) ? $customer_details[0]->customer_key : null;
        $apiKey      = !MoUtility::isBlank($customer_details[0]->api_key) ? $customer_details[0]->api_key : null;

        if ($customerKey == null || $apiKey == null){
            return json_encode(array('status'=>'FAILED'));
        }

        $fields = array(
            'customerKey' => $customerKey,
            'email' => $email,
            'phone' => $phone,
            'authType' => $auth_type,
            'transactionName' => MoOTPConstants::AREA_OF_INTEREST
        );
        $json = json_encode($fields);
        $authHeader = self::createAuthHeader($customerKey, $apiKey);
        $response = self::callAPI($url, $json, $authHeader);
       
        return $response;
    }

    public static function validate_otp_token($transactionId, $otpToken)
    {
        require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR .
            'com_joomlaotp' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'mo_otp_utility.php';

        $url = MoOTPConstants::HOSTNAME . '/moas/api/auth/validate';
        $columnName = array('customer_key','api_key');
        $customer_details = commonOtpUtilities::getCustomerDetails($columnName, '#__miniorange_otp_customer', 'loadObjectList', array('id' => 1,));

        $customerKey = !MoUtility::isBlank($customer_details[0]->customer_key) ? $customer_details[0]->customer_key : null;
        $apiKey      = !MoUtility::isBlank($customer_details[0]->api_key) ? $customer_details[0]->api_key : null;


        $fields = array(
            'txId' => $transactionId,
            'token' => $otpToken,
        );
        $json = json_encode($fields);
        $authHeader = self::createAuthHeader($customerKey, $apiKey);
        $response = self::callAPI($url, $json, $authHeader);


        return $response;
    }


    public static function check_customer_ln($customerKey, $apiKey)
    {
        $url = MoOTPConstants::HOSTNAME . '/moas/rest/customer/license';
        $fields = array(
            'customerId' => $customerKey,
            'applicationName' => MoOTPConstants::APPLICATION_NAME,
            'licenseType' => !MoUtility::micr() ? 'DEMO' : 'PREMIUM',
        );

        $json = json_encode($fields);
        $authHeader = self::createAuthHeader($customerKey, $apiKey);
        $response = self::callAPI($url, $json, $authHeader);
        return $response;
    }

    private static function createAuthHeader($customerKey, $apiKey)
    {
        $currentTimestampInMillis = round(microtime(true) * 1000);
        $currentTimestampInMillis = number_format($currentTimestampInMillis, 0, '', '');

        $stringToHash = $customerKey . $currentTimestampInMillis . $apiKey;
        $authHeader = hash("sha512", $stringToHash);

        $header = array(
            "Content-Type: application/json",
            "Customer-Key: $customerKey",
            "Timestamp: $currentTimestampInMillis",
            "Authorization: $authHeader"
        );
        return $header;
    }

    private static function callAPI($url, $json_string, $headers = array("Content-Type: application/json"))
    {
        $ch = curl_init($url);


        if (defined('WP_PROXY_HOST') && defined('WP_PROXY_PORT')
            && defined('WP_PROXY_USERNAME') && defined('WP_PROXY_PASSWORD')) {
            curl_setopt($ch, CURLOPT_PROXY, WP_PROXY_HOST);
            curl_setopt($ch, CURLOPT_PROXYPORT, WP_PROXY_PORT);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, WP_PROXY_USERNAME . ':' . WP_PROXY_PASSWORD);
        }
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        if (!is_null($headers)) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        if (!is_null($json_string)) curl_setopt($ch, CURLOPT_POSTFIELDS, $json_string);

        $content = curl_exec($ch);


        if (curl_errno($ch)) {

            echo 'Request Error:' . curl_error($ch);
            exit();
        }
        curl_close($ch);

        return $content;
    }

    public static function send_notif(NotificationSettings $settings)
    {
        $url = MoOTPConstants::HOSTNAME . '/moas/api/notify/send';
        $customerKey = get_mo_option('mo_customer_validation_admin_customer_key');
        $apiKey = get_mo_option('mo_customer_validation_admin_api_key');

        $fields = array(
            'customerKey' => $customerKey, 'sendEmail' => $settings->sendEmail, 'sendSMS' => $settings->sendSMS,
            'email' => array('customerKey' => $customerKey, 'fromEmail' => $settings->fromEmail, 'bccEmail' => $settings->bccEmail,
                'fromName' => $settings->fromName, 'toEmail' => $settings->toEmail, 'toName' => $settings->toEmail,
                'subject' => $settings->subject, 'content' => $settings->message
            ), 'sms' => array('customerKey' => $customerKey, 'phoneNumber' => $settings->phoneNumber,
                'message' => $settings->message));

        $json = json_encode($fields);
        $authHeader = self::createAuthHeader($customerKey, $apiKey);
        $response = self::callAPI($url, $json, $authHeader);
        return $response;
    }
}