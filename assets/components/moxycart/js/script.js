INIT = {
	tabify: function() {
		$('#moxytab').tabify();
	},

	load_currencies: function() {
		$.getJSON( "/assets/mycomponents/moxycart/assets/components/moxycart/connector.php?f=json_currencies", function( data ) {
			var template = Handlebars.compile( $('#currencyTemplate').html() );
			$('#currency_id').append( template( data.results ) );
		});
	},

	load_templates: function() {
		$.getJSON( "/assets/mycomponents/moxycart/assets/components/moxycart/connector.php?f=json_templates", function( data ) {
			var template = Handlebars.compile( $('#templateTpl').html() );
			$('#template_id').append( template( data.results ) );
		});
	},
	
	load_categories: function() {
		$.getJSON( "/assets/mycomponents/moxycart/assets/components/moxycart/connector.php?f=json_categories", function( data ) {
			var template = Handlebars.compile( $('#categoryTpl').html() );
			$('#category').append( template( data.results ) );
		});
	},

	load_categories: function() {
		$.getJSON( "/assets/mycomponents/moxycart/assets/components/moxycart/connector.php?f=json_stores", function( data ) {
			var template = Handlebars.compile( $('#storesTpl').html() );
			$('#store_id').append( template( data.results ) );
		});
	}


}

$(function() {
	INIT.load_currencies();
	INIT.load_templates();
	INIT.load_categories();
	INIT.tabify();
});