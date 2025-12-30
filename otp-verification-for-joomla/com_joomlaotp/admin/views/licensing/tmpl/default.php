<?php
   /**
    * @package     Joomla.Administrator
    * @subpackage  com_miniorange_twofa
    *
    * @license     GNU General Public License version 2 or later; see LICENSE.txt
    */
   defined('_JEXEC') or die('Restricted access');
   use Joomla\CMS\Uri\Uri;
   use Joomla\CMS\Language\Text;
   use Joomla\CMS\Factory;
   use Joomla\CMS\HTML\HTMLHelper;

   HTMLHelper::_('jquery.framework',false);

   $document = Factory::getApplication()->getDocument();
   $document->addStyleSheet(Uri::base() . 'components/com_joomlaotp/assets/css/miniorange_otp.css');
   $document->addStyleSheet(Uri::base() . 'components/com_joomlaotp/assets/css/miniorange_boot.css');
   $document->addStyleSheet('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');

   $mfa_active_tab="license";
    /*
    * Check is curl installed or not, if not show the instructions for installation.
    */
    MoOtpUtility::is_curl_installed();
?>
<div id="account" class="container-fluid mo_otp_license_tab">
    <div class="mo_boot_row">
        <div class="mo_boot_col-sm-12">
            <?php licensingtab(); ?>
        </div>
    </div>
