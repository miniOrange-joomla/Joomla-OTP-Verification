<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_joomlaotp
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
Use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Version;
use Joomla\CMS\Router\Route;
require_once JPATH_PLUGINS . '/user/miniorangesendotp/messages.php';

HTMLHelper::_('jquery.framework');

$document = Factory::getApplication()->getDocument();
$document->addScript(Uri::base() . 'components/com_joomlaotp/assets/js/mo_otp.js');
$document->addStyleSheet(Uri::base() . 'components/com_joomlaotp/assets/css/miniorange_otp.css');
$document->addStyleSheet(Uri::base() . 'components/com_joomlaotp/assets/css/miniorange_boot.css');
$document->addStyleSheet('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css');

jimport('miniorangeotpplugin.utility.commonOtpUtilities');

if (MoOtpUtility::is_curl_installed() == 0) { ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>


    <p class="mo_otp_red">(Warning: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP CURL
            extension</a> is not installed or disabled) Please go to Troubleshooting for steps to enable curl.</p>
    <?php
}
$otp_active_tab = 'account';
$app = Factory::getApplication();
$input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
$otp_active_tab = ($input && $input->get) ? $input->get->getArray() : [];
$otp_active_tab = isset($otp_active_tab['tab-panel']) ? $otp_active_tab['tab-panel'] : 'account';

