jQuery.noConflict();

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

function setRalOption(ralCode, colorCode, ralPrice) {
    jQuery('#' + optionId).data('price', ralPrice);
    jQuery('#' + optionId).val(ralCode);
    jQuery('#' + optionId + '_box').css('backgroundColor', '#' + colorCode);
    jQuery.fancybox.close();

    optionsPrice.changePrice('options', ralPrice);
    optionsPrice.reload();
}

