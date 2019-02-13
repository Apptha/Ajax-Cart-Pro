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
 *
 * Ajaxcartpro Helper class for creating layout
 *
 */

class Apptha_Ajaxcartpro_Helper_Data extends Mage_Core_Helper_Abstract {

/**
 * Function is used to generate the sidebar layout
 */

public function cartItemssidebar() {

$layout = Mage::getSingleton('core/layout');
$sidebar = $layout->createBlock('checkout/cart_sidebar')
->addItemRender('simple', 'checkout/cart_item_renderer', 'checkout/cart/sidebar/default.phtml')
->addItemRender('configurable', 'checkout/cart_item_renderer_configurable', 'checkout/cart/sidebar/default.phtml')
->addItemRender('grouped', 'checkout/cart_item_renderer_grouped', 'checkout/cart/sidebar/default.phtml')
->addItemRender('bundle', 'bundle/checkout_cart_item_renderer', 'checkout/cart/sidebar/default.phtml')
->setTemplate('checkout/cart/sidebar.phtml');

$express = $layout->createBlock('paypal/express_shortcut')
->setTemplate('paypal/express/shortcut.phtml');

return trim($sidebar->renderView() . $express->toHtml());
}

/**
 * Function is used to generate wishlist sidebar layout
 */

public function wishlistsidebar() {

$layout = Mage::getSingleton('core/layout');
$sidebar = $layout->createBlock('wishlist/customer_sidebar')
->setTemplate('wishlist/sidebar.phtml');

return trim($sidebar->renderView());
}

/**
 * Function is used to genearte Cart page layout
 */

public function rendercartpageUpdate() {

$layout = Mage::getSingleton('core/layout');


/**
 * Create layout block and setting up the template to cart for displays the Subtotal, Discount, Grand Total on the fly using createBlock() Method
 */

$totals = $layout->createBlock('checkout/cart_totals')
->setTemplate('checkout/cart/totals.phtml');


/**
 * Create layout block and setting up the template to cart for displays the Discount coupon code
 */

$coupon = $layout->createBlock('checkout/cart_coupon')
->setTemplate('ajaxcartpro/cart/coupon.phtml');


/**
 * Create layout block and setting up the template to cart for displays Proceed To Checkout Buton
 */

$t_onepage = $layout->createBlock('checkout/onepage_link')
->setTemplate('ajaxcartpro/onepage/link.phtml');

/**
 * Create layout block and setting up the template to cart for displays the Google checkout link
 *
 * check magento version 1.8.0 and above
 */

if (version_compare(Mage::getVersion(), '1.8.0.0', '<=')) {
$t_googlecheckout = $layout->createBlock('googlecheckout/link')
->setTemplate('googlecheckout/link.phtml');
}


/**
 * Create layout block and setting up the template to cart for for Top links layout for  checkout with Paypal
 */

$t_checkoutpaypal = $layout->createBlock('paypal/express_shortcut')
->setTemplate('paypal/express/shortcut.phtml');

$t_methods = $layout->createBlock('core/text_list')
->append($t_onepage, 'top_methods');

$onepage = $layout->createBlock('checkout/onepage_link')
->setTemplate('ajaxcartpro/onepage/link.phtml');

/**
 * Create layout block and setting up the template to cart for displays the Multishipping link
 *
 * check magento version 1.8.0 and above
 */

$multishipping = $layout->createBlock('checkout/multishipping_link')
->setTemplate('ajaxcartpro/checkout/multishipping/link.phtml');


/**
 * Create layout block and setting up the template to cart for displays shipping & Tax layout
 */

$shipping = $layout->createBlock('checkout/cart_shipping')
->setTemplate('ajaxcartpro/cart/shipping.phtml');

/**
 * Create layout block and setting up the template to cart for displays the Cross sell
 */

$crossell = $layout->createBlock('checkout/cart_crosssell')
->setTemplate('ajaxcartpro/cart/crosssell.phtml');

/**
 * Create layout block and setting up the template to cart for displays the  Multishipping layout
 */

if (version_compare(Mage::getVersion(), '1.8.0.0', '<=')) {
$methods = $layout->createBlock('core/text_list')
->append($onepage, "onepage")
->append($multishipping, "multishipping")
->append($t_googlecheckout, 'checkout.cart.methods.googlecheckout.bottom')
->append($t_checkoutpaypal, 'checkout.cart.methods.paypal_express.bottom');
} else {
$methods = $layout->createBlock('core/text_list')
->append($onepage, "onepage")
->append($multishipping, "multishipping")
->append($t_checkoutpaypal, 'checkout.cart.methods.paypal_express.bottom');
}

/**
 * Create layout block and setting up the template to cart for displays the combined layout
 *
 * The Layouts like no-items and cart items for product types
 */

$cartupdate = $layout
->createBlock('checkout/cart')
->setEmptyTemplate('ajaxcartpro/cart/noItems.phtml')
->setCartTemplate('ajaxcartpro/cart.phtml')
->addItemRender('simple', 'checkout/cart_item_renderer', 'ajaxcartpro/cart/item/default.phtml')
->addItemRender('configurable', 'checkout/cart_item_renderer_configurable', 'ajaxcartpro/cart/item/default.phtml')
->addItemRender('grouped', 'checkout/cart_item_renderer_grouped', 'ajaxcartpro/cart/item/default.phtml')
->addItemRender('downloadable', 'downloadable/checkout_cart_item_renderer', 'downloadable/checkout/cart/item/default.phtml')
->addItemRender('bundle', 'bundle/checkout_cart_item_renderer', 'ajaxcartpro/cart/item/default.phtml')
->addItemRender('subscription_simple', 'sarp/checkout_cart_item_renderer_simple', 'ajaxcartpro/cart/item/default.phtml')
->addItemRender('bookable', 'booking/checkout_cart_item_renderer', 'ajaxcartpro/cart/item/default.phtml')
->setChild('top_methods', $t_methods)
->setChild('totals', $totals)
->setChild('coupon', $coupon)
->setChild('methods', $methods);

/**
 * Check if shipping update is enabled or not in backend
 */

if (Mage::getstoreconfig('ajaxcartpro/general/Enable_shipping_update') == 1) {

$cartupdate->setChild('shipping', $shipping);
}

$fbenabled = Mage::helper('core')->isModuleEnabled('Apptha_Fbdiscount');

 if($fbenabled == 1){
    $fbdiscount = $layout->createBlock('fbdiscount/fbdiscount')
				  ->setTemplate('fbdiscount/fbdiscount.phtml');

	$cartupdate->setChild('fbdiscount', $fbdiscount);
 }
/**
 * Create child html for fb discount, if FB discount is enabled  
 */



/**
 * Check if cross sell update is enabled or not in backend
 */

if (Mage::getstoreconfig('ajaxcartpro/general/Enable_sell_update') == 1) {

$cartupdate->setChild('crosssell', $crossell);
}

$cartupdate->chooseTemplate();
return trim($cartupdate->renderView());
}

/**
 * Function to genearte Top link "My Cart" layout
 *
 * @return string My cart and items count
 */

public function topLinkTitle() {

/**
 * Get Total number of cart items count
 */

$cartItemsCount = Mage::helper('checkout/cart')->getSummaryCount();

if ($cartItemsCount == 1) {
$cartText = Mage::helper('checkout')->__('My Cart (%s item)', $cartItemsCount);
} elseif ($cartItemsCount > 0) {
$cartText = Mage::helper('checkout')->__('My Cart (%s items)', $cartItemsCount);
} else {
$cartText = Mage::helper('checkout')->__('My Cart');
}

return $cartText;
}

/**
 * Function to render the fbdiscount
 *
 * @return void
 */

//    public function renderCartFbdiscount(){
//    	$layout = Mage::getSingleton('core/layout');
//
//    	$fbdiscount = $layout->createBlock('fbdiscount/fbdiscount')
//                ->setTemplate('fbdiscount/fbdiscount.phtml');
//
//       return trim($fbdiscount);
//    }

/**
 * Function to genearte Cart page layout for ajax cart pro pop-up in whishlist add to cart button
 *
 */

public function renderwishlistUpdate() {

$layout = Mage::getSingleton('core/layout');

/**
 * Create the block and set up the pop-up content for pop-up
 *
 * It will occur when customers cliks the Add to cart button in whishlist
 */

$wishlist = $layout->createBlock('wishlist/customer_wishlist')
->setTemplate('wishlist/view.phtml')
->setTitle('My Wishlist');
/**
 * Load the phtml files above magento 1.7.0 version
 *
 */

if (!version_compare(Mage::getVersion(), '1.7.0', '<')) {

$wishlist_image = $layout->createBlock('wishlist/customer_wishlist_item_column_image')
->setTemplate('wishlist/item/column/image.phtml');

$wishlist_comment = $layout->createBlock('wishlist/customer_wishlist_item_column_comment')
->setTitle('Product Details and Comment')
->setTemplate('wishlist/item/column/info.phtml');

$wishlist_options = $layout->createBlock('wishlist/customer_wishlist_item_options');

$wishlist_remove = $layout->createBlock('wishlist/customer_wishlist_item_column_remove')
->setTemplate('wishlist/item/column/remove.phtml');

$wishlist_cart = $layout->createBlock('wishlist/customer_wishlist_item_column_cart')
->setTemplate('wishlist/item/column/cart.phtml')
->setTitle('Add to Cart')
->append($wishlist_options, 'customer.wishlist.item.remove');

$wishlist_items = $layout->createBlock('wishlist/customer_wishlist_items')
->setTemplate('wishlist/item/list.phtml')
->append($wishlist_image, 'customer.wishlist.item.image')
->append($wishlist_comment, 'customer.wishlist.item.info')
->append($wishlist_cart, 'customer.wishlist.item.cart')
->append($wishlist_remove, 'customer.wishlist.item.remove');
/**
 * Load the phtml files for displays the whishlist buttons
 *
 */

$shareButton = $layout->createBlock('wishlist/customer_wishlist_button')
->setTemplate('wishlist/button/share.phtml');

$cartButton = $layout->createBlock('wishlist/customer_wishlist_button')
->setTemplate('wishlist/button/tocart.phtml');

$updateButton = $layout->createBlock('wishlist/customer_wishlist_button')
->setTemplate('wishlist/button/update.phtml');

$wishlist_buttons = $layout->createBlock('core/text_list')
->append($shareButton, 'customer.wishlist.button.share')
->append($cartButton, 'customer.wishlist.button.toCart')
->append($updateButton, 'customer.wishlist.button.update');

$wishlist->setChild('items', $wishlist_items);

$wishlist->setChild('control_buttons', $wishlist_buttons);
}

return trim($wishlist->renderView());
}

/**
 * Function for displays the whishlist link in Top links section
 *
 * @return string My Wishlist and their count
 */

public function wishlistLinkTitle() {

/**
 * Get Total number of whishlist items count
 */

$whishlistCount = Mage::helper('wishlist')->getItemCount();

if ($whishlistCount == 1) {
$whishlistText = Mage::helper('checkout')->__('My Wishlist (%s item)', $whishlistCount);
} elseif ($whishlistCount > 0) {
$whishlistText = Mage::helper('checkout')->__('My Wishlist (%s items)', $whishlistCount);
} else {
$whishlistText = Mage::helper('checkout')->__('My Wishlist');
}

return $whishlistText;
}

}