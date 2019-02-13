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
 * Ajaxcartpro overwritten Google checkout class controller
 */
require_once 'Mage/GoogleCheckout/controllers/RedirectController.php';

class Apptha_Ajaxcartpro_RedirectController extends Mage_GoogleCheckout_RedirectController {

    public function checkoutAction() {
        $ajaxview = $this->getRequest()->getParam('ajaxview');
        if ($ajaxview == 1) {

            $messages = '';
            $session = Mage::getSingleton('checkout/session');
            Mage::dispatchEvent('googlecheckout_checkout_before', array('quote' => $session->getQuote()));
            $api = $this->_getApi();

            if ($api->getError()) {
                $messages = $api->getError();
                $url = Mage::getUrl('checkout/cart');
            } else {
                $url = $api->getRedirectUrl();
            }
            //$this->getResponse()->setRedirect($url);
            $response = Mage::getModel('ajaxcartpro/ajaxresponse');
            $response->setMessages($messages);
            $response->setLinks($url);
            $response->send();
        } else {
            parent::checkoutAction();
        }
    }

}
