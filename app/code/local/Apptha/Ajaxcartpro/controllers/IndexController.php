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
 * Ajaxcartpro index controller which includes the remove the item, check availablity actions 
 */
class Apptha_Ajaxcartpro_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     * Render Apptha ajax cart pro pop-up
     */
    public function indexAction() {

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Function to remove items from Cart
     */
    public function removeAction() {
        $response = Mage::getModel('ajaxcartpro/ajaxresponse');
        $id = $this->getRequest()->getParam('id');
        Mage::getSingleton('checkout/cart')->removeItem($id)->save();
        $response->setCart(Mage::helper('ajaxcartpro')->rendercartpageUpdate());
        $response->setSidebar(Mage::helper('ajaxcartpro')->cartItemssidebar());
        $response->setLinks(Mage::helper('ajaxcartpro')->topLinkTitle());
        $response->send();
    }

    /**
     * Function to items availablity for items while adding the Cart .
     * 
     * Also for whishlist section 
     * 
     * @return array JSON format array
     */
    public function checkavailAction() {

        /**
         * Retrives the @param string $url This param contains product URL
         */
        $url = $this->getRequest()->getParam('product_url');
        if ($url) {
            $ex_url = explode("/", $url);
            $count = count($ex_url) - 4;
            $id = $ex_url[$count];

            /**
             * Product Quantity as customer needs
             */
            if ($id) {
                $coun = count($ex_url) - 5;
                $id = $ex_url[$coun];
            }
        }

        /**
         * Retrive the @param integer $product product id from the cart URL
         */
        $produtId = $this->getRequest()->getParam('product');
        if ($produtId) {
            $id = $produtId;
        }

        /**
         * Product Quantity as customer needs
         */
        $qtyid = $this->getRequest()->getParam('qty');
        if ($qtyid) {
            $qty = $qtyid;
        }

        /**
         * When i clicks the add to cart button in whishlist page
         * 
         * @param string $whishlisturl Description
         */
        $wishlistUrl = $this->getRequest()->getParam('wishlist_url');
        if ($wishlistUrl) {
            $ex_url = explode("/", $wishlistUrl);
            $count = count($ex_url) - 4;
            $id = $ex_url[$count];
            $wishlist = Mage::getModel('wishlist/item')->load($id);
            $wishlitProdId = $wishlist->product_id;

            /**
             * Product Quantity as customer needs in whishlist section
             */
            $coun = count($ex_url) - 1;
            $q_ex = explode("=", $ex_url[$coun]);
            $qty = $q_ex[1];
        }

        $_product = Mage::getModel('catalog/product')->load($wishlitProdId);
        $stocklevel = (int) Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product)->getQty();

        $items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();

        foreach ($items as $item) {
            if ($wishlitProdId == $item->getProductId()) {
                $quantity += $item->getQty();
            }
            $totalQty += $item->getQty();
        }
        $totat_used = $stocklevel - $quantity;

        /**
         * Check if the quanity level is reached to stcklevel
         */
        if ($_product->getId()) {
            if (empty($totat_used)) {
                $message = 'The requested quantity for "' . $_product->getName() . '" is not available';
                $success = false;
            }

            if ($qty > $totat_used) {
                $message = 'The requested quantity for "' . $_product->getName() . '" is not available';
                $success = false;
            }
        }
        $max_sale_qty = Mage::getStoreConfig('cataloginventory/item_options/max_sale_qty');
        $max_sale = $qty + $totalQty;
        if ($max_sale > $max_sale_qty) {
            $message = 'The maximum quantity allowed for purchase is ' . $max_sale_qty;
            $success = false;
        }

        /**
         * choose the whishlist Product custom options
         */
        if ($wishlistUrl) {
            $prductOpt = $_product->getHasOptions();
            if ($prductOpt) {
                $option = '1';
                $message = 'Please specify the product required option(s).';
                $success = false;
                $wish = $wishlistUrl;
                $prourl = $_product->getProductUrl() . "?options=cart";
            }
        }
        $result = array("success" => $success, 'message' => $message, 'op' => $option, 'wish' => $wish, 'prourl' => $prourl);

        Mage::app()->getFrontController()->getResponse()->setBody(Zend_Json::encode($result));
    }

}