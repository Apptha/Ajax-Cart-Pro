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
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

oldLocation = setLocation;
pathArray = document.location;
var pathName = pathArray.pathname.substring(0, pathArray.pathname.lastIndexOf('/') + 1);


base_url = '';
var current_url = window.location.pathname;
/*
 * setLocation function starts here.
 * It wraps the original setlocation function
 */

setLocation = function(url) {

    if (ajaxcart.ajaxcartEnable == 1) {

        if (url.search('checkout/cart/add') != -1) { 
            ajaxcartupdate(url + 'ajaxview/1/');

        } else if (url.search('options=cart') != -1) {
            $('fade').setStyle({
                "display": "block"
            });
            ajaxcustomoptions(url + '&ajaxcustomoption=1');
        }
        else if (url.search('wishlist_id') != -1) {
            ajaxcartwishlistitem(url + '&ajaxview=1');
        }
        else if (url.search('wishlist') != -1) {
            ajaxcartwishlist(url + '&ajaxview=1');
        }

        else if (url.search('checkout/onepage') != -1) {
            window.open(url, '_self');
        }
        else if (url.search('&order') != -1) {
            window.open(url, '_self');
        }else{
            ajaxcustomoptions(url + '?options=cart&ajaxcustomoption=1')
        }
    }
};
setPLocation = function(url) {

    if (ajaxcart.ajaxcartEnable == 1) {

        if (url.search('checkout/cart/add') != -1) {
            ajaxcartupdate(url + 'ajaxview/1/');

        }
        else {

            window.open(url, "_self");
        }

    }
};
/* setLocation function ends here */

function ajaxcartwishlist(url) {

    var links = document.links;
    for (i = 0; i < links.length; i++) {
        if (links[i].href.search('/wishlist/') != -1) {
            links[i].setAttribute("id", "apptha_wishlist");
        }
    }


    /******check avialability start ******/

    var checkurl = base_url + 'ajaxcartpro/index/checkavail';
    var carturl = base_url + 'checkout/cart/index/?ajaxview=1';


    var reques = new Ajax.Request(checkurl, {
        method: 'get',
        evalJS: true,
        parameters: {wishlist_url: url},
        onSuccess: function(transport) {
            if (transport.status == 200) {
                var checkdata = transport.responseText.evalJSON();

                if (checkdata.success == false)
                {
                    if (checkdata.op == '1')
                    {
                        $('popup-block').setStyle({
                            "display": "none"
                        });
                        $('fade').setStyle({
                            "display": "block"
                        });
                        $('loading-box').setStyle({
                            "display": "none"
                        });

                        wishajaxcustomoptions(checkdata.prourl + '&ajaxcustomoption=1&wish=' + checkdata.wish);
                    }
                    else
                    {

                        $('popup-block').setStyle({
                            "display": "block"
                        });
                        $('fade').setStyle({
                            "display": "block"
                        });
                        $('loading-box').setStyle({
                            "display": "block"
                        });
                        var cart_error = $$('#cart-error').first();
                        cart_error.update('');
                        ajaxcartview(carturl + "&error=" + checkdata.message);
                    }
                }
                else
                {
                    $('popup-block').setStyle({
                        "display": "block"
                    });
                    $('fade').setStyle({
                        "display": "block"
                    });
                    $('loading-box').setStyle({
                        "display": "block"
                    });
                    var request = new Ajax.Request(url, {
                        method: 'post',
                        evalJS: true,
                        onSuccess: function(transport) {

                            if (transport.status == 200) {

                                var data = transport.responseText.evalJSON();
                                var toptitle = $$('a.top-link-cart').first();
                                var wishlist_sidebar = $$('div.block-wishlist').first();
                                var popup_contents = $$('#product-cart').first();
                                var wishlist_contents = $$('.my-account').first();
                                var apptha_wishlist = $$('a#apptha_wishlist').first();
                                $('loading-box').setStyle({
                                    "display": "none"
                                });
                                if (toptitle) {
                                    toptitle.update(data.links);
                                }
                                if (wishlist_sidebar)
                                {
                                    wishlist_sidebar.update(data.wishlistsidebar);
                                }
                                popup_contents.update(data.cart);
                                
                                domload();
                              //  fbAsyncInit();
                                wishlist_contents.update(data.wishlist);
                                
                                if (apptha_wishlist)
                                {
                                    apptha_wishlist.update(data.wishlist_links);
                                }
                            }
                        }
                    });
                }

            }
        }
    });

    /******check avialability ends ******/



}

