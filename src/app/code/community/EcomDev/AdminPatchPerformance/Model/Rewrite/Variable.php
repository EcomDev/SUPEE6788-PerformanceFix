<?php

class EcomDev_AdminPatchPerformance_Model_Rewrite_Variable
    extends Mage_Admin_Model_Variable
{
    /**
     * Check is config directive with given path can be parsed via configDirective method
     *
     * @param $path string
     * @return bool
     */
    public function isPathAllowed($path)
    {
        return Mage::getSingleton('ecomdev_adminperformance/permission')
            ->isAllowedVariablePath($path);
    }
}
