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
 *  Ajax cart Pro Cart Controller to View / Remove items in Cart
 */

require_once 'Mage/Checkout/controllers/CartController.php';

/**
 * Ajax Cartpro Controller for shopping cart data action
 */
class Apptha_Ajaxcartpro_CartController extends Mage_Checkout_CartController {

    /**
     * Function to view Shopping cart in Pop Up when customer click on the My Cart link
     */
    public function indexAction() {

        /**
         * Cart url having the @param string $ajaxview cart URL having this param loads the ajax cart pro pop-up
         */
        $isAjaxProRequest = $this->getRequest()->getParam('ajaxview', false);

        /**
         * If the @param is occurs in URL to loads the pop-up layout
         */
        if ($isAjaxProRequest) {
            $response = Mage::getModel('ajaxcartpro/ajaxresponse');
            $response->setLinks(Mage::helper('ajaxcartpro')->topLinkTitle());
            $response->setSidebar(Mage::helper('ajaxcartpro')->cartItemssidebar());
            $response->setCart(Mage::helper('ajaxcartpro')->rendercartpageUpdate());
            $response->send();
        }
    }

    /**
     * Function to Update items in Shopping cart
     * 
     * @return string returns the message while updating the shopping cart
     */
    public function updatePostAction() {

        $isAjaxProRequest = $this->getRequest()->getParam('ajaxview', false);

        if ($isAjaxProRequest) {
            $updateCart = $this->getRequest()->getParam('update_cart_action');
            $status = 'SUCCESS';

            /**
             * When cart is empty shows the status message to customer 
             * 
             * the @param string $empty_cart while cart is empty
             */
            if ($updateCart == 'empty_cart') {
                try {
                    
                    /**
                     * Deletes the cart and save into the seesion
                     */
                    $this->_getCart()->truncate()->save();
                    $this->_getSession()->setCartWasUpdated(true);
                } catch (Mage_Core_Exception $exception) {
                    $this->_getSession()->addError($exception->getMessage());
                    $status = 'ERROR';
                } catch (Exception $exception) {
                    $this->_getSession()->addException($exception, $this->__('Cannot update shopping cart.'));
                    $status = 'ERROR';
                }
            } else {
                try {

                    /**
                     * @param array $cart the URL having this param proceeds the further action
                     */
                    $cartData = $this->getRequest()->getParam('cart');

                    /**
                     * Updating the cart items and save into the customer session
                     * 
                     * check if cartdata is array or not
                     */
                    if (is_array($cartData)) {
                        $cart = $this->_getCart();
                        $cart->updateItems($cartData)
                                ->save();
                    }

                    $this->_getSession()->setCartWasUpdated(true);

                    if ($this->_getSession()->getQuote()->getHasError()) {
                        $this->_getSession()->getQuote()->setMessages(array());
                        $this->_getSession()->getQuote()->setHasError(false);
                    }

                    $this->_getSession()->getQuote()->setMessages(array());
                } catch (Exception $e) {
                    $message = $e->getMessage();
                    $status = 'ERROR';
                }
            }

            /**
             * Loads the ajax cart pro pop-up 
             */
            $response = Mage::getModel('ajaxcartpro/ajaxresponse');
            $response->setLinks(Mage::helper('ajaxcartpro')->topLinkTitle());
            $response->setSidebar(Mage::helper('ajaxcartpro')->cartItemssidebar());
            $response->setCart(Mage::helper('ajaxcartpro')->rendercartpageUpdate());
            if ($status == 'ERROR') {
                $message = $this->__('Error Occured');
            } else {

                /**
                 * Here is the success message while cart items is successfully updated 
                 * 
                 * @return string cart items update succcess message
                 */
                $message = $this->__("Cart Updated Sucessfully");
            }
            $response->setMessage($message);
            $response->send();
        } else {
            return parent::updatePostAction();
        }
    }

