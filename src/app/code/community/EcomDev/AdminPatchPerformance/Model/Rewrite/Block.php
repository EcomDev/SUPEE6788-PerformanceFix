<?php

class EcomDev_AdminPatchPerformance_Model_Rewrite_Block
    extends Mage_Admin_Model_Block
{
    /**
     * Check is block with such type allowed for parsing via blockDirective method
     *
     * @param $type
     * @return bool
     */
    public function isTypeAllowed($type)
    {
        return Mage::getSingleton('ecomdev_adminperformance/permission')
            ->isAllowedBlockType($type);
    }
}