?>  
    <div class="alert alert-info mo_boot_p-3 rounded">
        <h4 class="mo_boot_mb-2">
            <i class="fa fa-gift me-2 "></i>
            <?php echo Text::_('COM_MINIORANGE_PLUGIN_FREE_MESSAGE'); ?> <strong><?php echo Text::_('COM_MINIORANGE_PLUGIN_FREE_MESSAGE_TEXT'); ?></strong> <?php echo Text::_('COM_MINIORANGE_PLUGIN_FREE_MESSAGE_TEXT_2'); ?>.
        </h4>
        <p class="mo_boot_mb-0">
            <?php echo Text::_('COM_MINIORANGE_PURCHASE_OTP_TRANSACTIONS_MESSAGE'); ?> <a href="https://portal.miniorange.com/initializepayment?requestOrigin=joomla_otp_verification_plan">
            <?php echo Text::_('COM_MINIORANGE_PURCHASE_OTP_TRANSACTIONS'); ?>
        </a> <?php echo Text::_('COM_MINIORANGE_SMS_EMAIL_OTP_MESSAGE'); ?>
        </p>
    </div>
    <div class="container-fluid otp-container">
        <div class="mo_boot_row mo_boot_p-2 mo_boot_text-center mo_otp_head">
            <div class="mo_boot_col-sm-2">
                <h2>OTP</h2>
            </div>
            <div class="mo_boot_col-lg-5 mo_boot_offset-lg-5 mo_boot_col-sm-9 mo_boot_text-right my-auto">
                <button type="button" class="mo_boot_btn mo_btn_otp mo_boot_mr-2 mo_otp_btn_icon" onclick="window.open('<?php echo Uri::base().'index.php?option=com_joomlaotp&view=Licensing'?>','_self')">
                    <i class="fa fa-credit-card mo_otp_btn_icon"></i>
                    <span class="mo_otp_btn_text"><?php echo Text::_('COM_MINIORANGE_LICENSING_PLANS_BUTTON');?></span>
                </button>
                <button type="button" class="mo_boot_btn mo_btn_otp mo_otp_btn_icon mo_boot_mr-2" onclick="window.open('<?php echo Uri::base().'index.php?option=com_joomlaotp&view=accountsetup&tab-panel=request_demo'?>','_self')">
                    <i class="fa fa-life-ring mo_otp_btn_icon"></i>
                    <span class="mo_otp_btn_text"><?php echo Text::_('COM_MINIORANGE_SUPPORT_BUTTON');?></span>
                </button>
                <a class="mo_boot_btn mo_btn_otp mo_otp_btn_icon" href="index.php?option=com_joomlaotp&view=accountsetup&tab-panel=exportConfiguration">
                    <i class="fa fa-download mo_otp_btn_icon"></i>
                    <span class="mo_otp_btn_text"><?php echo Text::_('COM_MINIORANGE_EXPORT_IMPORT');?></span>
                </a>
            </div>
        </div>

        <div class="mo_boot_row">
            <div class="mo_boot_col-sm-2 otp-row">
                <div class="mo_boot_row">
              <?php
            $tabs = [
                ['id' => 'otp_tab_1', 'label' => Text::_('COM_JOOMLAOTP_TAB2_ACCOUNT_SETUP'), 'icon' => 'fa-user', 'active' => ($otp_active_tab == 'account')],
                ['id' => 'otp_tab_2', 'label' => Text::_('COM_JOOMLAOTP_TAB2_SETTINGS_SETUP'), 'icon' => 'fa-cogs', 'active' => ($otp_active_tab == 'setting')],
                ['id' => 'otp_tab_3', 'label' => Text::_('COM_JOOMLAOTP_TAB11_FORM_SETTINGS'), 'icon' => 'fa-file-alt', 'active' => ($otp_active_tab == 'custom_forms')],
                ['id' => 'otp_tab_4', 'label' => Text::_('COM_JOOMLAOTP_TAB4_MESSAGES'), 'icon' => 'fa-envelope', 'active' => ($otp_active_tab == 'custom_message')],
                ['id' => 'otp_tab_5', 'label' => Text::_('COM_JOOMLAOTP_TAB8_CONFIGURATION'), 'icon' => 'fa-wrench', 'active' => ($otp_active_tab == 'configuration')],
                ['id' => 'otp_tab_6', 'label' => Text::_('COM_JOOMLAOTP_ADDONS'), 'icon' => 'fa-plug', 'active' => ($otp_active_tab == 'addons')],
                ['id' => 'otp_tab_8', 'label' => Text::_('COM_JOOMLAOTP_TAB8_OTP_TRANSACTIONS'), 'icon' => 'fa-exchange-alt', 'active' => ($otp_active_tab == 'otp_report')],
                ['id' => 'otp_tab_7', 'label' => Text::_('COM_JOOMLAOTP_TAB7_DEMO'), 'icon' => 'fa-eye', 'active' => ($otp_active_tab == 'request_demo')]
            ];

            foreach ($tabs as $tab) {
                $activeClass = $tab['active'] ? 'mo_otp_tab-active' : 'mo_otp_tab-none';
                ?>
                <div onclick="mo_show_tab('<?php echo $tab['id']; ?>')" id="mo_<?php echo $tab['id']; ?>" class="mini_otp_tab mo_boot_col-sm-12 mo_boot_p-3 border-1 otp-tab <?php echo $activeClass; ?>">
                    <i class="fa-solid <?php echo $tab['icon']; ?>"></i>
                    <span class="mo_boot_px-2 tab-label"><?php echo $tab['label']; ?></span>
                </div>
                <?php
            }
            ?>     
        </div>
    </div>
    <div class="mo_boot_col-sm-10 mo_otp_log">
        <div class="mo_boot_row">

            <div class="mo_boot_col-sm-12 mo_otp_tab" id="otp_tab_1" style="<?php echo (($otp_active_tab=='account')?'display:block;':'display:none;');?>">
                <div class="mo_boot_row mo_boot_m-2 ">
                                <?php
                                $columnName          = array('login_status','registration_status');
                                $customer_details    = commonOtpUtilities::getCustomerDetails($columnName, '#__miniorange_otp_customer', 'loadObjectList', array('id' => 1,));
                                $login_status        = $customer_details[0]->login_status;
                                $registration_status = $customer_details[0]->registration_status;
                                $app = Factory::getApplication();
                                $input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
                                $get = ($input && $input->get) ? $input->get->getArray() : [];

                                if ($login_status) { //Show Login Page
                                    mo_otp_login_page();
                                } else { // Show Registration Page
                                    if ($registration_status == 'MO_OTP_DELIVERED_SUCCESS' || $registration_status == 'MO_OTP_VALIDATION_FAILURE' || $registration_status == 'MO_OTP_DELIVERED_FAILURE') {
                                        mo_otp_show_otp_verification();
                                    } else if (!commonOtpUtilities::is_customer_registered()) {
                                        mo_otp_registration_page();
                                    } else {
                                        mo_otp_account_page();
                                    }
                                }
                                ?>
                </div>
            </div>
            <div class="mo_boot_col-sm-12 mo_otp_tab" id="otp_tab_2"  style="<?php echo (($otp_active_tab=='setting')?'display:block;':'display:none;');?>">
                <div class="mo_boot_row mo_boot_p-3" >
                        <div class="mo_boot_col-sm-12">
                                <?php
                                if (!commonOtpUtilities::is_customer_registered()) { ?>
                                    <div class="mo_boot_text-center mo_boot_mb-3 mo_otp_warn_plugin">
                                        <?php echo Text::_('COM_WARNING_OF_PLUGIN_USING_WITHOUT_LOGIN_OR_REGISTER');?>
                                    </div>
                                    <div class="mo_boot_col-sm-12"></div>
                                    <?php
                                }
                                ?>
                                <div class="mo_boot_row">
                                    <div class="mo_boot_col-sm-12">
                                        <h2><?php echo Text::_('COM_MINIORANGE_OTP_REGISTRATION_METHOD_TITLE');?></h2>
                                    </div>
                                </div>
                                <div class="mo_boot_row">
                                    <div class="mo_boot_col-sm-12">
                                        <hr>
                                    </div>
                                </div>
                                <div class="mo_boot_row">
                                    <div class="mo_boot_col-sm-12">
                                    <?php
                                    $columnName          = array('login_status','registration_status');
                                    $customer_details    = commonOtpUtilities::getCustomerDetails($columnName, '#__miniorange_otp_customer', 'loadObjectList', array('id' => 1,));
                                    $login_status        = $customer_details[0]->login_status;
                                    $registration_status = $customer_details[0]->registration_status;
                                    mo_otp_settings_tab();
                                    ?>
                                </div>
                                </div>

                            <div class="mo_boot_col-sm-12">
                                <br>
                            </div>
                        </div>

                        <div class="mo_boot_col-sm-12">
                                        <form action="<?php echo Route::_('index.php?option=com_joomlaotp&view=accountsetup&task=accountsetup.saveDomainBlocks'); ?>" method="post" name="adminForm" id="otp_form">
                                            <input id="mo_otp_blocked_email_domains" type="hidden" name="option9" value="mo_domain_block"/>
                                            <input id="mo_otp_allowed_email_domains" type="hidden" name="option9" value="mo_domain_allow"/>
                                            <input id="reg_restriction" type="hidden" name="option9" value="mo_domain_allow"/>

                                            <?php
                                            $result         = commonOtpUtilities::__getDBValuesWOArray('#__miniorange_otp_customer');
                                            $white_or_black = isset($result['white_or_black']) ? $result['white_or_black'] : 0;
                                            $allowed_emails = isset($result['mo_otp_allowed_email_domains']) ? $result['mo_otp_allowed_email_domains'] : 0;
                                            $reg_restr      = isset($result['reg_restriction']) ? $result['reg_restriction'] : 0;
                                            ?>
                                            <div class="mo_boot_row">
                                                <div class="mo_boot_col-sm-12">
                                                <h2 style="padding-left: 0 !important;"><?php echo Text::_('COM_MINIORANGE_DOMAIN_RESTRICTION_TITLE');?></h2><hr>
                                                </div>
                                            </div>

                                            <div class="mo_boot_row">
                                                <div class="mo_boot_col-sm-12">
                                                    <?php if (commonOtpUtilities::is_customer_registered()) $disabled = true; else $disabled = false;?>
                                                    <strong>
                                                        <input class="mo_boot_mr-2" id="reg_restriction_for_email" name="reg_restriction" class="reg_restriction_for_email" type="checkbox" value="1"
                                                            <?php  if($disabled) {echo "enabled";}else{ echo "disabled";}?> <?php if ($reg_restr==1){ echo "checked";} ?>><?php echo Text::_('COM_MINIORANGE_DOMAIN_RESTRICTION_INFO');?>
                                                    </strong>
                                                </div>
                                            </div>
                                            <div class="mo_boot_row mo_boot_mt-3" id="otp_settings">
                                                <div class="mo_boot_col-sm-4">
                                                    <strong><?php echo Text::_('COM_MINIORANGE_EMAIL_DOMAINS');?></strong>
                                                </div>
                                                <div class="mo_boot_col-sm-6" style="padding-left: 0 !important;">
                                                    <textarea rows="3" cols="55" id="mo_otp_allowed_email_domains" name="mo_otp_allowed_email_domains" style="border-radius: 3px; border: 1px solid rgb(134, 131, 131) !important;resize: vertical;width: 100% !important;"
                                                    class="mo_otp_allowed_email_domains mo_boot_textarea-control" required placeholder="<?php echo Text::_('COM_MINIORANGE_EMAIL_DOMAINS_PLACEHOLDER');?>"
                                                    onkeyup="nospaces(this);" disabled><?php echo $allowed_emails; ?></textarea>
                                                </div>
                                            </div>
                                            <div class="mo_boot_row mo_boot_mt-2">
                                                <div class="mo_boot_col-sm-4 mo_boot_offset-sm-4">
                                                    <?php if (commonOtpUtilities::is_customer_registered()) $disabled = true; else $disabled = false;?>
                                                    <strong>
                                                        <input type="radio" checked id="white_or_black" name="white_or_black" value="1" class="white_or_black mo_otp_radiobox_style" <?php if ($white_or_black == 1) echo "checked"; ?> disabled>
                                                        <?php echo Text::_('COM_MINIORANGE_ALLOWED_EMAIL_DOMAINS');?>
                                                    </strong>
                                                </div>
                                                <div class="mo_boot_col-sm-4">
                                                    <strong>
                                                        <input type="radio" id="white_or_black" name="white_or_black" value="2" class="white_or_black mo_otp_radiobox_style" <?php if ($white_or_black == 2) echo "checked"; ?> disabled>
                                                        <?php echo Text::_('COM_MINIORANGE_BLOCKED_EMAIL_DOMAINS');?>
                                                    </strong>
                                                </div><br>
                                            </div>
                                            <div class="mo_boot_row">
                                                <div class="mo_boot_col-sm-12 mo_boot_text-center mo_boot_mt-3">
                                                    <input type="submit" <?php if ($disabled) echo "enabled";else echo "disabled"; ?> name="save" class="mo_boot_btn  mo_boot_btn-primary mo_otp_btns" value="<?php echo Text::_('COM_MINIORANGE_SAVE_SETTINGS_BUTTON');?>">
                                                </div>
                                            </div><br>

                                            <script type="text/javascript">

                                                jQuery(document).ready(function () {
                                                    if(jQuery('#reg_restriction_for_email').prop('checked'))
                                                    {
                                                        jQuery('.white_or_black').prop('disabled',false);
                                                        jQuery('.mo_otp_allowed_email_domains').prop('disabled',false);

                                                    }

                                                    jQuery('#reg_restriction_for_email').click(function () {

                                                        if(jQuery('#reg_restriction_for_email').prop('checked'))
                                                        {
                                                            jQuery('.white_or_black').prop('disabled',false);
                                                            jQuery('.mo_otp_allowed_email_domains').prop('disabled',false);
                                                        }
                                                        else{
                                                            jQuery('.white_or_black').prop('disabled',true);
                                                            jQuery('.mo_otp_allowed_email_domains').prop('disabled',true);
                                                        }
                                                    });
                                                });
                                            </script>
                                        </form>
                                </div>

                        <div class="mo_boot_col-sm-12">
                            <?php get_country_code_dropdown(); ?>
                        </div>
                        <div class="mo_boot_col-sm-12">
                            <?php __block_country_code(); ?>
                        </div>

                </div>
            </div>

            <div class="mo_boot_col-sm-12 mo_otp_tab" id="otp_tab_3" style="<?php echo (($otp_active_tab=='custom_forms')?'display:block;':'display:none;');?>">
                <div class="mo_boot_col-sm-12 ">
                    <?php echo get_rs_form(); ?>
                </div>
            </div>

            <div class="mo_boot_col-sm-12 mo_otp_tab" id="otp_tab_4" style="<?php echo (($otp_active_tab=='custom_message')?'display:block;':'display:none;');?>">
                <div class="mo_boot_col-sm-12 mo_boot_p-3">
                    <?php
                    if (!commonOtpUtilities::is_customer_registered()) { ?>
                        <div class="mo_boot_text-center mo_boot_mb-3 mo_otp_warn_plugin">
                            <?php echo Text::_('COM_WARNING_OF_PLUGIN_USING_WITHOUT_LOGIN_OR_REGISTER');?>
                        </div>
                        <div class="mo_boot_col-sm-12"></div>
                        <?php
                    }
                    ?>
                    <div class="mo_boot_row mo_boot_mt-3 mo_boot_text-center">
                        <div class="mo_boot_col-sm-12">
                        <h2><?php echo Text::_('COM_MINIORANGE_CUSTOM_MESSAGES_TITLE');?></h2>
                        </div>
                    </div>
                    <div class="mo_boot_row">
                        <div class="mo_boot_col-sm-12">
                            <hr>
                        </div>
                    </div>
                    <div class="mo_boot_row mo_boot_mt-4">
                        <div class="mo_boot_col-sm-12">
                            <?php _custom_email_messages();?>
                        </div>
                        <div class="mo_boot_col-sm-12">
                            <?php _custom_phone_messages(); ?>
                        </div>

                        <div class="mo_boot_col-sm-12">
                            <?php _custom_common_otp_messages(); ?>
                        </div>
                    </div>
                </div>

            </div>

            <div class="mo_boot_col-sm-12 mo_otp_tab" id="otp_tab_5" style="<?php echo (($otp_active_tab=='configuration')?'display:block;':'display:none;');?>">
                <div class="mo_boot_row mo_boot_m-3">
                    <div class="mo_boot_col-sm-12">
                        <h2><?php echo Text::_('COM_MINIORANGE_CONFIG_OTP_PREFERENCES');?></h2><hr>
                    </div>
                </div>

                <div class="mo_boot_row mo_boot_m-3">
                    <div class="mo_boot_col-sm-4">
                        <p><strong><?php echo Text::_('COM_MINIORANGE_OTP_LENGTH');?></strong></p>
                    </div>
                    <div class=" mo_boot_col-lg-8 mo_boot_col-sm-8">
                        <a class="mo_click_here mo_otp_table_layout_1" href="https://faq.miniorange.com/knowledgebase/change-length-otp/" target="blank">
                            <?php echo Text::_('COM_MINIORANGE_OTP_LENGTH_GUIDE_LINK');?>
                        </a>
                    </div>
                </div><br>

                <div class="mo_boot_row mo_boot_m-3">
                    <div class="mo_boot_col-sm-4">
                        <p><strong><?php echo Text::_('COM_MINIORANGE_OTP_VALIDITY');?></strong></p>
                    </div>
                    <div class=" mo_boot_col-lg-8 mo_boot_col-sm-8">
                        <a class="mo_click_here mo_otp_table_layout_1" href="https://faq.miniorange.com/knowledgebase/change-time-otp-stays-valid/" target="blank">
                            <?php echo Text::_('COM_MINIORANGE_OTP_VALIDITY_GUIDE_LINK');?>
                        </a>
                    </div>
                </div>

                    <div class="mo_boot_col-sm-12 mo_boot_m-3">
                        <div class="mo_boot_row">
                            <div class="mo_boot_col-sm-12 ">
                                <h2><?php echo Text::_('COM_MINIORANGE_SMS_AND_EMAIL_CONFIG');?></h2>
                            </div>
                        </div><hr>
                        <div class="mo_boot_row">
                            <div class=" mo_boot_col-sm-11  mo_otp_verification_highlight_background_note  mo_boot_ml-3">
                                <?php echo Text::_('COM_MINIORANGE_SMS_AND_EMAIL_CONFIG_DETAILS');?>
                            </div>
                        </div>
                        <div class="mo_boot_row">
                            <div class=" mo_boot_col-sm-11">
                                <br><strong><?php echo Text::_('COM_MINIORANGE_SMS_CAPS');?></strong><br>
                                1. <a class="mo_click_here" target="blank" href="https://login.xecurify.com/moas/login?redirectUrl=https://login.xecurify.com/moas/admin/customer/showsmstemplate"><?php echo Text::_('COM_MINIORANGE_SMS_CUSTOM_TEMPLATE');?></a><?php echo Text::_('COM_MINIORANGE_SMS_CHANGE_TEXT');?><br>
                                2. <a class="mo_click_here" href='#!faqa1' onclick='info2()'><?php echo Text::_('COM_MINIORANGE_SMS_CUSTOM_GATEWAY');?></a><?php echo Text::_('COM_MINIORANGE_SMS_CUSTOM_GATEWAY_DETAIL');?>
                                <div id='faqa1' style='display:none'>
                                    <?php echo Text::_('COM_MINIORANGE_SMS_CUSTOM_GATEWAY_COLLAPSE_INFO');?>
                                </div>
                                <br><br><strong><?php echo Text::_('COM_MINIORANGE_EMAIL_CAPS');?></strong><br>
                                1. <a class="mo_click_here" target="blank" href="https://login.xecurify.com/moas/login?redirectUrl=https://login.xecurify.com/moas/admin/customer/staticwelcomeemailtemplate">
                                    <?php echo Text::_('COM_MINIORANGE_EMAIL_CUSTOM_TEMPLATE');?></a><?php echo Text::_('COM_MINIORANGE_EMAIL_CHANGE_TEXT');?><br>
                                2. <a class="mo_click_here" href='#!faqa2' onclick='info3()'><?php echo Text::_('COM_MINIORANGE_EMAIL_CUSTOM_GATEWAY');?></a><?php echo Text::_('COM_MINIORANGE_EMAIL_CUSTOM_GATEWAY_DETAIL');?>

                                <div id='faqa2'  style='display:none'>
                                    <?php echo Text::_('COM_MINIORANGE_EMAIL_CUSTOM_GATEWAY_COLLAPSE_INFO');?>
                                </div>
                            </div>
                        </div>

                            <br>
                        <div class="mo_boot_row">
                        <div class=" mo_boot_col-sm-11 mo_otp_verification_highlight_background_note  mo_boot_ml-3">
                                <strong><a class="mo_click_here" href='#!faqb1' onclick='info4()'><?php echo Text::_('COM_MINIORANGE_CHANGE_SENDERID_OR_NUMBER_OF_SMS');?></a></strong>
                                <?php echo Text::_('COM_MINIORANGE_CHANGE_SENDERID_INFO');?>
                            </div>
                        </div>
                            <br>
                        <div class="mo_boot_row">
                        <div class=" mo_boot_col-sm-11">
                                <div id="faqb1" style='display:none'>
                                    <ol>
                                        <?php echo Text::_('COM_MINIORANGE_CHANGE_SENDERID_COLLAPSE_INFO');?>
                                    </ol>
                                </div>
                            </div>
                        </div>
                            <br>
                        <div class="mo_boot_row">
                            <div class=" mo_boot_col-sm-11 mo_otp_verification_highlight_background_note  mo_boot_ml-3">
                                <strong><a class="mo_click_here" href='#!faqb2' onclick='info5()'><?php echo Text::_('COM_MINIORANGE_CHANGE_SENDER_EMAIL');?></a></strong>
                                <?php echo Text::_('COM_MINIORANGE_CHANGE_SENDER_EMAIL_INFO');?>
                            </div>
                        </div>
                            <br>
                        <div class="mo_boot_row">
                        <div class=" mo_boot_col-sm-11">
                                <div id="faqb2" style='display:none'>
                                    <ol type="1">
                                        <?php echo Text::_('COM_MINIORANGE_CHANGE_SENDER_EMAIL_COLLAPSE_INFO');?>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <div class="mo_boot_col-sm-12 mo_otp_tab" id="otp_tab_6" style="<?php echo (($otp_active_tab=='addons')?'display:block;':'display:none;');?>">
                <div class="mo_boot_row mo_boot_mt-3">
                    <?php echo showAddonsContent(); ?>
                </div>
            </div>

            <div class="mo_boot_col-sm-12 mo_otp_tab" id="otp_tab_7" style="<?php echo (($otp_active_tab=='request_demo')?'display:block;':'display:none;');?>">
                <div class="mo_boot_row justify-content-center ">
                    <div class="mo_boot_col-sm-12 mo_boot_text-center mo_boot_mt-3">
                        <h2><?php echo Text::_('COM_MINIORANGE_SUPPORT_TITLE');?></h2>
                    </div>
                    <div class="mo_boot_col-sm-12 ">
                        <hr>
                    </div>
                    <div class="mo_boot_col-sm-12 ">
                        <?php
                        echo mo_otp_support();
                        ?>
                    </div>
                    <div class="mo_boot_col-sm-12">
                        <?php
                        echo JoomlaOtpViewAccountSetup::mo_user_support();
                        ?>
                    </div>
                </div>
            </div>

            <div class="mo_boot_col-sm-12 mo_otp_tab" id="otp_tab_8" style="<?php echo (($otp_active_tab=='otp_report')?'display:block;':'display:none;');?>">
                <div class="mo_boot_row mo_boot_p-3">
                    <?php
                    if (commonOtpUtilities::is_customer_registered()) $disabled="enabled";
                    else $disabled="disabled";
                    $base_url = Uri::base().'index.php?option=com_joomlaotp&task=accountsetup.joomlapagination';
                    ?>
                    <div class="mo_boot_col-sm-12">
                        <?php
                        if (!commonOtpUtilities::is_customer_registered()) { ?>
                            <div class="mo_boot_text-center mo_boot_mb-3 mo_otp_warn_plugin">
                                <?php echo Text::_('COM_WARNING_OF_PLUGIN_USING_WITHOUT_LOGIN_OR_REGISTER');?>
                            </div>
                            <div class="mo_boot_col-sm-12"></div>
                            <?php
                        }
                        ?>
                        <h3 class=" mo_boot_mt-3"><?php echo Text::_('COM_MINIORANGE_OTP_TNX_REPORT_TITLE');?></h3><hr>
                        <div class="mo_boot_row">
                            <div class="mo_boot_col-sm-8 mo_boot_col-lg-5 mo_otp_trans_report">
                                <select class="mo_boot_form-control mo_boot_m-1 mo_otp_report_no " id="select_number" onchange="list_of_entry()" <?php echo $disabled;?>>
                                    <option value="10" selected>10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <input type="text" id="search_text" class="mo_boot_form-control mo_otp_search mo_boot_m-1" onkeyup="search()" placeholder="<?php echo Text::_('COM_MINIORANGE_OTP_SEARCH');?>"<?php echo $disabled;?> />
                            </div>
                            <div class="mo_boot_col-sm-12 mo_boot_col-lg-7 mo_boot_text-right mo_otp_report_desc">
                                <form name="mo_ip_login" method="post" id="jnsp_clear_values" action="<?php echo Route::_('index.php?option=com_joomlaotp&task=accountsetup.otp_reports'); ?>">
                                    <div class="mo_boot_row">
                                        <div class="mo_boot_col-sm-12">
                                            <input type="submit" name="refresh_page" class="mo_boot_btn  mo_boot_btn-primary mo_otp_btns" value="<?php echo Text::_('COM_MINIORANGE_REFRESH_BUTTON');?>" <?php echo $disabled;?> >
                                            <input type="submit" name="clear_val" class="mo_boot_btn mo_boot_btn-danger" value="<?php echo Text::_('COM_MINIORANGE_CLEAR_BUTTON');?>" onclick="ClearReports();" <?php echo $disabled;?> >
                                            <input type="submit" name="download_reports" class="mo_boot_btn  mo_boot_btn-primary mo_otp_btns" value="<?php echo Text::_('COM_MINIORANGE_DOWNLOAD_BUTTON');?>" <?php echo $disabled;?> >
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div><br>
                        <script>
                            function ClearReports(){
                                if(confirm("<?php echo Text::_('COM_MINIORANGE_CLEAR_POPUP');?>")){
                                    jQuery('clear_val').attr('value', '<?php echo Text::_('COM_MINIORANGE_CLEAR_BUTTON');?>');
                                }
                                else {
                                    jQuery('#jnsp_clear_values').submit(function() {
                                        return false;
                                    });
                                }
                            }
                        </script>
                        <div id="show_paginations"></div>
                        <input type="hidden" id="next_page" value="0"><br>
                        <div>
                            <div id="next_btn">
                                <input type="submit" name="mo_next" class="mo_boot_btn  mo_boot_btn-primary mo_otp_report_btn mo_otp_btns mo_boot_m-1"
                                        onclick="next_or_prev_page('next','preserve');" value="Next">
                            </div>
                            <div id="pre_btn">
                                <input type="submit" name="mo_next" class="mo_boot_btn  mo_boot_btn-primary mo_boot_m-1 mo_otp_report_btn mo_otp_btns"
                                        onclick="next_or_prev_page('pre','preserve');" value="Prev">
                            </div>
                        </div>
                       <script>
                            jQuery(document).ready(function (){
                                next_or_prev_page('next');
                            });

                            function list_of_entry(){
                                no_of_entry=jQuery("#select_number").val();
                                next_or_prev_page('on');
                            }
                            function sort(button){
                                var order ="";
                                if(clock)
                                {
                                    clock = 0;
                                    order = 'up';
                                }
                                else
                                {
                                    clock = 1;
                                    order = 'down';
                                }
                                next_or_prev_page(button,order);
                            }

                            function search()
                            {
                                var value="";
                                value=jQuery("#search_text").val().toLowerCase();
                                jQuery("#myTable tbody tr").filter(function() {
                                    jQuery(this).toggle(jQuery(this).text().toLowerCase().indexOf(value) > -1)
                                });
                            }
                            function next_or_prev_page(button , order='down') {
                                var page = document.getElementById('next_page').value;
                                var orderBY='down';
                                if(button =='on')
                                    page=0;
                                if(order == 'up')
                                    orderBY='up';
                                if(order =='preserve'){
                                    orderBY = clock===1?'down':'up';
                                }
                                page = parseInt(page);
                                if (button == 'pre' && page != 0) {
                                    page -= 2;
                                    document.getElementById('next_page').value = page;
                                    document.getElementById('next_btn').style.display = "inline";
                                }
                                if (page == 0) {
                                    document.getElementById('pre_btn').style.display = "none";
                                    document.getElementById('next_btn').style.display = "inline";
                                }
                                else
                                    document.getElementById('pre_btn').style.display = "inline";

                                jQuery.ajax({
                                    url: '<?php echo $base_url; ?>',
                                    dataType: "text",
                                    method: "POST",
                                    data: {'page': page,'orderBY':orderBY,'no_of_entry':no_of_entry},
                                    success: function (data) {
                                        var arr = data.split("separator_for_count");
                                        jQuery("#show_paginations").html(arr[0]);
                                        if (arr[1] === 0) {
                                            document.getElementById('next_page').value = 0;
                                            next_or_prev_page('next','preserve');
                                        }
                                    }
                                });
                                page += 1;
                                document.getElementById('next_page').value = page;
                            }
                        </script>
                    </div>
                </div>
            </div>
        </div>
        <div id="exportConfiguration" class="mo_boot_col-sm-12 mo_otp_tab" style="<?php echo (($otp_active_tab == 'exportConfiguration') ? 'display:block;':'display:none;'); ?>">
            <div class="mo_boot_row">
                <div class="mo_boot_col-sm-12" >
                    <?php exportConfiguration();?>
                </div>
            </div>
        </div>
</div>
</div>
</div>

<?php

