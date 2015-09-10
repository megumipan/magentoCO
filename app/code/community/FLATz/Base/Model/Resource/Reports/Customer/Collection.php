<?php
/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Model_Resource_Reports_Customer_Collection extends Mage_Reports_Model_Resource_Customer_Collection
{
    /**
     * 
     * @return \FLATz_Base_Model_Resource_Reports_Customer_Collection
     */
    public function addNameToSelect()
    {
        if (!Mage::getStoreConfig('flatz_base_japanese/name/enablejp')) {
            return parent::addNameToSelect();
        }
        $fields = array();
        $customerAccount = Mage::getConfig()->getFieldset('customer_account');
        foreach ($customerAccount as $code => $node) {
            if ($node->is('name')) {
                $fields[$code] = $code;
            }
        }

        $adapter = $this->getConnection();
        $concatenate = array();
        if (isset($fields['prefix'])) {
            $concatenate[] = $adapter->getCheckSql(
                '{{prefix}} IS NOT NULL AND {{prefix}} != \'\'',
                'LTRIM(RTRIM({{prefix}}))',
                '\'\'');
        }
        $concatenate[] = 'LTRIM(RTRIM({{lastname}}))';
        if (isset($fields['middlename'])) {
            $concatenate[] = $adapter->getCheckSql(
                '{{middlename}} IS NOT NULL AND {{middlename}} != \'\'',
                'LTRIM(RTRIM({{middlename}}))',
                '\'\'');
        }
        $concatenate[] = 'LTRIM(RTRIM({{firstname}}))';
        if (isset($fields['suffix'])) {
            $concatenate[] = $adapter
                ->getCheckSql('{{suffix}} IS NOT NULL AND {{suffix}} != \'\'', "LTRIM(RTRIM({{suffix}}))", "''");
        }

        $nameExpr = $adapter->getConcatSql($concatenate, ' ');

        $this->addExpressionAttributeToSelect('name', $nameExpr, $fields);
        
        return $this;
    }
}
