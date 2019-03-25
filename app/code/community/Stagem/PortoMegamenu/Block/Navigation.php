<?php

class Stagem_PortoMegamenu_Block_Navigation extends Smartwave_Megamenu_Block_Navigation
{
    public function _getActiveChildren($parent, $level)
    {
        $activeChildren = [];
        // --- check level ---
        $maxLevel = (int) Mage::getStoreConfig('megamenu/general/max_level');
        if ($maxLevel > 0) {
            if ($level >= ($maxLevel - 1)) {
                return $activeChildren;
            }
        }
        // --- / check level ---
        if (Mage::helper('catalog/category_flat')->isEnabled()) {
            $children = $parent->getChildrenNodes();
            $childrenCount = $children->count();
        } else {
            $children = Mage::getModel('catalog/category')->getCategories($parent->getId());
            $childrenCount = $children->count();
        }
        $hasChildren = $children && $childrenCount;
        if ($hasChildren) {
            foreach ($children as $child) {
                if ($this->_isCategoryDisplayed($child)) {
                    array_push($activeChildren, $child);
                }
            }
        }

        return $activeChildren;
    }

    private function _isCategoryDisplayed(&$child)
    {
        if (!$child->getIsActive()) {
            return false;
        }
        // === check products count ===
        // --- get collection info ---
        if (!Mage::getStoreConfig('megamenu/general/display_empty_categories')) {
            $data = $this->_getProductsCountData();
            // --- check by id ---
            $id = $child->getId();
            #Mage::log($id); Mage::log($data);
            if (!isset($data[$id]) || !$data[$id]['product_count']) {
                return false;
            }
        }

        // === / check products count ===
        return true;
    }

    private function _getProductsCountData()
    {
        if (is_null($this->_productsCount)) {
            $collection = Mage::getModel('catalog/category')->getCollection();
            $storeId = Mage::app()->getStore()->getId();
            /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */
            $collection->addAttributeToSelect('name')
                ->addAttributeToSelect('is_active')
                ->setStoreId($storeId);
            if (!Mage::helper('catalog/category_flat')->isEnabled()) {
                $collection->setProductStoreId($storeId)
                    ->setLoadProductCount(true);
            }
            $productsCount = [];
            foreach ($collection as $cat) {
                $productsCount[$cat->getId()] = [
                    'name' => $cat->getName(),
                    'product_count' => $cat->getProductCount(),
                ];
            }
            #Mage::log($productsCount);
            $this->_productsCount = $productsCount;
        }

        return $this->_productsCount;
    }
}
