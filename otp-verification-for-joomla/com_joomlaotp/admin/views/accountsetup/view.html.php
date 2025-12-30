<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_joomlaotp
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\MVC\View\HtmlView;
jimport('miniorangeotpplugin.utility.commonOtpUtilities');

/**
 * Account Setup View
 *
 * @since  0.0.1
 */
class JoomlaOtpViewAccountSetup extends HtmlView
{
	function display($tpl = null)
	{
		// Get data from the model
		$this->lists		= $this->get('List');
		//$this->pagination	= $this->get('Pagination');
 
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			Factory::getApplication()->enqueueMessage(500, implode('<br />', $errors));
 
			return false;
		}
		$this->setLayout('accountsetup');
		// Set the toolbar
		$this->addToolBar();
 
		// Display the template
		parent::display($tpl);
	}
 
	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolBar()
	{
        ToolbarHelper::title(Text::_('mini<span style="color:orange;margin-right:0;"><strong>O</strong></span>range : ') . Text::_('COM_OTP_VERIFICATION_TITLE'), 'mo_otp_logo mo_otp_icon');
	}

	function mo_user_support()
            {
                $result = commonOtpUtilities::__getDBValuesWOArray('#__miniorange_otp_customer');
                $admin_email = isset($result['email']) ? $result['email'] : '';
                $current_user_email = Factory::getUser()->email;
                if ($admin_email == ''){
                    $admin_email = $current_user_email;
                }
                ?>
                <details class="mo_otp_req_quote">
                    <div class="mo_boot_row">
                            <div class="mo_boot_col-sm-12">
                                <form name="f" method="post" action="<?php echo Route::_('index.php?option=com_joomlaotp&view=accountsetup&task=accountsetup.request_setup_call'); ?>">
                                    <div class="mo_boot_row mo_boot_mt-3">
                                        <div class="mo_boot_col-sm-2 mo_boot_offset-sm-2">
                                            <strong><?php echo Text::_('COM_MINIORANGE_REQUEST_FOR_QUOTE_METHOD');?></strong>
                                        </div>
                                        <div class="mo_boot_col-lg-2 mo_boot_col-sm-2">
                                            <div class="mo_boot_d-flex mo_boot_align-items-center mo_boot_gap-2">
                                                <input type="radio" name="type_service" id="sms" value="SMS" CHECKED class="mo_boot_m-0">
                                                <label class="mo_boot_m-1 mo_boot_cursor-pointer" for="sms"><?php echo Text::_('COM_MINIORANGE_SMS_CAPS');?></label>
                                            </div>
                                        </div>
                                        <div class="mo_boot_col-lg-2 mo_boot_col-sm-3">
                                            <div class="mo_boot_d-flex mo_boot_align-items-center mo_boot_gap-2">
                                                <input type="radio" name="type_service" id="email" value="Email" class="mo_boot_m-0">
                                                <label class="mo_boot_m-1 mo_boot_cursor-pointer" for="email"><?php echo Text::_('COM_MINIORANGE_REQUEST_FOR_QUOTE_EMAIL');?></label>
                                            </div>
                                        </div>
                                        <div class="mo_boot_col-lg-2 mo_boot_col-sm-3 mo_boot_p-0 mo_boot_m-0">
                                            <div class="mo_boot_d-flex mo_boot_align-items-center mo_boot_gap-2">
                                                <input type="radio" name="type_service" id="both" value="Both" class="mo_boot_m-0">
                                                <label class="mo_boot_m-1 mo_boot_cursor-pointer" for="both"><?php echo Text::_('COM_MINIORANGE_REQUEST_FOR_QUOTE_BOTH');?></label>
                                            </div>
                                        </div>
                                    </div><br>
                                    <div class="mo_boot_row">
                                        <div class="mo_boot_col-sm-2 mo_boot_offset-sm-2">
                                            <strong><?php echo Text::_('COM_MINIORANGE_EMAIL');?><span class="mo_otp_red">*</span></strong>
                                        </div>
                                        <div class="mo_boot_col-sm-7 mo_boot_col-lg-5">
                                            <input type="email" name="email" id="email" class="mo_boot_form-control" value="<?php echo $admin_email?>" placeholder="<?php echo Text::_('COM_MINIORANGE_EMAIL_PLACEHOLDER1');?>" required>
                                        </div>
                                    </div><br>
                                    <div class="mo_boot_row">
                                        <div class="mo_boot_col-sm-2 mo_boot_offset-sm-2">
                                            <strong><?php echo Text::_('COM_MINIORANGE_REQUEST_FOR_QUOTE_NO_OF_OTPS');?><span class="mo_otp_red">*</span></strong>
                                        </div>
                                        <div class="mo_boot_col-sm-7 mo_boot_col-lg-5">
                                            <input type="text" name="no_of_otp" id="no_of_otp" class="mo_boot_form-control" pattern="^[1-9][0-9]*$" placeholder="<?php echo Text::_('COM_MINIORANGE_REQUEST_FOR_QUOTE_NO_OF_OTPS_PLACEHOLDER');?>" required>
                                        </div>
                                    </div><br>

                                    <div class="mo_boot_row mo_otp_trans_report" id="sms_required">
                                        <div class="mo_boot_col-sm-2 mo_boot_offset-sm-2">
                                            <strong><?php echo Text::_('COM_MINIORANGE_REQUEST_FOR_QUOTE_REQUIRED');?></strong>
                                        </div>
                                        <div class="mo_boot_col-sm-7 mo_boot_col-lg-5" id="type_country">
                                            <select name="select_country" id="select_country" class="mo_boot_form-control mo_otp_select_country">
                                                <option value="default" disabled selected><?php echo Text::_('COM_MINIORANGE_REQUEST_FOR_QUOTE_SELECT');?></option>
                                                <option value="all country"><?php echo Text::_('COM_MINIORANGE_REQUEST_FOR_ALL_COUNTRIES');?></option>
                                                <option value="single country"><?php echo Text::_('COM_MINIORANGE_REQUEST_FOR_SINGLE_COUNTRIES');?></option>
                                            </select>
                                            <br>
                                        </div>
                                    </div>

                                    <div class="mo_boot_row">
                                        <div class="mo_boot_col-sm-2 mo_boot_offset-sm-2 country_name mo_otp_disp_no" id="country_name">
                                            <strong><?php echo Text::_('COM_MINIORANGE_REQUEST_FOR_QUOTE_COUNTRY_NAME');?></strong>
                                        </div>
                                        <div class="mo_boot_col-sm-7 mo_boot_col-lg-5 select_type_country mo_otp_disp_no" id="select_type_country">
                                            <select class="mo_boot_form-control" name="which_country" id="which_country"  >
                                                <option value="default" disabled selected><?php echo Text::_('COM_MINIORANGE_REQUEST_FOR_QUOTE_SELECT_COUNTRY');?></option>
                                               <?php
                                                    $countries=getCountryCodeList();
                                                    foreach($countries as $data)
                                                    {
                                                        if($data['name']!="All Countries")
                                                        echo "<option value='".$data['name']."'>".$data['name']."</option>";
                                                    }
                                               ?>
                                            </select>
                                            <br>
                                        </div>
                                    </div>
                                    <div class="mo_boot_row">
                                        <div class="mo_boot_col-sm-2 mo_boot_offset-sm-2">
                                            <strong><?php echo Text::_('COM_MINIORANGE_REQUEST_FOR_QUOTE_EXTRA_QUERY');?></strong>
                                        </div>
                                        <div class="mo_boot_col-sm-7 mo_boot_col-lg-5">
                                            <textarea name="user_extra_requirement" id="user_extra_requirement" class="mo_boot_form-control textarea-control mo_otp_extra_query" cols="30" mo_boot_rows="5" placeholder="<?php echo Text::_('COM_MINIORANGE_REQUEST_FOR_QUOTE_EXTRA_QUERY_PLACEHOLDER');?>"></textarea>
                                        </div>
                                    </div><br>
                                    <div class="mo_boot_row">
                                        <div class="mo_boot_col-sm-12 mo_boot_text-center">
                                            <input type="submit" value="<?php echo Text::_('COM_MINIORANGE_SUBMIT_BUTTON');?>" class="btn btn-primary mo_otp_btns">
                                        </div>
                                    </div>
                                </form>
                            </div>
                    </div>
                    <summary><?php echo Text::_('COM_MINIORANGE_REQUEST_FOR_QUOTE');?></summary>
                </details>
                <br>
                <script>
                    
                    jQuery(document).change(function()
                    {
                        var selectedVal = ""; 
                        jQuery('input:radio[name="type_service"]').change(function(){
                            selectedVal =    jQuery(this).val();         
                        });
                        var elms = document.querySelectorAll("input[type='radio'][name='type_service']");
                        for(var i = 0; i < elms.length; i++) 
                        {
                            if(jQuery(elms[i]).prop("checked")==true)
                            {
                                selectedVal = jQuery("input[type='radio'][name='type_service']:checked").val();
                            }
                        }
                        if(selectedVal==="SMS" || selectedVal === "Both")
                        {
                            jQuery('#sms_required').css('display','flex');
                            var elms1 = document.querySelectorAll("[id='select_type_country']");
                            for(var i = 0; i < elms1.length; i++) 
                            {
                                jQuery(elms1[i]).css('display','none');
                                if(jQuery('#select_country option:selected').text()=="<?php echo Text::_('COM_MINIORANGE_REQUEST_FOR_SINGLE_COUNTRIES');?>")
                                {
                                    jQuery('#select_type_country').css('display','block');
                                    jQuery('#country_name').css('display','block');
                                }
                                else
                                {
                                    jQuery('#country_name').css('display','none');
                                }
                            }
                            var elms2 = document.querySelectorAll("[id='type_country']");
                            for(var i = 0; i < elms2.length; i++) 
                            {
                                jQuery(elms2[i]).css('display','');
                            }
                        }
                        else if(selectedVal==="Email")
                        {
                            jQuery('#sms_required').css('display','none');
                            jQuery('#country_name').css('display','none');
                            var elms1 = document.querySelectorAll("[id='select_type_country']");
                            for(var i = 0; i < elms1.length; i++) 
                            {
                                jQuery(elms1[i]).css('display','none');
                            }
                            var elms2 = document.querySelectorAll("[id='type_country']");
                            for(var i = 0; i < elms2.length; i++) 
                            {
                                jQuery(elms2[i]).css('display','none');
                            }             
                        }
                    });
                    jQuery('#select_country').click(function()
                    {
                        if(jQuery('#select_country option:selected').text()=="<?php echo Text::_('COM_MINIORANGE_REQUEST_FOR_SINGLE_COUNTRIES');?>")
                        {
                            jQuery('#select_type_country').css('display','block');
                            jQuery('#country_name').css('display','block');
                        }
                    });
                </script>

                <?php 
            }
}