function ajaxcartwishlistitem(url) {

    $('popup-block').setStyle({
        "display": "block"
    });
    $('fade').setStyle({
        "display": "block"
    });
    $('loading-box').setStyle({
        "display": "block"
    });
    var request = new Ajax.Request(url, {
        method: 'post',
        evalJS: true,
        onSuccess: function(transport) {

            if (transport.status == 200) {

                var data = transport.responseText.evalJSON();
                var toptitle = $$('a.top-link-cart').first();
                var wishlist_sidebar = $$('div.block-wishlist').first();
                var popup_contents = $$('#product-cart').first();
                var wishlist_contents = $$('.my-account').first();
                var apptha_wishlist = $$('a#apptha_wishlist').first();
                var cart_error = $$('#cart-error').first();
                $('loading-box').setStyle({
                    "display": "none"
                });
                if (toptitle) {
                    toptitle.update(data.links);
                }
                if (wishlist_sidebar)
                {
                    wishlist_sidebar.update(data.wishlistsidebar);
                }
                if (data.wisherror)
                {
                    var carterror = '<ul class="messages"><li class="error-msg"><ul><li><span>' + data.wisherror + '</span></li></ul></li></ul>';
                    var parcart = carterror.replaceAll(',', '<br>');
                    cart_error.update(parcart);
                }

                popup_contents.update(data.cart);
                wishlist_contents.update(data.wishlist);
                domload();
                if (apptha_wishlist)
                {
                    apptha_wishlist.update(data.wishlist_links);
                }
            }
        }
    });


}
/*
 * AjaxcartUpdate function starts here
 * It updates recently added items in cart,sidebar and menu
 */
function ajaxcartupdate(url) {

    if (ajaxcart.cartUrl == 1) {
        ajaxcartpageupdate(url);
    } else {

        $('popup-block').setStyle({
            "display": "block"
        });
        $('fade').setStyle({
            "display": "block"
        });
        $('loading-box').setStyle({
            "display": "block"
        });

        /******check avialability start ******/

        var checkurl = base_url + 'ajaxcartpro/index/checkavail';
        var carturl = base_url + 'checkout/cart/index/?ajaxview=1';
        var pqty;
        if ($('qty') == null)
        {
            pqty = '1';
        }
        else
        {
            pqty = $('qty').value;
        }

        var request = new Ajax.Request(checkurl, {
            method: 'get',
            evalJS: true,
            parameters: {product_url: url, product_qty: pqty},
            onSuccess: function(transport) {
                if (transport.status == 200) {
                    var checkdata = transport.responseText.evalJSON();
                    if (!checkdata.success)
                    {
                        var cart_error = $$('#cart-error').first();
                        cart_error.update('');
                        ajaxcartview(carturl + "&error=" + checkdata.message);
                       // fbAsyncInit();
                        return false;
                    }
                }
            }
        });
        /******check avialability ends ******/

        var request = new Ajax.Request(url, {
            method: 'post',
            evalJS: true,
            onSuccess: function(transport) {
                if (transport.status == 200) {

                    var data = transport.responseText.evalJSON();
                    var shipment_methods = $$('div.block-cart').first();

                    var toptitle = $$('a.top-link-cart').first();
                    var popup_contents = $$('#product-cart').first();


                    if (shipment_methods) {

                        shipment_methods.update(data.sidebar);

                    }
                    popup_contents.update(data.cart);
                    
                    domload();
                    
                    
                    $('loading-box').setStyle({
                        "display": "none"
                    });
                 //   fbAsyncInit();
                    if (toptitle) {
                        toptitle.update(data.links);
                    }

                    if (url.search('checkout/cart/add') != -1) {
                        popup_contents.update(data.cart);
                        
                        domload();
                     //   fbAsyncInit();
                        


                    }
                }
            }
        });
    }
}
/* AjaxcartUpdate function Ends */

