<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_joomlaotp
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
/**
 * AccountSetup Controller
 *
 * @package     Joomla.Administrator
 * @subpackage  com_joomlaotp
 * @since       0.0.9
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\Input\Input;

$document = Factory::getApplication()->getDocument();


$document->addStyleSheet(Uri::base() . 'components/com_joomlaotp/assets/css/miniorange_otp.css');

jimport('miniorangeotpplugin.utility.commonOtpUtilities');

class JoomlaOtpControllerAccountSetup extends FormController 
{
    function __construct()
    {
        $this->view_list = 'accountsetup';
        parent::__construct();
    }

    function saveOTP()
    {
        $app = Factory::getApplication();
        $input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
        $post = ($input && $input->post) ? $input->post->getArray() : [];
        $otp_method = $post['login_otp_type'] ?? '';
        $enable_otp = $post['otp_during_registration'] ?? '';
        if (isset($enable_otp) && !empty($enable_otp) && $otp_method  == null) {
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=setting', Text::_('COM_MINIORANGE_ACCOUNT_SELECT_VERIFICATION_METHOD'), 'warning');
            return;
        }

        $columnName = array('registration_status');
        $result     = commonOtpUtilities::getCustomerDetails($columnName, '#__miniorange_otp_customer', 'loadObject', array('id' => 1,));
        $reg_status = isset($result->registration_status) ? $result->registration_status : 'FALSE';

        if ($reg_status == 'SUCCESS') {

        $tab = $post['login_otp_type'];
        $tab2 = $post['otp_during_registration'];
        $resend = $post['resend_count'];

        if (!isset($tab2)) $tab = 0;

        if (!isset($tab3)) $tab1 = 0;

        $tab_va = isset($tab) ? $tab : 0;
        $tab_val = isset($tab2) ? $tab2 : 0;

        $db_table = '#__miniorange_otp_customer';
        $db_coloums = array(
            'registration_otp_type' => $tab_va,
            'enable_during_registration' => $tab_val,
            'resend_otp_count' => $resend,
        );

        commonOtpUtilities::__genDBUpdate($db_table, $db_coloums);
        $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=setting', Text::_('COM_MINIORANGE_CONFIGURATION_SAVES_SUCCESSFULLY'));
        }
        else
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=setting', Text::_('COM_MINIORANGE_REGISTER_OR_LOGIN_ERROR'), 'error');
    }

    function joomlapagination()
    {
        $total_entries = commonOtpUtilities::_get_all_otp_transaction_count();
        $app = Factory::getApplication();
        $input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
        $post = ($input && $input->post) ? $input->post->getArray() : [];
        $is_customer_registered = commonOtpUtilities::is_customer_registered();
        $start      = $post['page'] ?? '';
        $order      = $post['orderBY'] ?? 'down';
        $no_of_entry= $post['no_of_entry'] ?? '10';
        $start_val  = $start + 1;
        $first_val  = ($start_val - 1) * $no_of_entry;
        $first_val  = ($total_entries == 0) ? -1 : $first_val;
        $last_val   = $no_of_entry + $first_val;
        if($last_val > $total_entries)
        {
            $last_val = $total_entries;
        }
        $low_id    = $start * $no_of_entry;
        $first_val = $first_val + 1;

        if($last_val == $total_entries)
        {
            echo '<script>
                document.getElementById("next_btn").style.display = "none";              
            </script>';
        }

        $verification_method = Text::_('COM_MINIORANGE_OTP_COL_VERIFICATION_METHOD1');
        $user_email          = Text::_('COM_MINIORANGE_OTP_COL_USER_EMAIL');
        $user_phone          = Text::_('COM_MINIORANGE_OTP_COL_USER_PHONE');
        $otp_sent            = Text::_('COM_MINIORANGE_OTP_COL_OTP_SENT');
        $otp_verified        = Text::_('COM_MINIORANGE_OTP_COL_OTP_VERIFIED');
        $timestamp           = Text::_('COM_MINIORANGE_OTP_COL_TIMESTAMP');

        $list_of_otp_trnas = commonOtpUtilities::_get_otp_transaction_report($no_of_entry, $low_id,$order);
        $result = '';
        $result .= '<div class="table-responsive" style="font-family: sans-serif;font-size: 12px;" id="mo_otp_transaction_table">
            <table id="myTable" class="mo_otp_trans_table" >
            <thead>
                <tr class="header mo_boot_text-white mo_otp_report_head" style="line-height: 17px;background-color: #001b4c;">
                    <th class="mo_boot_text-center">'.$verification_method.'</th>
                    <th class="mo_boot_text-center">'.$user_email.'</th>
                    <th class="mo_boot_text-center">'.$user_phone.'</th>
                    <th class="mo_boot_text-center">'.$otp_sent.'</th>
                    <th class="mo_boot_text-center">'.$otp_verified.'</th>
                    <th class="mo_boot_text-center">'.$timestamp.'&nbsp;<span class="fa fa-sort" style="cursor: pointer;" onclick=sort("on",true)><input type="hidden" value="1" id="hidden_input"></span></th>
                </tr>
            </thead>
                <tbody style="font-size: 12px;color:black;">
                <tr style="line-height: 25px;">';


        foreach ($list_of_otp_trnas as $list2)
        {
            foreach ($list2 as $list)
            {
                if (empty($list['user_phone']))
                    $list['user_phone'] ='-';

                if ($is_customer_registered) {
                    $result .= '<tr style="line-height: 14px;">
                                    <td class="mo_boot_text-center  " >' . $list['verification_method'] . '</td>
                                    <td class="mo_boot_text-center " >' . $list['user_email'] . '</td>
                                    <td class="mo_boot_text-center " >' . $list['user_phone'] . '</td>
                                    <td class="mo_boot_text-center " >' . $list['otp_sent'] . '</td>
                                    <td class="mo_boot_text-center " >' . $list['otp_verified'] . '</td>
                                    <td class="mo_boot_text-center " >' . date("M j, Y, g:i:s a", $list['timestamp']) . '</td>
                                </tr>';
                }
                else {
                    //removing the entries from database if customer is not logged in.
                    $db = Factory::getDbo();
                    $db->truncateTable('#__miniorange_otp_transactions_report');
                    $first_val = 0;
                    $last_val = 0;
                    $total_entries = 0;
                }
            }
        }
        $result .= '</tr>
                            </tbody>
                        </table>
                    </div><br>
                    <div>'.Text::sprintf('COM_SHOWING_NO_OF_ENTRIES', $first_val, $last_val, $total_entries).'</div>';
        echo $result;
        exit;
    }

    function otp_reports(){
        $app = Factory::getApplication();
        $input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
        $post = ($input && $input->post) ? $input->post->getArray() : [];
        $login_report = commonOtpUtilities::_get_all_otp_transaction_count();
        $login_report_count = commonOtpUtilities::_get_otp_transaction_reports_val();

        $refresh = $post['refresh_page'] ?? '';
        if ($refresh == Text::_('COM_MINIORANGE_REFRESH_BUTTON'))
        {
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=otp_report',Text::_('COM_MINIORANGE_REFRESH_MSG'));
            return;
        }

        $download = $post['download_reports'] ?? '';
        if ($download == Text::_('COM_MINIORANGE_DOWNLOAD_BUTTON') && $login_report_count['verification_method'] != '' && $login_report != 0) {
            commonOtpUtilities::_download_reports();
        }
        else if ($download == Text::_('COM_MINIORANGE_DOWNLOAD_BUTTON')){
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=otp_report', Text::_('COM_MINIORANGE_DOWNLOAD_ERROR_MSG'),'error');
            return;
        }

        $clear_reports = $post['clear_val'] ?? '';
        if ($login_report_count['verification_method'] != '' && $login_report != 0) {
            if ($clear_reports == Text::_('COM_MINIORANGE_CLEAR_BUTTON')) {
                $db = Factory::getDbo();
                $db->truncateTable('#__miniorange_otp_transactions_report');
                $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=otp_report', Text::_('COM_MINIORANGE_CLEAR_SUCCESS_MSG'),'success');
                return;
            } else {
                $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=otp_report', Text::_('COM_MINIORANGE_CLEAR_ERROR_MSG'), 'warning');
                return;
            }
        }
        else {
            if ($clear_reports == Text::_('COM_MINIORANGE_CLEAR_BUTTON')){
                $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=otp_report', Text::_('COM_MINIORANGE_CLEAR_WARNING_MSG'), 'error');
                return;
            }
        }
    }

    function saveDomainBlocks()
    {
        $columnName = array('registration_status');
        $result     = commonOtpUtilities::getCustomerDetails($columnName, '#__miniorange_otp_customer', 'loadObject', array('id' => 1,));
        $reg_status = isset($result->registration_status) ? $result->registration_status : 'FALSE';

        if ($reg_status == 'SUCCESS') {

        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $app = Factory::getApplication();
        $input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
        $post = ($input && $input->post) ? $input->post->getArray() : [];
        $allow_domains = isset($post['mo_otp_allowed_email_domains']) ? $post['mo_otp_allowed_email_domains'] : 0;
        $white_or_black = isset($post['white_or_black']) ? $post['white_or_black'] : 0;
        $reg_restriction = isset($post['reg_restriction']) ? $post['reg_restriction'] : 0;
        $allow_domains = preg_replace('!\s+!', ';', $allow_domains);

        $db_table = '#__miniorange_otp_customer';
        if ($reg_restriction == 1) {
            $db_coloums = array(
                'reg_restriction' => $reg_restriction,
                'white_or_black' => $white_or_black,
                'mo_otp_allowed_email_domains' => $allow_domains,
            );
        } else {
            $db_coloums = array(
                'reg_restriction' => 0,
                'white_or_black' => 0,
                'mo_otp_allowed_email_domains' => '',
            );
        }
        commonOtpUtilities::__genDBUpdate($db_table, $db_coloums);
        $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=setting', Text::_('COM_MINIORANGE_CONFIGURATION_SAVES_SUCCESSFULLY'));
        }
        else
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=setting', Text::_('COM_MINIORANGE_REGISTER_OR_LOGIN_ERROR'),'error');
    }

    function customerLoginForm()
    {
        $db_table = '#__miniorange_otp_customer';

        $db_coloums = array(
            'login_status' => true,
            'password' => '',
            'email_count' => 0,
            'sms_count' => 0,
        );

        commonOtpUtilities::__genDBUpdate($db_table, $db_coloums);
        $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup');
    }

    function verifyCustomer()
    {
        $app = Factory::getApplication();
        $input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
        $post = ($input && $input->post) ? $input->post->getArray() : [];

        $email = '';
        $password = '';

        if (empty($post['email']) || empty($post['password'])) {
            Factory::getApplication()->enqueueMessage(4711, Text::_('COM_MINIORANGE_ALL_FIELDS_REQUIRED'));
            return;
        } else {
            $email = $post['email'];
            $password = $post['password'];
        }

        $customer = new MoOtpCustomer();
        $content = $customer->get_customer_key($email, $password);

        $customerKey = json_decode($content, true);
        if (strcasecmp($customerKey['apiKey'], 'CURL_ERROR') == 0) {
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup', $customerKey['token'], 'error');
        } else if (json_last_error() == JSON_ERROR_NONE) {
            if (isset($customerKey['id']) && isset($customerKey['apiKey']) && !empty($customerKey['id']) && !empty($customerKey['apiKey'])) {
                $this->save_customer_configurations($email, $customerKey['id'], $customerKey['apiKey'], $customerKey['token'], $customerKey['phone']);
                $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup', Text::_('COM_MINIORANGE_ACCOUNT_LOGIN_SUCCESS_MESSAGE'));
            } else {
                $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup', Text::_('COM_MINIORANGE_ERROR_FETCHING_DETAILS'), 'error');
            }
        } else {
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup', Text::_('COM_MINIORANGE_INVALID_USERNAME_OR_PASSWORD'), 'error');
        }
    }

    function saveCustomMessage()
    {
        $columnName = array('registration_status');
        $result     = commonOtpUtilities::getCustomerDetails($columnName,'#__miniorange_otp_customer', 'loadObject', array('id' => 1,));
        $reg_status = isset($result->registration_status) ? $result->registration_status : 'FALSE';

        if ($reg_status == 'SUCCESS') {
        $app = Factory::getApplication();
        $input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
        $post = ($input && $input->post) ? $input->post->getArray() : [];
        commonOtpUtilities::_save_custom_message($post);
        $this->setRedirect('index.php?option=com_joomlaotp&tab-panel=custom_message', Text::_('COM_MINIORANGE_SETTINGS_SAVES_SUCCESSFULLY'));
        }
        else
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=custom_message', Text::_('COM_MINIORANGE_REGISTER_OR_LOGIN_ERROR'), 'error');
    }

    function saveCustomPhoneMessage()
    {
        $columnName = array('registration_status');
        $result     = commonOtpUtilities::getCustomerDetails($columnName,'#__miniorange_otp_customer', 'loadObject', array('id' => 1,));
        $reg_status = isset($result->registration_status) ? $result->registration_status : 'FALSE';

        if ($reg_status == 'SUCCESS') {

        $app = Factory::getApplication();
        $input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
        $post = ($input && $input->post) ? $input->post->getArray() : [];
        commonOtpUtilities::_save_custom_phone_message($post);
        $this->setRedirect('index.php?option=com_joomlaotp&tab-panel=custom_message', Text::_('COM_MINIORANGE_SETTINGS_SAVES_SUCCESSFULLY'));
        }
        else
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=custom_message', Text::_('COM_MINIORANGE_REGISTER_OR_LOGIN_ERROR'), 'error');
    }

    function saveComOTPMessages()
    {
        $columnName = array('registration_status');
        $result     = commonOtpUtilities::getCustomerDetails($columnName,'#__miniorange_otp_customer', 'loadObject', array('id' => 1,));
        $reg_status = isset($result->registration_status) ? $result->registration_status : 'FALSE';

        if ($reg_status == 'SUCCESS') {

        $app = Factory::getApplication();
        $input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
        $post = ($input && $input->post) ? $input->post->getArray() : [];
        commonOtpUtilities::_save_com_message($post);
        $this->setRedirect('index.php?option=com_joomlaotp&tab-panel=custom_message', Text::_('COM_MINIORANGE_SETTINGS_SAVES_SUCCESSFULLY'));
        }
        else
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=custom_message', Text::_('COM_MINIORANGE_REGISTER_OR_LOGIN_ERROR'), 'error');
    }

    function block_country_codes()
    {
        $columnName = array('registration_status');
        $result     = commonOtpUtilities::getCustomerDetails($columnName,'#__miniorange_otp_customer', 'loadObject', array('id' => 1,));
        $reg_status = isset($result->registration_status) ? $result->registration_status : 'FALSE';

        if ($reg_status == 'SUCCESS') {

        $app = Factory::getApplication();
        $input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
        $post = ($input && $input->post) ? $input->post->getArray() : [];
        $check = commonOtpUtilities::_is_default_selected($post);

        if ($check) {
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=setting', Text::_('COM_MINIORANGE_COUNTRY_CODE_BLOCKING'), 'warning');
            return;
        }

        commonOtpUtilities::_block_country_code($post);
        $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=setting', Text::_('COM_MINIORANGE_SETTINGS_SAVES_SUCCESSFULLY'));
        }
        else
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=setting', Text::_('COM_MINIORANGE_REGISTER_OR_LOGIN_ERROR'), 'error');
    }

    function saveCustomSettings()
    {
        $columnName = array('registration_status');
        $result     = commonOtpUtilities::getCustomerDetails($columnName,'#__miniorange_otp_customer', 'loadObject', array('id' => 1,));
        $reg_status = isset($result->registration_status) ? $result->registration_status : 'FALSE';

        if ($reg_status == 'SUCCESS') {

        $app = Factory::getApplication();
        $input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
        $post = ($input && $input->post) ? $input->post->getArray() : [];
        $default_co_values = isset($post['default_country_code']) ? trim($post['default_country_code']) : '';
        
        if (empty($default_co_values) || $default_co_values == Text::_('COM_MINIORANGE_SELECT_COUNTRY_CODE_PLACEHOLDER')) {
            $default_co_code = 0; 
            $default_co_name = 'Not Selected'; 
        } else {
            $country_val = explode(',', $default_co_values);
            $default_co_code = isset($country_val[0]) && is_numeric(trim($country_val[0])) ? (int) trim($country_val[0]) : 0;
            $default_co_name = isset($country_val[1]) ? trim($country_val[1]) : 'Not Selected';
        }
            
        $is_blocked = false;
        if (!empty($default_co_code) && $default_co_code != '') {
            $is_blocked = commonOtpUtilities::_is_country_code_blocked($default_co_code);
        }
      
        if ($is_blocked) {
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=setting', Text::_('COM_MINIORANGE_ALREADY_BLOCKED_COUNTRY_CODE'), 'warning');
            return;
        }

        $db_table = '#__miniorange_otp_customer';

        $db_coloums = array(
            'mo_default_country_code' => $default_co_code,
            'mo_default_country' => $default_co_name,
        );

        commonOtpUtilities::__genDBUpdate($db_table, $db_coloums);
        $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=setting', Text::_('COM_MINIORANGE_SETTINGS_SAVES_SUCCESSFULLY'));
        }
        else
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=setting', Text::_('COM_MINIORANGE_REGISTER_OR_LOGIN_ERROR'), 'error');
    }

    function removeaccount()
    {
        $columnName = array('registration_status');
        $result     = commonOtpUtilities::getCustomerDetails($columnName,'#__miniorange_otp_customer', 'loadObject', array('id' => 1,));
        $status     = $result->registration_status;
        if ($status == 'SUCCESS') {

            $db_table = '#__miniorange_otp_customer';
            $db_coloums = array(
                'email' => '',
                'customer_key' => '',
                'api_key' => '',
                'customer_token' => '',
                'admin_phone' => '',
                'login_status' => 0,
                'registration_status' => 'FALSE',
                'password' => '',
                'email_count' => 0,
                'sms_count' => 0,
            );

            commonOtpUtilities::__genDBUpdate($db_table, $db_coloums);
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab=account', Text::_('COM_MINIORANGE_ACCOUNT_REMOVED_SUCCESS'));
        } else {
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab=account', Text::_('COM_MINIORANGE_ACCOUNT_REMOVE_ERROR'), 'error');
        }
    }

    public function checkLicense(){

        $customer = new MoOtpUtility();
        $response = json_decode($customer->fetchLicense());
        if($response->status=='SUCCESS'){
            MoOtpUtility::updateLicenseDetails($response);
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup',Text::_('COM_MINIORANGE_LICENSE_UPDATED_SUCCESSFULLY'),'success');
            return;
        }
        else{
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup',$response->message,'error');
            return;
        }

    }



    function save_customer_configurations($email, $id, $apiKey, $token, $phone)
    {
        $db_table = '#__miniorange_otp_customer';

        $db_coloums = array(
            'email' => $email,
            'customer_key' => $id,
            'api_key' => $apiKey,
            'customer_token' => $token,
            'admin_phone' => $phone,
            'login_status' => 0,
            'registration_status' => 'SUCCESS',
            'password' => '',
            'email_count' => 0,
            'sms_count' => 0,
        );

        commonOtpUtilities::__genDBUpdate($db_table, $db_coloums);
    }

    function registerCustomer()
    {
        //validate and sanitize

        $email = '';
        $phone = '';
        $password = '';
        $confirmPassword = '';

        $password = (Factory::getApplication()->input->post->getArray()["password"]);
        $confirmPassword = (Factory::getApplication()->input->post->getArray()["confirmPassword"]);

        $email = (Factory::getApplication()->input->post->getArray()["email"]);

        if (empty($email) || empty($password) || empty($confirmPassword)) {
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup', Text::_('COM_MINIORANGE_ALL_FIELDS_REQUIRED'), 'error');
            return;
        } else if (strlen($password) < 6 || strlen($confirmPassword) < 6) {    //check password is of minimum length 6
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup', Text::_('COM_MINIORANGE_PASSWORD_LENGTH'), 'error');
            return;
        } else {
            $email = Factory::getApplication()->input->post->getArray()["email"];
            $email = strtolower($email);
            $phone = Factory::getApplication()->input->post->getArray()["phone"];
            $password = Factory::getApplication()->input->post->getArray()["password"];
            $confirmPassword = Factory::getApplication()->input->post->getArray()["confirmPassword"];
        }

        if (strcmp($password, $confirmPassword) == 0) {

            $db_table = '#__miniorange_otp_customer';

            $db_coloums = array(
                'email' => $email,
                'admin_phone' => $phone,
                'password' => $password,
            );
            
            commonOtpUtilities::__genDBUpdate($db_table, $db_coloums);

            $customer = new MoOtpCustomer();
            $content = json_decode($customer->check_customer($email), true);
            if (strcasecmp($content['status'], 'CUSTOMER_NOT_FOUND') == 0) {
                $auth_type = 'EMAIL';
                $content = json_decode($customer->send_otp_token($auth_type, $email), true);
                if (strcasecmp($content['status'], 'SUCCESS') == 0) {

                    $db_table = '#__miniorange_otp_customer';

                    $db_coloums = array(
                        'email_count' => 1,
                        'transaction_id' => $content['txId'],
                        'login_status' => false,
                        'registration_status' => 'MO_OTP_DELIVERED_SUCCESS',
                    );

                    commonOtpUtilities::__genDBUpdate($db_table, $db_coloums);
                    $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup', Text::_('COM_MINIORANGE_OTP_SENT') . ' <strong>' . $email . '</strong>. ' . Text::_('COM_MINIORANGE_ENTER_OTP'));
                } else {
                    $db_table = '#__miniorange_otp_customer';

                    $db_coloums = array(
                        'login_status' => false,
                        'registration_status' => 'MO_OTP_DELIVERED_FAILURE',
                    );

                    commonOtpUtilities::__genDBUpdate($db_table, $db_coloums);
                    $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup', Text::_('COM_MINIORANGE_ERROR_IN_OTP_SENDING'), 'error');
                }
            } else if (strcasecmp($content['status'], 'CURL_ERROR') == 0) {

                $db_table = '#__miniorange_otp_customer';

                $db_coloums = array(
                    'login_status' => false,
                    'registration_status' => 'MO_OTP_DELIVERED_FAILURE',
                );

                commonOtpUtilities::__genDBUpdate($db_table, $db_coloums);
                $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup', $content['statusMessage'], 'error');

            } else {
                $content = $customer->get_customer_key($email, $password);
                $customerKey = json_decode($content, true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $this->save_customer_configurations($email, $customerKey['id'], $customerKey['apiKey'], $customerKey['token'], $customerKey['phone']);
                    $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup', Text::_('COM_MINIORANGE_ACCOUNT_LOGIN_SUCCESS_MESSAGE'));
                } else {
                    $db_table = '#__miniorange_otp_customer';

                    $db_coloums = array(
                        'login_status' => true,
                        'registration_status' => '',
                    );

                    commonOtpUtilities::__genDBUpdate($db_table, $db_coloums);
                    $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup', Text::_('COM_MINIORANGE_ACCOUNT_EXISTS'), 'error');
                }
            }

        } else {
            $db_table = '#__miniorange_otp_customer';
            $db_coloums = array(
                'login_status' => false,
            );
            commonOtpUtilities::__genDBUpdate($db_table, $db_coloums);
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup', Text::_('COM_MINIORANGE_PASSWORDS_DO_NOT_MATCH'), 'error');
        }
    }

    function validateOtp()
    {

        $otp_token = trim(Factory::getApplication()->input->post->getArray()["otp_token"]);
        //validation and sanitization
      
        if (empty($otp_token)) {
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup', Text::_('COM_MINIORANGE_ENTER_VALID_OTP'), 'error');
            return;
        } else {
            $otp_token = trim(Factory::getApplication()->input->post->getArray()['otp_token']);
        }

        $transaction_id = commonOtpUtilities::__getDBLoadResult('transaction_id', '#__miniorange_otp_customer');


        $customer = new MoOtpCustomer();
        $content = json_decode($customer->validate_otp_token($transaction_id, $otp_token), true);
        if (strcasecmp($content['status'], 'SUCCESS') == 0) {
            $customerKey = json_decode($customer->create_customer(), true);

            $db_table = '#__miniorange_otp_customer';

            $db_coloums = array(
                'email_count' => 0,
                'sms_count' => 0,
            );

            commonOtpUtilities::__genDBUpdate($db_table, $db_coloums);

            if (strcasecmp($customerKey['status'], 'CUSTOMER_USERNAME_ALREADY_EXISTS') == 0) {    //admin already exists in miniOrange
                $content = $customer->get_customer_key();
                $customerKey = json_decode($content, true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $this->save_customer_configurations($customerKey['email'], $customerKey['id'], $customerKey['apiKey'], $customerKey['token'], $customerKey['phone']);
                    $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup', Text::_('COM_MINIORANGE_ACCOUNT_LOGIN_SUCCESS_MESSAGE'));
                } else {
                    $db_table = '#__miniorange_otp_customer';

                    $db_coloums = array(
                        'login_status' => true,
                        'password' => '',
                    );

                    commonOtpUtilities::__genDBUpdate($db_table, $db_coloums);
                    $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup', Text::_('COM_MINIORANGE_ACCOUNT_EXISTS'), 'error');
                }
            } else if (strcasecmp($customerKey['status'], 'SUCCESS') == 0) {

                //registration successful
                $this->save_customer_configurations($customerKey['email'], $customerKey['id'], $customerKey['apiKey'], $customerKey['token'], $customerKey['phone']);
                $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup', Text::_('COM_MINIORANGE_ACCOUNT_CREATED_SUCCESSFULLY'));
            } else if (strcmp($customerKey['message'], 'Email is not enterprise email.') || strcmp($customerKey['status'], 'INVALID_EMAIL_QUICK_EMAIL') == 0) {

                $db_table = '#__miniorange_otp_customer';

                $db_coloums = array(
                    'registration_status' => '',
                    'email' => '',
                    'password' => '',
                    'transaction_id' => '',
                );

                commonOtpUtilities::__genDBUpdate($db_table, $db_coloums);
                $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup', Text::_('COM_MINIORANGE_ACCOUNT_CREATE_ERROR'), 'error');

            }
        } else if (strcasecmp($content['status'], 'CURL_ERROR') == 0) {

            $db_table = '#__miniorange_otp_customer';
            $db_coloums = array(
                'registration_status' => 'MO_OTP_VALIDATION_FAILURE',
            );

            commonOtpUtilities::__genDBUpdate($db_table, $db_coloums);
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup', $content['statusMessage'], 'error');

        } else {

            $db_table = '#__miniorange_otp_customer';
            $db_coloums = array(
                'registration_status' => 'MO_OTP_VALIDATION_FAILURE',
            );
            commonOtpUtilities::__genDBUpdate($db_table, $db_coloums);
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup', Text::_('COM_MINIOTANGE_INVALID_OTP_ENTERED'), 'error');
        }
    }

    function resendOtp()
    {
        $customer  = new MoOtpCustomer();
        $auth_type = 'EMAIL';
        $email     = commonOtpUtilities::__getDBLoadResult('email', '#__miniorange_otp_customer');
        $content   = json_decode($customer->send_otp_token($auth_type, $email), true);
        if (strcasecmp($content['status'], 'SUCCESS') == 0) {

            $columnName       = array('email_count','email');
            $customer_details = commonOtpUtilities::getCustomerDetails($columnName, '#__miniorange_otp_customer', 'loadObjectList', array('id' => 1,));
            $email_count      = $customer_details[0]->email_count;
            $admin_email      = $customer_details[0]->email;

            if ($email_count != '' && $email_count >= 1) {
                $email_count = $email_count + 1;

                $db_table = '#__miniorange_otp_customer';

                $db_coloums = array(
                    'email_count' => $email_count,
                    'transaction_id' => $content['txId'],
                    'registration_status' => 'MO_OTP_DELIVERED_SUCCESS',
                );

                commonOtpUtilities::__genDBUpdate($db_table, $db_coloums);
                $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup', Text::_('COM_MINIORANGE_OTP_SENT') . '<strong>' . ($admin_email) . '</strong>.' . Text::_('COM_MINIORANGE_ENTER_OTP'));

            } else {
                $db_table = '#__miniorange_otp_customer';
                $db_coloums = array(
                    'email_count' => 1,
                    'transaction_id' => $content['txId'],
                    'registration_status' => 'MO_OTP_DELIVERED_SUCCESS',
                );
                commonOtpUtilities::__genDBUpdate($db_table, $db_coloums);
                $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup', Text::_('COM_MINIORANGE_OTP_SENT') . '<strong>' . ($admin_email) . '</strong>.' . Text::_('COM_MINIORANGE_ENTER_OTP'));
            }

        } else if (strcasecmp($content['status'], 'CURL_ERROR') == 0) {
            $db_table = '#__miniorange_otp_customer';
            $db_coloums = array(
                'registration_status' => 'MO_OTP_DELIVERED_FAILURE',
            );
            commonOtpUtilities::__genDBUpdate($db_table, $db_coloums);
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup', $content['statusMessage'], 'error');

        } else {
            $db_table = '#__miniorange_otp_customer';
            $db_coloums = array(
                'registration_status' => 'MO_OTP_DELIVERED_FAILURE',
            );
            commonOtpUtilities::__genDBUpdate($db_table, $db_coloums);
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup', Text::_('COM_MINIORANGE_ERROR_IN_OTP_SENDING'), 'error');
        }
    }

    function cancelform()
    {
        $db_table = '#__miniorange_otp_customer';
        $db_coloums = array(
            'email' => '',
            'password' => '',
            'customer_key' => '',
            'api_key' => '',
            'customer_token' => '',
            'admin_phone' => '',
            'login_status' => 0,
            'registration_status' => '',
            'email_count' => 0,
            'sms_count' => 0,
        );
        commonOtpUtilities::__genDBUpdate($db_table, $db_coloums);
        $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup');
    }

    function phoneVerification()
    {
        $phone = Factory::getApplication()->input->post->getArray()['phone_number'];
        $phone = str_replace(' ', '', $phone);

        $pattern = "/[\+][0-9]{1,3}[0-9]{10}/";

        if (preg_match($pattern, $phone, $matches, PREG_OFFSET_CAPTURE)) {
            $auth_type = 'SMS';
            $customer = new MoOtpCustomer();
            $send_otp_response = json_decode($customer->send_otp_token($auth_type, $phone));
            if ($send_otp_response->status == 'SUCCESS') {
                $sms_count = commonOtpUtilities::__getDBLoadResult('sms_count', '#__miniorange_otp_customer');

                if ($sms_count >= 1) {
                    $sms_count = $sms_count + 1;
                    $db_table = '#__miniorange_otp_customer';
                    $db_coloums = array(
                        'sms_count' => $sms_count,
                        'transaction_id' => $send_otp_response->txId,
                    );
                    commonOtpUtilities::__genDBUpdate($db_table, $db_coloums);
                    $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup', Text::_('COM_MINIORANGE_ANOTHER_OTP_OVER_PHONE') . $phone);
                } else {
                    $db_table = '#__miniorange_otp_customer';
                    $db_coloums = array(
                        'sms_count' => 1,
                        'transaction_id' => $send_otp_response->txId,
                    );
                    commonOtpUtilities::__genDBUpdate($db_table, $db_coloums);
                    $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup', Text::_('COM_MINIORANGE_OTP_SENT') . $phone);
                }

            } else {
                $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup', Text::_('COM_MINIORAGNE_ERROR_SENDING_OTP_TO_PHONE'));
            }
        } else {
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup', Text::_('COM_MINIORANGE_PHONE_NUMBER_FORMAT'), 'error');
        }
    }

   
    function saveRSfield()
    {
            $db = Factory::getDbo();
            $query = $db->getQuery(true);
            $app = Factory::getApplication();
            $input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
            $post = ($input && $input->post) ? $input->post->getArray() : [];
            $rs_form_count=isset($post['form_count'])?$post['form_count']:1;
            $rs_form_configurtion=array();
            if ($rs_form_count != '0'){
                for ($i = 0; $i < $rs_form_count; $i++) 
                {
                    $key = 'rs_form_id' . $i;
                    $rs_email = 'email_id' . $i;
                    $rs_phone = 'contact_no' . $i;
                    $rs_password = 'rs_password' . $i;
                    $rs_form_configurtion[$post[$key]] = array($post[$rs_email], $post[$rs_phone], $post[$rs_password]);
                }
            }

            $rs_form_configuration_encode= json_encode($rs_form_configurtion);
            $db_table = '#__miniorange_otp_customer';

            //if form count is 0 then set columns 'rs_form_count' = 0 and 'rs_form_field_configuration' = NULL in database.
            if ($rs_form_count != '0') {
                $db_coloums = array(
                    'rs_form_field_configuration' => $rs_form_configuration_encode,
                    'rs_form_count'=>$rs_form_count, 
                );
            }
            else {
                $db_coloums = array(
                    'rs_form_field_configuration' => 'NULL',
                    'rs_form_count' => $rs_form_count,
                );
            }
            commonOtpUtilities::__genDBUpdate($db_table, $db_coloums);
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=custom_forms', Text::_('COM_MINIORANGE_CONFIGURATION_SAVES_SUCCESSFULLY'));
    }

    function contactUs()
    {
        $query_email = Factory::getApplication()->input->post->getArray()['query_email'];
        $query = Factory::getApplication()->input->post->getArray()['query'];
        $query = preg_replace('!\s+!', '', $query);
        if (empty($query_email) || empty($query)) {
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup', Text::_('COM_MINIORANGE_QUERY_ERROR'), 'error');
            return;
        } else {
            $query = Factory::getApplication()->input->post->getArray()['query'];
            $email = Factory::getApplication()->input->post->getArray()['query_email'];
            $phone = Factory::getApplication()->input->post->getArray()['query_phone'];
            $contact_us = new MoOtpCustomer();
            $submited = json_decode($contact_us->submit_contact_us($email, $phone, $query), true);
            if (json_last_error() == JSON_ERROR_NONE) {
                if (is_array($submited) && array_key_exists('status', $submited) && $submited['status'] == 'ERROR') {
                    $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=request_demo', $submited['message'], 'error');
                } else {
                    if ($submited == false) {
                        $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=request_demo', Text::_('COM_MINIORANGE_QUERY_NOT_SUBMITTED'), 'error');
                    } else {
                        $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=request_demo', Text::_('COM_MINIORANGE_QUERY_SUBMITTED_MSG'));
                    }
                }
            }
        }
    }
    function request_setup_call()
    {
        $app = Factory::getApplication();
        $input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
        $array = ($input && $input->post) ? $input->post->getArray() : [];

        $type_service=$array["type_service"];
        $email=$array["email"];
        $number_otp=$array['no_of_otp'];
        if($type_service=='SMS'||$type_service=='Both')
        {
            $select_country=$array['select_country'];
            if($select_country=="singlecountry")
            {
                $which_country=$array['select_country'];
            }
        }
        $query = isset($array['user_extra_requirement'])?$array['user_extra_requirement']:"null";
        if (empty($email)) {
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=request_demo', Text::_('COM_MINIORANGE_QUERY_ERROR'), 'error');
            return;
        }
        else
        {
             
            $select_country= isset($array['select_country']) && $type_service != "Email" ? $array['select_country'] : "null";
            $which_country= isset($array['which_country']) && $type_service != "Email" ? $array['which_country'] : "null";

            if ($select_country == "all country"){
                $which_country = null;
            }
            $user_support = new MoOtpCustomer();
            $submited = json_decode($user_support->submit_request_setup_call($type_service, $email, $number_otp,$select_country,$which_country,$query), true);
            if (json_last_error() == JSON_ERROR_NONE) 
            {
                if (is_array($submited) && array_key_exists('status', $submited) && $submited['status'] == 'ERROR') {
                    $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=request_demo', $submited['message'], 'error');
                } 
                else 
                {
                    if ($submited == false) 
                    {
                        $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=request_demo', Text::_('COM_MINIORANGE_QUERY_NOT_SUBMITTED'), 'error');
                    } 
                    else 
                    {
                        $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=request_demo', Text::_('COM_MINIORANGE_QUERY_SUBMITTED_MSG'));
                    }
                }
            }
        }
    }
    public function exportConfiguration()
    {
        // Define single or multiple table names here
        $tableNames = [
            '#__miniorange_otp_customer',
            '#__miniorange_otp_custom_message',
        ];

        // Include the helper file
        require_once JPATH_COMPONENT . '/helpers/mo_otp_utility.php';

        MoOtpUtility::exportData($tableNames);
    }

    public function importConfiguration()
    {
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $uploadedFile = $_FILES['file']['tmp_name'];
            $this->processImportFile($uploadedFile);
        } else {
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=exportConfiguration', Text::_('COM_MINIORANGE_FILE_UPLOAD_ERROR'), 'error');
        }
    }

    public function processImportFile($uploadedFile)
    {
        $jsonContent = file_get_contents($uploadedFile);
        $jsonData = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=exportConfiguration', Text::_('COM_MINIORANGE_INVALID_JSON_FILE'), 'error');
            return;
        }

        try {
            foreach ($jsonData as $tableName => $data) {
                $this->importTableData($tableName, $data);
            }
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=exportConfiguration', Text::_('COM_MINIORANGE_IMPORT_SUCCESS'), 'success');
        } catch (Exception $e) {
            $this->setRedirect('index.php?option=com_joomlaotp&view=accountsetup&tab-panel=exportConfiguration', $e->getMessage(), 'error');
        }
    }

  public function importTableData($tableName, $data)
{
    $db = Factory::getDbo();

    foreach ($data as $row) {
        $columns = array_keys($row);
        $values = array_values($row);

        // Only convert real nulls to SQL NULL. Keep empty strings as ''
        $safeValues = array_map(function ($val) use ($db) {
            return $val === null ? 'NULL' : $db->quote($val);
        }, $values);

        $insert = $db->getQuery(true)
                     ->insert($db->quoteName($tableName))
                     ->columns($db->quoteName($columns))
                     ->values(implode(',', $safeValues));

        $updates = [];
        foreach ($columns as $column) {
            if ($column === 'id') continue;
            $updates[] = $db->quoteName($column) . ' = VALUES(' . $db->quoteName($column) . ')';
        }

        $query = $insert . ' ON DUPLICATE KEY UPDATE ' . implode(', ', $updates);
        $db->setQuery($query);
        $db->execute();
    }
}


}
