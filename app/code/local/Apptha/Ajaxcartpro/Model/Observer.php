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

/**
 * Ajax Cartpro Model class for Observer
 */

class Apptha_Ajaxcartpro_Model_Observer extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('ajaxcartpro/ajaxcartpro');
    }

    /**
     * This function is used for loads the ajax cart pro pop-up when customer clicking the Add To Cart Button
     * 
     * Also it prevents the redirection of cart page 
     */
    public function addtoCart() {
        if (Mage::getStoreConfig('ajaxcartpro/general/Enable_cart_update')) {
            $request = Mage::app()->getFrontController()->getRequest();

            Mage::getSingleton('checkout/session')->setNoCartRedirect(true);
            Mage::getModel('ajaxcartpro/ajaxresponse')
                    ->setCart(Mage::helper('ajaxcartpro')->rendercartpageUpdate())
                    ->setSidebar(Mage::helper('ajaxcartpro')->cartItemssidebar())
                    ->setLinks(Mage::helper('ajaxcartpro')->topLinkTitle())
                    ->send();
        }
    }

    /**
     * Function for appending the Custom options for cart items
     * 
     */
 
    public function ajaxCustomOptions($observer) {
        $params = $observer->getControllerAction()->getRequest()->getParams();
        if (!isset($params['options']) || $params['options'] != 'cart' || !isset($params['ajaxcustomoption']))
            return;
        $product = Mage::registry('current_product');
        if (!$product->isConfigurable() && $product->getTypeId() != 'simple') {
            $msg = 'false';
            $observer->getControllerAction()->getResponse()->setBody($msg);;
        }
        $block = Mage::getSingleton('core/layout');
        $options = $block->createBlock('catalog/product_view_options', 'product_options')
                ->setTemplate('catalog/product/view/options.phtml')
                ->addOptionRenderer('text', 'catalog/product_view_options_type_text', 'catalog/product/view/options/type/text.phtml')
                ->addOptionRenderer('select', 'catalog/product_view_options_type_select', 'catalog/product/view/options/type/select.phtml');
        $price = $block->createBlock('catalog/product_view', 'product_price')
                ->setTemplate('catalog/product/view/price_clone.phtml');
        $js = $block->createBlock('core/template', 'product_js')
                ->setTemplate('catalog/product/view/options/js.phtml');
        if ($product->isConfigurable()) {
            $configurable = $block->createBlock('catalog/product_view_type_configurable', 'product_configurable_options')
                    ->setTemplate('catalog/product/view/type/options/configurable.phtml');
        }
        $main = $block->createBlock('catalog/product_view')
                ->setTemplate('ajaxcartpro/customoptions.phtml')
                ->append($options);
        if ($product->isConfigurable())
            $main->append($configurable);

        $main->append($js)->append($price);

        $mainjs = $block->createBlock('catalog/product_view')
                ->setTemplate('ajaxcartpro/customoptionsjs.phtml')
                ->append($options);
        if ($product->isConfigurable())
            $mainjs->append($configurable);

        $mainjs->append($js)->append($price);

        $mainwish = $block->createBlock('catalog/product_view')
                ->setTemplate('ajaxcartpro/wishcustomoptions.phtml')
                ->append($options);
        if ($product->isConfigurable())
            $mainwish->append($configurable);

        $mainwish->append($js)->append($price);

        $observer->getControllerAction()->getResponse()->setBody(json_encode(array('html' => $main->renderView(), 'js' => $mainjs->renderView(), 'wish' => $mainwish->renderView())));
    }

}