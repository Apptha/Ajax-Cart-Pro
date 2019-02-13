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
 * Ajaxcartpro overwritten Paypal Express checkout class controller
 */

require_once 'Mage/Paypal/controllers/ExpressController.php';

class Apptha_Ajaxcartpro_ExpressController extends Mage_Paypal_ExpressController {
    /*
     * Initialize the paypal express checkout
     */

    private function _initCheckout() {

        $quote = $this->_getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->getResponse()->setHeader('HTTP/1.1', '403 Forbidden');
            Mage::throwException(Mage::helper('paypal')->__('Unable to initialize Express Checkout.'));
        }
        $this->_checkout = Mage::getSingleton($this->_checkoutType, array(
                    'config' => $this->_config,
                    'quote' => $quote,
        ));
    }

    /*
     * Get cart items in checkout session
     */

    private function _getQuote() {
        if (!$this->_quote) {
            $this->_quote = $this->_getCheckoutSession()->getQuote();
        }
        return $this->_quote;
    }

    /*
     * Get checkout session
     */

    private function _getCheckoutSession() {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Start Express Checkout by requesting initial token and dispatching customer to PayPal
     */
    public function startAction() {
        $ajaxview = $this->getRequest()->getParam('ajaxview');
        if ($ajaxview == 1) {
            $messages = '';
            try {
                $this->_initCheckout();
                if ($this->_getQuote()->getIsMultiShipping()) {
                    $this->_getQuote()->setIsMultiShipping(false);
                    $this->_getQuote()->removeAllAddresses();
                }

                $customer = Mage::getSingleton('customer/session')->getCustomer();
                if ($customer && $customer->getId()) {
                    $this->_checkout->setCustomerWithAddressChange(
                            $customer, $this->_getQuote()->getBillingAddress(), $this->_getQuote()->getShippingAddress()
                    );
                }

                /**
                 * Billing agreements 
                 */
                $isBARequested = (bool) $this->getRequest()
                                ->getParam(Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_BILLING_AGREEMENT);
                if ($customer && $customer->getId()) {
                    $this->_checkout->setIsBillingAgreementRequested($isBARequested);
                }

                /**
                 * Giropay
                 */
                $this->_checkout->prepareGiropayUrls(
                        Mage::getUrl('checkout/onepage/success'), Mage::getUrl('paypal/express/cancel'), Mage::getUrl('checkout/onepage/success')
                );

                $token = $this->_checkout->start(Mage::getUrl('*/*/return'), Mage::getUrl('*/*/cancel'));

                if ($token && $url = $this->_checkout->getRedirectUrl()) {
                    $this->_initToken($token);
                }
            } catch (Mage_Core_Exception $e) {
                $messages .= $e->getMessage();
            } catch (Exception $e) {
                $messages .= $this->__('Unable to start Express Checkout.');
                Mage::logException($e);
            }
            $response = Mage::getModel('ajaxcartpro/ajaxresponse');
            $response->setMessages($messages);
            $response->setLinks($url);
            $response->send();
        } else {
            parent::startAction();
        }
    }

}
