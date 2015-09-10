<?php
/*
 * Irvine Systems Award Points
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category    Magento Sale Extension
 * @package        IrvineSystems_AwardPoints
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_Awardpoints_Model_Account extends Mage_Core_Model_Abstract
{
	// Class parameters
    protected $points_received;
    protected $points_spent;

    /**
     * Model Constructor
     * 
     */
    public function _construct()
    {
        // Construct parent
        parent::_construct();
        // Initialize awardpoints Resource
		// @see /etc/config.xml
        $this->_init('awardpoints/awardpoints');
    }

    /**
     * Get all Customers Expiring points before the given days
     * 
     * @param string $days Number of Days before expiration
     * @return array || null
     */
    public function getCustomersExpiration($days)
    {
		// Set the Expiration Date
		$expDate = date("Y-m-d", mktime(date("H"), date("i"), date("s"), date("n"), date("j") + $days, date("Y")));
        // Get the Data Collection
		$collection = $this->getCollection()->addExpDaysFilter($expDate);
        // Group the collection by customer Id
		$collection->getSelect()->group('customer_id');

		// Exit if there is no data
        if (!$collection->getFirstItem()->getData()) return null;
		
		// Get all customer Ids from the points database
		$customerIds = array();
		foreach ($collection as $row) {
			$customerIds[]=$row->getData('customer_id');
		}
		// Define the returning Array
		$expirations = array();
		// Add Customer Information for all ids
		foreach ($customerIds as $id) {
		    // Load Cuatomer Data
			$customerData = Mage::getModel('customer/customer')->load($id);
		    // Get Point Balances
			$expiringPoints = $this->getExpiringPoints($id, $customerData->getStoreId(),$expDate);

		    // Update Balances Array
			$expirations[] = array(
				'customer_id'				=> $customerData->getEntityId(),
				'store_id'					=> $customerData->getStoreId(),
				'name'						=> $customerData->getName(),
				'email'						=> $customerData->getEmail(),
				'expiring_points'			=> $expiringPoints,
				'expiration_date'			=> $expDate,
			);
		}
	    // Return the Expirations
		return $expirations;
	}

    /**
     * Get all Customers Balance Information
     * 
     * @return array || null
     */
    public function getCustomersBalance()
    {
        // Get the Data Collection
		$collection = $this->getCollection();
        // Group the collection by customer Id
		$collection->getSelect()->group('customer_id');
        // Exit if there is no data
        if (!$collection->getFirstItem()->getData()) return null;

		// Get all customer Ids from the points database
		$customerIds = array();
		foreach ($collection as $row) {
			$customerIds[]=$row->getData('customer_id');
		}
		// Define the returning Array
		$balances = array();
		// Add Customer Information for all ids
		foreach ($customerIds as $id) {
			// Reset Class Parameters
			$this->points_received = 0;
			$this->points_spent = 0;

		    // Load Cuatomer Data
			$customerData = Mage::getModel('customer/customer')->load($id);
		    // Get Point Balances
			$pointsRecieved				= $this->getPointsReceived($id, $customerData->getStoreId());
			$pointsSpent				= $this->getPointsSpent($id, $customerData->getStoreId());
			$pointsCurrent				= $pointsRecieved - $pointsSpent;
			$pointsWaiting_validation	= $this->getPointsWaitingValidation($id, $customerData->getStoreId());
		    // Update Balances Array
			$balances[] = array(
				'customer_id'				=> $customerData->getEntityId(),
				'store_id'					=> $customerData->getStoreId(),
				'name'						=> $customerData->getName(),
				'email'						=> $customerData->getEmail(),
				'points_recieved'			=> $pointsRecieved,
				'points_spent'				=> $pointsSpent,
				'points_current'			=> $pointsCurrent,
				'points_waiting_validation'	=> $pointsWaiting_validation,
			);
		}

		// Reset Class Parameters
		$this->points_received = 0;
		$this->points_spent = 0;

	    // Return the Balances
		return $balances;
	}

    /**
     * Check if the Given Customer already has Registration Points
     * 
     * @param string $id Unique Cuatomer Id
     * @return bool
     */
    public function hasRegistrationPoints($id)
    {
        // Get the Data Collection
		$collection = $this->getCollection();
		// Get Points Type identifier
		$pointsType = Mage::getModel('awardpoints/awardpoints')->getRegistrationPointsType();
        // Query the Data
        $collection->getSelect()->where('customer_id = ?', $id);
        $collection->getSelect()->where('points_type = ?', $pointsType);
        // Check the reult of the query
		$row = $collection->getFirstItem()->getData();
        // If we have data return true
        if (!$row) return false;
        // Otherwise return False
		return true;
    }

    /**
     * Check if the Given Customer already has Newsletter Points
     * 
     * @param string $id Unique Cuatomer Id
     * @return bool
     */
    public function hasNewsPoints($id)
    {
        // Get the Data Collection
		$collection = $this->getCollection();
		// Get Points Type identifier
		$pointsType = Mage::getModel('awardpoints/awardpoints')->getNewsPointsType();
        // Query the Data
        $collection->getSelect()->where('customer_id = ?', $id);
        $collection->getSelect()->where('points_type = ?', $pointsType);
        // Check the reult of the query
		$row = $collection->getFirstItem()->getData();
        // If we have data return true
        if (!$row) return false;
        // Otherwise return False
		return true;
    }

    /**
     * Check if the Given Customer already has Newsletter Points
     * 
     * @param string $id Unique Cuatomer Id
     * @return bool
     */
    public function deleteNewsPoints($id)
    {
        // Get the Data Collection
		$collection = $this->getCollection();
		// Get Points Type identifier
		$pointsType = Mage::getModel('awardpoints/awardpoints')->getNewsPointsType();
        // Query the Data
        $collection->getSelect()->where('customer_id = ?', $id);
        $collection->getSelect()->where('points_type = ?', $pointsType);
        // delete the data
		$collection->getFirstItem()->delete();
    }

    /**
     * Check if the relation between customer and order has already been processed for Points.
     * 
     * @params $customer_id (int) Unique Customer ID
     * @params $order_id (int) Unique Order Number ID
     * @return bool
     */
    public function isOrderProcessed($customer_id, $order_id)
    {
        // Get the Data Collection
		$collection = $this->getCollection();
		// Get Points Type identifier
		$pointsType = Mage::getModel('awardpoints/awardpoints')->getOrderPointsType();
        // Query the Data
        $collection->getSelect()->where('customer_id = ?', $customer_id);
        $collection->getSelect()->where('order_id = ?', $order_id);
        $collection->getSelect()->where('points_type = ?', $pointsType);
        // Check the reult of the query
		$row = $collection->getFirstItem()->getData();
        // If we have data return true
        if (!$row) return false;
        // Otherwise return False
		return true;
    }

    /**
     * Get All the curreent point before the given expirqtion date
     * 
     * @param int $customer_id  Unique customer Id
     * @param int $store_id  Unique Store Id
     * @param string $expDate  Expiration Date
     * 
     * @return int|float points Amount
     */
    public function getExpiringPoints($customer_id, $store_id,$expDate)
	{
		// Get all recieve points on the expiration range
		$collection = $this->getCollection();
        $collection->joinValidPoints($customer_id, $store_id)->addExpDaysFilter($expDate);
        $row = $collection->getFirstItem();
		$receivedPoints = $row->getPointsValue();
		// Get all spent points on the expiration range
		$collection = $this->getCollection();
        $collection->joinValidPoints($customer_id, $store_id, true)->addExpDaysFilter($expDate);
        $row = $collection->getFirstItem();
		$spentPoints = $row->getPointsValue();
		//Return the points Difference
		return $receivedPoints-$spentPoints;
    }

    /**
     * Get the current amount of points for the given customer and store relation
     * 
     * @param int $customer_id  Unique customer Id
     * @param int $store_id  Unique Store Id
     * 
     * @return int|float points Amount
     */
    public function getPointsCurrent($customer_id, $store_id)
	{
		// Get thew Customer accumulated points for the given store
		$points = $this->getPointsReceived($customer_id, $store_id);
		// Remove the spent points
		$points -= $this->getPointsSpent($customer_id, $store_id);
		// return the value if positive
		if ($points > 0) return $points;
		// return 0 if by any chance it is negative
		return 0;
    }

    /**
     * Get all valid recieved points for the given customer and store relation
     * 
     * @param int $customer_id  Unique customer Id
     * @param int $store_id  Unique Store Id
     * 
     * @return int|float points Amount
     */
    public function getPointsReceived($customer_id, $store_id)
	{
		// Check if the value is already processed
        if ($this->points_received){
            return $this->points_received;
        }

		// Get Current Collection
		$collection = $this->getCollection();
		// Join all valid orders points
        $collection->joinValidPoints($customer_id, $store_id);

		// Get data row
        $row = $collection->getFirstItem();
        // Set class points_received
		$this->points_received = $row->getPointsValue();

        // Return Point value
        return $row->getPointsValue();
    }

    /**
     * Get all valid spent points for the given customer and store relation
     * 
     * @param int $customer_id  Unique customer Id
     * @param int $store_id  Unique Store Id
     * 
     * @return int|float points Amount
     */
    public function getPointsSpent($customer_id, $store_id)
	{
		// Check if the value is already processed
        if ($this->points_spent){
            return $this->points_spent;
        }

		// Get Current Collection
        $collection = $this->getCollection();
		// Join all valid orders points
        $collection->joinValidPoints($customer_id, $store_id, true);
		// Get data row
        $row = $collection->getFirstItem();
        // Set class points_received
        $this->points_spent = $row->getPointsValue();

		// Customer Recieved Points
        return $row->getPointsValue();
    }

    /**
     * Get all valid spent points for the given customer and store relation
     * 
     * @param int $customer_id  Unique customer Id
     * @param int $store_id  Unique Store Id
     * 
     * @return int|float points Amount
     */
    public function getPointsWaitingValidation($customer_id, $store_id){
        
		// Get Current Collection
		$collection = $this->getCollection()->joinFullCustomerPoints($customer_id, $store_id);
		// Get data row
        $row = $collection->getFirstItem();
		// Customer Recieved Points
		$pointsRecieved = $this->getPointsReceived($customer_id, $store_id);
		// Remove recieved points from total value
		$points = $row->getPointsValue() - $pointsRecieved;

		// Customer Recieved Points
        return $points;
    }
}