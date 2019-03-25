<?php

class Stagem_PortoMegamenu_Helper_Data extends Smartwave_Megamenu_Helper_Data
{
    private $_menuData = null;
    private $_block = null;

    public function getMenuData()
    {
        if (!is_null($this->_menuData)) return $this->_menuData;

        if(!$this->_block) {
			$blockClassName = Mage::getConfig()->getBlockClassName('megamenu/navigation');
			$this->_block = new $blockClassName();
		}
        $categories = $this->_block->getStoreCategories();        
        if (is_object($categories)) $categories = $categories->getNodes();

        $this->_menuData = array(
            '_block'                        => $this->_block,
            '_categories'                   => $categories,
            '_isWide'                       => Mage::getStoreConfig('megamenu/general/wide_style'),
            '_showHomeLink'                 => Mage::getStoreConfig('megamenu/general/show_home_link'),
            '_showHomeIcon'                 => Mage::getStoreConfig('megamenu/general/show_home_icon'),
            '_popupWidth'                   => (int) Mage::getStoreConfig('megamenu/popup/width') + 0
        );        
        return $this->_menuData;
    }
    

}