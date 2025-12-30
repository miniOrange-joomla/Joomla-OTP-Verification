<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;

/**
 * Script file of miniorange otp verification.
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
class pkg_MINIORANGEOTPVERIFICATIONInstallerScript
{
    /**
     * This method is called after a component is installed.
     *
     * @param  \stdClass $parent - Parent object calling this method.
     *
     * @return void
     */
    public function install($parent) 
    {
        require_once JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_joomlaotp' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'mo_otp_customer_setup.php';

        $siteName = $_SERVER['SERVER_NAME'];
        $currentUser = Factory::getUser();
        $currentUserEmail = $currentUser->email;    
        $jVersion           = new Version();
        $moPluginVersion = commonOtpUtilities::GetPluginVersion();
        $jCmsVersion = $jVersion->getShortVersion();
        $phpVersion = phpversion();
        $serverSoftware = isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : 'Unknown';
        $webServer = !empty($serverSoftware) ? trim(explode('/', $serverSoftware)[0]) : 'Unknown';
        $query1 = '[Plugin ' . $moPluginVersion . ' | PHP ' . $phpVersion .' | Joomla Version '. $jCmsVersion .' | Web Server '. $webServer .']';
        $content = '<div>
            Hello,<br><br>
            Plugin has been successfully installed on the following site.<br><br>
            <strong>Company:</strong> <a href="http://' . $siteName . '" target="_blank">' . $siteName . '</a><br>
            <strong>Admin Email:</strong> <a href="mailto:' . $currentUserEmail . '">' . $currentUserEmail . '</a><br>
            <strong>System Information:</strong> ' . $query1 . '<br><br>
        </div>';
        $moOtpCustomer = new MoOtpCustomer();
        $moOtpCustomer->send_tfa_test_mail($currentUserEmail, $content);    }

    /**
     * This method is called after a component is uninstalled.
     *
     * @param  \stdClass $parent - Parent object calling this method.
     *
     * @return void
     */
    public function uninstall($parent) 
    {
        //echo '<p>' . Text::_('COM_HELLOWORLD_UNINSTALL_TEXT') . '</p>';
    }

    /**
     * This method is called after a component is updated.
     *
     * @param  \stdClass $parent - Parent object calling object.
     *
     * @return void
     */
    public function update($parent) 
    {
        //echo '<p>' . Text::sprintf('COM_HELLOWORLD_UPDATE_TEXT', $parent->get('manifest')->version) . '</p>';
    }

    /**
     * Runs just before any installation action is performed on the component.
     * Verifications and pre-requisites should run in this function.
     *
     * @param  string    $type   - Type of PreFlight action. Possible values are:
     *                           - * install
     *                           - * update
     *                           - * discover_install
     * @param  \stdClass $parent - Parent object calling object.
     *
     * @return void
     */
    public function preflight($type, $parent) 
    {
        //echo '<p>' . Text::_('COM_HELLOWORLD_PREFLIGHT_' . $type . '_TEXT') . '</p>';
    }

    /**
     * Runs right after any installation action is performed on the component.
     *
     * @param  string    $type   - Type of PostFlight action. Possible values are:
     *                           - * install
     *                           - * update
     *                           - * discover_install
     * @param  \stdClass $parent - Parent object calling object.
     *
     * @return void
     */
    function postflight($type, $parent) 
    {
       if ($type == 'uninstall') {
        return true;
        }
       $this->showInstallMessage('');
    }

    protected function showInstallMessage($messages=array()) {
        ?>
        <style>
        
	.mo-row {
		width: 100%;
		display: block;
		margin-bottom: 2%;
	}

	.mo-row:after {
		clear: both;
		display: block;
		content: "";
	}
    </style>
        <?php
            $lang =Factory::getApplication()->getLanguage();
            $lang->load('pkg_miniorangeotpverification',JPATH_ROOT);
            $lang->load('com_joomlaotp',JPATH_ROOT);
            echo Text::_('COM_INSTALLATION_MSG');
        ?>

    	<div class="mo-row">
            <a class="btn btn-secondary" style="background-color: #226a8b; color : white" href="index.php?option=com_joomlaotp&view=accountsetup&tab-panel=account"><?php echo Text::_('COM_MINIORANGE_START_USING_OTP_PLUGIN');?></a>
            <a class="btn btn-secondary" style="background-color: #226a8b; color : white" href="https://plugins.miniorange.com/otp-verification-joomla" target="_blank"><?php echo Text::_('COM_MINIORANGE_READ_MINIORANGE_DOCUMENTS');?></a>
		    <a class="btn btn-secondary" style="background-color: #226a8b; color : white" href="https://www.miniorange.com/contact" target="_blank"><?php echo Text::_('COM_MINIORANGE_GET_SUPPORT');?></a>
        </div>
        <?php
    }
  
}