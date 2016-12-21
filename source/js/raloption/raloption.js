/**
 * Yireo RalOption for Magento
 *
 * @package     Yireo_RalOption
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2016 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

var RalOption = (function () {

    /**
     * List of RAL codes within this instance
     *
     * @type {{}}
     */
    var codes = {};

    /**
     * Function to set the available codes in RALOption
     *
     * @param codes
     * @returns {setCodes}
     */
    var setCodes = function (codes) {
        this.codes = codes;
        return this;
    };

    /**
     * Update the pricing of this product
     *
     * @param code
     * @param color
     * @param price
     * @param optionId
     */
    var change = function (code, color, price, optionId) {

        $productOption = jQuery('#' + optionId);
        this.debug('change(): code ' + code + ' / color ' + color + ' / price ' + price + ' / optionId ' + optionId);

        if (code === undefined) {
            return;
        }

        if (!$productOption) {
            this.debug('change(): No element found with ID ' + optionId);
            return;
        }

        $productOption.data('price', price);
        $productOption.val(code);

        jQuery('#' + optionId + '_box').css('backgroundColor', '#' + color);

        jQuery.fancybox.close();

        RalOption.setProductPrice(price);
    };

    /**
     * Function to update Magento pricing
     *
     * @param price
     */
    var setProductPrice = function (price) {
        optionsPrice.changePrice('price', price);
        optionsPrice.changePrice('oldPrice', '0');
        optionsPrice.reload();
    };

    var debug = function (string) {
        console.log('[RalOption] ' + string);
    };

    /**
     * Constructor
     */
    return {
        setCodes: setCodes,
        change: change,
        setProductPrice: setProductPrice,
        debug: debug,
    };
}());

/**
 *
 * @param element
 * @returns {boolean}
 */
function raloptionCheckValue(element) {

    if(ralCodes.length < 1) {
        alert('No RAL-codes available');
        return false;
    }

    var inArray = jQuery.inArray(element.value, ralCodes);
    if(inArray == -1) {
        alert('This RAL-code is not valid');
        return false;
    } 

    //optionsPrice.getOptionPrices();
    //optionsPrice.reload();

    return true;
}

/**
 * Helper function to fetch a modal target from an element
 *
 * @param $element
 * @param parentIdentifier
 * @returns {*}
 * @constructor
 */
function RalOptionGetModalFromElement($element, parentIdentifier) {
    modalTarget = $element.attr('data-modal');
    if (!modalTarget) {
        $parentElement = $element.parents(parentIdentifier);
        modalTarget = $parentElement.attr('data-modal');
    }

    return modalTarget;
}
