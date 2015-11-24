<?php

/**
 * Permission retrieval
 *
 * Single place of truth for template parsing,
 * so no additional DB operations are executed
 *
 */
class EcomDev_AdminPatchPerformance_Model_Permission
{
    const CACHE_KEY_BLOCK = 'ecomdev_patch_permission_block';
    const CACHE_KEY_VARIABLE = 'ecomdev_patch_permission_variable';

    const XML_PATH_CREATE_BLOCK = 'admin/security/ecomdev_template_block_auto';
    const XML_PATH_CREATE_VARIABLE = 'admin/security/ecomdev_template_variable_auto';

    /**
     * ACL hash of allowed block types
     *
     * @var bool[]
     */
    private $blockTypes;

    /**
     * List of allowed variable pathess
     *
     * @var bool[]
     */
    private $variablePaths;

    /**
     * Loads cached flags
     *
     * @param string $cacheKey
     * @return bool|bool[]
     */
    public function loadCachedFlags($cacheKey)
    {
        if (!Mage::app()->useCache('config')) {
            return false;
        }

        $flags = Mage::app()->loadCache($cacheKey);

        if ($flags) {
            return @unserialize($flags);
        }

        return false;
    }

    /**
     * Loads cached flags
     *
     * @param string $cacheKey
     * @return bool|bool[]
     */
    public function saveCacheFlags($cacheKey, $flags)
    {
        if (!Mage::app()->useCache('config')) {
            return $this;
        }

        Mage::app()->saveCache(serialize($flags), $cacheKey);

        return $this;
    }

    /**
     * Init block types
     *
     * @return $this
     */
    private function initBlockTypes()
    {
        $blockTypes = $this->loadCachedFlags(self::CACHE_KEY_BLOCK);
        if (is_array($blockTypes)) {
            $this->blockTypes = $blockTypes;
            return $this;
        }

        $this->blockTypes = [];

        /** @var Mage_Admin_Model_Resource_Block_Collection $collection */
        $collection = Mage::getResourceModel('admin/block_collection');
        foreach ($collection->getData() as $data) {
            $this->blockTypes[$data['block_name']] = (bool)$data['is_allowed'];
        }

        $this->saveCacheFlags(self::CACHE_KEY_BLOCK, $this->blockTypes);

        return $this;
    }

    /**
     * Init variable paths
     *
     * @return $this
     */
    private function initVariablePath()
    {
        $variablePaths = $this->loadCachedFlags(self::CACHE_KEY_BLOCK);
        if (is_array($variablePaths)) {
            $this->variablePaths = $variablePaths;
            return $this;
        }

        $this->variablePaths = [];

        /** @var Mage_Admin_Model_Resource_Block_Collection $collection */
        $collection = Mage::getResourceModel('admin/variable_collection');
        foreach ($collection->getData() as $data) {
            $this->variablePaths[$data['variable_name']] = (bool)$data['is_allowed'];
        }

        $this->saveCacheFlags(self::CACHE_KEY_VARIABLE, $this->variablePaths);

        return $this;
    }

    /**
     * Lazy-loads block types
     * and creates a new one if configuration option is enabled
     *
     * @param string $type
     * @return bool
     */
    public function isAllowedBlockType($type)
    {
        if ($this->blockTypes === null) {
            $this->initBlockTypes();
        }


        if (isset($this->blockTypes[$type])) {
            return $this->blockTypes[$type];
        }

        if (Mage::getStoreConfigFlag(self::XML_PATH_CREATE_BLOCK) && $type) {
            try {
                Mage::getModel('admin/block')
                    ->setBlockName($type)
                    ->setIsAllowed('0')
                    ->save();
            } catch (Exception $e) {
                Mage::logException($e);
            }

            $this->blockTypes[$type] = false;
            $this->saveCacheFlags(self::CACHE_KEY_BLOCK, $this->blockTypes);
        }

        return false;
    }

    /**
     * Lazy-loads variable paths,
     * and creates a new one if configuration option is enabled
     *
     * @param string $path
     * @return bool
     */
    public function isAllowedVariablePath($path)
    {
        if ($this->variablePaths === null) {
            $this->initVariablePath();
        }

        if (isset($this->variablePaths[$path])) {
            return $this->variablePaths[$path];
        }

        if (Mage::getStoreConfigFlag(self::XML_PATH_CREATE_VARIABLE) && $path) {
            try {
                Mage::getModel('admin/variable')
                    ->setVariableName($path)
                    ->setIsAllowed('0')
                    ->save();
            } catch (Exception $e) {
                Mage::logException($e);
            }

            $this->variablePaths[$path] = false;
            $this->saveCacheFlags(self::CACHE_KEY_VARIABLE, $this->variablePaths);
        }

        return false;
    }
}
