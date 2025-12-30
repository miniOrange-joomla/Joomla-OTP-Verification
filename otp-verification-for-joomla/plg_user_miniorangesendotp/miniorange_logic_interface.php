<?php
/**
 * @package     Joomla.User
 * @subpackage  plg_user_miniorangesendotp
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
	/**
	 * Interface class that's extended by the email nd phone logic classes.
	 * It defines some of the common actions and functions for each of those 
	 * classes. 
	 */
	abstract class LogicInterface
	{
		// Some abstract functions that needs to implemented by each logic class
		abstract public function _handle_logic($user_login,$user_email,$phone_number,$otp_type,$from_both);
		
		abstract public function _handle_otp_sent($user_login,$user_email,$phone_number,$otp_type,$from_both,$content);
		abstract public function _handle_otp_sent_failed($user_login,$user_email,$phone_number,$otp_type,$from_both,$content);
		abstract public function _get_otp_sent_message();
		abstract public function _get_otp_sent_failed_message();
		abstract public function _get_otp_invalid_format_message();
		abstract public function _get_is_blocked_message();
		abstract public function _handle_matched($user_login,$user_email,$phone_number,$otp_type,$from_both);
		abstract public function _handle_not_matched($phone_number,$otp_type,$from_both);
		abstract public function _start_otp_verification($user_login,$user_email,$phone_number,$otp_type,$from_both);

		/**
		 * Static function to detect if the current form being submitted for which 
		 * OTP Verification has started is an AJAX form.
		 */
		public static function _is_ajax_form()
		{
			//return (Bool) apply_filters('is_ajax_form',FALSE);
		}
	}