function _custom_email_messages()
{
    $messages = unserialize(MO_MESSAGES);

    $otp_sent      = isset($messages['OTP_SENT_EMAIL']) ? $messages['OTP_SENT_EMAIL'] : '';
    $otp_error     = isset($messages['ERROR_OTP_EMAIL']) ? $messages['ERROR_OTP_EMAIL'] : '';
    $email_blocked = isset($messages['ERROR_EMAIL_BLOCKED']) ? $messages['ERROR_EMAIL_BLOCKED'] : '';
    $email_format  = isset($messages['EMAIL_FORMAT']) ? $messages['EMAIL_FORMAT'] : '';

    $result                       = commonOtpUtilities::_get_custom_message();
    $custom_success_email_message = isset($result['mo_custom_email_success_message']) && !empty($result['mo_custom_email_success_message']) ? $result['mo_custom_email_success_message'] : $otp_sent;
    $error_otp_message            = isset($result['mo_custom_email_error_message']) && !empty($result['mo_custom_email_error_message']) ? $result['mo_custom_email_error_message'] : $otp_error;
    $invalid_format               = isset($result['mo_custom_email_invalid_format_message']) && !empty($result['mo_custom_email_invalid_format_message']) ? $result['mo_custom_email_invalid_format_message'] : $email_format;
    $blocked_email_message        = isset($result['mo_custom_email_blocked_message']) && !empty($result['mo_custom_email_blocked_message']) ? $result['mo_custom_email_blocked_message'] : $email_blocked;

    if (commonOtpUtilities::is_customer_registered()) $disabled = "enabled";
    else $disabled = "disabled";
    ?>
    <div class="mo_boot_row">
        <div class="mo_boot_col-sm-12">
            <form action="<?php echo Route::_('index.php?option=com_joomlaotp&view=accountsetup&task=accountsetup.saveCustomMessage'); ?>" method="post" name="custom_message">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12">
                        <details class="mo_otp_bg_white">
                            <div class="mo_boot_row mo_boot_mx-1">
                                <div class="mo_boot_col-sm-12 mo_boot_mt-3">
                                    <details open>
                                        <br><p class="mo_otp_msg_note"><?php echo Text::_('COM_MINIORANGE_EMAIL_MESSAGES_NOTE');?></p>
                                        <div class="mo_boot_col-sm-12">
                                            <textarea name="mo_custom_email_success_message_send" class="mo_textarea_css mo_otp_custom_msg" cols="52" mo_boot_rows="5" <?php echo $disabled; ?>><?php echo $custom_success_email_message; ?></textarea>
                                        </div>
                                        <summary>
                                            <?php echo Text::_('COM_MINIORANGE_SUCCESS_OTP_MESSAGE');?>
                                        </summary>
                                    </details>
                                </div>

                                <div class="mo_boot_col-sm-12 mo_boot_mt-4">
                                    <details>
                                        <br>
                                        <div class="mo_boot_col-sm-12">
                                            <textarea name="mo_custom_email_error_message" class="mo_textarea_css mo_otp_custom_msg" cols="52" mo_boot_rows="5" <?php echo $disabled; ?>><?php echo $error_otp_message; ?></textarea>
                                        </div>
                                        <summary>
                                            <?php echo Text::_('COM_MINIORANGE_ERROR_OTP_MESSAGE');?>
                                        </summary>
                                    </details>
                                </div>

                                <div class="mo_boot_col-sm-12 mo_boot_mt-4">
                                    <details>
                                        <br>
                                        <p class="mo_otp_msg_note"><?php echo Text::_('COM_MINIORANGE_EMAIL_MESSAGES_NOTE');?></p>
                                        <div class="mo_boot_col-sm-12">
                                            <textarea name="mo_custom_email_invalid_format_message" class="mo_textarea_css mo_otp_custom_msg" cols="52" mo_boot_rows="5" <?php echo $disabled; ?>><?php echo $invalid_format; ?></textarea>
                                        </div>
                                        <summary>
                                            <?php echo Text::_('COM_MINIORANGE_INVALID_FORMAT_MESSAGE');?>
                                        </summary>
                                    </details>
                                </div>

                                <div class="mo_boot_col-sm-12 mo_boot_mt-4">
                                    <details>
                                        <br>
                                        <div class="mo_boot_col-sm-12">
                                            <textarea name="mo_custom_email_blocked_message" class="mo_textarea_css mo_otp_custom_msg" cols="52"mo_boot_rows="5" <?php echo $disabled; ?>><?php echo $blocked_email_message; ?></textarea>
                                        </div>
                                        <summary>
                                            <?php echo Text::_('COM_MINIORANGE_BLOCKED_EMAIL_MESSAGE');?>
                                        </summary>
                                    </details>
                                </div>
                            </div>
                            <div class="mo_boot_col-sm-12 mo_boot_text-center mo_boot_mt-2">
                                <input type="submit" class="mo_boot_btn  mo_boot_btn-primary mo_otp_btns" name="custom_email_messages" value="<?php echo Text::_('COM_MINIORANGE_SAVE_SETTINGS_BUTTON');?>" <?php echo $disabled;?> />
                            </div>
                            <summary>
                                <?php echo Text::_('COM_MINIORANGE_EMAIL_MESSAGES');?>
                            </summary>
                        </details>
                    </div>
                </div>
            </div>
            </form>
        </div>
    <?php
}

function get_rs_form()
{
    if (commonOtpUtilities::is_customer_registered()) $disabled = "enabled"; else $disabled = "disabled";
    $result         = commonOtpUtilities::__getDBValuesWOArray('#__miniorange_otp_customer');
    $rs_form_configuration = isset($result['rs_form_field_configuration']) ? json_decode($result['rs_form_field_configuration']) : array();
    $rs_form_count = isset($result['rs_form_count']) ? ($result['rs_form_count']==0)?1: $result['rs_form_count'] : 1;
    ?>

    <div class="mo_boot_row mo_otp_rs">
        <div class="mo_boot_col-sm-12 mo_boot_p-3">
            <?php
            if (!commonOtpUtilities::is_customer_registered()) { ?>
                <div class="mo_boot_text-center mo_boot_mb-3 mo_otp_warn_plugin">
                    <?php echo Text::_('COM_WARNING_OF_PLUGIN_USING_WITHOUT_LOGIN_OR_REGISTER');?>
                </div>
                <?php
            }
            ?>
            <form action="<?php echo Route::_('index.php?option=com_joomlaotp&view=accountsetup&task=accountsetup.saveRSfield'); ?>" method="post" name="rs_Form" id="rs_form">
                <div class="mo_boot_row mo_boot_mt-3">
                    <div class=" mo_boot_col-sm-5  mo_boot_col-lg-8">
                        <h2><?php echo Text::_('COM_MINIORANGE_RS_FORM_TITLE');?></h2>
                    </div>
                    <div class="mo_boot_col-sm-6 mo_boot_col-lg-4 mo_boot_text-right">
                        <input type="button" class="mo_boot_btn  mo_boot_btn-primary mo_otp_btns" value="<?php echo Text::_('COM_MINIORANGE_RS_FORM_ADD_FORM_BUTTON');?>" onclick="add_new_form();" <?php echo $disabled;?> >
                        <input type="button" class="mo_boot_btn mo_boot_btn-danger " value="<?php echo Text::_('COM_MINIORANGE_RS_FORM_REMOVE_FORM_BUTTON');?>" onclick="remove_new_form();" <?php echo $disabled;?> >
                    </div>

                </div>
                <hr>
                <?php echo Text::_('COM_MINIORANGE_RS_FORM_DETAILS');?><br><br>
                <?php echo Text::_('COM_MINIORANGE_RS_FORM_DETAILS1');?><br>

                <input type="hidden" name="form_count" id="form_count" class="mo_boot_form-control" value="<?php echo $rs_form_count ?>">
                <?php
                $counter=0;
                $form_name_count=$counter+1;
                if(!empty($rs_form_configuration))
                {
                    foreach($rs_form_configuration as $key=> $values)
                    {
                        ?>
                        <div class="rs_forms">
                            <div class='mo_boot_col-sm-12'  id='form_<?php echo $form_name_count?>'>
                                <h3><?php echo Text::_('COM_MINIORANGE_RS_FORM');?><?php echo $form_name_count?></h3>
                                <div class="mo_boot_row mo_boot_mt-3">
                                    <div class="mo_boot_col-sm-3 mo_boot_col-lg-2">
                                        <p><strong><?php echo Text::_('COM_MINIORANGE_RS_FORM_ID');?></strong></p>
                                    </div>
                                    <div class="mo_boot_col-sm-3 mo_boot_col-lg-3">
                                        <input type="number" name="rs_form_id<?php echo $counter?>" id="rs_form_id" class="mo_boot_form-control" value="<?php echo $key;?>" <?php echo $disabled;?> required>
                                    </div>
                                    <div class="mo_boot_col-sm-3 mo_boot_offset-lg-2 mo_boot_col-lg-2">
                                        <p><strong><?php echo Text::_('COM_MINIORANGE_RS_EMAIL_ID');?></strong></p>
                                    </div>
                                    <div class="mo_boot_col-sm-3 mo_boot_col-lg-3">
                                        <input type="text" name="email_id<?php echo $counter?>" id="email_id" class="mo_boot_form-control" value="<?php echo $values[0];?>" <?php echo $disabled;?> required>
                                    </div>
                                </div>
                                <div class="mo_boot_row mo_boot_mt-3">
                                    <div class="mo_boot_col-sm-3 mo_boot_col-lg-2">
                                        <p><strong><?php echo Text::_('COM_MINIORANGE_RS_PHONE_NUMBER');?></strong></p>
                                    </div>
                                    <div class="mo_boot_col-sm-3 mo_boot_col-lg-3">
                                        <input type="text" name="contact_no<?php echo $counter?>" id="contact_no" class="mo_boot_form-control" value="<?php echo $values[1];?>" <?php echo $disabled;?> >
                                    </div>
                                    <div class="mo_boot_col-sm-3 mo_boot_offset-lg-2 mo_boot_col-lg-2">
                                        <p><strong><?php echo Text::_('COM_MINIORANGE_RS_PASSWORD');?></strong></p>
                                    </div>
                                    <div class="mo_boot_col-sm-3 mo_boot_col-lg-3">
                                        <input type="text" name="rs_password<?php echo $counter?>" id="rs_password" class="mo_boot_form-control" value="<?php echo $values[2];?>" <?php echo $disabled;?> >
                                    </div>
                                </div>
                            </div><br>
                        </div>
                        <?php $counter++;
                        $form_name_count++;
                    }}
                else
                { ?>
                    <div class="rs_forms">
                        <div class='mo_boot_col-sm-12'  id='form_<?php echo $form_name_count?>'>
                            <h3><?php echo Text::_('COM_MINIORANGE_RS_FORM');?><?php echo $form_name_count?></h3>
                            <div class="mo_boot_row mo_boot_mt-3">
                                <div class="mo_boot_col-sm-3 mo_boot_col-lg-2">
                                    <p><strong><?php echo Text::_('COM_MINIORANGE_RS_FORM_ID');?></strong></p>
                                </div>
                                <div class="mo_boot_col-sm-3">
                                    <input type="number" name="rs_form_id<?php echo $counter?>" id="rs_form_id" class="mo_boot_form-control" value="" <?php echo $disabled;?> required>
                                </div>
                                <div class="mo_boot_col-sm-3 mo_boot_offset-lg-2 mo_boot_col-lg-2">
                                    <p><strong><?php echo Text::_('COM_MINIORANGE_RS_EMAIL_ID');?></strong></p>
                                </div>
                                <div class="mo_boot_col-sm-3">
                                    <input type="text" name="email_id<?php echo $counter?>" id="email_id" class="mo_boot_form-control" <?php echo $disabled;?> value="" required>
                                </div>
                            </div>
                            <div class="mo_boot_row mo_boot_mt-3">
                                <div class="mo_boot_col-sm-3 mo_boot_col-lg-2">
                                    <p><strong><?php echo Text::_('COM_MINIORANGE_RS_PHONE_NUMBER');?></strong></p>
                                </div>
                                <div class="mo_boot_col-sm-3">
                                    <input type="text" name="contact_no<?php echo $counter?>" id="contact_no" class="mo_boot_form-control" <?php echo $disabled;?> value="">
                                </div>
                                <div class="mo_boot_col-sm-3 mo_boot_offset-lg-2 mo_boot_col-lg-2">
                                    <p><strong><?php echo Text::_('COM_MINIORANGE_RS_PASSWORD');?></strong></p>
                                </div>
                                <div class="mo_boot_col-sm-3">
                                    <input type="text" name="rs_password<?php echo $counter?>" id="rs_password" class="mo_boot_form-control" <?php echo $disabled;?> value="" >
                                </div>
                            </div>
                        </div><br>
                    </div>
                <?php }?>
                <div class="mo_boot_col-sm-12 mo_boot_text-center mo_boot_mt-3">
                    <input type="submit" <?php echo $disabled;?> name="save_rs_form" class="mo_boot_btn  mo_boot_btn-primary mo_otp_btns" value="<?php echo Text::_('COM_MINIORANGE_SAVE_BUTTON');?>">
                </div>

            </form>
        </div>
    </div>
    <script>
        function add_new_form()
        {
            var countnoForms= jQuery('#form_count').val();
            var formno=countnoForms;
            formno++;
            var forms="<br><div class='rs_forms'><div class='mo_boot_col-sm-12' id='form_"+formno+"'><h3><?php echo Text::_('COM_MINIORANGE_RS_FORM');?> "+formno+"</h3>"+
                "<div class='mo_boot_row mo_boot_mt-3'>"+
                "<div class='mo_boot_col-sm-2'><p><strong><?php echo Text::_('COM_MINIORANGE_RS_FORM_ID');?></strong></p></div>"+
                "<div class='mo_boot_col-sm-3'><input type='number' name='rs_form_id"+countnoForms+"' id='rs_form_id"+countnoForms+"' class='mo_boot_form-control' value='' required></div>"+
                "<div class='mo_boot_col-sm-3 mo_boot_offset-lg-2 mo_boot_col-lg-2'><p><strong><?php echo Text::_('COM_MINIORANGE_RS_EMAIL_ID');?></strong></p></div>"+
                "<div class='mo_boot_col-sm-3'><input type='text' name='email_id"+countnoForms+"' id='email_id1"+countnoForms+"' class='mo_boot_form-control' value='' required></div>"+
                "</div>"+
                "<div class='mo_boot_row mo_boot_mt-3'>"+
                "<div class='mo_boot_col-sm-2'><p><strong><?php echo Text::_('COM_MINIORANGE_RS_PHONE_NUMBER');?></strong></p></div>"+
                "<div class='mo_boot_col-sm-3'><input type='text' name='contact_no"+countnoForms+"' id='contact_no"+countnoForms+"' class='mo_boot_form-control' value=''></div>"+
                "<div class='mo_boot_col-sm-3 mo_boot_offset-lg-2 mo_boot_col-lg-2'><p><strong><?php echo Text::_('COM_MINIORANGE_RS_PASSWORD');?></strong></p></div>"+
                "<div class='mo_boot_col-sm-3'><input type='text' name='rs_password"+countnoForms+"' id='rs_password"+countnoForms+"' class='mo_boot_form-control' value='' ></div>"+
                "</div></div>";

            jQuery(forms).insertAfter(jQuery("#form_" +(countnoForms)));
            countnoForms++;
            jQuery('#form_count').attr('value',countnoForms);
        }
    </script>
    <?php
}