/*
 * Ajax Cart View function starts here
 * It shows the checkout page when click on My cart link.
 */

function ajaxcartview(url) {

    $('popup-block').setStyle({
        "display": "block"
    });
    $('fade').setStyle({
        "display": "block"
    });
    $('loading-box').setStyle({
        "display": "block"
    });
     // alert(url);
    console.log(url);
    var request = new Ajax.Request(url, {	
        method: 'post',
        evalJS: true,
        onSuccess: function(transport) {
    	
            if (transport.status == 200) {

                var data = transport.responseText.evalJSON();
                
                console.log(data);
                var shipment_methods = $$('div.block-cart').first();

                var toptitle = $$('a.top-link-cart').first();
                var popup_contents = $$('#product-cart').first();

                popup_contents.update(data.cart);
                domload();
                
               
                $('loading-box').setStyle({
                    "display": "none"
                });
                
                fbAsyncInit();
                var cart_error = $$('#cart-error').first();
                cart_error.update('');

                if (shipment_methods) {
                    shipment_methods.update(data.sidebar);
                }
                if (toptitle) {
                    toptitle.update(data.links);
                }


                if (url.search('checkout/cart/') != -1) {
                    popup_contents.update(data.cart);
                    domload();
                    fbAsyncInit();
                    
                    morebutton();
                }
            }
        }
    });

}
/*
 * Ajax Cart view ends here
 */

function ajaxcartexpresscheckout(url) {


    url = url + '?ajaxview=1';

    $('loading-box').setStyle({
        "display": "block"
    });

    var request = new Ajax.Request(url, {
        evalJS: true,
        onSuccess: function(transport) {
            if (transport.status == 200) {

                var data = transport.responseText.evalJSON();



                $('loading-box').setStyle({
                    "display": "none"
                });

                if (data.messages != '') {
                    alert(data.messages);
                    return false;
                }

                if (data.links) {
                    window.open(data.links, '_self');
                }
            }
        }
    });

}

function ajaxcartreorder(url, params) {


    url = url + '?ajaxview=1';
    $('popup-block').setStyle({
        "display": "block"
    });
    $('fade').setStyle({
        "display": "block"
    });
    $('loading-box').setStyle({
        "display": "block"
    });

    var request = new Ajax.Request(url, {
        method: 'post',
        parameters: params,
        evalJS: true,
        onSuccess: function(transport) {
            if (transport.status == 200) {

                var data = transport.responseText.evalJSON();
                var shipment_methods = $$('div.block-cart').first();

                var toptitle = $$('a.top-link-cart').first();
                var popup_contents = $$('#product-cart').first();

                popup_contents.update(data.cart);
                domload();
              //  fbAsyncInit();
                

                $('loading-box').setStyle({
                    "display": "none"
                });

                if (shipment_methods) {
                    shipment_methods.update(data.sidebar);
                }
                if (toptitle) {
                    toptitle.update(data.links);
                }


                if (url.search('checkout/cart/') != -1) {
                    popup_contents.update(data.cart);
                    
                    domload();
                   // fbAsyncInit();
                    
                    morebutton();


                }


            }
        }
    });

}

function wishajaxcustomoptions(url)
{
    $('loading-box').setStyle({
        "display": "block"
    });

    var request = new Ajax.Request(url, {
        method: 'post',
        evalJS: true,
        onSuccess: function(transport) {
            var data = transport.responseText.evalJSON();
            $('loading-box').setStyle({
                "display": "none"
            });

            $('custom-option').innerHTML = data.wish;

            $('custom-option').setStyle({
                "display": "block"
            });

        }
    });

}

