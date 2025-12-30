<?php

    /**
     * @package     Joomla.User
     * @subpackage  plg_user_miniorangesendotp
     *
     * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
     * @license     GNU General Public License version 2 or later; see LICENSE.txt
     */

defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\CMS\Factory;

jimport('miniorangeotpplugin.utility.commonOtpUtilities');

/**
 * This class handles all the phone related logic for OTP Verification
 * Process the phone number and starts the Phone verification process.
 */

class PhoneLogic extends LogicInterface
{
    /**
     * This function is called to handle Phone Verification request. Processes
     * the request and starts the OTP Verification process.
     *
     * @param $user_login 	- username of the user
     * @param $user_phone 	- phone of the user
     * @param $phone_number - phone number of the user
     * @param $otp_type 	- phone or sms verification
     * @param $from_both 	- has user enabled from both
     */
    public function _handle_logic($user_login,$user_email,$phone_number,$otp_type,$from_both,$resend=0)
    {

        $this->_start_otp_verification($user_login,$user_email,$phone_number,$otp_type,$from_both,$resend);
    }


    /**
     * This function is called to handle Phone Verification request. Processes
     * the request and starts the OTP Verification process to send OTP to user's
     * email address.
     *
     * @param $user_login 	- username of the user
     * @param $user_email 	- email of the user
     * @param $phone_number - phone number of the user
     * @param $otp_type 	- email or sms verification
     * @param $from_both 	- has user enabled from both
     */
    public function _start_otp_verification($user_login,$user_email,$phone_number,$otp_type,$from_both,$resend=0)
    {
        
        $session = Factory::getSession();
		$counter=$session->get('phone-test-refresh');
		$temp =$session->get('reset-refresh');
        if ($otp_type == 'otp_over_email_and_sms'){
            $session->set('otp_type', "otp_over_email_and_sms");
            $session->set('inPhone','yes');
        }
		$temp=($temp==NULL)?0:$temp;
		
		if($counter==NULL)
		{	
			$session->set('phone-test-refresh',1);
			$content =  MoOTPConstants::MO_TEST_MODE ? array('status'=>'SUCCESS','txId'=> MoUtility::rand())
			: json_decode(MocURLOTP::mo_send_otp_token('SMS',NULL,$phone_number), true);
            $session->set('transaction_id_email',$content['txId']);
		}
		else if($resend!=0&&($temp==NULL||$resend!=$temp))
		{
            
			$session->set('reset-refresh',$temp+1);
			$content =  MoOTPConstants::MO_TEST_MODE ? array('status'=>'SUCCESS','txId'=> MoUtility::rand())
			: json_decode(MocURLOTP::mo_send_otp_token('SMS',NULL,$phone_number), true);
            $session->set('transaction_id_email',$content['txId']);
		}
		else
		{	
			$content=array('status'=>'SUCCESS','txId'=> $session->get('transaction_id_email'));
            
		}

        $otp_status = $content['status'] ?? 'FAILED';
        $session->set('otp_status', $otp_status);
        switch ($content['status'])
        {
            case 'SUCCESS':
                $this->_handle_otp_sent($user_login,$user_email,$phone_number,$otp_type,$from_both,$content);
                break;

            default:
                $this->_handle_otp_sent_failed($user_login,$user_email,$phone_number,$otp_type,$from_both,$content);
                break;
        }
    }


    /**
     * This function is called to handle what needs to be done when OTP sending is successful.
     * Checks if the current form is an AJAX form and decides what message has to be
     * shown to the user.
     *
     * @param $user_login 	- username of the user
     * @param $user_email 	- email of the user
     * @param $phone_number - phone number of the user
     * @param $otp_type 	- email or sms verification
     * @param $from_both 	- has user enabled from both
     * @param $content 		- the json decoded response from server
     */
    public function _handle_otp_sent($user_login,$user_email,$phone_number,$otp_type,$from_both,$content)
    {
       
        MoUtility::checkSession();
        $session = Factory::getSession();
        $session->set('test', $content['txId']);
        $message = str_replace("##phone##",$phone_number,$this->_get_otp_sent_message()); 
        miniorange_site_otp_validation_form($user_login, $user_email,$phone_number,$message,$otp_type,$from_both);
        
    }

    /**
     * This function is called to handle what needs to be done when OTP sending fails.
     * Checks if the current form is an AJAX form and decides what message has to be
     * shown to the user.
     *
     * @param $user_login 	- username of the user
     * @param $user_email 	- email of the user
     * @param $phone_number - phone number of the user
     * @param $otp_type 	- email or sms verification
     * @param $from_both 	- has user enabled from both
     * @param $content 		- the json decoded response from server
     */
    public function _handle_otp_sent_failed($user_login,$user_email,$phone_number,$otp_type,$from_both,$content)
    {
        $message = str_replace("##phone##",$phone_number,$this->_get_otp_sent_failed_message());
        miniorange_site_otp_validation_form(null,null,null,$message,$otp_type,$from_both);
    }


    /**
     * Get the success message to be shown to the user when OTP was sent
     * sucessfully. If admin has set his own unique message then
     * show that to the user instead of the default one.
     */
    public function _get_otp_sent_message()
    {
        $result = commonOtpUtilities::_get_custom_message();
        $custom_success_phone_message = isset($result['mo_custom_phone_success_message']) ? $result['mo_custom_phone_success_message'] : '';
        if(!empty($custom_success_phone_message)){
            return $custom_success_phone_message;
        }
        else{
            return MoMessages::showMessage('OTP_SENT_PHONE');
        }
    }


    /**
     * Get the error message to be shown to the user when there was an
     * error sending OTP. If admin has set his own unique message then
     * show that to the user instead of the default one.
     */
    public function _get_otp_sent_failed_message()
    {
        $result = commonOtpUtilities::_get_custom_message();
        $custom_failed_phone_message = isset($result['mo_custom_phone_error_message']) ? $result['mo_custom_phone_error_message'] : '';
        if(!empty($custom_failed_phone_message)){
            return $custom_failed_phone_message;
        }else{
            return MoMessages::showMessage('ERROR_OTP_PHONE');
        }
    }


    /**
     * Function decides what message needs to be shown to the user when he enteres a
     * blocked email domain. It checks if the admin has set any message in the
     * plugin settings and returns that instead of the default one.
     */
    public function _get_is_blocked_message()
    {
        return MoMessages::showMessage('ERROR_EMAIL_BLOCKED');
    }


    /**
     * Get OTP Invalid email format. This is not required in context
     * to the email address and email verification. Can be extended
     * and used in the future.
     */
    public function _get_otp_invalid_format_message() { return; }


    /**
     * Function should handle what needs to be done if email/phone number
     * don't match the required format match the required format. This is not
     * required in context to the email address and email verification.
     * Can be extended and used in the future.
     */
    public function _handle_not_matched($phone_number,$otp_type,$from_both){ return; }


    /**
     * Function should handle what needs to be done if email/phone number
     * does match the required format. This is not required in context to
     * the email address and email verification. Can be extended and used in the future.
     */
    public function _handle_matched($user_login,$user_email,$phone_number,$otp_type,$from_both){ return; }
}
global $phoneLogic;
$phoneLogic = new PhoneLogic();