function _custom_phone_messages()
{
    $messages = unserialize(MO_MESSAGES);

    $ph_otp_sent   = isset($messages['OTP_SENT_PHONE']) ? $messages['OTP_SENT_PHONE'] : '';
    $ph_otp_error  = isset($messages['ERROR_OTP_PHONE']) ? $messages['ERROR_OTP_PHONE'] : '';
    $phone_blocked = isset($messages['ERROR_PHONE_BLOCKED']) ? $messages['ERROR_PHONE_BLOCKED'] : '';
    $phone_format  = isset($messages['ERROR_PHONE_FORMAT']) ? $messages['ERROR_PHONE_FORMAT'] : '';

    $result                  = commonOtpUtilities::_get_custom_message();
    $success_phone_message   = isset($result['mo_custom_phone_success_message']) && !empty($result['mo_custom_phone_success_message']) ? $result['mo_custom_phone_success_message'] : $ph_otp_sent;
    $error_phone_otp_message = isset($result['mo_custom_phone_error_message']) && !empty($result['mo_custom_phone_error_message']) ? $result['mo_custom_phone_error_message'] : $ph_otp_error;
    $invalid_phone_format    = isset($result['mo_custom_phone_invalid_format_message']) && !empty($result['mo_custom_phone_invalid_format_message']) ? $result['mo_custom_phone_invalid_format_message'] : $phone_format;
    $blocked_phone_message   = isset($result['mo_custom_phone_blocked_message']) && !empty($result['mo_custom_phone_blocked_message']) ? $result['mo_custom_phone_blocked_message'] : $phone_blocked;

    if (commonOtpUtilities::is_customer_registered()) $disabled = "enabled";
    else $disabled = "disabled";
    ?>
    <div class="mo_boot_row">
        <div class="mo_boot_col-sm-12">
            <form action="<?php echo Route::_('index.php?option=com_joomlaotp&view=accountsetup&task=accountsetup.saveCustomPhoneMessage'); ?>" method="post" name="custom_phone_message">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12">
                        <details class="mo_otp_bg_white">
                            <div class="mo_boot_row mo_boot_mx-1">
                                <div class="mo_boot_col-sm-12 mo_boot_mt-3">
                                    <details open>
                                    <br><p class="mo_otp_msg_note"><?php echo Text::_('COM_MINIORANGE_PHONE_MESSAGE_NOTE');?></p>
                                        <div class="mo_boot_col-sm-12">
                                            <textarea name="mo_custom_phone_success_message" class="mo_textarea_css mo_otp_custom_msg" cols="52" mo_boot_rows="5" <?php echo $disabled; ?>><?php echo $success_phone_message; ?></textarea>
                                        </div>
                                        <summary>
                                            <?php echo Text::_('COM_MINIORANGE_SUCCESS_OTP_MESSAGE');?>
                                        </summary>
                                    </details>
                                </div>
                                <div class="mo_boot_col-sm-12 mo_boot_mt-4">
                                    <details>
                                        <br><div class="mo_boot_col-sm-12">
                                        <textarea name="mo_custom_phone_error_message" class="mo_textarea_css mo_otp_custom_msg" cols="52" mo_boot_rows="5" <?php echo $disabled; ?>><?php echo $error_phone_otp_message; ?></textarea>
                                        </div>
                                        <summary>
                                            <?php echo Text::_('COM_MINIORANGE_ERROR_OTP_MESSAGE');?>
                                        </summary>
                                    </details>
                                </div>
                                <div class="mo_boot_col-sm-12 mo_boot_mt-4">
                                    <details >
                                        <br>
                                        <p class="mo_otp_msg_note"><?php echo Text::_('COM_MINIORANGE_PHONE_MESSAGE_NOTE');?></p>
                                        <div class="mo_boot_col-sm-12">
                                        <textarea name="mo_custom_phone_invalid_format_message" class="mo_textarea_css mo_otp_custom_msg"cols="52" mo_boot_rows="5" <?php echo $disabled; ?>><?php echo $invalid_phone_format; ?></textarea>
                                        </div>
                                        <summary>
                                            <?php echo Text::_('COM_MINIORANGE_INVALID_FORMAT_MESSAGE');?>
                                        </summary>
                                    </details>
                                </div>
                                <div class="mo_boot_col-sm-12 mo_boot_mt-4">
                                    <details>
                                        <br>
                                        <div class="mo_boot_col-sm-12">
                                        <textarea name="mo_custom_phone_blocked_message" class="mo_textarea_css mo_otp_custom_msg" cols="52" mo_boot_rows="5" <?php echo $disabled; ?>><?php echo $blocked_phone_message; ?></textarea>
                                        </div>
                                        <summary>
                                            <?php echo Text::_('COM_MINIORANGE_BLOCKED_COUNTRY_CODE_MESSAGE');?>
                                        </summary>
                                    </details>
                                </div>
                            </div>
                            <div class="mo_boot_col-sm-12 mo_boot_text-center mo_boot_mt-2">
                                <input type="submit" class="mo_boot_btn  mo_boot_btn-primary mo_otp_btns" name="custom_phone_messages" value="<?php echo Text::_('COM_MINIORANGE_SAVE_SETTINGS_BUTTON');?>" <?php echo $disabled?> />
                            </div>
                            <summary>
                                <?php echo Text::_('COM_MINIORANGE_SMS_MESSAGES');?>
                            </summary>
                        </details>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php
}

function _custom_common_otp_messages()
{
    $messages = unserialize(MO_MESSAGES);
    $com_messages = isset($messages['COMMON_MESSAGES']) ? $messages['COMMON_MESSAGES'] : '';
    $result = commonOtpUtilities::_get_custom_message();
    $invalid_otp_message = isset($result['mo_custom_invalid_otp_message']) && !empty($result['mo_custom_invalid_otp_message']) ? $result['mo_custom_invalid_otp_message'] : $com_messages;
    if (commonOtpUtilities::is_customer_registered()) $disabled = "enabled";
    else $disabled = "disabled";
    ?>
    <div class="mo_boot_row">
        <div class="mo_boot_col-sm-12">
            <form action="<?php echo Route::_('index.php?option=com_joomlaotp&view=accountsetup&task=accountsetup.saveComOTPMessages'); ?>" method="post" name="block_country_codes">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12">
                        <details class="mo_otp_bg_white">
                            <div class="mo_boot_row mo_boot_mx-1">
                                <div class="mo_boot_col-sm-12 mo_boot_mt-3">
                                    <details open>
                                            <div class="mo_boot_col-sm-12 mo_boot_mt-4">
                                                <textarea name="mo_custom_invalid_otp_message" class="mo_textarea_css mo_otp_custom_msg" cols="52" mo_boot_rows="5" <?php echo $disabled; ?>><?php echo $invalid_otp_message; ?></textarea>
                                            </div>
                                            <summary>
                                                <?php echo Text::_('COM_MINIORANGE_INVALID_OTP_MESSAGE');?>
                                            </summary>
                                    </details>
                                </div>
                            </div>
                            <div class="mo_boot_col-sm-12 mo_boot_text-center mo_boot_mt-2">
                                <input type="submit" class="mo_boot_btn  mo_boot_btn-primary mo_otp_btns" name="custom_otp_messages" value="<?php echo Text::_('COM_MINIORANGE_SAVE_SETTINGS_BUTTON');?>" <?php echo $disabled;?> />
                            </div>
                            <summary>
                                <?php echo Text::_('COM_MINIORANGE_COMMON_MESSAGES');?>
                            </summary>
                        </details>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php
}

function __block_country_code()
{
    $result = commonOtpUtilities::_get_custom_message();
    $country_code = isset($result['mo_block_country_code']) ? $result['mo_block_country_code'] : '';
    $country_code = unserialize($country_code);
    $result            = commonOtpUtilities::__getDBValuesWOArray('#__miniorange_otp_customer');
    $enable_during_registration = $result['enable_during_registration'];
    $final_disabled = ($enable_during_registration != 1) ? 'disabled' : '';

    if (!empty($country_code)) {
        $country_code = implode(';', $country_code);
    } else {
        $country_code = '';
    }
    if (commonOtpUtilities::is_customer_registered()) {
        $disabled = true;
    } else {
        $disabled = false;
    }

    ?>
    <div class="mo_boot_row">
        <div class="mo_boot_col-sm-12 mo_boot_p-3">
            <form action="<?php echo Route::_('index.php?option=com_joomlaotp&view=accountsetup&task=accountsetup.block_country_codes'); ?>" method="post" name="block_country_code">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12">
                        <h2><?php echo Text::_('COM_MINIORANGE_BLOCKED_COUNTRY_CODE_TITLE');?></h2>
                    </div>
                    <div class="mo_boot_col-sm-12">
                        <hr>
                        <?php echo Text::_('COM_MINIORANGE_BLOCKED_COUNTRY_CODE_DETAILS');?>
                    </div>
                    <div class="mo_boot_col-sm-4">
                        <strong><?php echo Text::_('COM_MINIORANGE_BLOCKED_COUNTRY_CODE');?></strong>
                    </div>
                    <div class="mo_boot_col-sm-6 ps-1">
                    <textarea name="mo_block_country_code" class="mo_boot_form-control textarea-control mo_otp_email_domain"
                        onkeyup="nospaces(this)" cols="55" mo_boot_rows="3"
                        placeholder="<?php echo Text::_('COM_MINIORANGE_BLOCKED_COUNTRY_CODE_PLACEHOLDER');?>"                           
                        <?php echo $final_disabled; ?> <?php if ($disabled) echo "enabled"; else echo "disabled"; ?>><?php echo trim($country_code); ?></textarea>
                    </div>
                    <div class="mo_boot_col-sm-12"><br></div>
                    <div class="mo_boot_col-sm-12 mo_boot_text-center">
                        <input type="submit" class="mo_boot_btn  mo_boot_btn-primary mo_otp_btns" value="<?php echo Text::_('COM_MINIORANGE_SAVE_SETTINGS_BUTTON');?>" <?php if ($disabled) echo "enabled";else echo "disabled"; ?> />
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php
}

function get_country_code_dropdown()
{
    $result            = commonOtpUtilities::__getDBValuesWOArray('#__miniorange_otp_customer');
    $default_cont_code = isset($result['mo_default_country_code']) ? $result['mo_default_country_code'] : '';
    $enable_during_registration = $result['enable_during_registration'];
    $final_disabled = ($enable_during_registration != 1) ? 'disabled' : '';
    
    if (commonOtpUtilities::is_customer_registered()) $disabled = true;
    else $disabled = false;
 
    ?>
    <div class="mo_boot_row">
        <div class="mo_boot_col-sm-12 mo_boot_p-3">
            <form action="<?php echo Route::_('index.php?option=com_joomlaotp&view=accountsetup&task=accountsetup.saveCustomSettings'); ?>" method="post" name="custom_set" id="otp_form">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12">
                        <h2><?php echo Text::_('COM_MINIORANGE_COUNTRY_CODE_TITLE');?></h2>
                    </div>

                    <div class="mo_boot_col-sm-12">
                        <hr>
                        <?php echo Text::_('COM_MINIORANGE_COUNTRY_CODE_DETAILS');?>
                        <div class="mo_boot_row">
                            <div class="mo_boot_col-sm-4">
                                <p><strong><?php echo Text::_('COM_MINIORANGE_SELECT_COUNTRY_CODE');?></strong></p>
                            </div>
                            <div class="mo_boot_col-sm-6">
                            <select name="default_country_code" class="mo_boot_form-control mo_boot_pl-2 mo_otp_country_select" id="mo_country_code" <?php echo $final_disabled; ?> <?php if ($disabled) echo "enabled"; else echo "disabled"; ?>>
                            <hr>
                                    <option class="mo_boot_text-center" value="" selected="selected"><?php echo Text::_('COM_MINIORANGE_SELECT_COUNTRY_CODE_PLACEHOLDER');?></option>
                                    <?php
                                    foreach (getCountryCodeList() as $key => $country) {
                                        echo '<option id="mo_count" data-countrycode="' . $country['countryCode'] . '" value="' . $country['countryCode'], ',' . $country['name'] . '"';
                                        echo $default_cont_code == $country['countryCode'] ? 'selected' : '';
                                        echo '>' . $country['name'] . ', ' . $country['countryCode'] . '</option>';
                                    }
                                    echo '</select>';
                                    ?>
                            </div>
                        </div>
                    </div>
                    <div class="mo_boot_col-sm-12"><br></div>
                    <div class="mo_boot_col-sm-12 mo_boot_text-center">
                        <input type="submit" class="mo_boot_btn  mo_boot_btn-primary mo_otp_btns" value="<?php echo Text::_('COM_MINIORANGE_SAVE_SETTINGS_BUTTON');?>" <?php if ($disabled) echo "enabled";else echo "disabled"; ?> />
                    </div>
                </div>
            </form>
        </div>
    </div>       
    <?php
}

function showAddonsContent(){

    define("MO_ADDONS_CONTENT",serialize( array(

                "PASSWORDLESS_LOGIN_FOR_JOOMLA" =>      [
                    'addonName'  => Text::_('COM_MINIORANGE_ADDON_PASSWORDLESS_LOGIN_NAME'),
                    'addonDescription'  => Text::_('COM_MINIORANGE_ADDON_PASSWORDLESS_LOGIN_DESC'),
                    'addonSiteLink' => 'https://www.miniorange.com/contact',
                ],
                "JOOMLA_SMS_NOTIFICATION" =>      [
                    'addonName'  => Text::_('COM_MINIORANGE_ADDON_SMS_NOTIFICATION_NAME'),
                    'addonDescription'  => Text::_('COM_MINIORANGE_ADDON_SMS_NOTIFICATION_DESC'),
                    'addonSiteLink' => 'https://plugins.miniorange.com/sms-and-email-notification-for-joomla',
                ],
                "JOOMLA_PASSWORD_RESET" =>      [
                    'addonName'  => Text::_('COM_MINIORANGE_ADDON_PASSWORD_RESET_OVER_OTP_NAME'),
                    'addonDescription'  => Text::_('COM_MINIORANGE_ADDON_PASSWORD_RESET_OVER_OTP_DESC'),
                    'addonSiteLink' => 'https://www.miniorange.com/contact',
                ],
                "REGISTER_USING_ONLY_PHONE" =>      [
                    'addonName'  => Text::_('COM_MINIORANGE_ADDON_REGISTER_USING_ONLY_PHONE_NAME'),
                    'addonDescription'  => Text::_('COM_MINIORANGE_ADDON_REGISTER_USING_ONLY_PHONE_DESC'),
                    'addonSiteLink' => 'https://plugins.miniorange.com/login-using-phone-number-into-joomla',
                ],
                "RESEND_OTP_CONTROL" =>      [
                    'addonName'  => Text::_('COM_MINIORANGE_ADDON_RESEND_OTP_CONTROL_NAME'),
                    'addonDescription'  => Text::_('COM_MINIORANGE_ADDON_RESEND_OTP_CONTROL_DESC'),
                    'addonSiteLink' => 'https://www.miniorange.com/contact',
                ],
                "OTP_OVER_VOICE" =>      [
                    'addonName'  => Text::_('COM_MINIORANGE_ADDON_OTP_OVER_VOICE_NAME'),
                    'addonDescription'  => Text::_('COM_MINIORANGE_ADDON_OTP_OVER_VOICE_DESC'),
                    'addonSiteLink' => 'https://www.miniorange.com/contact',
                ],
                "OTP_OVER_WHATSAPP" =>      [
                    'addonName'  => Text::_('COM_MINIORANGE_ADDON_OTP_OVER_WHATSAPP_NAME'),
                    'addonDescription'  => Text::_('COM_MINIORANGE_ADDON_OTP_OVER_WHATSAPP_DESC'),
                    'addonSiteLink' => 'https://www.miniorange.com/contact',
                ],
                "LOGIN_OPTIONS_FOR_JOOMLA" =>      [
                    'addonName'  => Text::_('COM_MINIORANGE_ADDON_LOGIN_OPTIONS_NAME'),
                    'addonDescription'  => Text::_('COM_MINIORANGE_ADDON_LOGIN_OPTIONS_DESC'),
                    'addonSiteLink' => 'https://plugins.miniorange.com/login-using-phone-number-into-joomla',
                ],
                "LOGIN_REPORTS_FOR_JOOMLA" =>      [
                    'addonName'  => Text::_('COM_MINIORANGE_ADDON_LOGIN_AUDIT_NAME'),
                    'addonDescription'  => Text::_('COM_MINIORANGE_ADDON_LOGIN_AUDIT_DESC'),
                    'addonSiteLink' => 'https://plugins.miniorange.com/joomla-login-audit-login-activity-report',
                ],
                "SWEET_ALERT_FOR_JOOMLA" =>      [
                    'addonName'  => Text::_('COM_MINIORANGE_ADDON_SWEET_ALERT_NAME'),
                    'addonDescription'  => Text::_('COM_MINIORANGE_ADDON_SWEET_ALERT_DESC'),
                    'addonSiteLink' => 'https://plugins.miniorange.com/joomla-sweet-alert',
                ],
                "SESSION_MANAGEMENT_FOR_JOOMLA" =>      [
                    'addonName'  => Text::_('COM_MINIORANGE_ADDON_SESSION_MANAGEMENT_NAME'),
                    'addonDescription'  => Text::_('COM_MINIORANGE_ADDON_SESSION_MANAGEMENT_DESC'),
                    'addonSiteLink' => 'https://plugins.miniorange.com/joomla-session-management',
                ],
                )));

            $displayMessage = "";
            $messages = unserialize(MO_ADDONS_CONTENT);

            ?>
            <div class="mo_boot_col-sm-12 mo_boot_text-center">
            <h2 class="ms-2"><?php echo Text::_('COM_MINIORANGE_OTP_ADDONS');?></h2>
            </div>
            <br>
            <div class="mo_boot_col-sm-12"><hr></div>
            <div class="mo_otp_wrapper mo_boot_row g-3 mo_boot_px-3">
            <?php
            $queryBody = "Hi! I am interested in the {{addonName}} addon, could you please tell me more about this addon?";
            foreach ($messages as $messageKey)
            {
            $addon_plugin_site = $messageKey["addonSiteLink"];
            ?>
            <div class="mo_boot_col-md-4 mo_boot_col-sm-6 mo_boot_col-12 mo_boot_mb-1 mo_otp_addons" id="<?php echo $messageKey['addonName']; ?>">
                    <div class="card-body">
                        <center>
                            <h3 class="mo_otp_addon_name"><?php echo $messageKey["addonName"]; ?><br><br></h3>
                        </center>
                        <footer>
                            <center>
                                <a href="<?php echo $addon_plugin_site; ?>" target="_blank" class="mo_boot_btn btn-medium mo_btn_inter mo_otp_white_text"><?php echo Text::_('COM_MINIORANGE_INTERESTED'); ?></a>
                                <br><br>
                            </center>
                        </footer>
                        <span class="cd-pricing-body">
                            <ul class="cd-pricing-features">
                                <li class="mo_otp_addon_desc"><?php echo $messageKey["addonDescription"]; ?></li>
                            </ul>
                        </span>
                    </div>
            </div>
            <?php
        }
        ?>
        </div><br>
        <?php
        return $displayMessage;
}