    /**
     * Function for Discount Coupon code in ajax cart pro pop-up
     * 
     * It helps the finds out the apply coupon code is valid or not
     * 
     * @return string shows the success/error message when coupon code is validated 
     */
    public function couponPostAction() {

        $isAjaxProRequest = $this->getRequest()->getParam('ajaxview', false);
        $message = '';
        if ($isAjaxProRequest) {
            $couponCode = (string) $this->getRequest()->getParam('coupon_code');

            if ($this->getRequest()->getParam('remove') == 1) {
                $couponCode = '';
            }

            try {

                $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
                $this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
                        ->collectTotals()
                        ->save();

                /**
                 * get coupon code length of the coupon code
                 * 
                 * @return integer Coupon code length
                 * 
                 */
                if (strlen($couponCode)) {

                    /**
                     * Check if couponcode is valid or not
                     * 
                     * @return string Coupon code validated message
                     * 
                     */
                    if ($couponCode == $this->_getQuote()->getCouponCode()) {
                        $message .=$this->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode));
                    } else {
                        $message .=$this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode));
                    }
                } else {
                    $message .=$this->__('Coupon code was canceled.');
                }
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {

                $this->_getSession()->addError($this->__('Cannot apply the coupon code.'));
                Mage::logException($e);
            }
            $response = Mage::getModel('ajaxcartpro/ajaxresponse');
            $response->setCart(Mage::helper('ajaxcartpro')->rendercartpageUpdate());
            $response->setMessage($message);
            $response->send();
        } else {
            return parent::updatePostAction();
        }
    }

    /**
     * Function for Estimate shipping And Tax
     * 
     * Update the grand total amount with shipping and handling fee. 
     * 
     */
    public function estimatePostAction() {

        $isAjaxProRequest = $this->getRequest()->getParam('ajaxview', false);

        if ($isAjaxProRequest) {

            $country = (string) $this->getRequest()->getParam('country_id');
            $postcode = (string) $this->getRequest()->getParam('estimate_postcode');
            $city = (string) $this->getRequest()->getParam('estimate_city');
            $regionId = (string) $this->getRequest()->getParam('region_id');
            $region = (string) $this->getRequest()->getParam('region');
            try {
                $this->_getQuote()->getShippingAddress()
                        ->setCountryId($country)
                        ->setCity($city)
                        ->setPostcode($postcode)
                        ->setRegionId($regionId)
                        ->setRegion($region)
                        ->setCollectShippingRates(true);
                $this->_getQuote()->save();
                Mage::dispatchEvent('checkout_cart_save_before', array('cart' => $this));

                $this->_getQuote()->getBillingAddress();
                $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
                $this->_getQuote()->collectTotals();
                $this->_getQuote()->save();

                /**
                 * Cart save usually called after changes with cart items.
                 */
                Mage::dispatchEvent('checkout_cart_save_after', array('cart' => $this));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            Mage::getSingleton('checkout/session')->setNoCartRedirect(true);
            $response = Mage::getModel('ajaxcartpro/ajaxresponse');
            $response->setCart(Mage::helper('ajaxcartpro')->rendercartpageUpdate());
            $response->send();
        } else {
            return parent::estimatePostAction();
        }
    }

    /**
     * Function for Apply estimated shipping 
     * 
     * Update the estimated shipping and Tax amount. 
     * 
     */
    public function estimateUpdatePostAction() {
        $isAjaxProRequest = $this->getRequest()->getParam('ajaxview', false);

        if ($isAjaxProRequest) {
            $code = (string) $this->getRequest()->getParam('estimate_method');
            if (!empty($code)) {
                $this->_getQuote()->getShippingAddress()->setShippingMethod($code)->save();
            }
            $this->_getQuote()->collectTotals()
                    ->save();
            $response = Mage::getModel('ajaxcartpro/ajaxresponse');
            $response->setCart(Mage::helper('ajaxcartpro')->rendercartpageUpdate());
            $response->send();
        } else {
            return parent::estimateUpdatePostAction();
        }
    }

    /**
     * Function for add reorder sidebar
     * 
     */
    public function addgroupAction() {
        $isAjaxProRequest = $this->getRequest()->getParam('ajaxview', false);

        if ($isAjaxProRequest) {
            $orderItemIds = $this->getRequest()->getParam('order_items', array());
            if (is_array($orderItemIds)) {
                $itemsCollection = Mage::getModel('sales/order_item')
                        ->getCollection()
                        ->addIdFilter($orderItemIds)
                        ->load();

                /**
                 * @var $itemsCollection Mage_Sales_Model_Mysql4_Order_Item_Collection
                 * 
                 * @return array items collection array
                 */
                $cart = $this->_getCart();
                foreach ($itemsCollection as $item) {
                    try {
                        $cart->addOrderItem($item, 1);
                    } catch (Mage_Core_Exception $e) {
                        if ($this->_getSession()->getUseNotice(true)) {
                            $this->_getSession()->addNotice($e->getMessage());
                        } else {
                            $this->_getSession()->addError($e->getMessage());
                        }
                    } catch (Exception $e) {
                        $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
                        Mage::logException($e);
                    }
                }
                $cart->save();
                $this->_getSession()->setCartWasUpdated(true);
            }
            $response = Mage::getModel('ajaxcartpro/ajaxresponse');
            $response->setCart(Mage::helper('ajaxcartpro')->rendercartpageUpdate());
            $response->send();
        } else {
            return parent::addgroupAction();
        }
    }

    /**
     * Update product configuration for a cart item and updated the cart items
     */
    public function updateItemOptionsAction() {

        $cart = $this->_getCart();
        $id = (int) $this->getRequest()->getParam('id');
        $params = $this->getRequest()->getParams();

        if (!isset($params['options'])) {
            $params['options'] = array();
        }
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                        array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $quoteItem = $cart->getQuote()->getItemById($id);
            if (!$quoteItem) {
                Mage::throwException($this->__('Quote item is not found.'));
            }

            $item = $cart->updateItem($id, new Varien_Object($params));
            if (is_string($item)) {
                Mage::throwException($item);
            }
            if ($item->getHasError()) {
                Mage::throwException($item->getMessage());
            }

            $related = $this->getRequest()->getParam('related_product');
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()) {
                    $message = $this->__('%s was updated in your shopping cart.', Mage::helper('core')->htmlEscape($item->getProduct()->getName()));
                }
            }
        } catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice($e->getMessage());
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError($message);
                }
            }
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot update the item.'));
            Mage::logException($e);
        }
        $response = Mage::getModel('ajaxcartpro/ajaxresponse');
        $response->setCart(Mage::helper('ajaxcartpro')->rendercartpageUpdate());
        $response->setMessage($message);
        $response->send();
    }

}