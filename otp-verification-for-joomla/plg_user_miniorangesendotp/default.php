<?php
/**
 * @package     Joomla.User
 * @subpackage  plg_user_miniorangesendotp
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

jimport('miniorangeotpplugin.utility.commonOtpUtilities');
$session = Factory::getSession();
$no_of_resend=is_null($session->get('reset-refresh'))?1:1+$session->get('reset-refresh');
$columnName = array('resend_otp_count');
$result = commonOtpUtilities::getCustomerDetails($columnName, '#__miniorange_otp_customer', 'loadObject', array('id' => 1,));
$resend_otp_count = $result->resend_otp_count;
$app = Factory::getApplication();
$input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
$get = ($input && $input->get) ? $input->get->getArray() : [];
$post = ($input && $input->post) ? $input->post->getArray() : [];


echo'<html>
        <head>
	    	<link rel="stylesheet" type="text/css" href="' . Uri::root() . 'plugins/user/miniorangesendotp/media/css/mo_customer_validation_style.css"/>			
		   		<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		</head>
		<body onLoad="noBack();" onpageshow="if (event.persisted) noBack();" onUnload="">
		<script>
            function noBack(){
                window.history.forward();
            }
			
			jQuery(document).load(function(){
				jQuery("#verification_resend_otp_form").reset();
			});
		</script>
			<div class="mo-modal-backdrop">
				<div class="mo_customer_validation-modal" tabindex="-1" role="dialog" id="mo_site_otp_form">
					<div class="mo_customer_validation-modal-backdrop"></div>
						<div class="mo_customer_validation-modal-dialog mo_customer_validation-modal-md">
							<div class="login mo_customer_validation-modal-content">
								<div class="mo_customer_validation-modal-header">
									<strong>Validate OTP (One Time Passcode)</strong>
									<a class="close" href="#" onclick="mo_validation_goback();" >' . '&larr; Go Back' . '</a>
								</div>
								<div class="mo_customer_validation-modal-body center">
					    			<div>' . $message . '</div><br /> ';
								
									if (!MoUtility::isBlank($user_email)) 
									{
										echo '<div class="mo_customer_validation-login-container">
												<form id="mo_validate_form" name="f" method="post" action="">
													<input type="hidden" name="option1" value="miniorange-validate-otp-form" />';
													echo HTMLHelper::_('form.token');
													echo '<input type="number" name="mo_customer_validation_otp_token" autofocus="true" placeholder="" id="mo_customer_validation_otp_token" required="true" class="mo_customer_validation-textbox" autofocus="true" pattern="[0-9]{4,8}" title="Only digits within range 4-8 are allowed."/>
															<br />
															<input type="submit" name="miniorange_otp_token_submit" id="miniorange_otp_token_submit" class="miniorange_otp_token_submit mo_otp_btns"  value="Validate OTP" />
															<input type="hidden" name="otp_type" value="' . $otp_type . '">';
                                        if ($resend_otp_count == "default" || $resend_otp_count == NULL){
                                            if (!$from_both)
                                            {
                                                echo '<input type="hidden" id="from_both" name="from_both" value="false" disabled="disabled" >
                                                                    <p class="mo_otp_content_right" id="resendOtpTimer"> Resend OTP in <span id="countdowntimer">10 </span> Seconds</p>
																	<a class="mo_otp_resend_otp miniorange_otp_token_submit mo_otp_btns" id="resendOtp" onclick="mo_otp_verification_resend();">Resend OTP</a>';
                                            }
                                            else
                                            {
                                                echo '	<input type="hidden" id="from_both" name="from_both" value="true">
																	<a class="mo_otp_content_right" onclick="mo_select_goback();">Resend OTP</a>';
                                            }
                                        }
                                        else if ($no_of_resend <= $resend_otp_count ) {
                                            if (!$from_both)
                                            {
                                                echo '<input type="hidden" id="from_both" name="from_both" value="false" disabled="disabled" >
                                                                    <p class="mo_otp_content_right" id="resendOtpTimer"> Resend OTP in <span id="countdowntimer">10 </span> Seconds</p>
																	<a class="mo_otp_resend_otp miniorange_otp_token_submit mo_otp_btns" id="resendOtp" onclick="mo_otp_verification_resend();">Resend OTP</a>';
                                            }
                                            else
                                            {
                                                echo '	<input type="hidden" id="from_both" name="from_both" value="true">
																	<a class="mo_otp_content_right"  onclick="mo_select_goback();">Resend OTP</a>';
                                            }
                                        }

                                        extra_post_data($postdata);
                                        echo '	</form>
												<div id="mo_message" hidden class="mo_otp_msg">' . $img .'</div>
															
											 </div>';
                                    }
echo '</div>
							</div>
						</div>
					</div>
				</div>
				<form name="f" method="post" action="" id="validation_goBack_form">
					<input id="validation_goBack" name="option1" value="validation_goBack" type="hidden">
					<input id="email" name="email" type="hidden" value='. $user_email .'>
					<input id="phone" name="phone" type="hidden" value='. $phone_number .'>';
echo HTMLHelper::_('form.token');
echo '	</form>			
				<form name="f" method="post" action="" id="verification_resend_otp_form">';
extra_post_data($postdata);

echo '
					<input type="hidden" id="form_resend_click" name="form_resend_click" value="'. $no_of_resend .'" autocomplete="off">
					<input id="verification_resend_otp" name="option1" value="verification_resend_otp_' . $otp_type . '" type="hidden">';
echo HTMLHelper::_('form.token');
if (!$from_both)
    echo '<input type="hidden" id="from_both" name="from_both" value="false">';
else
    echo '	<input type="hidden" id="from_both" name="from_both" value="true">';

echo '	</form>
				<form name="f" method="post" action="" id="goBack_choice_otp_form">
					<input id="verification_resend_otp" name="option1" value="verification_resend_otp_both" type="hidden"></input>
					<input type="hidden" id="from_both" name="from_both" value="true">';
extra_post_data($postdata);
echo '	</form>
				<style> .mo_customer_validation-modal{ display: block !important; } </style>
				<script>
				//Resend OTP timer
                    $("#resendOtp").delay(10000).show(0);
                    var timeleft = 10;
                    var downloadTimer = setInterval(function(){
                    timeleft--;
                    document.getElementById("countdowntimer").textContent = timeleft;
                    if(timeleft <= 0)
                        clearInterval(downloadTimer);
                    },1000);
                    
                    //Intially resendOtp is hidden, after 60 seconds it is displayed
                    $("#resendOtpTimer").delay(9999).hide(0);
				
					function mo_validation_goback(){
						document.getElementById("validation_goBack_form").submit();
					
					}
						
					function mo_otp_verification_resend(){
						document.getElementById("form_resend_click").value='.$no_of_resend.';
						document.getElementById("verification_resend_otp_form").submit();
						
					}
					function mo_select_goback(){
						document.getElementById("goBack_choice_otp_form").submit();
						
					}
					jQuery(document).ready(function() {
						$mo = jQuery;
					
						$mo("#mo_validate_form").submit(function(){
							$mo(this).hide();
							$mo("#mo_message").show();
						});
					});
				</script>
			</div>
		</body>
	</html>';