INIT = {
	tabify: function() {
		$('#moxytab').tabify();
	},

	exec_wysiwyg: function(){
      bkLib.onDomLoaded(function() {
            new nicEditor({buttonList : ['bold','italic','underline','left','center','right','justify','ol','ul','fontSize','fontFamily','fontFormat','indent','outdent','image','forecolor','bgcolor']}).panelInstance('content');          
      });
    },

	get_components: function(comp_function,comp_template,comp_holder) {
		$.getJSON( "/assets/mycomponents/moxycart/assets/components/moxycart/connector.php?f="+comp_function, function( data ) {
			var template = Handlebars.compile( $('#'+comp_template).html() );
			$('#'+comp_holder).append( template( data.results ) );
		});
	},

	update_product: function(){
		$('#product_update').on('submit',function(e){
	    	var values = $(this).serialize();
	    	var url = connector_url + 'product_save';
		    $.post( url+"&action=update", values, function(data){
		    	$('.moxy-msg').show();
		    	data = $.parseJSON(data);
		    	if(data.success == true) {
		    		$('#moxy-result').html('Success');
		    	} else{
		    		$('#moxy-result').html('Failed');
		    	}
		    	$('#moxy-result-msg').html(data.msg);
		    	$(".moxy-msg").delay(3200).fadeOut(300);
		    } );
		    e.preventDefault();
	    })
	},

	create_product: function(){
		$('#product_create').on('submit',function(e){
	    	var values = $(this).serialize();
	    	var url = connector_url + 'product_save';
		    $.post( url+"&action=create", values, function(data){
		    	data = $.parseJSON(data);
		    	window.location.href = redirect_url + data.product_id;
		    } );
		    e.preventDefault();
	    })
	},

	fill_form_fields : function() {
		$.each(product, function(name, val){
	        var $el = $('#'+name),
	            type = $el.attr('type');	    
	        switch(type){
	            case "checkbox":
	                $el.attr("checked", "checked");
	                break;
	            case "radio":
	                $el.filter('[value="'+val+'"]').attr("checked", "checked");
	                break;
	            default:
	                $el.val(val);
	        }
	    });
	},

	load_currencies: function() {
		INIT.get_components('json_currencies&limit=0','currencyTemplate','currency_id');
	},

	load_templates: function() {
		INIT.get_components('json_templates&limit=0','templateTpl','template_id');
	},
	
	load_categories: function() {
		INIT.get_components('json_categories&limit=0','categoryTpl','category');
	},

	load_stores: function() {
		INIT.get_components('json_stores&limit=0','storesTpl','store_id');
	}


}

$(function() {
	INIT.load_currencies();
	INIT.load_templates();
	INIT.load_categories();
	INIT.load_stores();
	INIT.tabify();
	INIT.exec_wysiwyg();
	INIT.update_product();
	INIT.create_product();
	INIT.fill_form_fields();
});