function getCountryCodeList()
{
    $countries = array(
                array(
                    'name' => 'Afghanistan (‫افغانستان‬‎)',
                    'alphacode' => 'af',
                    'countryCode' => '+93'
                    ),
                array(
                    'name' => 'Albania (Shqipëri)',
                    'alphacode' => 'al',
                    'countryCode' => '+355'
                ),
                array(
                    'name' => 'Algeria (‫الجزائر‬‎)',
                    'alphacode' => 'dz',
                    'countryCode' => '+213'
                ),
                array(
                    'name' => 'American Samoa',
                    'alphacode' => 'as',
                    'countryCode' => '+1684'
                ),
                array(
                    'name' => 'Andorra',
                    'alphacode' => 'ad',
                    'countryCode' => '+376'
                ),
                array(
                    'name' => 'Angola',
                    'alphacode' => 'ao',
                    'countryCode' => '+244'
                ),
                array(
                    'name' => 'Anguilla',
                    'alphacode' => 'ai',
                    'countryCode' => '+1264'
                ),
                array(
                    'name' => 'Antigua and Barbuda',
                    'alphacode' => 'ag',
                    'countryCode' => '+1268'
                ),
                array(
                    'name' => 'Argentina',
                    'alphacode' => 'ar',
                    'countryCode' => '+54'
                ),
                array(
                    'name' => 'Armenia (Հայաստան)',
                    'alphacode' => 'am',
                    'countryCode' => '+374'
                ),
                array(
                    'name' => 'Aruba',
                    'alphacode' => 'aw',
                    'countryCode' => '+297'
                ),
                array(
                    'name' => 'Australia',
                    'alphacode' => 'au',
                    'countryCode' => '+61'
                ),
                array(
                    'name' => 'Austria (Österreich)',
                    'alphacode' => 'at',
                    'countryCode' => '+43'
                ),
                array(
                    'name' => 'Azerbaijan (Azərbaycan)',
                    'alphacode' => 'az',
                    'countryCode' => '+994'
                ),
                array(
                    'name' => 'Bahamas',
                    'alphacode' => 'bs',
                    'countryCode' => '+1242'
                ),
                array(
                    'name' => 'Bahrain (‫البحرين‬‎)',
                    'alphacode' => 'bh',
                    'countryCode' => '+973'
                ),
                array(
                    'name' => 'Bangladesh (বাংলাদেশ)',
                    'alphacode' => 'bd',
                    'countryCode' => '+880'
                ),
                array(
                    'name' => 'Barbados',
                    'alphacode' => 'bb',
                    'countryCode' => '+1246'
                ),
                array(
                    'name' => 'Belarus (Беларусь)',
                    'alphacode' => 'by',
                    'countryCode' => '+375'
                ),
                array(
                    'name' => 'Belgium (België)',
                    'alphacode' => 'be',
                    'countryCode' => '+32'
                ),
                array(
                    'name' => 'Belize',
                    'alphacode' => 'bz',
                    'countryCode' => '+501'
                ),
                array(
                    'name' => 'Benin (Bénin)',
                    'alphacode' => 'bj',
                    'countryCode' => '+229'
                ),
                array(
                    'name' => 'Bermuda',
                    'alphacode' => 'bm',
                    'countryCode' => '+1441'
                ),
                array(
                    'name' => 'Bhutan (འབྲུག)',
                    'alphacode' => 'bt',
                    'countryCode' => '+975'
                ),
                array(
                    'name' => 'Bolivia',
                    'alphacode' => 'bo',
                    'countryCode' => '+591'
                ),
                array(
                    'name' => 'Bosnia and Herzegovina (Босна и Херцеговина)',
                    'alphacode' => 'ba',
                    'countryCode' => '+387'
                ),
                array(
                    'name' => 'Botswana',
                    'alphacode' => 'bw',
                    'countryCode' => '+267'
                ),
                array(
                    'name' => 'Brazil (Brasil)',
                    'alphacode' => 'br',
                    'countryCode' => '+55'
                ),
                array(
                    'name' => 'British Indian Ocean Territory',
                    'alphacode' => 'io',
                    'countryCode' => '+246'
                ),
                array(
                    'name' => 'British Virgin Islands',
                    'alphacode' => 'vg',
                    'countryCode' => '+1284'
                ),
                array(
                    'name' => 'Brunei',
                    'alphacode' => 'bn',
                    'countryCode' => '+673'
                ),
                array(
                    'name' => 'Bulgaria (България)',
                    'alphacode' => 'bg',
                    'countryCode' => '+359'
                ),
                array(
                    'name' => 'Burkina Faso',
                    'alphacode' => 'bf',
                    'countryCode' => '+226'
                ),
                array(
                    'name' => 'Burundi (Uburundi)',
                    'alphacode' => 'bi',
                    'countryCode' => '+257'
                ),
                array(
                    'name' => 'Cambodia (កម្ពុជា)',
                    'alphacode' => 'kh',
                    'countryCode' => '+855'
                ),
                array(
                    'name' => 'Cameroon (Cameroun)',
                    'alphacode' => 'cm',
                    'countryCode' => '+237'
                ),
                array(
                    'name' => 'Canada',
                    'alphacode' => 'ca',
                    'countryCode' => '+1'
                ),
                array(
                    'name' => 'Cape Verde (Kabu Verdi)',
                    'alphacode' => 'cv',
                    'countryCode' => '+238'
                ),
                array(
                    'name' => 'Caribbean Netherlands',
                    'alphacode' => 'bq',
                    'countryCode' => '+599'
                ),
                array(
                    'name' => 'Cayman Islands',
                    'alphacode' => 'ky',
                    'countryCode' => '+1345'
                ),
                array(
                    'name' => 'Central African Republic (République centrafricaine)',
                    'alphacode' => 'cf',
                    'countryCode' => '+236'
                ),
                array(
                    'name' => 'Chad (Tchad)',
                    'alphacode' => 'td',
                    'countryCode' => '+235'
                ),
                array(
                    'name' => 'Chile',
                    'alphacode' => 'cl',
                    'countryCode' => '+56'
                ),
                array(
                    'name' => 'China (中国)',
                    'alphacode' => 'cn',
                    'countryCode' => '+86'
                ),
                array(
                    'name' => 'Christmas Island',
                    'alphacode' => 'cx',
                    'countryCode' => '+61'
                ),
                array(
                    'name' => 'Cocos (Keeling) Islands',
                    'alphacode' => 'cc',
                    'countryCode' => '+61'
                ),
                array(
                    'name' => 'Colombia',
                    'alphacode' => 'co',
                    'countryCode' => '+57'
                ),
                array(
                    'name' => 'Comoros (‫جزر القمر‬‎)',
                    'alphacode' => 'km',
                    'countryCode' => '+269'
                ),
                array(
                    'name' => 'Congo (DRC) (Jamhuri ya Kidemokrasia ya Kongo)',
                    'alphacode' => 'cd',
                    'countryCode' => '+243'
                ),
                array(
                    'name' => 'Congo (Republic) (Congo-Brazzaville)',
                    'alphacode' => 'cg',
                    'countryCode' => '+242'
                ),
                array(
                    'name' => 'Cook Islands',
                    'alphacode' => 'ck',
                    'countryCode' => '+682'
                ),
                array(
                    'name' => 'Costa Rica',
                    'alphacode' => 'cr',
                    'countryCode' => '+506'
                ),
                array(
                    'name' => 'Côte d’Ivoire',
                    'alphacode' => 'ci',
                    'countryCode' => '+225'
                ),
                array(
                    'name' => 'Croatia (Hrvatska)',
                    'alphacode' => 'hr',
                    'countryCode' => '+385'
                ),
                array(
                    'name' => 'Cuba',
                    'alphacode' => 'cu',
                    'countryCode' => '+53'
                ),
                array(
                    'name' => 'Curaçao',
                    'alphacode' => 'cw',
                    'countryCode' => '+599'
                ),
                array(
                    'name' => 'Cyprus (Κύπρος)',
                    'alphacode' => 'cy',
                    'countryCode' => '+357'
                ),
                array(
                    'name' => 'Czech Republic (Česká republika)',
                    'alphacode' => 'cz',
                    'countryCode' => '+420'
                ),
                array(
                    'name' => 'Denmark (Danmark)',
                    'alphacode' => 'dk',
                    'countryCode' => '+45'
                ),
                array(
                    'name' => 'Djibouti',
                    'alphacode' => 'dj',
                    'countryCode' => '+253'
                ),
                array(
                    'name' => 'Dominica',
                    'alphacode' => 'dm',
                    'countryCode' => '+1767'
                ),
                array(
                    'name' => 'Dominican Republic (República Dominicana)',
                    'alphacode' => 'do',
                    'countryCode' => '+1'
                ),
                array(
                    'name' => 'Ecuador',
                    'alphacode' => 'ec',
                    'countryCode' => '+593'
                ),
                array(
                    'name' => 'Egypt (‫مصر‬‎)',
                    'alphacode' => 'eg',
                    'countryCode' => '+20'
                ),
                array(
                    'name' => 'El Salvador',
                    'alphacode' => 'sv',
                    'countryCode' => '+503'
                ),
                array(
                    'name' => 'Equatorial Guinea (Guinea Ecuatorial)',
                    'alphacode' => 'gq',
                    'countryCode' => '+240'
                ),
                array(
                    'name' => 'Eritrea',
                    'alphacode' => 'er',
                    'countryCode' => '+291'
                ),
                array(
                    'name' => 'Estonia (Eesti)',
                    'alphacode' => 'ee',
                    'countryCode' => '+372'
                ),
                array(
                    'name' => 'Ethiopia',
                    'alphacode' => 'et',
                    'countryCode' => '+251'
                ),
                array(
                    'name' => 'Falkland Islands (Islas Malvinas)',
                    'alphacode' => 'fk',
                    'countryCode' => '+500'
                ),
                array(
                    'name' => 'Faroe Islands (Føroyar)',
                    'alphacode' => 'fo',
                    'countryCode' => '+298'
                ),
                array(
                    'name' => 'Fiji',
                    'alphacode' => 'fj',
                    'countryCode' => '+679'
                ),
                array(
                    'name' => 'Finland (Suomi)',
                    'alphacode' => 'fi',
                    'countryCode' => '+358'
                ),
                array(
                    'name' => 'France',
                    'alphacode' => 'fr',
                    'countryCode' => '+33'
                ),
                array(
                    'name' => 'French Guiana (Guyane française)',
                    'alphacode' => 'gf',
                    'countryCode' => '+594'
                ),
                array(
                    'name' => 'French Polynesia (Polynésie française)',
                    'alphacode' => 'pf',
                    'countryCode' => '+689'
                ),
                array(
                    'name' => 'Gabon',
                    'alphacode' => 'ga',
                    'countryCode' => '+241'
                ),
                array(
                    'name' => 'Gambia',
                    'alphacode' => 'gm',
                    'countryCode' => '+220'
                ),
                array(
                    'name' => 'Georgia (საქართველო)',
                    'alphacode' => 'ge',
                    'countryCode' => '+995'
                ),
                array(
                    'name' => 'Germany (Deutschland)',
                    'alphacode' => 'de',
                    'countryCode' => '+49'
                ),
                array(
                    'name' => 'Ghana (Gaana)',
                    'alphacode' => 'gh',
                    'countryCode' => '+233'
                ),
                array(
                    'name' => 'Gibraltar',
                    'alphacode' => 'gi',
                    'countryCode' => '+350'
                ),
                array(
                    'name' => 'Greece (Ελλάδα)',
                    'alphacode' => 'gr',
                    'countryCode' => '+30'
                ),
                array(
                    'name' => 'Greenland (Kalaallit Nunaat)',
                    'alphacode' => 'gl',
                    'countryCode' => '+299'
                ),
                array(
                    'name' => 'Grenada',
                    'alphacode' => 'gd',
                    'countryCode' => '+1473'
                ),
                array(
                    'name' => 'Guadeloupe',
                    'alphacode' => 'gp',
                    'countryCode' => '+590'
                ),
                array(
                    'name' => 'Guam',
                    'alphacode' => 'gu',
                    'countryCode' => '+1671'
                ),
                array(
                    'name' => 'Guatemala',
                    'alphacode' => 'gt',
                    'countryCode' => '+502'
                ),
                array(
                    'name' => 'Guernsey',
                    'alphacode' => 'gg',
                    'countryCode' => '+44'
                ),
                array(
                    'name' => 'Guinea (Guinée)',
                    'alphacode' => 'gn',
                    'countryCode' => '+224'
                ),
                array(
                    'name' => 'Guinea-Bissau (Guiné Bissau)',
                    'alphacode' => 'gw',
                    'countryCode' => '+245'
                ),
                array(
                    'name' => 'Guyana',
                    'alphacode' => 'gy',
                    'countryCode' => '+592'
                ),
                array(
                    'name' => 'Haiti',
                    'alphacode' => 'ht',
                    'countryCode' => '+509'
                ),
                array(
                    'name' => 'Honduras',
                    'alphacode' => 'hn',
                    'countryCode' => '+504'
                ),
                array(
                    'name' => 'Hong Kong (香港)',
                    'alphacode' => 'hk',
                    'countryCode' => '+852'
                ),
                array(
                    'name' => 'Hungary (Magyarország)',
                    'alphacode' => 'hu',
                    'countryCode' => '+36'
                ),
                array(
                    'name' => 'Iceland (Ísland)',
                    'alphacode' => 'is',
                    'countryCode' => '+354'
                ),
                array(
                    'name' => 'India (भारत)',
                    'alphacode' => 'in',
                    'countryCode' => '+91'
                ),
                array(
                    'name' => 'Indonesia',
                    'alphacode' => 'id',
                    'countryCode' => '+62'
                ),
                array(
                    'name' => 'Iran (‫ایران‬‎)',
                    'alphacode' => 'ir',
                    'countryCode' => '+98'
                ),
                array(
                    'name' => 'Iraq (‫العراق‬‎)',
                    'alphacode' => 'iq',
                    'countryCode' => '+964'
                ),
                array(
                    'name' => 'Ireland',
                    'alphacode' => 'ie',
                    'countryCode' => '+353'
                ),
                array(
                    'name' => 'Isle of Man',
                    'alphacode' => 'im',
                    'countryCode' => '+44'
                ),
                array(
                    'name' => 'Israel (‫ישראל‬‎)',
                    'alphacode' => 'il',
                    'countryCode' => '+972'
                ),
                array(
                    'name' => 'Italy (Italia)',
                    'alphacode' => 'it',
                    'countryCode' => '+39'
                ),
                array(
                    'name' => 'Jamaica',
                    'alphacode' => 'jm',
                    'countryCode' => '+1876'
                ),
                array(
                    'name' => 'Japan (日本)',
                    'alphacode' => 'jp',
                    'countryCode' => '+81'
                ),
                array(
                    'name' => 'Jersey',
                    'alphacode' => 'je',
                    'countryCode' => '+44'
                ),
                array(
                    'name' => 'Jordan (‫الأردن‬‎)',
                    'alphacode' => 'jo',
                    'countryCode' => '+962'
                ),
                array(
                    'name' => 'Kazakhstan (Казахстан)',
                    'alphacode' => 'kz',
                    'countryCode' => '+7'
                ),
                array(
                    'name' => 'Kenya',
                    'alphacode' => 'ke',
                    'countryCode' => '+254'
                ),
                array(
                    'name' => 'Kiribati',
                    'alphacode' => 'ki',
                    'countryCode' => '+686'
                ),
                array(
                    'name' => 'Kosovo',
                    'alphacode' => 'xk',
                    'countryCode' => '+383'
                ),
                array(
                    'name' => 'Kuwait (‫الكويت‬‎)',
                    'alphacode' => 'kw',
                    'countryCode' => '+965'
                ),
                array(
                    'name' => 'Kyrgyzstan (Кыргызстан)',
                    'alphacode' => 'kg',
                    'countryCode' => '+996'
                ),
                array(
                    'name' => 'Laos (ລາວ)',
                    'alphacode' => 'la',
                    'countryCode' => '+856'
                ),
                array(
                    'name' => 'Latvia (Latvija)',
                    'alphacode' => 'lv',
                    'countryCode' => '+371'
                ),
                array(
                    'name' => 'Lebanon (‫لبنان‬‎)',
                    'alphacode' => 'lb',
                    'countryCode' => '+961'
                ),
                array(
                    'name' => 'Lesotho',
                    'alphacode' => 'ls',
                    'countryCode' => '+266'
                ),
                array(
                    'name' => 'Liberia',
                    'alphacode' => 'lr',
                    'countryCode' => '+231'
                ),
                array(
                    'name' => 'Libya (‫ليبيا‬‎)',
                    'alphacode' => 'ly',
                    'countryCode' => '+218'
                ),
                array(
                    'name' => 'Liechtenstein',
                    'alphacode' => 'li',
                    'countryCode' => '+423'
                ),
                array(
                    'name' => 'Lithuania (Lietuva)',
                    'alphacode' => 'lt',
                    'countryCode' => '+370'
                ),
                array(
                    'name' => 'Luxembourg',
                    'alphacode' => 'lu',
                    'countryCode' => '+352'
                ),
                array(
                    'name' => 'Macau (澳門)',
                    'alphacode' => 'mo',
                    'countryCode' => '+853'
                ),
                array(
                    'name' => 'Macedonia (FYROM) (Македонија)',
                    'alphacode' => 'mk',
                    'countryCode' => '+389'
                ),
                array(
                    'name' => 'Madagascar (Madagasikara)',
                    'alphacode' => 'mg',
                    'countryCode' => '+261'
                ),
                array(
                    'name' => 'Malawi',
                    'alphacode' => 'mw',
                    'countryCode' => '+265'
                ),
                array(
                    'name' => 'Malaysia',
                    'alphacode' => 'my',
                    'countryCode' => '+60'
                ),
                array(
                    'name' => 'Maldives',
                    'alphacode' => 'mv',
                    'countryCode' => '+960'
                ),
                array(
                    'name' => 'Mali',
                    'alphacode' => 'ml',
                    'countryCode' => '+223'
                ),
                array(
                    'name' => 'Malta',
                    'alphacode' => 'mt',
                    'countryCode' => '+356'
                ),
                array(
                    'name' => 'Marshall Islands',
                    'alphacode' => 'mh',
                    'countryCode' => '+692'
                ),
                array(
                    'name' => 'Martinique',
                    'alphacode' => 'mq',
                    'countryCode' => '+596'
                ),
                array(
                    'name' => 'Mauritania (‫موريتانيا‬‎)',
                    'alphacode' => 'mr',
                    'countryCode' => '+222'
                ),
                array(
                    'name' => 'Mauritius (Moris)',
                    'alphacode' => 'mu',
                    'countryCode' => '+230'
                ),
                array(
                    'name' => 'Mayotte',
                    'alphacode' => 'yt',
                    'countryCode' => '+262'
                ),
                array(
                    'name' => 'Mexico (México)',
                    'alphacode' => 'mx',
                    'countryCode' => '+52'
                ),
                array(
                    'name' => 'Micronesia',
                    'alphacode' => 'fm',
                    'countryCode' => '+691'
                ),
                array(
                    'name' => 'Moldova (Republica Moldova)',
                    'alphacode' => 'md',
                    'countryCode' => '+373'
                ),
                array(
                    'name' => 'Monaco',
                    'alphacode' => 'mc',
                    'countryCode' => '+377'
                ),
                array(
                    'name' => 'Mongolia (Монгол)',
                    'alphacode' => 'mn',
                    'countryCode' => '+976'
                ),
                array(
                    'name' => 'Montenegro (Crna Gora)',
                    'alphacode' => 'me',
                    'countryCode' => '+382'
                ),
                array(
                    'name' => 'Montserrat',
                    'alphacode' => 'ms',
                    'countryCode' => '+1664'
                ),
                array(
                    'name' => 'Morocco (‫المغرب‬‎)',
                    'alphacode' => 'ma',
                    'countryCode' => '+212'
                ),
                array(
                    'name' => 'Mozambique (Moçambique)',
                    'alphacode' => 'mz',
                    'countryCode' => '+258'
                ),
                array(
                    'name' => 'Myanmar (Burma) (မြန်မာ)',
                    'alphacode' => 'mm',
                    'countryCode' => '+95'
                ),
                array(
                    'name' => 'Namibia (Namibië)',
                    'alphacode' => 'na',
                    'countryCode' => '+264'
                ),
                array(
                    'name' => 'Nauru',
                    'alphacode' => 'nr',
                    'countryCode' => '+674'
                ),
                array(
                    'name' => 'Nepal (नेपाल)',
                    'alphacode' => 'np',
                    'countryCode' => '+977'
                ),
                array(
                    'name' => 'Netherlands (Nederland)',
                    'alphacode' => 'nl',
                    'countryCode' => '+31'
                ),
                array(
                    'name' => 'New Caledonia (Nouvelle-Calédonie)',
                    'alphacode' => 'nc',
                    'countryCode' => '+687'
                ),
                array(
                    'name' => 'New Zealand',
                    'alphacode' => 'nz',
                    'countryCode' => '+64'
                ),
                array(
                    'name' => 'Nicaragua',
                    'alphacode' => 'ni',
                    'countryCode' => '+505'
                ),
                array(
                    'name' => 'Niger (Nijar)',
                    'alphacode' => 'ne',
                    'countryCode' => '+227'
                ),
                array(
                    'name' => 'Nigeria',
                    'alphacode' => 'ng',
                    'countryCode' => '+234'
                ),
                array(
                    'name' => 'Niue',
                    'alphacode' => 'nu',
                    'countryCode' => '+683'
                ),
                array(
                    'name' => 'Norfolk Island',
                    'alphacode' => 'nf',
                    'countryCode' => '+672'
                ),
                array(
                    'name' => 'North Korea (조선 민주주의 인민 공화국)',
                    'alphacode' => 'kp',
                    'countryCode' => '+850'
                ),
                array(
                    'name' => 'Northern Mariana Islands',
                    'alphacode' => 'mp',
                    'countryCode' => '+1670'
                ),
                array(
                    'name' => 'Norway (Norge)',
                    'alphacode' => 'no',
                    'countryCode' => '+47'
                ),
                array(
                    'name' => 'Oman (‫عُمان‬‎)',
                    'alphacode' => 'om',
                    'countryCode' => '+968'
                ),
                array(
                    'name' => 'Pakistan (‫پاکستان‬‎)',
                    'alphacode' => 'pk',
                    'countryCode' => '+92'
                ),
                array(
                    'name' => 'Palau',
                    'alphacode' => 'pw',
                    'countryCode' => '+680'
                ),
                array(
                    'name' => 'Palestine (‫فلسطين‬‎)',
                    'alphacode' => 'ps',
                    'countryCode' => '+970'
                ),
                array(
                    'name' => 'Panama (Panamá)',
                    'alphacode' => 'pa',
                    'countryCode' => '+507'
                ),
                array(
                    'name' => 'Papua New Guinea',
                    'alphacode' => 'pg',
                    'countryCode' => '+675'
                ),
                array(
                    'name' => 'Paraguay',
                    'alphacode' => 'py',
                    'countryCode' => '+595'
                ),
                array(
                    'name' => 'Peru (Perú)',
                    'alphacode' => 'pe',
                    'countryCode' => '+51'
                ),
                array(
                    'name' => 'Philippines',
                    'alphacode' => 'ph',
                    'countryCode' => '+63'
                ),
                array(
                    'name' => 'Poland (Polska)',
                    'alphacode' => 'pl',
                    'countryCode' => '+48'
                ),
                array(
                    'name' => 'Portugal',
                    'alphacode' => 'pt',
                    'countryCode' => '+351'
                ),
                array(
                    'name' => 'Puerto Rico',
                    'alphacode' => 'pr',
                    'countryCode' => '+1'
                ),
                array(
                    'name' => 'Qatar (‫قطر‬‎)',
                    'alphacode' => 'qa',
                    'countryCode' => '+974'
                ),
                array(
                    'name' => 'Réunion (La Réunion)',
                    'alphacode' => 're',
                    'countryCode' => '+262'
                ),
                array(
                    'name' => 'Romania (România)',
                    'alphacode' => 'ro',
                    'countryCode' => '+40'
                ),
                array(
                    'name' => 'Russia (Россия)',
                    'alphacode' => 'ru',
                    'countryCode' => '+7'
                ),
                array(
                    'name' => 'Rwanda',
                    'alphacode' => 'rw',
                    'countryCode' => '+250'
                ),
                array(
                    'name' => 'Saint Barthélemy',
                    'alphacode' => 'bl',
                    'countryCode' => '+590'
                ),
                array(
                    'name' => 'Saint Helena',
                    'alphacode' => 'sh',
                    'countryCode' => '+290'
                ),
                array(
                    'name' => 'Saint Kitts and Nevis',
                    'alphacode' => 'kn',
                    'countryCode' => '+1869'
                ),
                array(
                    'name' => 'Saint Lucia',
                    'alphacode' => 'lc',
                    'countryCode' => '+1758'
                ),
                array(
                    'name' => 'Saint Martin (Saint-Martin (partie française))',
                    'alphacode' => 'mf',
                    'countryCode' => '+590'
                ),
                array(
                    'name' => 'Saint Pierre and Miquelon (Saint-Pierre-et-Miquelon)',
                    'alphacode' => 'pm',
                    'countryCode' => '+508'
                ),
                array(
                    'name' => 'Saint Vincent and the Grenadines',
                    'alphacode' => 'vc',
                    'countryCode' => '+1784'
                ),
                array(
                    'name' => 'Samoa',
                    'alphacode' => 'ws',
                    'countryCode' => '+685'
                ),
                array(
                    'name' => 'San Marino',
                    'alphacode' => 'sm',
                    'countryCode' => '+378'
                ),
                array(
                    'name' => 'São Tomé and Príncipe (São Tomé e Príncipe)',
                    'alphacode' => 'st',
                    'countryCode' => '+239'
                ),
                array(
                    'name' => 'Saudi Arabia (‫المملكة العربية السعودية‬‎)',
                    'alphacode' => 'sa',
                    'countryCode' => '+966'
                ),
                array(
                    'name' => 'Senegal (Sénégal)',
                    'alphacode' => 'sn',
                    'countryCode' => '+221'
                ),
                array(
                    'name' => 'Serbia (Србија)',
                    'alphacode' => 'rs',
                    'countryCode' => '+381'
                ),
                array(
                    'name' => 'Seychelles',
                    'alphacode' => 'sc',
                    'countryCode' => '+248'
                ),
                array(
                    'name' => 'Sierra Leone',
                    'alphacode' => 'sl',
                    'countryCode' => '+232'
                ),
                array(
                    'name' => 'Singapore',
                    'alphacode' => 'sg',
                    'countryCode' => '+65'
                ),
                array(
                    'name' => 'Sint Maarten',
                    'alphacode' => 'sx',
                    'countryCode' => '+1721'
                ),
                array(
                    'name' => 'Slovakia (Slovensko)',
                    'alphacode' => 'sk',
                    'countryCode' => '+421'
                ),
                array(
                    'name' => 'Slovenia (Slovenija)',
                    'alphacode' => 'si',
                    'countryCode' => '+386'
                ),
                array(
                    'name' => 'Solomon Islands',
                    'alphacode' => 'sb',
                    'countryCode' => '+677'
                ),
                array(
                    'name' => 'Somalia (Soomaaliya)',
                    'alphacode' => 'so',
                    'countryCode' => '+252'
                ),
                array(
                    'name' => 'South Africa',
                    'alphacode' => 'za',
                    'countryCode' => '+27'
                ),
                array(
                    'name' => 'South Korea (대한민국)',
                    'alphacode' => 'kr',
                    'countryCode' => '+82'
                ),
                array(
                    'name' => 'South Sudan (‫جنوب السودان‬‎)',
                    'alphacode' => 'ss',
                    'countryCode' => '+211'
                ),
                array(
                    'name' => 'Spain (España)',
                    'alphacode' => 'es',
                    'countryCode' => '+34'
                ),
                array(
                    'name' => 'Sri Lanka (ශ්‍රී ලංකාව)',
                    'alphacode' => 'lk',
                    'countryCode' => '+94'
                ),
                array(
                    'name' => 'Sudan (‫السودان‬‎)',
                    'alphacode' => 'sd',
                    'countryCode' => '+249'
                ),
                array(
                    'name' => 'Suriname',
                    'alphacode' => 'sr',
                    'countryCode' => '+597'
                ),
                array(
                    'name' => 'Svalbard and Jan Mayen',
                    'alphacode' => 'sj',
                    'countryCode' => '+47'
                ),
                array(
                    'name' => 'Swaziland',
                    'alphacode' => 'sz',
                    'countryCode' => '+268'
                ),
                array(
                    'name' => 'Sweden (Sverige)',
                    'alphacode' => 'se',
                    'countryCode' => '+46'
                ),
                array(
                    'name' => 'Switzerland (Schweiz)',
                    'alphacode' => 'ch',
                    'countryCode' => '+41'
                ),
                array(
                    'name' => 'Syria (‫سوريا‬‎)',
                    'alphacode' => 'sy',
                    'countryCode' => '+963'
                ),
                array(
                    'name' => 'Taiwan (台灣)',
                    'alphacode' => 'tw',
                    'countryCode' => '+886'
                ),
                array(
                    'name' => 'Tajikistan',
                    'alphacode' => 'tj',
                    'countryCode' => '+992'
                ),
                array(
                    'name' => 'Tanzania',
                    'alphacode' => 'tz',
                    'countryCode' => '+255'
                ),
                array(
                    'name' => 'Thailand (ไทย)',
                    'alphacode' => 'th',
                    'countryCode' => '+66'
                ),
                array(
                    'name' => 'Timor-Leste',
                    'alphacode' => 'tl',
                    'countryCode' => '+670'
                ),
                array(
                    'name' => 'Togo',
                    'alphacode' => 'tg',
                    'countryCode' => '+228'
                ),
                array(
                    'name' => 'Tokelau',
                    'alphacode' => 'tk',
                    'countryCode' => '+690'
                ),
                array(
                    'name' => 'Tonga',
                    'alphacode' => 'to',
                    'countryCode' => '+676'
                ),
                array(
                    'name' => 'Trinidad and Tobago',
                    'alphacode' => 'tt',
                    'countryCode' => '+1868'
                ),
                array(
                    'name' => 'Tunisia (‫تونس‬‎)',
                    'alphacode' => 'tn',
                    'countryCode' => '+216'
                ),
                array(
                    'name' => 'Turkey (Türkiye)',
                    'alphacode' => 'tr',
                    'countryCode' => '+90'
                ),
                array(
                    'name' => 'Turkmenistan',
                    'alphacode' => 'tm',
                    'countryCode' => '+993'
                ),
                array(
                    'name' => 'Turks and Caicos Islands',
                    'alphacode' => 'tc',
                    'countryCode' => '+1649'
                ),
                array(
                    'name' => 'Tuvalu',
                    'alphacode' => 'tv',
                    'countryCode' => '+688'
                ),
                array(
                    'name' => 'U.S. Virgin Islands',
                    'alphacode' => 'vi',
                    'countryCode' => '+1340'
                ),
                array(
                    'name' => 'Uganda',
                    'alphacode' => 'ug',
                    'countryCode' => '+256'
                ),
                array(
                    'name' => 'Ukraine (Україна)',
                    'alphacode' => 'ua',
                    'countryCode' => '+380'
                ),
                array(
                    'name' => 'United Arab Emirates (‫الإمارات العربية المتحدة‬‎)',
                    'alphacode' => 'ae',
                    'countryCode' => '+971'
                ),
                array(
                    'name' => 'United Kingdom',
                    'alphacode' => 'gb',
                    'countryCode' => '+44'
                ),
                array(
                    'name' => 'United States',
                    'alphacode' => 'us',
                    'countryCode' => '+1'
                ),
                array(
                    'name' => 'Uruguay',
                    'alphacode' => 'uy',
                    'countryCode' => '+598'
                ),
                array(
                    'name' => 'Uzbekistan (Oʻzbekiston)',
                    'alphacode' => 'uz',
                    'countryCode' => '+998'
                ),
                array(
                    'name' => 'Vanuatu',
                    'alphacode' => 'vu',
                    'countryCode' => '+678'
                ),
                array(
                    'name' => 'Vatican City (Città del Vaticano)',
                    'alphacode' => 'va',
                    'countryCode' => '+39'
                ),
                array(
                    'name' => 'Venezuela',
                    'alphacode' => 've',
                    'countryCode' => '+58'
                ),
                array(
                    'name' => 'Vietnam (Việt Nam)',
                    'alphacode' => 'vn',
                    'countryCode' => '+84'
                ),
                array(
                    'name' => 'Wallis and Futuna (Wallis-et-Futuna)',
                    'alphacode' => 'wf',
                    'countryCode' => '+681'
                ),
                array(
                    'name' => 'Western Sahara (‫الصحراء الغربية‬‎)',
                    'alphacode' => 'eh',
                    'countryCode' => '+212'
                ),
                array(
                    'name' => 'Yemen (‫اليمن‬‎)',
                    'alphacode' => 'ye',
                    'countryCode' => '+967'
                ),
                array(
                    'name' => 'Zambia',
                    'alphacode' => 'zm',
                    'countryCode' => '+260'
                ),
                array(
                    'name' => 'Zimbabwe',
                    'alphacode' => 'zw',
                    'countryCode' => '+263'
                ),
                array(
                    'name' => 'Åland Islands',
                    'alphacode' => 'ax',
                    'countryCode' => '+358'
                ),
            );
            return $countries;
}

