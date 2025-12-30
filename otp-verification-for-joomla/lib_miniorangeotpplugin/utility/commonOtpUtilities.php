<?php

/**
 * @package     Joomla.Library
 * @subpackage  lib_miniorangeotpplugin
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
use Joomla\CMS\Factory;
$lang = Factory::getLanguage();
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Installer\Installer;

$lang->load('lib_miniorangeotpplugin',JPATH_SITE);

class commonOtpUtilities
{
    public static function is_customer_registered()
    {
        $columnName = array('email','customer_key','registration_status');
        $result = self::getCustomerDetails($columnName, '#__miniorange_otp_customer', 'loadObjectList', array('id' => 1,));
        $email = $result[0]->email;
        $customerKey = $result[0]->customer_key;
        $status = $result[0]->registration_status;
        if ($email && $customerKey && is_numeric(trim($customerKey)) && $status == 'SUCCESS') {
            return 1;
        } else {
            return 0;
        }
    }

    public static function getCustomerDetails($columnName, $tableName, $method, $condition=TRUE){
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select($columnName);
        $query->from($db->quoteName($tableName));
        if ($condition !== TRUE){
            foreach ($condition as $key=>$value)
            {
                $query->where($db->quoteName($key) . " = " . $db->quote($value));
            }
        }
        $db->setQuery($query);
        if ($method=='loadColumn')
            return $db->loadColumn();
        else if($method == 'loadObjectList')
            return $db->loadObjectList();
        else if($method == 'loadObject')
            return $db->loadObject();
        else if($method== 'loadResult')
            return $db->loadResult();
        else if($method == 'loadRow')
            return $db->loadRow();
        else if($method == 'loadRowList')
            return $db->loadRowList();
        else if($method == 'loadAssocList')
            return $db->loadAssocList();
        else
            return $db->loadAssoc();
    }

    public static function getHostname()
    {
        return 'https://login.xecurify.com';
    }

    public static function get_userid_from_username($username)
    {
        //Check if username exist in database
        $db = Factory::getDBO();

        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__users')
            ->where('username=' . $db->quote($username));

        $db->setQuery($query);
        return $db->loadColumn();
    }

    public static function get_userid_from_email($email)
    {
        //Check if email exist in database
        $db = Factory::getDBO();

        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__users')
            ->where('email=' . $db->quote($email));

        $db->setQuery($query);
        return $db->loadColumn();
    }

    public static function user_email_phone_check($post, $get){
        $columnName    = array('rs_form_field_configuration','reg_restriction','white_or_black','mo_otp_allowed_email_domains');
        $result        = self::getCustomerDetails($columnName, '#__miniorange_otp_customer', 'loadObjectList', array('id' => 1,));
        $requested_uri = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $rs_form_configuration=isset($result[0]->rs_form_field_configuration)?json_decode($result[0]->rs_form_field_configuration):array();

        if ($result[0]->reg_restriction == 1 )
        {
            //Check for blacklisted/whitelisted Email domains.
            if ($result[0]->white_or_black == 1)
            {
                $allowed_domains = $result[0]->mo_otp_allowed_email_domains ?? 0;
            }
            else if ($result[0]->white_or_black == 2) {
                $blocked_domains = $result[0]->mo_otp_allowed_email_domains ?? 0;
            }

            if( isset($post['task']) && $post['task'] =='saveUser' ) { //Virtuemart Form
                isset ($post['email']) ? $domain = explode('@', $post['email'])[1] : '';
            }
            elseif ( isset($post['cf']['hnpt']) && $get['task'] == 'submit') { //Convert Form
                isset($post['cf']['email']) ? $domain = explode('@', $post['cf']['email'])[1] : '';
            }
            else if ( isset($post['__cf_token']) && $get['gpage'] == 'start_page') { //Chrono Form
                isset($post['email']) ? $domain = explode('@', $post['email'])[1] : '';
            }
            else if ( isset($post['form']) ){ //RS Form
                foreach ($rs_form_configuration as $key=>$value){
                    if ($post['form']['formId'] == $key) {
                        isset($post['form'][$value[0]]) ? $domain = explode('@', $post['form'][$value[0]])[1] : '';
                    }
                }
            }
            else if( isset($post['gid']) && isset($post['emailpass']) && strpos($requested_uri, 'cb-profile') ){ //Community Builder Form
                isset($post['email']) ? $domain = explode('@', $post['email'])[1] : '';
            }
            else { // Joomla default Registration Form
                isset ($post['jform']['email1']) ? $domain = explode('@', $post['jform']['email1'])[1] : '';
            }
        }
        $blocked_domains = isset($blocked_domains) ? explode(';', $blocked_domains) : [0];
        $allowed_domains = isset($allowed_domains) ? explode(';', $allowed_domains) : [0];

        $phone_number = '';
        //Check for blocked Country codes.
        if( isset($post['task']) && $post['task'] =='saveUser') { //Virtuemart Form
            $phone_number= $post['phone_1'] ?? '';
        }
        else if ( isset($post['cf']['hnpt']) && isset($get['task']) && $get['task'] == 'submit' ) { //Convert Form
            $phone_number = $post['cf']['phone'] ?? '';
        }
        else if ( isset($post['__cf_token']) && isset($get['gpage']) && $get['gpage'] == 'start_page' ) { //Chrono Form
            $phone_number = $post['phone'] ?? '';
        }
        else if ( isset($post['form']['formId']) ) { //RS Form
            foreach ($rs_form_configuration as $key=>$value){
                if ($post['form']['formId'] == $key) {
                    $phone_number = $post['form'][$value[1]] ?? '';
                }
            }
        }
        else if ( isset($post['gid']) && isset($post['emailpass']) ) { //Community Builder Form
            $phone_number = isset($post);
        }
        else { // Joomla default Registration Form
            $phone_number = $post['jform']['profile']['phone'] ?? '';
        }

        $is_blocked = self::_check_country_code_blocked($phone_number);
        $is_email = false;
        $is_phone = false;
        if ($is_blocked) {
            $is_phone = true;
        }
        if (isset($domain)) {
            if (!((!in_array($domain, $blocked_domains) || empty($blocked_domains[0])) && ((in_array($domain, $allowed_domains)) || empty($allowed_domains[0])))) {
                $is_email = true;
            }
        }
        self::_show_blocked_message($is_phone, $is_email);
    }

    public static function _show_blocked_message($is_phone, $is_email)
    {
        $result = self::_get_custom_message();

        if ($is_email && $is_phone){
            $custom_blocked_email_and_phone_message = 'You are not allowed to register. Your country code and email domain are blocked. Please contact your administrator.';
            self::_redirect_url($custom_blocked_email_and_phone_message);
        }
        else if ($is_phone) {
            $custom_blocked_phone_message = $result['mo_custom_phone_blocked_message'] ?? '';
            if (empty($custom_blocked_phone_message) || $custom_blocked_phone_message == ''){
                $custom_blocked_phone_message = 'You are not allowed to register. Your country may be blocked. Please contact your administrator.';
            }
            self::_redirect_url($custom_blocked_phone_message);
        } else if ($is_email) {
            $custom_blocked_email_message = $result['mo_custom_email_blocked_message'] ?? '';
            if (empty($custom_blocked_email_message) || $custom_blocked_email_message == 'You are not allowed to register. Your Domain may be blocked. Please contact your administrator.'){
                $custom_blocked_email_message = 'You are not allowed to register. Your domain may be blocked. Please contact your administrator.';
            }
            self::_redirect_url($custom_blocked_email_message);
        }
    }

    public static function _redirect_url($message)
    {
        $app = Factory::getApplication();
        $app->enqueueMessage($message, 'error');
        $app->redirect(Route::_('index.php'));
    }

    public static function _get_custom_message()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__miniorange_otp_custom_message'));
        $query->where($db->quoteName('id') . " = 1");
        $db->setQuery($query);
        return ($db->loadAssoc());
    }

    public static function _check_country_code_blocked($phone_number)
    {
        $result = self::_get_custom_message();
        $blocked_list = isset($result['mo_block_country_code']) ? $result['mo_block_country_code'] : '';

        if (!empty($blocked_list) && $blocked_list != '') {
            $blocked_list = unserialize($blocked_list);

            for ($i = 0; $i < count($blocked_list); $i++) {
                if (isset($blocked_list[$i]) && !empty($blocked_list[$i])) {
                    $val = $blocked_list[$i];
                    if (strpos($phone_number, $val) !== false ) {
                        return 1;
                    }
                }
            }
        }
        return 0;
    }

    public static function __getDBValuesWOArray($table)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName($table));
        $query->where($db->quoteName('id') . " = 1");
        $db->setQuery($query);
        return ($db->loadAssoc());
    }

    public static function _is_country_code_blocked($country_code)
    {
        $result = self::_get_custom_message();

        $blocked_list = isset($result['mo_block_country_code']) ? $result['mo_block_country_code'] : '';

        if (empty($blocked_list)) {
            return 0;
        }
        $blocked_list = unserialize($blocked_list);
        if (in_array($country_code, $blocked_list))
        {
            return 1;
        } else
        {
            return 0;
        }
    }

    public static function _is_default_selected($post)
    {
        $country_code = isset($post['mo_block_country_code']) ? $post['mo_block_country_code'] : '';
        $country_code = trim($country_code);
        $columnName   = array('mo_default_country_code');
        $results      = self::getCustomerDetails($columnName, '#__miniorange_otp_customer', 'loadObject', array('id' => 1,));
        $default_country_code = isset($results->mo_default_country_code) && !empty($results->mo_default_country_code)
            ? $results->mo_default_country_code
            : '';

        $default_country_code = '+' . $default_country_code;
        $country_code = explode(';', $country_code);

        if (in_array($default_country_code, $country_code)) {
            return true;
        } else {
            return false;
        }
    }

    public static function _block_country_code($post)
    {
        $country_code = isset($post['mo_block_country_code']) ? $post['mo_block_country_code'] : '';
        $country_code = trim($country_code);
        $country_code = explode(';', $country_code);
        $country_code = serialize($country_code);

        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $fields = array(
            $db->quoteName('mo_block_country_code') . ' = ' . $db->quote($country_code),
        );
        $conditions = array(
            $db->quoteName('id') . ' = 1'
        );
        $query->update($db->quoteName('#__miniorange_otp_custom_message'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $db->execute();
    }

    public static function _save_custom_message($post)
    {
        $message         = Text::_('LIB_EMAIL_SENT_MSG');
        $error_otp       = Text::_('LIB_EMAIL_ERROR_IN_SENDING_OTP');
        $blocked_email   = Text::_('LIB_EMAIL_BLOCKED_MSG');
        $email_success   = isset($post['mo_custom_email_success_message_send']) ? $post['mo_custom_email_success_message_send'] : $message;
        $email_fail      = isset($post['mo_custom_email_error_message']) ? $post['mo_custom_email_error_message'] : $error_otp;
        $invalid_email   = isset($post['mo_custom_email_invalid_format_message']) ? $post['mo_custom_email_invalid_format_message'] : '';
        $blocked_message = isset($post['mo_custom_email_blocked_message']) ? $post['mo_custom_email_blocked_message'] : $blocked_email;

        $email_success   = trim($email_success);
        $email_fail      = trim($email_fail);
        $invalid_email   = trim($invalid_email);
        $blocked_message = trim($blocked_message);

        $db = Factory::getDbo();

        $query = $db->getQuery(true);
        // Fields to update.

        $fields = array(
            $db->quoteName('mo_custom_email_success_message') . ' = ' . $db->quote($email_success),
            $db->quoteName('mo_custom_email_error_message') . ' = ' . $db->quote($email_fail),
            $db->quoteName('mo_custom_email_invalid_format_message') . ' = ' . $db->quote($invalid_email),
            $db->quoteName('mo_custom_email_blocked_message') . ' = ' . $db->quote($blocked_message),
        );

        // Conditions for which records should be updated.
        $conditions = array(
            $db->quoteName('id') . ' = 1'
        );

        $query->update($db->quoteName('#__miniorange_otp_custom_message'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $db->execute();
    }

    public static function _save_custom_phone_message($post)
    {
        $success_message = "An OTP (One Time Passcode) has been sent to ##phone##. Please enter the OTP in the field below to verify your phone.";
        $error_message = "There was an error in sending the OTP to the given Phone Number. Please Try Again or contact site Admin.";

        $phone_success = isset($post['mo_custom_phone_success_message']) ? $post['mo_custom_phone_success_message'] : $success_message;
        $phone_error = isset($post['mo_custom_phone_error_message']) ? $post['mo_custom_phone_error_message'] : $error_message;
        $invalid_format = isset($post['mo_custom_phone_invalid_format_message']) ? $post['mo_custom_phone_invalid_format_message'] : '';
        $phone_blocked = isset($post['mo_custom_phone_blocked_message']) ? $post['mo_custom_phone_blocked_message'] : '';

        $phone_success = trim($phone_success);
        $phone_error = trim($phone_error);
        $invalid_format = trim($invalid_format);
        $phone_blocked = trim($phone_blocked);

        $db = Factory::getDbo();

        $query = $db->getQuery(true);
        // Fields to update.

        $fields = array(
            $db->quoteName('mo_custom_phone_success_message') . ' = ' . $db->quote($phone_success),
            $db->quoteName('mo_custom_phone_error_message') . ' = ' . $db->quote($phone_error),
            $db->quoteName('mo_custom_phone_invalid_format_message') . ' = ' . $db->quote($invalid_format),
            $db->quoteName('mo_custom_phone_blocked_message') . ' = ' . $db->quote($phone_blocked),
        );

        // Conditions for which records should be updated.
        $conditions = array(
            $db->quoteName('id') . ' = 1'
        );

        $query->update($db->quoteName('#__miniorange_otp_custom_message'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $db->execute();
    }

    public static function _save_com_message($post)
    {
        $invalid_otp = isset($post['mo_custom_invalid_otp_message']) ? $post['mo_custom_invalid_otp_message'] : '';

        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $fields = array(
            $db->quoteName('mo_custom_invalid_otp_message') . ' = ' . $db->quote($invalid_otp),
        );
        $conditions = array(
            $db->quoteName('id') . ' = 1'
        );
        $query->update($db->quoteName('#__miniorange_otp_custom_message'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $db->execute();
    }

    public static function GetPluginVersion()
    {
        $db = Factory::getDbo();
        $dbQuery = $db->getQuery(true)
            ->select('manifest_cache')
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('element') . " = " . $db->quote('com_joomlaotp'));
        $db->setQuery($dbQuery);
        $manifest = json_decode($db->loadResult());
        return($manifest->version);
    }

    public static function _get_os_info()
    {

        if (isset($_SERVER)) {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
        } else {
            global $HTTP_SERVER_VARS;
            if (isset($HTTP_SERVER_VARS)) {
                $user_agent = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
            } else {
                global $HTTP_USER_AGENT;
                $user_agent = $HTTP_USER_AGENT;
            }
        }

        $os_array = [
            'windows nt 10' => 'Windows 10',
            'windows nt 6.3' => 'Windows 8.1',
            'windows nt 6.2' => 'Windows 8',
            'windows nt 6.1|windows nt 7.0' => 'Windows 7',
            'windows nt 6.0' => 'Windows Vista',
            'windows nt 5.2' => 'Windows Server 2003/XP x64',
            'windows nt 5.1' => 'Windows XP',
            'windows xp' => 'Windows XP',
            'windows nt 5.0|windows nt5.1|windows 2000' => 'Windows 2000',
            'windows me' => 'Windows ME',
            'windows nt 4.0|winnt4.0' => 'Windows NT',
            'windows ce' => 'Windows CE',
            'windows 98|win98' => 'Windows 98',
            'windows 95|win95' => 'Windows 95',
            'win16' => 'Windows 3.11',
            'mac os x 10.1[^0-9]' => 'Mac OS X Puma',
            'macintosh|mac os x' => 'Mac OS X',
            'mac_powerpc' => 'Mac OS 9',
            'linux' => 'Linux',
            'ubuntu' => 'Linux - Ubuntu',
            'iphone' => 'iPhone',
            'ipod' => 'iPod',
            'ipad' => 'iPad',
            'android' => 'Android',
            'blackberry' => 'BlackBerry',
            'webos' => 'Mobile',

            '(media center pc).([0-9]{1,2}\.[0-9]{1,2})' => 'Windows Media Center',
            '(win)([0-9]{1,2}\.[0-9x]{1,2})' => 'Windows',
            '(win)([0-9]{2})' => 'Windows',
            '(windows)([0-9x]{2})' => 'Windows',

            // Doesn't seem like these are necessary...not totally sure though..
            //'(winnt)([0-9]{1,2}\.[0-9]{1,2}){0,1}'=>'Windows NT',
            //'(windows nt)(([0-9]{1,2}\.[0-9]{1,2}){0,1})'=>'Windows NT', // fix by bg

            'Win 9x 4.90' => 'Windows ME',
            '(windows)([0-9]{1,2}\.[0-9]{1,2})' => 'Windows',
            'win32' => 'Windows',
            '(java)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2})' => 'Java',
            '(Solaris)([0-9]{1,2}\.[0-9x]{1,2}){0,1}' => 'Solaris',
            'dos x86' => 'DOS',
            'Mac OS X' => 'Mac OS X',
            'Mac_PowerPC' => 'Macintosh PowerPC',
            '(mac|Macintosh)' => 'Mac OS',
            '(sunos)([0-9]{1,2}\.[0-9]{1,2}){0,1}' => 'SunOS',
            '(beos)([0-9]{1,2}\.[0-9]{1,2}){0,1}' => 'BeOS',
            '(risc os)([0-9]{1,2}\.[0-9]{1,2})' => 'RISC OS',
            'unix' => 'Unix',
            'os/2' => 'OS/2',
            'freebsd' => 'FreeBSD',
            'openbsd' => 'OpenBSD',
            'netbsd' => 'NetBSD',
            'irix' => 'IRIX',
            'plan9' => 'Plan9',
            'osf' => 'OSF',
            'aix' => 'AIX',
            'GNU Hurd' => 'GNU Hurd',
            '(fedora)' => 'Linux - Fedora',
            '(kubuntu)' => 'Linux - Kubuntu',
            '(ubuntu)' => 'Linux - Ubuntu',
            '(debian)' => 'Linux - Debian',
            '(CentOS)' => 'Linux - CentOS',
            '(Mandriva).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)' => 'Linux - Mandriva',
            '(SUSE).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)' => 'Linux - SUSE',
            '(Dropline)' => 'Linux - Slackware (Dropline GNOME)',
            '(ASPLinux)' => 'Linux - ASPLinux',
            '(Red Hat)' => 'Linux - Red Hat',
            // Loads of Linux machines will be detected as unix.
            // Actually, all of the linux machines I've checked have the 'X11' in the User Agent.
            //'X11'=>'Unix',
            '(linux)' => 'Linux',
            '(amigaos)([0-9]{1,2}\.[0-9]{1,2})' => 'AmigaOS',
            'amiga-aweb' => 'AmigaOS',
            'amiga' => 'Amiga',
            'AvantGo' => 'PalmOS',
            //'(Linux)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}(rel\.[0-9]{1,2}){0,1}-([0-9]{1,2}) i([0-9]{1})86){1}'=>'Linux',
            //'(Linux)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}(rel\.[0-9]{1,2}){0,1} i([0-9]{1}86)){1}'=>'Linux',
            //'(Linux)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}(rel\.[0-9]{1,2}){0,1})'=>'Linux',
            '[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3})' => 'Linux',
            '(webtv)/([0-9]{1,2}\.[0-9]{1,2})' => 'WebTV',
            'Dreamcast' => 'Dreamcast OS',
            'GetRight' => 'Windows',
            'go!zilla' => 'Windows',
            'gozilla' => 'Windows',
            'gulliver' => 'Windows',
            'ia archiver' => 'Windows',
            'NetPositive' => 'Windows',
            'mass downloader' => 'Windows',
            'microsoft' => 'Windows',
            'offline explorer' => 'Windows',
            'teleport' => 'Windows',
            'web downloader' => 'Windows',
            'webcapture' => 'Windows',
            'webcollage' => 'Windows',
            'webcopier' => 'Windows',
            'webstripper' => 'Windows',
            'webzip' => 'Windows',
            'wget' => 'Windows',
            'Java' => 'Unknown',
            'flashget' => 'Windows',

            // delete next line if the script show not the right OS
            //'(PHP)/([0-9]{1,2}.[0-9]{1,2})'=>'PHP',
            'MS FrontPage' => 'Windows',
            '(msproxy)/([0-9]{1,2}.[0-9]{1,2})' => 'Windows',
            '(msie)([0-9]{1,2}.[0-9]{1,2})' => 'Windows',
            'libwww-perl' => 'Unix',
            'UP.Browser' => 'Windows CE',
            'NetAnts' => 'Windows',
        ];

        $arch_regex = '/\b(x86_64|x86-64|Win64|WOW64|x64|ia64|amd64|ppc64|sparc64|IRIX64)\b/ix';
        $arch = preg_match($arch_regex, $user_agent) ? '64' : '32';

        foreach ($os_array as $regex => $value) {
            if (preg_match('{\b(' . $regex . ')\b}i', $user_agent)) {
                return $value . ' x' . $arch;
            }
        }

        return 'Unknown';
    }

    public static function _get_feedback_form($post)
    {
        $radio = isset($post['deactivate_plugin']) ? $post['deactivate_plugin'] : '';
        $data = isset($post['query_feedback']) ? $post['query_feedback'] : '';
        $db_table = '#__miniorange_otp_customer';
        $db_coloums = array('uninstall_feedback' => 1,);// use cookie instead of db query.------------------------------

        self::__genDBUpdate($db_table, $db_coloums);
        $columnName     = array('admin_phone');
        $customerResult = self::getCustomerDetails($columnName,'#__miniorange_otp_customer', 'loadObject', array('id' => 1,));

            $radio = isset($post['deactivate_plugin']) ? $post['deactivate_plugin'] : '';
            $data = isset($post['query_feedback']) ? $post['query_feedback'] : '';

            $current_user = Factory::getUser();
            $admin_email_default = isset($current_user->email) ? $current_user->email : '';

            $admin_email = isset($post['query_email']) ? $post['query_email'] : '';
            $admin_phone = isset($customerResult->admin_phone) ? $customerResult->admin_phone : '';
            $data1 = !isset($post['skip_feedback']) ? $radio . ' : ' . $data : 'Skipped the Feedback form.';

            require_once JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_joomlaotp' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'mo_otp_customer_setup.php';
            MoOtpCustomer::submit_feedback_form($admin_email,$admin_email_default,$admin_phone, $data1);
        
        require_once JPATH_SITE . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Installer' . DIRECTORY_SEPARATOR . 'Installer.php';

        if (isset($post['result'])) {
            foreach ($post['result'] as $fbkey) {
                $result = self::__getDBValuesUsingColumns('type', '#__extensions', $fbkey);
                $identifier = $fbkey;
                $type = 0;

                foreach ($result as $results) {
                    $type = $results;
                }
                if ($type) {
                    $cid = 0;
                    $installer = new Installer();
                    $installer->setDatabase(Factory::getDbo());
                    $installer->uninstall($type, $identifier, $cid);
                }
            }
        }
    }

    public static function get_com_extension_id(){
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('extension_id');
        $query->from('#__extensions');
        $query->where($db->quoteName('name') . " = " . $db->quote('LIB_MINIORANGEOTPPLUGIN_NAME'));
        $db->setQuery($query);
        return $db->loadColumn();
    }

    public static function __getDBValuesWColumn($columnName, $tableName)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select($columnName);
        $query->from($db->quoteName($tableName));
        $query->where($db->quoteName('id') . " = 1");
        $db->setQuery($query);
        return ($db->loadAssoc());
    }

    public static function __getDBValuesUsingColumns($type, $table, $fbkey)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select($type);
        $query->from($table);
        $query->where($db->quoteName('extension_id') . " = " . $db->quote($fbkey));
        $db->setQuery($query);
        return ($db->loadColumn());
    }

    public static function __getDBLoadResult($col_name, $table_name)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select($col_name);
        $query->from($db->quoteName($table_name));
        $query->where($db->quoteName('id') . " = 1");
        $db->setQuery($query);
        return ($db->loadResult());
    }

    public static function __getDBValuesArray($table)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select(array('*'));
        $query->from($db->quoteName($table));
        $query->where($db->quoteName('id') . " = 1");
        $db->setQuery($query);
        return ($db->loadAssoc());
    }

    public static function __genDBUpdate($db_table, $db_coloums)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        foreach ($db_coloums as $key => $value) {
            $database_values[] = $db->quoteName($key) . ' = ' . $db->quote($value);
        }

        $query->update($db->quoteName($db_table))->set($database_values)->where($db->quoteName('id') . " = 1");
        $db->setQuery($query);
        $db->execute();
    }

    //Storing OTP Transactions details in Database.
    public static function add_OTP_transaction($method, $user_email, $user_phone_number, $otp_sent, $otp_verified, $timestamp){

        $db = Factory::getDbo();
        $query =$db->getQuery(true);
        $fields =array(
            $db->quoteName('verification_method')    . ' = ' . $db->quote($method),
            $db->quoteName('user_email')    . ' = ' . $db->quote($user_email),
            $db->quoteName('user_phone')    . ' = ' . $db->quote($user_phone_number),
            $db->quoteName('otp_sent')    . ' = ' . $db->quote($otp_sent),
            $db->quoteName('otp_verified')    . ' = ' . $db->quote($otp_verified),
            $db->quoteName('timestamp')    . ' = ' . $db->quote($timestamp),
        );
        $query->insert($db->quoteName('#__miniorange_otp_transactions_report'))->set($fields);

        $db->setQuery($query);
        $db->execute();
    }

    public static function _get_all_otp_transaction_count(){
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('COUNT(*)');
        $query->from($db->quoteName('#__miniorange_otp_transactions_report'));
        $db->setQuery($query);
        $config = $db->loadResult();
        return $config;
    }

    public static function _get_otp_transaction_report_download()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__miniorange_otp_transactions_report'));
        $db->setQuery($query);
        $config = $db->loadAssocList();
        return $config;
    }

    public static function _get_otp_transaction_report($limit, $offset, $order="down")
    {
        $db = Factory::getDbo();
        $temp = array();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__miniorange_otp_transactions_report'));
        if($order=="down")
            $query->order('timestamp DESC');
        $query->setLimit($limit, $offset);
        $db->setQuery($query);
        $temp[] = $db->loadAssocList();
        return $temp;
    }

    public static function _get_otp_transaction_reports_val()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__miniorange_otp_transactions_report'));
        $db->setQuery($query);
        $attributes = $db->loadAssoc();
        return $attributes;
    }

    public static function _download_reports()
    {
        $data = self::_get_otp_transaction_report_download();
        $reports = Text::_('LIB_TNX_REPORT_COLUMNS');

        $i = 1;
        foreach ($data as $key => $value) {
            $timestamp = $value['timestamp'];
            $date = date('d-m-Y H:i:s', $timestamp);
            $reports .= $i . ',' . $value['verification_method'] . ',' . $value['user_email'] . ','
                . $value['user_phone'] . ',' . $value['otp_sent'] . ',' . $value['otp_verified'] .',' . $date . "\n";
            $i++;
        }

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="OTP Transaction Report.csv"');
        print_r($reports);
        exit();
    }

}