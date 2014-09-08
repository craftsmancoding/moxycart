// init var
var options = [];
// parse amount to float
var original_price = jQuery('.price').text();
	original_price = Number(original_price.replace(/[^0-9\.]+/g,''));

/**
 * compute amount from collected options
 * parse the operator and the amount
 */
function compute_price(options) {
	var total = original_price;
	$.each(options, function( index, price ) {
	 	console.log(price);
	 	var start = price.indexOf('|');
	 	var end = price.length - 1;
	 	var removeprice = price.slice(start,end);
	 	price = price.replace(removeprice,'');
	 	console.log(price);
		match = price.match(/\{([^)]+)\}/g);	
		if(match) {
			var	match = match[0].substring(1, match[0].length-1),
				operator = match.charAt(1)
				amount = match.substring(2);
			amount = Number(amount.replace(/[^0-9\.]+/g,''));
			if(operator == '+') {
				total += amount;
			} else {
				total -= amount;
			}
		}
	});
	return total;
}

/**
 * set_new_price
 */
 function set_new_price() {
 	// Loop on all select option
 	jQuery(".cart-default-select").each(function()
	{
		options.push(jQuery(this).val());
	});
	// set the price on the price placeholder
	jQuery('.price').html('$'+compute_price(options));
 }

/**
 * Compute price on select change
 * @param obj
 */
function onchange_price(obj) {
	// Calculate the price onchange event
	// empty options
	options = [];
	set_new_price();
}

// Calculate price on page loads
set_new_price();