function mo_otp_login_page()
{
    $admin_email = commonOtpUtilities::__getDBLoadResult('email', '#__miniorange_otp_customer');
    ?>
    <div class="mo_boot_row">
        <div class="mo_boot_col-sm-12 mo_boot_p-3">
            <form name="f" method="post" action="<?php echo Route::_('index.php?option=com_joomlaotp&view=accountsetup&task=accountsetup.verifyCustomer'); ?>">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12 mo_boot_text-center">
                        <?php echo Text::_('COM_MINIORANGE_LOGIN_MERGE');?><hr>
                        <p><?php echo Text::_('COM_MINIORANGE_LOGIN_PAGE_MESSAGE');?></p>
                    </div>
                </div>
                <div class="mo_boot_row mo_boot_mt-3">
                    <div class="mo_boot_col-lg-2 mo_boot_offset-lg-3 mo_boot_col-sm-3 mo_boot_offset-sm-2">
                        <strong><?php echo Text::_('COM_MINIORANGE_EMAIL');?><span class="mo_otp_red">*</span></strong>
                    </div>
                    <div class="mo_boot_col-lg-5 mo_boot_col-sm-6">
                        <input class="mo_boot_form-control otp-textfield" type="email" name="email" id="email" required placeholder="<?php echo Text::_('COM_MINIORANGE_EMAIL_PLACEHOLDER');?>" value="<?php echo $admin_email; ?>"/>
                    </div>
                </div><br>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-lg-2 mo_boot_offset-lg-3 mo_boot_col-sm-3 mo_boot_offset-sm-2">
                        <strong><?php echo Text::_('COM_MINIORANGE_PASSWORD');?><span class="mo_otp_red">*</span></strong>
                    </div>
                    <div class="mo_boot_col-lg-5 mo_boot_col-sm-6">
                        <input class="mo_boot_form-control otp-textfield" type="password" name="password" id="password" required/>
                    </div>
                </div><br>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12 mo_boot_offset-sm-2 mo_boot_offset-lg-4 mo_boot_col-lg-7">
                        <input type="submit" class="mo_boot_btn  mo_boot_btn-primary mo_otp_btns mo_boot_mr-2" value="<?php echo Text::_('COM_MINIORANGE_LOGIN_BUTTON');?>"/>
                        <a class="mo_boot_btn  mo_boot_btn-primary mo_otp_btns mo_boot_mr-2" onclick="back_reg()"><?php echo Text::_('COM_MINIORANGE_BACK_BUTTON');?></a>
                        <button type="button" class="mo_boot_btn mo_boot_btn-danger mo_boot_m-1" onclick="window.open('https://login.xecurify.com/moas/idp/resetpassword');"><?php echo Text::_('COM_MINIORANGE_FORGOT_PASSWORD');?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <form id="otp_cancel_form" method="post" action="<?php echo Route::_('index.php?option=com_joomlaotp&view=accountsetup&task=accountsetup.cancelform'); ?>">
    </form>
    <?php
}

