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
 * Ajaxcartpro overwritten wishlist class
 */
require_once 'Mage/Wishlist/controllers/IndexController.php';

class Apptha_Ajaxcartpro_WishlistController extends Mage_Wishlist_IndexController {

    public function cartAction() {
        
        $ajaxview = $this->getRequest()->getParam('ajaxview');
        if ($ajaxview == 1) {
            $itemId = (int) $this->getRequest()->getParam('item');

            /* @var $item Mage_Wishlist_Model_Item */
            $item = Mage::getModel('wishlist/item')->load($itemId);
            if (!$item->getId()) {
                //return $this->_redirect('*/*');
            }
            $wishlist = Mage_Wishlist_IndexController::_getWishlist($item->getWishlistId());
            if (!$wishlist) {
                return false;
            }

            // Set qty
            $qty = $this->getRequest()->getParam('qty');
            if (is_array($qty)) {
                if (isset($qty[$itemId])) {
                    $qty = $qty[$itemId];
                } else {
                    $qty = 1;
                }
            }
            $qty = $this->_processLocalizedQty($qty);
            if ($qty) {
                $item->setQty($qty);
            }

            /* @var $session Mage_Wishlist_Model_Session */
            $session = Mage::getSingleton('wishlist/session');
            $cart = Mage::getSingleton('checkout/cart');

           

            try {
                $options = Mage::getModel('wishlist/item_option')->getCollection()
                                ->addItemFilter(array($itemId));
                $item->setOptions($options->getOptionsByItem($itemId));

                $buyRequest = Mage::helper('catalog/product')->addParamsToBuyRequest(
                                $this->getRequest()->getParams(),
                                array('current_config' => $item->getBuyRequest())
                );

                $item->mergeBuyRequest($buyRequest);
                $item->addToCart($cart, true);
                $cart->save()->getQuote()->collectTotals();
                $wishlist->save();

                Mage::helper('wishlist')->calculate();


                Mage::helper('wishlist')->calculate();
            } catch (Mage_Core_Exception $e) {
                if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SALABLE) {
                    $session->addError(Mage::helper('wishlist')->__('This product(s) is currently out of stock'));
                } else if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                    Mage::getSingleton('catalog/session')->addNotice($e->getMessage());
                } else {
                    Mage::getSingleton('catalog/session')->addNotice($e->getMessage());
                }
            } catch (Exception $e) {
                $session->addException($e, Mage::helper('wishlist')->__('Cannot add item to shopping cart'));
            }

