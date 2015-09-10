<?php
/*
 * Irvine Systems Award Points
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Sale Extension
 * @package		IrvineSystems_AwardPoints
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_Awardpoints_Model_System_Config_Cron_Expiration extends Mage_Core_Model_Config_Data
{
	// CRON Constants
    const CRON_STRING_PATH  = 'crontab/jobs/awardpoints_expiration_points_cron/schedule/cron_expr';
    const CRON_MODEL_PATH   = 'crontab/jobs/awardpoints_expiration_points_cron/run/model';

    /**
     * Cron settings after save
     *
     * @return Mage_Adminhtml_Model_System_Config_Backend_Log_Cron
     */
    //protected function _afterSave()
    protected function _afterLoad()
    {
		// Get configuration
        $enabled	= Mage::getModel('awardpoints/awardpoints')->getConfig('expiration_enable');
        $time		= Mage::getModel('awardpoints/awardpoints')->getConfig('expiration_time');
        $time = explode(',',$time);
        $weekday	= Mage::getModel('awardpoints/awardpoints')->getConfig('expiration_weekday');
        $date		= Mage::getModel('awardpoints/awardpoints')->getConfig('expiration_monthdate');
        $frequency	= Mage::getModel('awardpoints/awardpoints')->getConfig('expiration_frequency');

		// Update the cron expression if the ntification is enable
        if ($enabled) {
            $cronExprArray = array(
                intval($time[1]),							# Minute
                intval($time[0]),							# Hour
                ($frequency == 'M') ? (int)$date	: '*',	# Day of the Month
                '*',										# Month of the Year
                ($frequency == 'W') ? (int)$weekday : '*',	# Day of the Week
            );
            // Convert the array into a string
			$cronExprString = join(' ', $cronExprArray);
        }
        else {
			// Otherwise reset it
            $cronExprString = '';
        }

		// Updtate the Cron configuration
        try {
            Mage::getModel('core/config_data')
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();

            Mage::getModel('core/config_data')
                ->load(self::CRON_MODEL_PATH, 'path')
                ->setValue((string) Mage::getConfig()->getNode(self::CRON_MODEL_PATH))
                ->setPath(self::CRON_MODEL_PATH)
                ->save();
        }
		// Catch exception if any error are enountered
        catch (Exception $e) {
            Mage::throwException(Mage::helper('awardpoints')->__('Unable to save the cron expression.'));
        }
    }
}