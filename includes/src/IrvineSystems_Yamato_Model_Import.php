<?php
/*
 * Irvine Systems Shipping Japan Ymt
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Shipping Extension
 * @package		IrvineSystems_Yamato
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_Yamato_Model_Import extends Varien_Object
{
    // File Parameters Constants
	const FIELD_NAME_SOURCE_FILE	= 'import_file';
    const MAX_TMP_SIZE				= 64000;
    const FILE_NAME_FORMAT			= 'ymt_slips_import_%s.';

    /**
     * Get Working Directory
     * @return string
     */
    public function getWorkingDir()
    {
        return Mage::getBaseDir('var') . DS . 'ymt_slips_import' . DS;
    }

    /**
     * set Upload File
     * 
     * @return IrvineSystems_Yamato_Model_Import
     */
    public function setUploadFile()
    {
		// Initialize upload
        $uploader  = Mage::getModel('core/file_uploader', self::FIELD_NAME_SOURCE_FILE);

		// Skip database processing was integreted from 1.6
		if (version_compare(Mage::getVersion(), '1.6.0.0', '>=')) {
	        $uploader->skipDbProcessing(true);
		}

		// Get uploaded file information
		$result    = $uploader->save(self::getWorkingDir());
        $extension = pathinfo($result['file'], PATHINFO_EXTENSION);

		// Get uploaded file information
        $uploadedFile  = $result['path'] . $result['file'];
        $sourceName    = sprintf( self:: FILE_NAME_FORMAT, date( 'Ymdhis') ) . $extension;
        $sourceFile    = $result['path'] . $sourceName ;

		// Validate filename and update result object
        if(strtolower($uploadedFile) != strtolower($sourceFile)) {
            if (file_exists($sourceFile)) {
                unlink($sourceFile);
            }
            if (!@rename($uploadedFile, $sourceFile)) {

            }
            $result['file'] = $sourceName;
        }

		// Set file data
        $this->setData( self::FIELD_NAME_SOURCE_FILE , $result);
        return $this;
    }

    /**
     * Public getter for file Information
     * 
     * @return string
     */
    public function getUploadFile()
    {
        $result = $this->getData( self::FIELD_NAME_SOURCE_FILE );
        return $result['path'] . $result['file'];
    }
}