            Mage::helper('wishlist')->calculate();
            $response = Mage::getModel('ajaxcartpro/ajaxresponse');
            $response->setCart(Mage::helper('ajaxcartpro')->rendercartpageUpdate());
            $response->setSidebar(Mage::helper('ajaxcartpro')->cartItemssidebar());
            $response->setWishlist(Mage::helper('ajaxcartpro')->renderwishlistUpdate());
             $response->setWishlistsidebar(Mage::helper('ajaxcartpro')->wishlistsidebar());
            $response->setLinks(Mage::helper('ajaxcartpro')->topLinkTitle());
            $response->setwishlistLinks(Mage::helper('ajaxcartpro')->wishlistLinkTitle());
            $response->send();
        } else {
            return parent::cartAction();
        }
    }

    public function allcartAction() {
        $ajaxview = $this->getRequest()->getParam('ajaxview');
        if ($ajaxview == 1) {
            $wishlist = $this->_getWishlist();
            if (!$wishlist) {
                $this->_forward('noRoute');
                return;
            }
            $isOwner = $wishlist->isOwner(Mage::getSingleton('customer/session')->getCustomerId());

            $messages = array();
            $addedItems = array();
            $notSalable = array();
            $hasOptions = array();

            $cart = Mage::getSingleton('checkout/cart');
            $collection = $wishlist->getItemCollection()
                            ->setVisibilityFilter();

            $qtys = $this->getRequest()->getParam('qty');
            foreach ($collection as $item) {
                /** @var Mage_Wishlist_Model_Item */
                try {
                    $disableAddToCart = $item->getProduct()->getDisableAddToCart();
                    $item->unsProduct();

                    // Set qty
                    if (isset($qtys[$item->getId()])) {
                        $qty = $this->_processLocalizedQty($qtys[$item->getId()]);
                        if ($qty) {
                            $item->setQty($qty);
                        }
                    }
                    $item->getProduct()->setDisableAddToCart($disableAddToCart);
                    // Add to cart
                    if ($item->addToCart($cart, $isOwner)) {
                        $addedItems[] = $item->getProduct();
                    }
                } catch (Mage_Core_Exception $e) {
                    if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SALABLE) {
                        $notSalable[] = $item;
                    } else if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                        $hasOptions[] = $item;
                    } else {
                        $messages[] = $this->__('%s for "%s".', trim($e->getMessage(), '.'), $item->getProduct()->getName());
                    }
                } catch (Exception $e) {
                    Mage::logException($e);
                    $messages[] = Mage::helper('wishlist')->__('Cannot add the item to shopping cart.');
                }
            }

            if ($isOwner) {
                $indexUrl = Mage::helper('wishlist')->getListUrl($wishlist->getId());
            } else {
                $indexUrl = Mage::getUrl('wishlist/shared', array('code' => $wishlist->getSharingCode()));
            }


            if ($notSalable) {
                $products = array();
                foreach ($notSalable as $item) {
                    $products[] = '"' . $item->getProduct()->getName() . '"';
                }
                $messages[] = Mage::helper('wishlist')->__('Unable to add the following product(s) to shopping cart: %s.', join(', ', $products));
            }

            if ($hasOptions) {
                $products = array();
                foreach ($hasOptions as $item) {
                    $products[] = '"' . $item->getProduct()->getName() . '"';
                }
                $messages[] = Mage::helper('wishlist')->__('Product(s) %s have required options. Each of them can be added to cart separately only.', join(', ', $products));
            }

            if ($messages) {
                $isMessageSole = (count($messages) == 1);
                if ($isMessageSole && count($hasOptions) == 1) {
                    $item = $hasOptions[0];
                    if ($isOwner) {
                        $item->delete();
                    }
                } else {
                    $wishlistSession = Mage::getSingleton('wishlist/session');
                    foreach ($messages as $message) {
                        $wishlistSession->addError($message);
                    }
                }
            }

            if ($addedItems) {
                // save wishlist model for setting date of last update
                try {
                    $wishlist->save();
                } catch (Exception $e) {
                    Mage::getSingleton('wishlist/session')->addError($this->__('Cannot update wishlist'));
                }

                $products = array();
                foreach ($addedItems as $product) {
                    $products[] = '"' . $product->getName() . '"';
                }

                Mage::getSingleton('checkout/session')->addSuccess(
                        Mage::helper('wishlist')->__('%d product(s) have been added to shopping cart: %s.', count($addedItems), join(', ', $products))
                );
            }
            $messages = Mage::getSingleton('wishlist/session')->getMessages(true);
foreach($messages->getItems() as $message)
{
$mess[] = $message->getText();
}
            // save cart and collect totals
            $cart->save()->getQuote()->collectTotals();

            Mage::helper('wishlist')->calculate();

            Mage::helper('wishlist')->calculate();
            $response = Mage::getModel('ajaxcartpro/ajaxresponse');
            $response->setCart(Mage::helper('ajaxcartpro')->rendercartpageUpdate());
            $response->setSidebar(Mage::helper('ajaxcartpro')->cartItemssidebar());
            $response->setWishlist(Mage::helper('ajaxcartpro')->renderwishlistUpdate());
            $response->setLinks(Mage::helper('ajaxcartpro')->topLinkTitle());
            $response->setwishlistLinks(Mage::helper('ajaxcartpro')->wishlistLinkTitle());
            $response->setwisherror($mess);
            $response->send();
        }
        else {
            return parent::allcartAction();
        }
    }
    }