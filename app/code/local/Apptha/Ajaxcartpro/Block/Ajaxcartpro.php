<?php
/**
 * Apptha
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    Apptha
 * @package     Apptha_AjaxCartPro
 * @version     1.1
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 * */

/*
 * Ajaxcartpro Block class for Layout 
 * 
 * see the pop-up ajax content
 */

class Apptha_Ajaxcartpro_Block_Ajaxcartpro extends Mage_Core_Block_Template {
   
    /*
     * Preparing the ajax cart pro layout if extension is enabled
     * 
     * Load JS and CSS files if it enabled
     */

    public function _prepareLayout() {
        if (Mage::getStoreConfig('ajaxcartpro/general/Enable_cart_update')) {
            $this->getLayout()->getBlock('head')->addJs('ajaxcartpro/ajaxcartpro.js');
            $this->getLayout()->getBlock('head')->addJs('varien/product.js');
            $this->getLayout()->getBlock('head')->addJs('varien/configurable.js');

            $this->getLayout()->getBlock('head')->addCss('ajaxcartpro/css/ajaxcartpro.css');
        }
        return parent::_prepareLayout();
    }

    public function getAjaxcart() {
        if (!$this->hasData('ajaxcartpro')) {
            $this->setData('ajaxcartpro', Mage::registry('ajaxcartpro'));
        }
        return $this->getData('ajaxcartpro');
    }

}