/* Ajaxcustom options function Starts */
function ajaxcustomoptions(url) {

    $('loading-box').setStyle({
        "display": "block"
    });

    var request = new Ajax.Request(url, {
        method: 'post',
        evalJS: true,
        onSuccess: function(transport) {
            var data = transport.responseText.evalJSON();
            $('loading-box').setStyle({
                "display": "none"
            });

            $('custom-option').innerHTML = data.html;

            $('custom-option').setStyle({
                "display": "block"
            });

        }
    });
}

/*
 * DeleteLinks function Starts it update the delete links to the recently added
 * items
 */
function DeleteLinks() {
    var links = document.links;
    
    for (var i = 0; i < links.length; i++) {

        if (links[i].href.search('checkout/cart/delete') != -1) {

            var url = links[i].href.replace(/\/uenc\/.+,/g, "");
            var getbase = links[i].href.split('checkout/cart/delete');
            var base_url = getbase[0];
            var del = url.match(/delete\/id\/\d+\//g);
            var id = del[0].match(/\d+/g);

            if (window.location.protocol == 'https:') {
                var base_url = base_url.replace("http:", "https:");
            }

            if (ajaxcart.cartUrl != 1) {

                links[i].href = 'javascript:ajaxcartupdate("' + base_url
                        + 'ajaxcartpro/index/remove/id/' + id + '")';

            } else {

                links[i].href = 'javascript:ajaxcartupdate("' + base_url
                        + 'ajaxcartpro/index/remove/id/' + id + '/is_checkout/1")';
            }

        }
        if (links[i].href.search('paypal/express/start') != -1 && links[i].href.search('javascript:ajaxcartexpresscheckout') == -1) {
            var getbase = links[i].href.split('paypal/express/start');
            var base_url = getbase[0];
            links[i].href = 'javascript:ajaxcartexpresscheckout("' + base_url + 'ajaxcartpro/express/start'
                    + '")';
        }

    }
}


/* Ajaxcart class Initialization Starts */
var Ajaxcartpro = Class.create();
Ajaxcartpro.prototype = {
    initialize: function(cartUrl, ajaxcartEnable) {
        this.cartUrl = cartUrl;
        this.ajaxcartEnable = ajaxcartEnable;

    }
};
/* Ajaxcart class Initialization Ends */

document.observe("dom:loaded", function() {
  
  domload();
});

/**
 * domload function starts here
 */
function domload() {


    /*
     *Coding to block the side bar Cart page redirect into Pop Up
     */
    $$(".top-link-cart").each(function(element) {
        var newVal = element.href;
        element.setAttribute('onclick', 'ajaxcartview("' + newVal + 'index/index?ajaxview=1' + '");return false;');
    })

    /*
     *Coding to block the top link Cart page redirect into Pop Up
     */
    $$(".amount a").each(function(element) {
        var newVal = element.href;

        element.setAttribute('onclick', 'ajaxcartview("' + newVal + 'index/index?ajaxview=1' + '");return false;');

    })



    $$(".link-cart").each(function(element) {
        var newVal = element.href;
        element.setAttribute('onclick', 'ajaxcartwishlist("' + newVal + '?ajaxview=1' + '");return false;');
    })

    if ((typeof ('.cart') != 'undefined') && (ajaxcart.cartUrl)) {

        DeleteLinks();
    }

    /*
     *Function calling for More & Hide functionality.
     */
    morebutton();


    $$('button.btn-continue').each(function(element) {
        element.observe('click', function(event) {
            ajaxClose('popup-block');
            event.stop();
            if (current_url.search('checkout') != -1)
            {
                window.open(current_url, '_self');
            }
        });
    })


    /*
     *Coding for Continue, Proceed to Checkout, Update Shopping Cart buttons.
     */
    var shoppingCartTable = $('shopping-cart-table');

    if (shoppingCartTable) {

        var shoppingCartForm = shoppingCartTable.up('form');

        if (typeof shoppingCartForm != 'undefined') {

            shoppingCartForm.observe('submit', function(event) {
                Event.stop(event);

                var params = Event.element(event).serialize();

                var url = Event.element(event).action;

                ajaxupdatepost(url, params);

            });

        }

    }

    /*
     *Coding for Discount coupon code form
     */
    var shoppingCoupon = $('discount-coupon-form');
    if (shoppingCoupon) {

        shoppingCoupon.observe(
                'click',
                function(event) {
                    var discountForm = new VarienForm(
                            'discount-coupon-form');
                    var elm;
                    discountForm.submit = function(isRemove) {

                        if (isRemove) {
                            $('coupon_code').removeClassName(
                                    'required-entry');
                            $('remove-coupone').value = "1";

                        } else {
                            $('coupon_code').addClassName(
                                    'required-entry');
                            $('remove-coupone').value = "0";
                        }

                        return VarienForm.prototype.submit.bind(
                                discountForm)();
                    };
                    if (elm = event
                            .findElement('button[value="Apply Coupon"]')) {
                        discountForm.submit(false);
                    } else if (elm = event
                            .findElement('button[value="Cancel Coupon"]')) {
                        discountForm.submit(true);
                    }

                });
        if (typeof shoppingCoupon != 'undefined') {

            shoppingCoupon.submit = function() {

                var params = shoppingCoupon.serialize();
                var redirectUrl = shoppingCoupon.action;

                couponcodepost(redirectUrl, params);
            };

        }

    }

    /*
     *Coding for Estimate Shipping & Tax functionality
     */
    var shippingForm = $('shipping-zip-form');
    if (shippingForm) {
        shippingForm.observe(
                'click',
                function(event) {

                    var elm;
                    var coShippingMethodForm = new VarienForm('shipping-zip-form');

                    var submit = event.findElement('button[type=button]');
                    if (submit == undefined) {
                        return false;
                    }
                    coShippingMethodForm.submit = function() {
                        var coShippingMethodForm = new VarienForm('shipping-zip-form');
                        var country = $F('country');
                        var optionalZip = false;

                        if (optionalZip) {
                            $('postcode').removeClassName('required-entry');
                        }
                        else {
                            $('postcode').addClassName('required-entry');
                        }
                        return VarienForm.prototype.submit.bind(coShippingMethodForm)();
                    }
                    coShippingMethodForm.submit();

                });

        shippingForm.submit = function() {

            var params = shippingForm.serialize();

            var redirectUrl = shippingForm.action;
            couponcodepost(redirectUrl, params);
        };
    }

    var coshipForm = $('co-shipping-method-form');
    if (coshipForm)
    {

        coshipForm.observe('submit', function(event) {
            Event.stop(event);
            var params = Event.element(event).serialize();

            var url = Event.element(event).action;
            couponcodepost(url, params);

        });

    }

    /*
     *Coding for reorder sidebar
     */
    var sidebarform = $('reorder-validate-detail');
    if (sidebarform) {


        if (typeof sidebarform != 'undefined') {

            sidebarform.observe('submit', function(event) {
                Event.stop(event);
                var params = Event.element(event).serialize();
                if (params)
                {
                    document.getElementById("reorder-validate-detail").reset();
                    var url = Event.element(event).action;
                    ajaxcartreorder(url, params);
                    return true;
                }
                else {
                    return false;
                }

            });

        }

    }
    /*
     *Coding for google checkout
     */
    var checkouttypes = $$('.checkout-types');

    if (checkouttypes) {

        var checkouttypesForm = $$(".checkout-types");

        if (typeof checkouttypesForm != 'undefined') {

            $$(".checkout-types form").each(function(index, c) {

                index.id = "form" + c;
                index.observe('submit', function(event) {
                    Event.stop(event);
                    var params = Event.element(event).serialize();
                    if (params)
                    {
                        var url = Event.element(event).action;
                        ajaxcartexpresscheckout(url);
                        return true;
                    }
                });
            });

        }

    }

}

/*
 * ajaxcartpageupdate function Starts it triggers when click delete link in
 * cartpage
 */
function ajaxcartpageupdate(url) {

    var request = new Ajax.Request(url, {
        method: 'post',
        evalJS: true,
        onSuccess: function(transport) {
            if (transport.status == 200) {
                var data = transport.responseText.evalJSON();
                var shipment_methods = $$('div.block-cart').first();

                var toptitle = $$('a.top-link-cart').first();
                var cartpage = $$('.cart').first();
                cartpage.innerHTML = data.cart;
                var cart_error = $$('#cart-error').first();
                cart_error.update('');
                $$('.item-msg').each(function(c) {
                    var cart_error = $$('#cart-error').first();
                    cart_error.update($(c));
                });
                var x = cartpage.getElementsByTagName("script");

                for (var i = 0; i < x.length; i++)
                {
                    eval(x[i].text);
                }
                toptitle.innerHTML = data.links;
                if ($('shopping-cart-table')) {
                    decorateTable('shopping-cart-table');


                    domload();
                }
                if (shipment_methods) {

                    shipment_methods.update(data.sidebar);

                }
                toptitle.innerHTML = data.links;
                domload();

            }
        }
    });
}
/* ajaxcartpageupdate function Ends */

var productAddToCartForm = new VarienForm('product_addtocart_form');
var discountForm = new VarienForm('discount-coupon-form');
var coShippingMethodForm = new VarienForm('shipping-zip-form');
var coForm = new VarienForm('co-shipping-method-form');
if (!Prototype.Browser.IE6) {

    var cnt1 = 20;
    __intId = setInterval(
            function() {

                addEvent();
            }, 500);
    addEvent();

}
/* addEvent function Starts */

function addEvent() {

    productAddToCartForm.submit = function(url) {

        if (this.validator && this.validator.validate()) {

            var url = url + '?ajaxview=1';

            var updatetest = $('product_addtocart_form').action;

            if (ajaxcart.ajaxcartUpdate == 1) {
                return this.form.submit();
            }
            $('product_addtocart_form').action += url;
            $('popup-block').setStyle({
                "display": "block"
            });
            $('fade').setStyle({
                "display": "block"
            });
            $('loading-box').setStyle({
                "display": "block"
            });

            /******check avialability start ******/

            var checkurl = base_url + 'ajaxcartpro/index/checkavail';
            var carturl = base_url + 'checkout/cart/index/?ajaxview=1';

            var request = new Ajax.Request(checkurl, {
                method: 'get',
                evalJS: true,
                parameters: $('product_addtocart_form').serialize(true),
                onSuccess: function(transport) {
                    if (transport.status == 200) {
                        var checkdata = transport.responseText.evalJSON();
                        if (!checkdata.success)
                        {
                            var cart_error = $$('#cart-error').first();
                            cart_error.update('');
                            ajaxcartview(carturl + "&error=" + checkdata.message);

                            return false;
                        }
                    }
                }
            });
            /******check avialability ends ******/

            $('product_addtocart_form').request(
                    {
                        onComplete: function(transport) {
                            try {

                                if (transport.status == 200) {

                                    var data = transport.responseText.evalJSON();
                                    var shipment_methods = $$('div.block-cart').first();

                                    var toptitle = $$('a.top-link-cart').first();

                                    if (shipment_methods) {

                                        shipment_methods.update(data.sidebar);

                                    }
                                    toptitle.innerHTML = data.links;
                                    $('product-cart').innerHTML = data.cart;
                                    var x = $('product-cart').getElementsByTagName("script");
                                    for (var i = 0; i < x.length; i++)
                                    {
                                        eval(x[i].text);
                                    }
                                    domload();
                                    $('loading-box').setStyle({
                                        "display": "none"
                                    });

                                }
                            } catch (e) {
                                return obj.form.submit();
                            }
                        }
                    });
        }
        return false;
    };

}
/* addEvent function Ends */

/* ajaxupdatepost function Starts */

function ajaxupdatepost(url, params) {
    $('loading-box').setStyle({
        "display": "block"
    });

    var url = url + '?ajaxview=1';

    var request = new Ajax.Request(url, {
        method: 'post',
        evalJS: true,
        parameters: params,
        onSuccess: function(transport) {
            if (transport.status == 200) {

                var data = transport.responseText.evalJSON();
                var shipment_methods = $$('div.block-cart').first();

                var toptitle = $$('a.top-link-cart').first();
                var cartpage = $$('.cart').first();
                cartpage.innerHTML = data.cart;
                $('loading-box').setStyle({
                    "display": "none"
                });
                var cart_error = $$('#cart-error').first();
                cart_error.update('');
                $$('.item-msg').each(function(c) {
                    var cart_error = $$('#cart-error').first();
                    cart_error.update($(c));
                });
                var x = cartpage.getElementsByTagName("script");
                for (var i = 0; i < x.length; i++)
                {
                    eval(x[i].text);
                }

                domload();

                toptitle.innerHTML = data.links;
                var message = data.message;
                if ($('shopping-cart-table')) {
                    decorateTable('shopping-cart-table');
                    if (message)
                    {
                        //alert(message);
                    }

                    domload();
                }
                if (shipment_methods) {

                    shipment_methods.update(data.sidebar);

                }
                toptitle.innerHTML = data.links;


            }
        }
    });
}
function couponcodepost(url, params) {

    var url = url + '?ajaxview=1';
    $('loading-box').setStyle({
        "display": "block"
    });

    var request = new Ajax.Request(url, {
        method: 'post',
        parameters: params,
        evalJS: true,
        onSuccess: function(transport) {
            if (transport.status == 200) {

                var data = transport.responseText.evalJSON();

                var cartpage = $$('.cart').first();
                $('loading-box').setStyle({
                    "display": "none"
                });

                cartpage.innerHTML = data.cart;

                var message = data.message;
                if (message)
                {
                   // alert(message);
                }
                var x = cartpage.getElementsByTagName("script");
                for (var i = 0; i < x.length; i++)
                {
                    eval(x[i].text);
                }

                domload();

            }
        }
    });
}

function ajaxClose(id)
{
    Effect.DropOut(id);
    $(id).setStyle({
        "display": "none"
    });
    $('fade').setStyle({
        "display": "none"
    });

    if (current_url.search('checkout') != -1)
    {
        window.open(current_url, '_self');
    }

}
function morebutton()
{

    var morebtnup = $('more-btn-up');

    if (morebtnup) {
        morebtnup.observe('click', function() {

            $('more-btn-up').setStyle({
                "display": "none"
            });
            $('more-btn-down').setStyle({
                "display": "block"
            });
            $$('.cart-collaterals').each(function(index) {

                $(index).setStyle({
                    "display": "block"
                })
            })
        });

    }
    var morebtnupdown = $('more-btn-down');

    if (morebtnupdown) {
        morebtnupdown.observe('click', function() {
            $('more-btn-up').setStyle({
                "display": "block"
            });
            $('more-btn-down').setStyle({
                "display": "none"
            });
            $$('.cart-collaterals').each(function(index) {

                $(index).setStyle({
                    "display": "none"
                })
            })
        });

    }
}

function addcart(url)
{
    var err = 0;
    $$('.productoptions select').each(function(c) {

        if (!$(c).getValue())
        {
            $(c).addClassName('validation-failed');
            err = 1;
        }

    });
    if (err)
    {
        return false;
    }
    var par = $('product_addtocart_form').serialize();

    var par1 = par.replaceAll('%5B', '[');
    var par2 = par1.replaceAll('%5D', ']');

    setLocation(url + "?" + par2 + "&qty=1/");
    $('custom-option').setStyle({
        "display": "none"
    });
    addEvent();

}


String.prototype.replaceAll = function(target, replacement) {
    return this.split(target).join(replacement);
};

