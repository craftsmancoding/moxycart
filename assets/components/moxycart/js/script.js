INIT = {
	tabify: function() {
		$('#moxytab').tabify();
	},

	get_components: function(comp_function,comp_template,comp_holder) {
		$.getJSON( "/assets/mycomponents/moxycart/assets/components/moxycart/connector.php?f="+comp_function, function( data ) {
			var template = Handlebars.compile( $('#'+comp_template).html() );
			$('#'+comp_holder).append( template( data.results ) );
		});
	},

	load_currencies: function() {
		INIT.get_components('json_currencies','currencyTemplate','currency_id');
	},

	load_templates: function() {
		INIT.get_components('json_templates','templateTpl','template_id');
	},
	
	load_categories: function() {
		INIT.get_components('json_categories','categoryTpl','category');
	},

	load_stores: function() {
		INIT.get_components('json_stores','storesTpl','store_id');
	}


}

$(function() {
	INIT.load_currencies();
	INIT.load_templates();
	INIT.load_categories();
	INIT.load_stores();
	INIT.tabify();
});