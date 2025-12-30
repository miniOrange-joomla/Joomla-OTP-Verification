<?php
/** miniOrange enables user to log in using otp credentials.
Copyright (C) 2015  miniOrange

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>
 * @package 		miniOrange OAuth
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */
/**
This library is miniOrange Authentication Service. 
Contains Request Calls to Customer service.

 **/

defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\CMS\Factory;
use Joomla\CMS\Version;
jimport('miniorangeotpplugin.utility.commonOtpUtilities');

class MoOtpCustomer{

    public $email;
    public $phone;
    public $customerKey;
    public $transactionId;

    /*
    ** Initial values are hardcoded to support the miniOrange framework to generate OTP for email.
    ** We need the default value for creating the OTP the first time,
    ** As we don't have the Default keys available before registering the user to our server.
    ** This default values are only required for sending an One Time Passcode at the user provided email address.
    */

    //auth
    private $defaultCustomerKey = "16555";
    private $defaultApiKey = "fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq";

    function create_customer(){
        if(!MoOtpUtility::is_curl_installed()) {
            return json_encode(array("statusCode"=>'ERROR','statusMessage'=>$error . '. Please check your configuration. Also check troubleshooting under otp configuration.'));
        }
        $hostname = commonOtpUtilities::getHostname();

        $url = $hostname . '/moas/rest/customer/add';
        $ch = curl_init($url);
        $current_user =  Factory::getUser();
        $columnName= array('email','admin_phone','password');
        $customer_details = commonOtpUtilities::getCustomerDetails($columnName, '#__miniorange_otp_customer', 'loadObjectList', array('id' => 1,));

        $this->email = $customer_details[0]->email;
        $this->phone = $customer_details[0]->admin_phone;
        $password = $customer_details[0]->password;

        $fields = array(
            'companyName' => $_SERVER['SERVER_NAME'],
            'areaOfInterest' => 'JOOMLA OTP Plugin',
            'firstname' => $current_user->name,
            'lastname' => '',
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => $password
        );
        $field_string = json_encode($fields);

        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_ENCODING, "" );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls

        curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'charset: UTF - 8',
            'Authorization: Basic'
        ));
        curl_setopt( $ch, CURLOPT_POST, true);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $field_string);
        $content = curl_exec($ch);

        if(curl_errno($ch)){
            echo 'Request Error:' . curl_error($ch);
            exit();
        }
        curl_close($ch);
        return $content;
    }

    function get_customer_key($email,$password) {
        if(!MoOtpUtility::is_curl_installed()) {
            return json_encode(array("apiKey"=>'CURL_ERROR','token'=>'<a href="http://php.net/manual/en/curl.installation.php">PHP cURL extension</a> is not installed or disabled.'));
        }

        $hostname = commonOtpUtilities::getHostname();
        $url = $hostname. "/moas/rest/customer/key";
        $ch = curl_init($url);

        $fields = array(
            'email' => $email,
            'password' => $password
        );
        $field_string = json_encode($fields);

        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_ENCODING, "" );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls
        curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'charset: UTF - 8',
            'Authorization: Basic'
        ));
        curl_setopt( $ch, CURLOPT_POST, true);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $field_string);
        $content = curl_exec($ch);
        if(curl_errno($ch)){
            echo 'Request Error:' . curl_error($ch);
            exit();
        }
        curl_close($ch);
		return $content;
	}


    public static function submit_feedback_form($email,$admin_email,$phone,$query)
    {
        $url =  'https://login.xecurify.com/moas/api/notify/send';
        $ch = curl_init($url);
        $customerKey = "16555";
        $apiKey = "fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq";
        $currentTimeInMillis= round(microtime(true) * 1000);
        $stringToHash 		= $customerKey .  number_format($currentTimeInMillis, 0, '', '') . $apiKey;
        $hashValue 			= hash("sha512", $stringToHash);
        $customerKeyHeader 	= "Customer-Key: " . $customerKey;
        $timestampHeader 	= "Timestamp: " .  number_format($currentTimeInMillis, 0, '', '');
        $authorizationHeader= "Authorization: " . $hashValue;
        $fromEmail 			= $email;
        $Admin_Email        = $admin_email;
        $jVersion           = new Version();
        $phpVersion         = phpversion();
        $jCmsVersion        = $jVersion->getShortVersion();
        $moPluginVersion    = commonOtpUtilities::GetPluginVersion();
        $os                 = commonOtpUtilities::_get_os_info();
        $serverSoftware = isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : 'Unknown';
        $webServer = !empty($serverSoftware) ? trim(explode('/', $serverSoftware)[0]) : 'Unknown';
        $system_info        = "Joomla ".$jCmsVersion." | PHP ".$phpVersion." | Plugin ".$moPluginVersion." | OS ".$os." | Web Server ".$webServer;
        $subject            = "Feedback for MiniOrange Joomla OTP Verification Plugin Free";
        $query1 =" MiniOrange joomla [Free] OTP ";
        $content='<div >Hello, <br><br><strong>Company :</strong><a href="'.$_SERVER['SERVER_NAME'].'" target="_blank" >'.$_SERVER['SERVER_NAME'].'</a><br><br><strong>Phone Number :</strong>'.$phone.'<br><br><strong>Email :</strong><a href="mailto:'.$fromEmail.'" target="_blank">'.$fromEmail.'</a><br><br><strong>Admin Email: </strong>'.$Admin_Email.'<br><br><strong>Plugin Deactivated: </strong>'.$query1. '<br><br><strong>Reason: </strong>' .$query. '<br><br><strong>System info: </strong>'.$system_info.'</div>';
        $fields = array(
            'customerKey'	=> $customerKey,
            'sendEmail' 	=> true,
            'email' 		=> array(
                'customerKey' 	=> $customerKey,
                'fromEmail' 	=> $fromEmail,
                'bccEmail' 		=> 'joomlasupport@xecurify.com',
                'fromName' 		=> 'miniOrange',
                'toEmail' 		=> 'joomlasupport@xecurify.com',
                'toName' 		=> 'joomlasupport@xecurify.com',
                'subject' 		=> $subject,
                'content' 		=> $content
            ),
        );
        $field_string = json_encode($fields);
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_ENCODING, "" );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls
        curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", $customerKeyHeader,
            $timestampHeader, $authorizationHeader));
        curl_setopt( $ch, CURLOPT_POST, true);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $field_string);
        $content = curl_exec($ch);

        if(curl_errno($ch)){
            return json_encode(array("status"=>'ERROR','statusMessage'=>curl_error($ch)));
        }
        curl_close($ch);
        return ($content);
    }
    function submit_request_setup_call($type_service, $email,$number_otp, $select_country,$which_country=null,$query=null)
	{
		$url =  'https://login.xecurify.com/moas/api/notify/send';
        $ch = curl_init($url);
        $customerKey = "16555";
        $apiKey = "fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq";
        $currentTimeInMillis= round(microtime(true) * 1000);
        $stringToHash 		= $customerKey .  number_format($currentTimeInMillis, 0, '', '') . $apiKey;
        $hashValue 			= hash("sha512", $stringToHash);
        $customerKeyHeader 	= "Customer-Key: " . $customerKey;
        $timestampHeader 	= "Timestamp: " .  number_format($currentTimeInMillis, 0, '', '');
        $authorizationHeader= "Authorization: " . $hashValue;
        $fromEmail 			= $email;
        $phpVersion         = phpversion();
        $jVersion           = new Version;
        $jCmsVersion        = $jVersion->getShortVersion();
        $moPluginVersion    = commonOtpUtilities::GetPluginVersion();
        $os                 = commonOtpUtilities::_get_os_info();
        $system_info = 'Joomla '.$jCmsVersion .'| PHP '.$phpVersion .'| Plugin '.$moPluginVersion.'| OS '.$os;
        $current_user       = Factory::getUser();
        $adminEmail         = $current_user->email ?? '';
        $subject = 'MiniOrange Joomla OTP Free - Quote Request';
            $content=' 
                <div>
                    Hello, <br><br>
                    <strong>Company: </strong> <a href="'.$_SERVER['SERVER_NAME'].'" target="_blank" >'.$_SERVER['SERVER_NAME'].'</a><br><br>
                    <strong>Email: </strong><a href="mailto:'.$fromEmail.'" target="_blank">'.$fromEmail.'</a><br><br>
                    <strong>Admin Email:</strong><a href="mailto:'.$adminEmail.'" target="_blank">'.$adminEmail.'</a><br><br>
                    <strong>Quote Requested for OTP through: </strong> '.$type_service.'<br><br>
                    <strong>Total number of OTP requested: </strong> '.$number_otp. '<br>
                    <br><strong>Service Requested for Country: </strong> '.$select_country.'<br>
                    <br><strong>Requested for Country: </strong>'.$which_country.'<br><br>
                    <strong>Extra Query:</strong> '.$query. '
                    <br><br><strong>System info: </strong>'.$system_info.'
                </div>';

		
        $fields = array(
            'customerKey'	=> $customerKey,
            'sendEmail' 	=> true,
            'email' 		=> array(
                'customerKey' 	=> $customerKey,
                'fromEmail' 	=> $fromEmail,                
                'fromName' 		=> 'miniOrange',
                'toEmail' 		=> 'joomlasupport@xecurify.com',
                'toName' 		=> 'joomlasupport@xecurify.com',
                'subject' 		=> $subject,
                'content' 		=> $content
            ),
		);
        $field_string = json_encode($fields);


        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_ENCODING, "" );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls

        curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", $customerKeyHeader,
            $timestampHeader, $authorizationHeader));
        curl_setopt( $ch, CURLOPT_POST, true);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $field_string);
        $content = curl_exec($ch);

        if(curl_errno($ch)){
            return json_encode(array("status"=>'ERROR','statusMessage'=>curl_error($ch)));
        }
        curl_close($ch);

        return ($content);
	}
    function submit_contact_us( $q_email, $q_phone, $query ) {
        if(!MoOtpUtility::is_curl_installed()) {
            return json_encode(array("status"=>'CURL_ERROR','statusMessage'=>'<a href="http://php.net/manual/en/curl.installation.php">PHP cURL extension</a> is not installed or disabled.'));
        }
        $hostname = commonOtpUtilities::getHostname();
        $url = $hostname . "/moas/rest/customer/contact-us";
        $ch = curl_init($url);
        $current_user =  Factory::getUser();
        $query = '[Joomla OTP Verification]: ' . $query;
        $fields = array(
            'firstName'			=> $current_user->username,
            'lastName'	 		=> '',
            'company' 			=> $_SERVER['SERVER_NAME'],
            'email' 			=> $q_email,
            'ccEmail'           => 'joomlasupport@xecurify.com',
            'phone'				=> $q_phone,
            'query'				=> $query
        );
        $field_string = json_encode( $fields );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_ENCODING, "" );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls

        curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'charset: UTF-8', 'Authorization: Basic' ) );
        curl_setopt( $ch, CURLOPT_POST, true);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $field_string);
        $content = curl_exec( $ch );

        if(curl_errno($ch)){
            echo 'Request Error:' . curl_error($ch);
            return false;
        }
        curl_close($ch);
        return true;
    }

    function send_otp_token($auth_type, $emailOrPhone){
        if(!MoOtpUtility::is_curl_installed()) {
            return json_encode(array("status"=>'CURL_ERROR','statusMessage'=>'<a href="http://php.net/manual/en/curl.installation.php">PHP cURL extension</a> is not installed or disabled.'));
        }

        $hostname = commonOtpUtilities::getHostname();
        $url = $hostname . '/moas/api/auth/challenge';
        $ch = curl_init($url);
        $customerKey =  $this->defaultCustomerKey;
        $apiKey =  $this->defaultApiKey;

        /* Current time in milliseconds since midnight, January 1, 1970 UTC. */
        $currentTimeInMillis = round(microtime(true) * 1000);

        /* Creating the Hash using SHA-512 algorithm */
        $stringToHash = $customerKey .  number_format($currentTimeInMillis, 0, '', '') . $apiKey;
        $hashValue = hash("sha512", $stringToHash);

        $customerKeyHeader = "Customer-Key: " . $customerKey;
        $timestampHeader = "Timestamp: " .  number_format($currentTimeInMillis, 0, '', '');
        $authorizationHeader = "Authorization: " . $hashValue;
        if($auth_type=="EMAIL")
        {
            $fields = array(
                'customerKey' => $this->defaultCustomerKey,
                'email' => $emailOrPhone,
                'authType' => $auth_type,
                'transactionName' => 'JOOMLA OTP Plugin'
            );
        }
        else{
            $fields = array(
                'customerKey' => $this->defaultCustomerKey,
                'phone' => $emailOrPhone,
                'authType' => $auth_type,
                'transactionName' => 'JOOMLA OTP Plugin'
            );
        }

        $field_string = json_encode($fields);

        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_ENCODING, "" );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls

        curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", $customerKeyHeader,
            $timestampHeader, $authorizationHeader));
        curl_setopt( $ch, CURLOPT_POST, true);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $field_string);
        $content = curl_exec($ch);

        if(curl_errno($ch)){
            echo 'Request Error:' . curl_error($ch);
            exit();
        }
        curl_close($ch);
        return $content;
    }

    function validate_otp_token($transactionId,$otpToken){
        if(!MoOtpUtility::is_curl_installed()) {
            return json_encode(array("status"=>'CURL_ERROR','statusMessage'=>'<a href="http://php.net/manual/en/curl.installation.php">PHP cURL extension</a> is not installed or disabled.'));
        }
        $hostname = commonOtpUtilities::getHostname();
        $url = $hostname . '/moas/api/auth/validate';
        $ch = curl_init($url);

        $customerKey =  $this->defaultCustomerKey;
        $apiKey =  $this->defaultApiKey;

        /* Current time in milliseconds since midnight, January 1, 1970 UTC. */
        $currentTimeInMillis = round(microtime(true) * 1000);

        /* Creating the Hash using SHA-512 algorithm */
        $stringToHash = $customerKey .  number_format($currentTimeInMillis, 0, '', '') . $apiKey;
        $hashValue = hash("sha512", $stringToHash);

        $customerKeyHeader = "Customer-Key: " . $customerKey;
        $timestampHeader = "Timestamp: " .  number_format($currentTimeInMillis, 0, '', '');
        $authorizationHeader = "Authorization: " . $hashValue;

        $fields = '';

        //*check for otp over sms/email
        $fields = array(
            'txId' => $transactionId,
            'token' => $otpToken,
        );

        $field_string = json_encode($fields);

        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_ENCODING, "" );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls

        curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", $customerKeyHeader,
            $timestampHeader, $authorizationHeader));
        curl_setopt( $ch, CURLOPT_POST, true);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $field_string);
        $content = curl_exec($ch);

        if(curl_errno($ch)){
            echo 'Request Error:' . curl_error($ch);
            exit();
        }
        curl_close($ch);
        return $content;
    }

    function check_customer($email) {
        if(!MoOtpUtility::is_curl_installed()) {
            return json_encode(array("status"=>'CURL_ERROR','statusMessage'=>'<a href="http://php.net/manual/en/curl.installation.php">PHP cURL extension</a> is not installed or disabled.'));
        }
        $hostname = commonOtpUtilities::getHostname();
        $url = $hostname . "/moas/rest/customer/check-if-exists";
        $ch 	= curl_init( $url );

        $fields = array(
            'email' 	=> $email,
        );
        $field_string = json_encode( $fields );

        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_ENCODING, "" );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls

        curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'charset: UTF - 8', 'Authorization: Basic' ) );
        curl_setopt( $ch, CURLOPT_POST, true);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $field_string);
        $content = curl_exec( $ch );
        if( curl_errno( $ch ) ){
            echo 'Request Error:' . curl_error( $ch );
            exit();
        }
        curl_close( $ch );

        return $content;
    }

    function send_tfa_test_mail($fromEmail, $content)
    {
        $url = 'https://login.xecurify.com/moas/api/notify/send';
        // Fetch customer details
        $columnName= array('email','admin_phone','password');
        $customer_details = commonOtpUtilities::getCustomerDetails($columnName, '#__miniorange_otp_customer', 'loadObjectList', array('id' => 1,));
        $customerKey = !empty($customer_details[0]->customer_key) ? $customer_details[0]->customer_key : '16555';
        $apiKey = !empty($customer_details[0]->api_key) ? $customer_details[0]->api_key : 'fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq';
        // Timestamp and hash
        $currentTimeInMillis = round(microtime(true) * 1000);
        $stringToHash = $customerKey . $currentTimeInMillis . $apiKey;
        $hashValue = hash("sha512", $stringToHash);
        // Headers
        $headers = [
            "Content-Type: application/json",
            "Customer-Key: $customerKey",
            "Timestamp: $currentTimeInMillis",
            "Authorization: $hashValue"
        ];
        $fields = [
            'customerKey' => $customerKey,
            'sendEmail' => true,
            'email' => [
            'customerKey' => $customerKey,
            'fromEmail' => $fromEmail,
            'fromName' => 'miniOrange',
            'toEmail' => 'nutan.barad@xecurify.com',
            'bccEmail' => 'mandar.maske@xecurify.com',
            'subject' => 'Installation of Joomla OTP Verification Plugin [Free]',
            'content' => '<div>' . $content . '</div>',
            ],
        ];
        $field_string = json_encode($fields);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $errorMsg = 'SendMail CURL Error: ' . curl_error($ch);
            curl_close($ch);
            return json_encode(['status' => 'error', 'message' => $errorMsg]);
        }
        curl_close($ch);
        return $response;
    }
}