</div>
<?php
   function licensingtab()
    {
        $version = (new Joomla\CMS\Version)->getShortVersion()[0];
        ?>
        <div class="mo_boot_row mo_otp_license_paln">
            <div class="mo_boot_col-sm-12">
                <div class="mo_boot_row mo_boot_mt-2">
                    <div class="mo_boot_col-sm-12 mo_boot_mt-4 mo_otp_plan_head">
                        <h2><?php echo Text::_('COM_MINIORANGE_PLANS_AND_FEATURES_TITLE');?></h2>
                        <a href="<?php echo Uri::base().'index.php?option=com_joomlaotp&tab-panel=account';?>" class="mo_boot_btn mo_boot_btn-primary mo_otp_plan_back mo_otp_btns"><?php echo Text::_('COM_MINIORANGE_BACK_TO_PLUGIN_BUTTON');?></a>
                        <hr>
                    </div>
                </div>
                <div class="mo_boot_row mo_boot_mt-4 mo_boot_text-center">
                    <div class="tfa_plans_container mx-auto mo_otp_plan_card">
                        <div class="mo_boot_row mo_boot_m-1 mo_otp_first_card">
                            <div class="mo_boot_col-sm-12 mo_boot_mt-4">
                                <h2><?php echo Text::_('COM_MINIORANGE_MO_GATEWAY');?></h2>
                                <hr>
                                <span class="mo_otp_plan_description"></span><br>
                                <span id="plus_total_price" class="mo_otp_plan_price">$0</span><br>
                                <span id="plus_total_price" class="mo_otp_plan_desc">+</span><br>
                                <span id="plus_total_price" class="mo_otp_plan_desc">SMS/Email transactional charges</span><br><br>

                                <u><h5><?php echo Text::_('COM_MINIORNAGE_MO_GATEWAY_INFO');?></h5></u>
                                <u><h5><?php echo Text::_('COM_MINIORNAGE_MO_GATEWAY_INFO1');?></h5></u>
            

                            </div>
                            <div class="mo_boot_col-sm-12">
                                <div class="mo_boot_row">
                                    <div class="mo_boot_col-sm-12 mo_boot_py-3 mo_otp_first_feature">
                                        <span><?php echo Text::_('COM_MINIORANGE_EMAIL_VERIFICATION');?></span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-3 mo_otp_features">
                                        <span><?php echo Text::_('COM_MINIORANGE_PHONE_VERIFICATION');?></span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-3 mo_otp_features">
                                        <span><?php echo Text::_('COM_MINIORANGE_CUSTOM_EMAIL_TEMPLATE');?></span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-3 mo_otp_features">
                                        <span><?php echo Text::_('COM_MINIORANGE_CUSTOM_SMS_TEMPLATE');?></span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-3 mo_otp_features">
                                        <span><?php echo Text::_('COM_MINIORANGE_CUSTOM_EMAIL_SMS_MSG');?></span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-3 mo_otp_features">
                                        <span><?php echo Text::_('COM_MINIORANGE_CUSTOM_OTP_LENGTH');?></span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-3 mo_otp_features">
                                        <span><?php echo Text::_('COM_MINIORANGE_CUSTOM_OTP_VALIDITY');?></span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-3 mo_otp_features">
                                        <span><?php echo Text::_('COM_MINIORANGE_SUPPORT_VIRTUEMART');?></span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-3 mo_otp_features">
                                        <span><?php echo Text::_('COM_MINIORANGE_SUPPORT_RSFORM');?></span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-3 mo_otp_features">
                                        <span><?php echo Text::_('COM_MINIORANGE_SUPPORT_FLEXI_CONTACT');?></span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-3 mo_otp_features">
                                        <span><?php echo Text::_('COM_MINIORANGE_CUSTOM_INTEGRATION');?></span>
                                    </div>
                                    
                                    <div class="mo_boot_col-sm-12 mo_boot_py-3 mo_otp_features">
                                        <span><?php echo Text::_('COM_MINIORANGE_EMAIL_SUPPORT');?></span>
                                    </div>
                                  
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tfa_plans_container mx-auto mo_otp_plan_card">
                        <div class="mo_boot_row mo_boot_m-1 mo_otp_first_card">
                            <div class="mo_boot_col-sm-12 mo_boot_mt-4">
                                <h2><?php echo Text::_('COM_MINIORANGE_CUSTOM_GATEWAY');?></h2>
                                <hr>

                                <br>
                                <span id="plus_total_price" class="mo_otp_plan_price">$49</span>
                                <br><br><span ><strong><?php echo Text::_('COM_MINIORANGE_CUSTOM_GATEWAY_ONE_TIME_PAYMENT');?></strong></span><br><br>
                                <u><h5><?php echo Text::_('COM_MINIORANGE_CUSTOM_GATEWAY_INFO');?></h5></u>
                                <u><h5 ><?php echo Text::_('COM_MINIORANGE_CUSTOM_GATEWAY_INFO1');?></h5></u>
                                <br><?php if ($version == '3') echo '<br><br>';?>
                                <input type="submit" class="mo_boot_btn mo_boot_btn-primary mo_otp_btns" onclick="window.open('https://www.miniorange.com/contact')" value="<?php echo Text::_('COM_MINIORANGE_CUSTOM_GATEWAY_CONTACT_BUTTON');?>">
                                <br><br><br><br>
                            </div>
                            <div class="mo_boot_col-sm-12">
                                <div class="mo_boot_row">
                                    <div class="mo_boot_col-sm-12 mo_boot_py-3 mo_otp_first_feature">
                                        <span><?php echo Text::_('COM_MINIORANGE_EMAIL_VERIFICATION');?></span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-3 mo_otp_features">
                                        <span><?php echo Text::_('COM_MINIORANGE_PHONE_VERIFICATION');?></span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-3 mo_otp_features">
                                        <span><?php echo Text::_('COM_MINIORANGE_CUSTOM_EMAIL_TEMPLATE');?></span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-3 mo_otp_features">
                                        <span><?php echo Text::_('COM_MINIORANGE_CUSTOM_SMS_TEMPLATE');?></span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-3 mo_otp_features">
                                        <span><?php echo Text::_('COM_MINIORANGE_CUSTOM_EMAIL_SMS_MSG');?></span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-3 mo_otp_features">
                                        <span><?php echo Text::_('COM_MINIORANGE_CUSTOM_OTP_LENGTH');?></span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-3 mo_otp_features">
                                        <span><?php echo Text::_('COM_MINIORANGE_CUSTOM_OTP_VALIDITY');?></span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-3 mo_otp_features">
                                        <span><?php echo Text::_('COM_MINIORANGE_SUPPORT_VIRTUEMART');?></span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-3 mo_otp_features">
                                        <span><?php echo Text::_('COM_MINIORANGE_SUPPORT_RSFORM');?></span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-3 mo_otp_features">
                                        <span><?php echo Text::_('COM_MINIORANGE_SUPPORT_FLEXI_CONTACT');?></span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-3 mo_otp_features">
                                        <span><?php echo Text::_('COM_MINIORANGE_CUSTOM_INTEGRATION');?></span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-3 mo_otp_features">
                                        <span><?php echo Text::_('COM_MINIORANGE_CUSTOM_SMS_OR_SMTP_GATEWAY');?></span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-3 mo_otp_features">
                                        <span><?php echo Text::_('COM_MINIORANGE_EXTERNAL_GATEWAYS');?></span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-3" >
                                        <span><?php echo Text::_('COM_MINIORANGE_EMAIL_SUPPORT');?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mo_boot_col-sm-12 mo_boot_mt-2">
                <div class="mo_boot_my-1 mo_otp_upgrade_note">
                <?php echo Text::_('COM_MINIORANGE_UPGRADE_NOTE1');?>
                <?php echo Text::_('COM_MINIORANGE_UPGRADE_NOTE2');?>
                </div>
            </div>

            <div class="mo_boot_col-sm-12 mo_boot_my-2">
                <div class="mo_boot_my-4 mo_otp_upgrade_note" id="upgrade-steps">
                    <div>
                        <h2 class="mo_boot_mt-3 mo_otp_text_center"><?php echo Text::_('COM_MINIORANGE_UPGRADE_TO_PREMIUM');?></h2>
                    </div><hr>
            		<section id="section-steps">
                        <div class="mo_boot_col-sm-12 mo_boot_row ms-1">
                            <div class=" mo_boot_col-sm-6 mo_works-step mx-auto">
                                <div><strong>1</strong></div>
                                <?php echo Text::_('COM_MINIORANGE_UPGRADE_STEP1');?>
                            </div>
                            <div class="mo_boot_col-sm-6 mo_works-step mx-auto">
                                <div><strong>4</strong></div>
                                <?php echo Text::_('COM_MINIORANGE_UPGRADE_STEP4');?>
                            </div>            
                        </div>
                        <div class="mo_boot_col-sm-12 mo_boot_row ms-1 ">
                            <div class=" mo_boot_col-sm-6 mo_works-step mx-auto">
                                <div><strong>2</strong></div>
                                <?php $link=Uri::base() . 'index.php?option=com_joomlaotp&tab-panel=account';
                                echo Text::sprintf('COM_MINIORANGE_UPGRADE_STEP2', $link);?>
                            </div>
                            <div class="mo_boot_col-sm-6 mo_works-step mx-auto">
                                <div><strong>5</strong></div>
                                <?php echo Text::_('COM_MINIORANGE_UPGRADE_STEP5');?>
                            </div>     
                        </div>
                        <div class="mo_boot_col-sm-12 mo_boot_row ms-1">
                           <div class="mo_boot_col-sm-6 mo_works-step mx-auto">
                                <div><strong>3</strong></div>
                               <?php echo Text::_('COM_MINIORANGE_UPGRADE_STEP3');?>
                            </div>
                            <div class=" mo_boot_col-sm-6 mo_works-step mx-auto">
                                <div><strong>6</strong></div>
                                <?php echo Text::_('COM_MINIORANGE_UPGRADE_STEP6');?>
                            </div>
                        </div> 
                    </section>
                </div>
                <div class="mo_boot_my-4 mo_otp_upgrade_note">
                    <h2 class="mo_boot_mt-3 mo_otp_text_center"><?php echo Text::_('COM_MINIORANGE_RETURN_POLICY');?></h2><hr>
                    <div class="mo_boot_m-3">
                        <?php echo Text::_('COM_MINIORANGE_RETURN_POLICY_INFO');?>
                        <?php echo Text::_('COM_MINIORANGE_RETURN_POLICY_NOTE');?>
                        <br>
                    </div>
                </div>
            </div>
        </div>
    <?php
}
?>