/* Show OTP verification page*/
function mo_otp_show_otp_verification()
{
    ?>
        <div class="mo_boot_col-sm-12 mo_boot_p-3">
            <form name="f" method="post" id="otp_form" action="<?php echo Route::_('index.php?option=com_joomlaotp&view=accountsetup&task=accountsetup.validateOtp'); ?>">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12">
                        <h3><?php echo Text::_('COM_MINIORANGE_EMAIL_VERIFICATION_PAGE_TITLE');?></h3>
                        <hr>
                    </div>
                </div>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-3">
                        <strong><?php echo Text::_('COM_MINIORANGE_OTP');?><span class="mo_otp_red">*</span></strong></td>
                    </div>
                    <div class="mo_boot_col-sm-6">
                        <input class="mo_boot_form-control" autofocus="true" type="text" name="otp_token" required placeholder="<?php echo Text::_('COM_MINIORANGE_OTP_VERIFICATION_PLACEHOLDER');?>"/>
                    </div>
                </div>
                <div class="mo_boot_row mo_boot_mt-4 mo_boot_text-center">
                    <div class="mo_boot_col-sm-12">
                        <input type="submit" value="<?php echo Text::_('COM_MINIORANGE_VALIDATE_OTP_BUTTON');?>" class="mo_boot_btn mo_boot_btn-success"/>
                        <button href="#mo_otp_resend_otp_email" class="mo_boot_btn mo_boot_btn-success" onclick='submit_form()'><?php echo Text::_('COM_MINIORANGE_RESEND_OTP_BUTTON');?></button>
                        <input type="button" value="<?php echo Text::_('COM_MINIORANGE_BACK_BUTTON');?>" id="back_btn" class="mo_boot_btn mo_boot_btn-danger"/>
                    </div>
                </div>
            </form>
        </div>
    <form method="post" action="<?php echo Route::_('index.php?option=com_joomlaotp&view=accountsetup&task=accountsetup.cancelform'); ?>" id="mo_otp_cancel_form">
    </form>
    <form name="f" id="resend_otp_form" method="post" action="<?php echo Route::_('index.php?option=com_joomlaotp&view=accountsetup&task=accountsetup.resendOtp'); ?>">

    </form>
    <hr>
    <div class="mo_boot_row">
        <div class="mo_boot_col-sm-12">
            <h3><?php echo Text::_('COM_MINIORANGE_OTP_NOT_RECEIVED_TITLE');?></h3>
        </div>
        <div class="mo_boot_col-sm-12">
            <form id="phone_verification" method="post" action="<?php echo Route::_('index.php?option=com_joomlaotp&view=accountsetup&task=accountsetup.phoneVerification'); ?>">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12">
                        <?php echo Text::_('COM_MINIORANGE_OTP_NOT_RECEIVED_INFO');?>
                    </div>
                </div>
                <div class="mo_boot_row mo_boot_mt-3">
                    <div class="mo_boot_col-sm-4">
                        <input class="mo_boot_form-control" required="true" pattern="[\+]\d{1,3}\d{10}" autofocus="true" type="text" name="phone_number" id="phone_number" placeholder="<?php echo Text::_('COM_MINIORANGE_OTP_PHONE_PLACEHOLDER');?>" title="Enter phone number without any space or dashes with country code."/>
                    </div>
                    <div class="mo_boot_col-sm-4">
                        <input type="submit" value="<?php echo Text::_('COM_MINIORANGE_SEND_OTP_BUTTON');?>" class="mo_boot_btn mo_boot_btn-success"/>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php
}

/* Create Customer function */
function mo_otp_registration_page()
{
    $current_user = Factory::getUser();
    $isUserEnabled = PluginHelper::isEnabled('user', 'miniorangesendotp');
    $isSystemEnabled = PluginHelper::isEnabled('system', 'miniorangeverifyotp');
    if (!$isSystemEnabled || !$isUserEnabled)
    {
        ?>
        <div id="system-message-container">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <div class="alert alert-error">
                <h4 class="alert-heading">Warning!</h4>
                <div class="alert-message">
                    <h4>This component requires User and System Plugin to be activated. Please activate the
                        following 2
                        plugins
                        to proceed further.</h4>
                    <ul>
                        <li>User - miniOrange OTP Verification</li>
                        <li>PLG_SYSTEM_MINIORANGEVERIFYOTP_NAME</li>
                    </ul>
                    <h4>Steps to activate the plugins.</h4>
                    <ul>
                        <li>In the top menu, click on Extensions and select Plugins.</li>
                        <li>Search for miniOrange in the search box and press 'Search' to display the plugins.
                        </li>
                        <li>Now enable both User and System plugin.</li>
                    </ul>
                </div>
            </div>
        </div>
    <?php
    } ?>

    <!--Register with miniOrange-->
    <div class="mo_boot_row">
        <div class="mo_boot_col-sm-12 mo_boot_p-4">
            <form name="f" method="post" action="<?php echo Route::_('index.php?option=com_joomlaotp&view=accountsetup&task=accountsetup.registerCustomer'); ?>">
                <div class="mo_boot_row">
                
                    <div class="mo_boot_col-sm-12">
                        <div class="mo_boot_col-sm-12 mo_boot_text-center">
                            <?php echo Text::_('COM_MINIORANGE_REGISTER_MERGE');?>
                        </div>
                    </div>
                </div>

                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12">
                        <hr>
                    </div>
                </div>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12">
                        <p class='alert alert-info mo_boot_text-center'>
                            <?php echo Text::_('COM_MINIORANGE_ALERT_MESSAGE');?>
                        </p>
                        <p class="mo_otp_merge"><em>
                        <?php echo Text::_('COM_MINIORANGE_MERGE');?>
                        </p></em>
                    </div>
                </div>
                <div class="mo_boot_row mo_boot_mt-3">
                    <div class="mo_boot_col-sm-3 mo_boot_offset-lg-3 mo_boot_col-lg-2">
                        <strong><?php echo Text::_('COM_MINIORANGE_EMAIL');?><span class="text-red">*</span></strong>
                    </div>
                    <div class="mo_boot_col-sm-9 mo_boot_col-lg-5">
                        <input class="mo_boot_form-control otp-textfield" type="email" name="email" required placeholder="<?php echo Text::_('COM_MINIORANGE_EMAIL_PLACEHOLDER');?>"/>
                    </div>
                </div>
                <br>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-3 mo_boot_offset-lg-3 mo_boot_col-lg-2">
                        <strong><?php echo Text::_('COM_MINIORANGE_PHONE_NUMBER');?></strong>
                    </div>
                    <div class="mo_boot_col-sm-9 mo_boot_col-lg-5">
                        <input class="mo_boot_form-control otp-textfield" type="tel" id="phone"
                            pattern="[\+]\d{11,14}|[\+]\d{1,4}([\s]{0,1})(\d{0}|\d{9,10})" name="phone"
                            title="<?php echo Text::_('COM_MINIORANGE_PHONE_NUMBER_PLACEHOLDER');?>"
                            placeholder="<?php echo Text::_('COM_MINIORANGE_PHONE_NUMBER_PLACEHOLDER');?>"/>
                        <p><em><?php echo Text::_('COM_MINIORANGE_PHONE_SUPPORT');?></em></p>
                    </div>
                </div>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-3 mo_boot_offset-lg-3 mo_boot_col-lg-2">
                        <strong><?php echo Text::_('COM_MINIORANGE_PASSWORD');?><span class="text-red">*</span></strong>
                    </div>
                    <div class="mo_boot_col-sm-9 mo_boot_col-lg-5">
                        <input class="mo_boot_form-control otp-textfield" required type="password" name="password" placeholder="<?php echo Text::_('COM_MINIORANGE_PASSWORD_PLACEHOLDER');?>"/>
                    </div>
                </div>
                <div class="mo_boot_row mo_boot_mt-3">
                    <div class="mo_boot_col-sm-3 mo_boot_offset-lg-3 mo_boot_col-lg-2">
                        <strong><?php echo Text::_('COM_MINIORANGE_CONFIRM_PASSWORD');?><span class="text-red">*</span></strong>
                    </div>
                    <div class="mo_boot_col-sm-9 mo_boot_col-lg-5">
                        <input class="mo_boot_form-control otp-textfield" required type="password" name="confirmPassword" placeholder="<?php echo Text::_('COM_MINIORANGE_CONFIRM_PASSWORD_PLACEHOLDER');?>"/>
                    </div>
                </div>
                <br>
                <div class="mo_boot_col-sm-7 mo_boot_offset-sm-5 mo_otp_disp_flex">
                        <input type="submit" value="<?php echo Text::_('COM_MINIORANGE_REGISTER_BUTTON');?>" class="mo_boot_btn  mo_boot_btn-primary mo_otp_regi_btn mo_otp_btns"/>
                        <a href="#otp_account_exist" class="mo_boot_btn  mo_boot_btn-primary mo_otp_btns" onclick="submit_form()"><?php echo Text::_('COM_MINIORANGE_LOGIN_BUTTON');?></a>
                </div>
            </form>
        </div>
    </div>
    </form>

    <form name="f" id="resend_otp_form" method="post"
            action="<?php echo Route::_('index.php?option=com_joomlaotp&view=accountsetup&task=accountsetup.customerLoginForm'); ?> ">
    </form>
    <?php
}

