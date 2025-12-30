<?php

/**
 * @package     Joomla.System
 * @subpackage  plg_system_miniorangeverifyotp
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;

jimport('joomla.plugin.plugin');
jimport('miniorangesendotp.miniorange_form_handler');
jimport('miniorangeotpplugin.utility.commonOtpUtilities');

/**
 * miniOrange OTP System plugin
 */
class plgSystemMiniorangeverifyotp extends CMSPlugin 
{
    public function onAfterRender()
    {
        $app = Factory::getApplication();
        $requested_uri = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        if( strpos($requested_uri, 'component/users/registration?Itemid=101') ) {
            $foobar = '<script>
                window.addEventListener("DOMContentLoaded", function (){
                    window.history.forward();
                });
				</script>';
            $body = $app->getBody();
            $body = str_replace('</body>', $foobar . '</body>', $body);
            $app->setBody($body);
        }
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function onAfterInitialise()
    {
        $app = Factory::getApplication();
        $input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
        $post = ($input && $input->post) ? $input->post->getArray() : [];
        $get = ($input && $input->get) ? $input->get->getArray() : [];
        $result = commonOtpUtilities::__getDBValuesWOArray('#__miniorange_otp_customer');
        $customer_registered = commonOtpUtilities::is_customer_registered();

        if (isset($post['mojsp_feedback'])) {
            commonOtpUtilities::_get_feedback_form($post);
        } 
        elseif ($customer_registered) {
            require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'user' . DIRECTORY_SEPARATOR . 'miniorangesendotp' . DIRECTORY_SEPARATOR . 'miniorange_form_handler.php';
            require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'user' . DIRECTORY_SEPARATOR . 'miniorangesendotp' . DIRECTORY_SEPARATOR . 'moutility.php';
            require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'user' . DIRECTORY_SEPARATOR . 'miniorangesendotp' . DIRECTORY_SEPARATOR . 'curl.php';
            require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'user' . DIRECTORY_SEPARATOR . 'miniorangesendotp' . DIRECTORY_SEPARATOR . 'constants.php';
        
            
            if (isset($post) && !empty($post)) {
                //Check for Blocked Email domains and Country codes.
                commonOtpUtilities::user_email_phone_check($post, $get);

                if (!isset($post['option1'])) {
                    $post['option1'] = 'First_time_allowed';
                }
                if (!isset($post['task'])) {
                    $post['task'] = 'allowed_first_time';
                }

                $requested_uri = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

                $errors = NULL;
                $extra_data = NULL;
                $registration_otp_type = isset($result['registration_otp_type']) ? $result['registration_otp_type'] : '';
                $form_resend_click     = isset($post['form_resend_click'])?$post['form_resend_click']:0;

                //Convert form Registration
                if( isset($get['task']) && $get['task'] == 'submit' && isset($post['cf']['form_id']) && $post['cf']['form_id'] == 1 && $post['option1'] != 'miniorange-validate-otp-form')
                {
                    $username     = isset($post['cf']['username']) ? $post['cf']['username'] : '';
                    $email        = isset($post['cf']['email']) ? $post['cf']['email'] : '';
                    $phone_number = isset($post['cf']['phone']) ? $post['cf']['phone'] : '';
                    $password     = isset($post['cf']['password']) ? $post['cf']['password'] : '';
                }
                //Chrono Form Registration
                else if ( isset($post['__cf_token']) && $post['option1'] != 'miniorange-validate-otp-form')
                {
                    $username     = isset($post['username']) ? $post['username'] : '';
                    $email        = isset($post['email']) ? $post['email'] : '';
                    $phone_number = isset($post['phone']) ? $post['phone'] : '';
                    $password     = isset($post['password']) ? $post['password'] : '';
                }
                //Joomla default Registration Form
                else if (($post['task'] == 'registration.register') && ($post['option1'] != 'miniorange-validate-otp-form'))
                {
                    $username     = isset($post['jform']['username']) ? $post['jform']['username'] : '';
                    $email        = isset($post['jform']['email1']) ? $post['jform']['email1'] : '';
                    $phone_number = isset($post['jform']['profile']['phone']) ? $post['jform']['profile']['phone'] : '';
                    $password     = isset($post['jform']['password1']) ? $post['jform']['password1'] : '';
                }
                //VirtueMart Registration Form
                else if (($post['task'] =='saveUser') && ($post['option'] == 'com_virtuemart')&& ($post['option1'] != 'miniorange-validate-otp-form'))
                {
                    $username     = isset($post['username']) ? $post['username'] : '';
                    $email        = isset($post['email']) ? $post['email'] : '';
                    $phone_number = isset($post['phone_2']) ? $post['phone_2'] : '';
                    $password     = isset($post['password']) ? $post['password'] : '';
                }
                //RS Registration Form
                else if(isset($post['form']))
                {
                     $rs_form_configuration=isset($result['rs_form_field_configuration'])?json_decode($result['rs_form_field_configuration']):array();
                     foreach($rs_form_configuration as $key=> $value)
                     {
                        if(($post['form']['formId']==$key )&& ($post['option1'] != 'miniorange-validate-otp-form'))
                        {
                            $username     = isset($post['form'][$value[0]]) ? $post['form'][$value[0]] : '';
                            $email        = isset($post['form'][$value[0]]) ? $post['form'][$value[0]] : '';
                            $phone_number = isset($post['form'][$value[1]]) ? $post['form'][$value[1]] : '';
                            $password     = isset($post['form'][$value[2]]) ? $post['form'][$value[2]] : '';
                        }
                     }
                }
                //Community Builder Registration Form
                else if (isset($post['gid']) && isset($post['emailpass']) && strpos($requested_uri, 'cb-profile') && $post['option1'] != 'miniorange-validate-otp-form')
                {
                    $username     = isset($post['username']) ? $post['username'] : '';
                    $email        = isset($post['email']) ? $post['email'] : '';
                    $phone_number = isset($post['cb_mobile']) ? $post['cb_mobile'] : '';
                    $password     = isset($post['password']) ? $post['password'] : '';
                }

                $username_exists  = isset($username) ? commonOtpUtilities::get_userid_from_username($username) : 0;
                $email_exists     = isset($email) ? commonOtpUtilities::get_userid_from_email($email) : 0;
                if ( isset($username) && isset($email) && empty($username_exists) && empty($email_exists))
                    PlgUserMiniorangesendotp::startVerificationProcess($registration_otp_type, $username, $email, $errors, $phone_number, $password, $extra_data, $form_resend_click);
            }
       
            miniorange_customer_validation_handle_form();
        }
    }

    function onExtensionBeforeUninstall($id)
    {
        //Get Extension id of Component from extensions table.
        $result = commonOtpUtilities::get_com_extension_id();

        $tables = Factory::getDbo()->getTableList();
        $tab = 0;
        foreach ($tables as $table) {
            if (strpos($table, "miniorange_otp_customer"))
                $tab = $table;
        }

        if ($tab) {
            $current_user = Factory::getUser();

            $db = Factory::getDbo();
            $query = $db->getQuery(true);
            $query->select('uninstall_feedback') . "," . 'email';
            $query->from('#__miniorange_otp_customer');
            $query->where($db->quoteName('id') . " = " . $db->quote(1));
            $db->setQuery($query);
            $fid = $db->loadColumn();

            $admin_email = isset($current_user->email) ? $current_user->email : '';

            $app = Factory::getApplication();
        $input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
        $post = ($input && $input->post) ? $input->post->getArray() : [];
            $tpostData = $post;

            foreach ($fid as $value) {
                if ($value == 0) {
                    foreach ($result as $results) {
                        if ($results == $id) {
                            ?>
                            <div class="form-style-6">
                                <h1>Feedback for Joomla OTP</h1>
                                <h3>Email </h3>
                                <form name="f" method="post" action="" id="mojsp_feedback">
                                    <input type="hidden" name="mojsp_feedback" value="mojsp_feedback"/>
                                    <div>
                                        <input type="email" id="query_email" name="query_email" style="width: 100%" value="<?php echo $admin_email; ?>"
                                               placeholder="Enter your email" required/>
                                        <h3>What Happened?</h3>
                                        <p style="margin-left:2%">
                                            <?php
                                            $deactivate_reasons = array(
                                                "Facing issues During Registration",
                                                "Not receiving OTP during Registration",
                                                "Does not have the features I'm looking for",
                                                "Not able to Configure",
                                                "Bugs in the plugin",
                                                "Other Reasons:"
                                            );
                                            foreach ($deactivate_reasons as $deactivate_reasons) { ?>
                                        <div class="radio" style="padding: 1px;margin-left: 2%">
                                            <label style="font-weight: normal;font-size: 14.6px;" for="<?php echo $deactivate_reasons; ?>">
                                                <input type="radio" name="deactivate_plugin" id="deactivate_plg_id" value="<?php echo $deactivate_reasons; ?>" required>
                                                <?php echo $deactivate_reasons; ?>
                                            </label>
                                        </div>
                                        <?php } ?>
                                        <br>
                                        <textarea id="query_feedback" name="query_feedback" rows="4" style="width:100%;resize: vertical !important;"
                                                  cols="50" placeholder="Write your query here"></textarea>

                                        <?php
                                        if (isset($tpostData['cid'])){
                                        foreach ($tpostData['cid'] as $key) { ?>
                                            <input type="hidden" name="result[]" value=<?php echo $key ?>>

                                        <?php }
                                        }
                                        ?>

                                        <br><br>
                                        <div class="mojsp_modal-footer">
                                            <input type="submit" name="miniorange_feedback_submit" class="button button-primary button-large" value="Submit"/>
                                        </div><br>
                                        <div>
                                            <input type="submit" id="skip" name="skip_feedback" class="button" onclick="skip_feedback_form();" value="Skip Feedback"/>
                                        </div>
                                    </div>
                                </form>
                                <form name="f" method="post" action="" id="mojsp_feedback_form_close">
                                    <input type="hidden" name="option" value="mojsp_skip_feedback"/>
                                </form>
                            </div>
                            <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
                            <script>
                                function skip_feedback_form(){
                                    var radios = document.querySelectorAll("[id^='deactivate_plg_id']");
                                    for (var i = 0; i <6 ; i++) {
                                        radios[i].disabled = true;
                                    }
                                }

                                jQuery('input:radio[name="deactivate_plugin"]').click(function () {
                                    var reason = jQuery(this).val();
                                    jQuery('#query_feedback').removeAttr('required')
                                    if (reason === 'Facing issues During Registration') {
                                        jQuery('#query_feedback').attr("placeholder", "Can you please describe the issue in detail?");
                                    } else if (reason === "Not receving OTP during Registration") {
                                        jQuery('#query_feedback').attr("placeholder", "Could you please describe in detail");
                                    } else if (reason === "Does not have the features I'm looking for") {
                                        jQuery('#query_feedback').attr("placeholder", "Let us know what feature are you looking for");
                                    } else if (reason === "Bugs in the plugin") {
                                        jQuery('#query_feedback').attr("placeholder", "Could you please describe the bug in detail");
                                    } else if (reason === "Other Reasons:") {
                                        jQuery('#query_feedback').attr("placeholder", "Can you let us know the reason for deactivation");
                                        jQuery('#query_feedback').prop('required', true);
                                    } else if (reason === "Not able to Configure") {
                                        jQuery('#query_feedback').attr("placeholder", "Not able to Configure? let us know so that we can improve the interface");
                                    }
                                });
                            </script>
                            <style type="text/css">
                                .form-style-6 {
                                    font: 95% Arial, Helvetica, sans-serif;
                                    max-width: 400px;
                                    margin: 10px auto;
                                    padding: 16px;
                                    background: #F7F7F7;
                                }

                                .form-style-6 h1 {
                                    background: #43D1AF;
                                    padding: 20px 0;
                                    font-size: 140%;
                                    font-weight: 300;
                                    text-align: center;
                                    color: #fff;
                                    margin: -16px -16px 16px -16px;
                                }

                                .form-style-6 input[type="text"],
                                .form-style-6 input[type="date"],
                                .form-style-6 input[type="datetime"],
                                .form-style-6 input[type="email"],
                                .form-style-6 input[type="number"],
                                .form-style-6 input[type="search"],
                                .form-style-6 input[type="time"],
                                .form-style-6 input[type="url"],
                                .form-style-6 textarea,
                                .form-style-6 select {
                                    -webkit-transition: all 0.30s ease-in-out;
                                    -moz-transition: all 0.30s ease-in-out;
                                    -ms-transition: all 0.30s ease-in-out;
                                    -o-transition: all 0.30s ease-in-out;
                                    outline: none;
                                    box-sizing: border-box;
                                    -webkit-box-sizing: border-box;
                                    -moz-box-sizing: border-box;
                                    width: 100%;
                                    background: #fff;
                                    margin-bottom: 4%;
                                    border: 1px solid #ccc;
                                    padding: 3%;
                                    color: #555;
                                    font: 95% Arial, Helvetica, sans-serif;
                                }

                                .form-style-6 input[type="text"]:focus,
                                .form-style-6 input[type="date"]:focus,
                                .form-style-6 input[type="datetime"]:focus,
                                .form-style-6 input[type="email"]:focus,
                                .form-style-6 input[type="number"]:focus,
                                .form-style-6 input[type="search"]:focus,
                                .form-style-6 input[type="time"]:focus,
                                .form-style-6 input[type="url"]:focus,
                                .form-style-6 textarea:focus,
                                .form-style-6 select:focus {
                                    box-shadow: 0 0 5px #43D1AF;
                                    padding: 3%;
                                    border: 1px solid #43D1AF;
                                }

                                .form-style-6 input[type="submit"],
                                .form-style-6 input[type="button"] {
                                    box-sizing: border-box;
                                    -webkit-box-sizing: border-box;
                                    -moz-box-sizing: border-box;
                                    width: 100%;
                                    padding: 3%;
                                    background: #43D1AF;
                                    border-bottom: 2px solid #30C29E;
                                    border-top-style: none;
                                    border-right-style: none;
                                    border-left-style: none;
                                    color: #fff;
                                }

                                .form-style-6 input[type="submit"]:hover,
                                .form-style-6 input[type="button"]:hover {
                                    background: #2EBC99;
                                }
                            </style>
                            <?php
                            exit;
                        }
                    }
                }
            }
        }
    }
}