function mo_otp_form_tab(){
    $current_user = Factory::getUser();
    $isUserEnabled = PluginHelper::isEnabled('user', 'miniorangesendotp');
    $isSystemEnabled = PluginHelper::isEnabled('system', 'miniorangeverifyotp');


}


function mo_otp_settings_tab()
{
    $result = commonOtpUtilities::__getDBValuesWOArray('#__miniorange_otp_customer');
    $enable_otp = $result['registration_otp_type'];
    $enable_during_registration = $result['enable_during_registration'];
    $resend = $result['resend_otp_count'];

    if (commonOtpUtilities::is_customer_registered()) $disabled = true;
    else $disabled = false;
    ?>
    <div class="mo_boot_row">
    <div class="mo_boot_col-sm-12">
            <form action="<?php echo Route::_('index.php?option=com_joomlaotp&view=accountsetup&task=accountsetup.saveOTP'); ?>"
            method="post" name="adminForm" id="otp_form">
                <input id="mo_otp_form_action" type="hidden" name="option9" value="mo_otp"/>

                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12">
                        <strong> <input type="checkbox" name="otp_during_registration" id="otp_during_registration" class="otp_during_registration"
                                        value="1" <?php if ($enable_during_registration == 1) echo "checked"; ?>
                                        class="mo_otp_registration" <?php if ($disabled) echo "enabled"; else echo "disabled"; ?>>
                            <?php echo Text::_('COM_MINIORANGE_ENABLE_DURING_REGISTRATION');?>
                        </strong>
                    </div>
                </div><br>

                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-4">
                        <strong><?php echo Text::_('COM_MINIORANGE_VERIFICATION_METHOD');?></strong>
                    </div>
                    <div class="mo_boot_col-sm-6 ps-1">
                        <select class="otp_reg_dropdown mo_boot_form-control" id="failure_response" name="login_otp_type">
                            <option value="" disabled="" selected="selected"><?php echo Text::_('COM_MINIORANGE_SELECT_DEFAULT_VALUE');?></option>
                            <option value="1" <?php if ($enable_otp == 1) echo "selected"; ?>>
                                <?php echo Text::_('COM_MINIORANGE_SELECT_EMAIL');?>
                            </option>
                            <option value="2" <?php if ($enable_otp == 2) echo "selected"; ?>>
                                <?php echo Text::_('COM_MINIORANGE_SELECT_SMS');?>
                            </option>
                            <option value="3" <?php if ($enable_otp == 3) echo "selected"; ?>>
                                <?php echo Text::_('COM_MINIORANGE_SELECT_EMAIL_OR_SMS');?>
                            </option>
                            <option value="4" <?php if ($enable_otp == 4) echo "selected"; ?>>
                                <?php echo Text::_('COM_MINIORANGE_SELECT_EMAIL_AND_SMS');?>
                            </option>
                        </select>
                    </div>
                </div><br>

                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-4">
                        <strong><?php echo Text::_('COM_MINIORANGE_RESEND_TITLE');?></strong>
                    </div>
                    <div class="mo_boot_col-sm-6 ps-1">
                        <strong>
                            <select class="mo_resend_otp_dropdown mo_boot_form-control mo_otp_country_code p-1" name="resend_count">
                                <option value="default" selected="selected" <?php if ($resend == "default") echo "selected";?>><?php echo Text::_('COM_MINIORANGE_RESEND_DEFAULT');?></option>
                                <option value="1" <?php if ($resend == 1) echo "selected"; ?>>1</option>
                                <option value="2" <?php if ($resend == 2) echo "selected"; ?>>2</option>
                                <option value="3" <?php if ($resend == 3) echo "selected"; ?>>3</option>
                                <option value="4" <?php if ($resend == 4) echo "selected"; ?>>4</option>
                            </select>
                        </strong>
                    </div>
                </div>
                <div class="mo_boot_row mo_boot_mt-3">
                    <div class="mo_boot_col-sm-12">
                        <?php echo Text::_('COM_MINIORANGE_RESEND_OTP_NOTE');?>
                    </div>
                </div><br>

                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12">
                        <?php echo Text::_('COM_MINIORANGE_CUSTOM_PHONE_FIELD_HELP');?>
                    </div>
                </div><br>

                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12 mo_boot_text-center">
                        <input type="submit" class="mo_boot_btn  mo_boot_btn-primary mo_otp_btns"
                                value="<?php echo Text::_('COM_MINIORANGE_SAVE_SETTINGS_BUTTON');?>" <?php if ($disabled) echo "enabled"; else echo "disabled"; ?>>
                    </div>
                </div><br>
                
            </form>
    </div>
    </div>
    <?php
}

function mo_otp_account_page()
{
    $sample = MoOtpUtility::fetchLicense();
    $sample = json_decode($sample);
    MoOtpUtility::updateLicenseDetails($sample);
    $result = commonOtpUtilities::__getDBValuesWOArray('#__miniorange_otp_customer');
    $transctionUrl = "<a href='".commonOtpUtilities::getHostname()."/moas/login?username=".$result['email']."&redirectUrl=".commonOtpUtilities::getHostname()."/moas/viewtransactions;' target='_blank'>Check here</a>";

    $email = isset($result['email']) ? $result['email'] : '';
    $email_count = isset($result['email_count']) ? $result['email_count'] : '';
    $sms_count= isset($result['sms_count']) ? $result['sms_count'] : '';
    $license_Plan = isset($result['license_plan']) ? $result['license_plan'] : '';
    $isUserEnabled = PluginHelper::isEnabled('user', 'miniorangesendotp');
    $isSystemEnabled = PluginHelper::isEnabled('system', 'miniorangeverifyotp');
    $jVersion           = new Version();
    $phpVersion         = phpversion();
    $jCmsVersion        = $jVersion->getShortVersion();
    $moPluginVersion    = commonOtpUtilities::GetPluginVersion();
    if (!$isSystemEnabled || !$isUserEnabled)
    {
        ?>
        <div id="system-message-container">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <div class="alert alert-error">
            <h4 class="alert-heading">Warning!</h4>
            <div class="alert-message">
                <h4>This component requires User and System Plugin to be activated. Please activate the following 2 plugins to proceed further.</h4>
                <ul>
                    <li>User - miniOrange OTP Verification</li>
                    <li>PLG_SYSTEM_MINIORANGEVERIFYOTP_NAME</li>
                </ul>
                <h4>Steps to activate the plugins.</h4>
                <ul>
                    <li>In the top menu, click on Extensions and select Plugins.</li>
                    <li>Search for miniOrange in the search box and press 'Search' to display the plugins.
                    </li>
                    <li>Now enable both User and System plugin.</li>
                </ul>
            </div>
        </div>
    </div>
    <?php
    }
    $url = "https://login.xecurify.com/moas/login?username=$email&redirectUrl=https://login.xecurify.com/moas/viewtransactions " ?>

<!--                <div class="mo_boot_col-sm-12"><br></div>-->
    <div class="mo_boot_col-sm-12">
    <p class='mo_otp_welcome_message mo_boot_py-2'><?php echo Text::_('COM_MINIORANGE_WELCOME_PAGE_MESSAGE');?><p>
    </div>

    <div class="mo_boot_col-sm-12">
        <input type="submit" onclick="click_to_view_transaction()" value="<?php echo Text::_('COM_MINIORANGE_VIEW_TNX_BUTTON');?>"
                class="mo_boot_btn  mo_boot_btn-primary mo_otp_btns mo_otp_report_btn"/>
    </div>
    <script>
        var url = "<?php echo $url ?>";

        function click_to_view_transaction() {
            window.open(url, "_blank");
        }
    </script>

    <div class="mo_boot_col-sm-12"><br></div>
    <div class="mo_boot_col-sm-12 mo_boot_text-center">
        <h3><?php echo Text::_('COM_MINIORANGE_PROFILE_TITLE');?></h3>
    </div>
    <div class="mo_boot_col-sm-12"><hr></div>
    <table class="mo_boot_table mo_boot_table-striped mo_boot_table-hover mo_boot_table-bordered">
        <tr>
            <td class="profile_style mo_otp_profile"><strong><?php echo Text::_('COM_MINIORANGE_PROFILE_USERNAME_OR_EMAIL');?></strong></td>
            <td class="profile_style"><?php echo $email ?></td>
        </tr>
        <tr>
            <td class="profile_style mo_otp_profile"><strong><?php echo Text::_('COM_MINIORANGE_PROFILE_PLUGIN_VERSION');?></strong></td>
            <td class="profile_style"><?php echo $moPluginVersion ?></td>
        </tr>
        <tr>
            <td class="profile_style mo_otp_profile"><strong><?php echo Text::_('COM_MINIORANGE_PROFILE_PHP_VERSION');?></strong></td>
            <td class="profile_style"><?php echo $phpVersion ?></td>
        </tr>
        <tr>
            <td class="profile_style mo_otp_profile"><strong><?php echo Text::_('COM_MINIORANGE_PROFILE_JOOMLA_VERSION');?></strong></td>
            <td class="profile_style"><?php echo $jCmsVersion ?></td>
        </tr>
    </table>
    <div class="mo_boot_col-sm-12"><br></div>

    <div class="mo_boot_col-sm-12 mo_boot_text-center">
        <input type="button" value="<?php echo Text::_('COM_MINIORANGE_REMOVE_ACCOUNT_BUTTON');?>" class="mo_boot_btn mo_boot_btn-danger" onclick="remove()"/>
        <input type="submit" class="mo_boot_btn  mo_boot_btn-primary mo_otp_btns" onclick="window.open('https://portal.miniorange.com/initializepayment?requestOrigin=joomla_otp_verification_plan');" value="<?php echo Text::_('COM_MINIORANGE_UPGRADE_BUTTON');?>">
    </div>
    <div class="mo_boot_col-sm-12"><br></div>
    <div class="mo_boot_col-sm-12"><br></div>
    <form method="post" id="remove_acc" action="<?php echo Route::_('index.php?option=com_joomlaotp&view=accountsetup&task=accountsetup.removeaccount'); ?>" ></form>
    <?php
}

function mo_otp_support()
{
    $joomla_version = new Version();
    $jcms_version = $joomla_version->getShortVersion();
    $result = commonOtpUtilities::__getDBValuesWOArray('#__miniorange_otp_customer');
    $admin_email = isset($result['email']) ? $result['email'] : '';
    $admin_phone = isset($result['admin_phone']) ? $result['admin_phone'] : '';
    ?>
    <div>
        <details class="mo_otp_sup_request">
            <form name="f" method="post" action="<?php echo Route::_('index.php?option=com_joomlaotp&view=accountsetup&task=accountsetup.contactUs'); ?>">
                <div class="mo_boot_row mo_boot_mt-3">
                    <div class="mo_boot_col-sm-12">
                        <p class="mo_boot_text-center"><?php echo Text::_('COM_MINIORANGE_SUPPORT_OR_FEATURE_REQUEST_DESC');?></p>
                        <div class="mo_boot_row mo_boot_mt-3">
                            <div class="mo_boot_col-sm-2 mo_boot_offset-sm-2">
                                <strong><?php echo Text::_('COM_MINIORANGE_EMAIL');?><span class="mo_otp_red">*</span></strong>
                            </div>
                            <div class="mo_boot_col-sm-7 mo_boot_col-lg-5">
                                <input type="email" class="mo_otp_support_table_textbox mo_boot_form-control"
                                        id="query_email" name="query_email" value="<?php echo $admin_email; ?>"
                                        placeholder="<?php echo Text::_('COM_MINIORANGE_EMAIL_PLACEHOLDER1');?>" required/>
                            </div>
                        </div>

                        <div class="mo_boot_row mo_boot_mt-3">
                            <div class="mo_boot_col-sm-2 mo_boot_offset-sm-2">
                                <strong><?php echo Text::_('COM_MINIORANGE_PHONE');?></strong>
                            </div>
                            <div class="mo_boot_col-sm-7 mo_boot_col-lg-5">
                                <input type="text" class="mo_otp_support_table_textbox mo_boot_form-control"
                                        name="query_phone" id="query_phone" value="<?php echo $admin_phone; ?>" pattern="[\+]\d{1,3}\d{10}"
                                        placeholder="<?php echo Text::_('COM_MINIORANGE_OTP_PHONE_PLACEHOLDER');?>"/>
                            </div>
                        </div>

                        <div class="mo_boot_row mo_boot_mt-3">
                            <div class="mo_boot_col-sm-2 mo_boot_offset-sm-2">
                                <strong><?php echo Text::_('COM_MINIORANGE_QUERY');?><span class="mo_otp_red">*</span></strong>
                            </div>
                            <div class="mo_boot_col-sm-7 mo_boot_col-lg-5">
                                <textarea id="query" name="query" class=" mo_boot_form-control textarea-control mo_otp_custom_msg"
                                            cols="52" mo_boot_rows="5" onkeyup="mo_otp_valid(this)" onblur="mo_otp_valid(this)"
                                            onkeypress="mo_otp_valid(this)" placeholder="<?php echo Text::_('COM_MINIORANGE_QUERY_PLACEHOLDER');?>"></textarea>
                            </div>
                        </div><br>

                        <div class="mo_boot_row">
                            <div class="mo_boot_col-sm-12 mo_boot_text-center">
                                <input type="submit" name="send_query" id="send_query" value="<?php echo Text::_('COM_MINIORANGE_SUBMIT_QUERY_BUTTON');?>" class="mo_boot_btn  mo_boot_btn-primary mo_otp_btns"/>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <summary>
                <?php echo Text::_('COM_MINIORANGE_SUPPORT_OR_FEATURE_REQUEST');?>
            </summary>
        </details>
    </div>
    <?php if ($jcms_version[0] != 4) echo '<br>';?>
    <div hidden id="mootp-feedback-overlay"></div>
    <?php
}

function exportConfiguration(){
    ?>
<div class="container-fluid mo_boot_m-0 mo_boot_p-0">
    <div class="mo_boot_row mo_ldap_tab_theme mo_boot_p-0">
        <div class="export-configuration">
            <h3 class="mo_export_heading mo_boot_pt-4"><?php echo Text::_('COM_MINIORANGE_EXPORT_CONFIGURATION'); ?></h3>
            <p>
                <?php echo Text::_('COM_MINIORANGE_EXPORT_CONFIGURATION_TEXT'); ?>
            </p>
            <form action="<?php echo Route::_('index.php?option=com_joomlaotp&task=accountsetup.exportConfiguration'); ?>" method="post">
                <button type="submit" class="mo_boot_btn  mo_boot_btn-primary mo_otp_btns"><?php echo Text::_('COM_MINIORANGE_EXPORT_CONFIG'); ?></button>
            </form>
            <?php echo HTMLHelper::_('form.token'); ?>
            <hr>
            <h3 class="mo_export_heading mo_boot_pt-4"><?php echo Text::_('COM_MINIORANGE_IMPORT_CONFIGURATION'); ?></h3>
            <p>
                <?php echo Text::_('COM_MINIORANGE_IMPORT_CONFIGURATION_TEXT'); ?>
            </p>
            <form action="<?php echo Route::_('index.php?option=com_joomlaotp&task=accountsetup.importConfiguration'); ?>" method="post" enctype="multipart/form-data">
                <div class="mo_upload_container">
                    <input type="file" id="fileInput" name="file" accept=".json" class="mo_boot_mb-4" onchange="displayFileName()">
                </div>
                <button type="submit" class="mo_boot_btn  mo_boot_btn-primary mo_otp_btns mo_boot_mb-4"><?php echo Text::_('COM_MINIORANGE_IMPORT_CONFIG'); ?></button>
                <?php echo HTMLHelper::_('form.token'); ?>
            </form>
            <script>
                function displayFileName() {
                    const fileInput = document.getElementById('fileInput');
                    const fileName = document.getElementById('fileName');
                    if (fileInput.files.length > 0) {
                        fileName.textContent = fileInput.files[0].name;
                    } else {
                        fileName.textContent = '';
                    }
                }
            </script>
        </div>
    </div>
</div>
    <?php
}