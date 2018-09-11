(function( $ ) {
	'use strict';

	$(window).on("load", function(){
		
		$.get(cf7pd_url()+'partials/form.html', function(data)
		{
			var cf7pdform = $.parseHTML(data);
			console.log();
			var cf7pdfields = JSON.parse(JSON.stringify(cf7pd_fields()));
			var cf7pdpanel = $("<div>").attr({"id": "cf7pdpanel", "class": "large hidden"}).text("PIPEDRIVE Leads - Tag Panel");
			cf7pdform_recaptcha();
			cf7pdform_button();
			cf7pdform_builder(cf7pdfields, cf7pdform, cf7pdpanel);
			cf7pdform_actions(cf7pdfields, cf7pdform);
		});
	});
	
	function cf7pdform_button()
	{
		var cf7pdbutton = $("<a>").attr({"class": "PIPEDRIVE_button thickbox",
			"title": "Pipedrive Fields - Tag Generator",
			"href": "#TB_inline?width=400&height=400&inlineId=cf7pdpanel"}).text("Pipedrive Fields");
		$("#tag-generator-list").prepend(cf7pdbutton);	
	}
	
	function cf7pdform_recaptcha()
	{
		var cf7pdsubmit = $("<a>").attr({"class": "PIPEDRIVE_button",
			"title": "Secure Submit Button",
			"href": "#wpcf7-form"}).text("Secure Submit");
			$("#tag-generator-list").prepend(cf7pdsubmit);
			
			$(cf7pdsubmit).click(function(e){
				wpcf7.taggen.insert('[recaptcha_button "Submit"]');
				return false;
			});
			
	}
	
	
	function cf7pdform_builder(cf7pdfields, cf7pdform, cf7pdpanel)
	{
		cf7pdform = $(cf7pdform);
		var cf7pdfields_select = cf7pdform.find('#cf7pdselect');
			
		cf7pdfields.sort(function (a, b) {
			if (a.name.toLowerCase() > b.name.toLowerCase()) 
			{
				return 1;
			}
			if (a.name.toLowerCase() < b.name.toLowerCase()) 
			{
				return -1;
			}
			return 0;
		});			
		for(var x = 0; x < cf7pdfields.length; x++)
		{
				var cf7pdfields_value =  'PIPEDRIVE_'+cf7pdfields[x].id;
				cf7pdfields_value = cf7pdfields_value.replace(/ /g,"_"); 
				var cf7pdfields_option = $('<option></option>').attr({'value': cf7pdfields_value, 'pipedrive_dv': cf7pdfields[x].id, 'PIPEDRIVE_type': cf7pdfields[x].type}).text(cf7pdfields[x].name);
				cf7pdfields_select.append(cf7pdfields_option);	
		}	
		cf7pdpanel.append(cf7pdform);
		$("#tag-generator-list").append(cf7pdpanel);
	}

	function cf7pdform_actions(cf7pdfields, cf7pdform)
	{
		$('.PIPEDRIVE_button').click(function(){
			$(cf7pdform).find(".showoptions, .showdatepicker").hide();
			$(cf7pdform).find('input[type="text"], textarea').val('');
			$(cf7pdform).find('input[type="checkbox"]').prop( 'checked', false );
			$('#cf7pdselect').val($('#cf7pdselect option:first').val());
		});
		$(cf7pdform).find('input[name="cf7pd_datepicker"]').change(function(){
			if($(this).is(":checked"))
			{
				$(cf7pdform).find('input[name="class"]').val('cf7pd_datepicker');
			}
			else
			{
				$(cf7pdform).find('input[name="class"]').val('');
			}
		});
		$(cf7pdform).find('#cf7pdselect').change(function(){
			$(cf7pdform).find('input[type="text"], textarea').val('');
			$(cf7pdform).find('.tg-name').val($(this).find('option:selected').val());
			cf7pdform_code(cf7pdfields, $(cf7pdform));
		});
		$(cf7pdform).find('input, textarea').bind('blur focus change', function( i ) {
			cf7pdform_code(cf7pdfields, $(cf7pdform));			
		});
		$(".add-tag").click(function(){
			var tag = $(cf7pdform).find('input.tag').val();
			cf7pdform_code(cf7pdfields, $(cf7pdform));
			wpcf7.taggen.insert(tag);
			tb_remove(); // close thickbox
			return false;
		});		
	}
	
	function cf7pdform_code(cf7pdfields, cf7pdform)
	{
		cf7pdform_tag(cf7pdfields, $(cf7pdform));
		var cf7pdform_arr = $(cf7pdform).serializeArray();
		var cf7pdform_filtered = [];
		
		if($('input[name="required"]').is(':checked'))
		{
			cf7pdform_filtered.push(cf7pdform_type(cf7pdfields, $(cf7pdform))+'*');
		}
		else
		{
			cf7pdform_filtered.push(cf7pdform_type(cf7pdfields, $(cf7pdform)));
		}
		for(var x = 0; x < cf7pdform_arr.length; x++)
		{
			if(cf7pdform_arr[x].value)
			{
				if(cf7pdform_arr[x].name == 'name')
				{
					cf7pdform_filtered.push(cf7pdform_arr[x].value);
				}
				else if(cf7pdform_arr[x].name == 'id')
				{
					var cf7pdform_vlines = cf7pdform_arr[x].value;
					cf7pdform_filtered.push('id:'+cf7pdform_vlines.split(' ').join(''));
				}
				else if(cf7pdform_arr[x].name == 'class')
				{
					var cf7pdform_vlines = (cf7pdform_arr[x].value).split(' ');
					var cf7pdform_varr = [];
					for(var i = 0; i < cf7pdform_vlines.length; i++)
					{
						if(cf7pdform_vlines[i].length > 0)
						{
							cf7pdform_varr.push('class:'+cf7pdform_vlines[i]);							
						}
					}
					cf7pdform_filtered.push(cf7pdform_varr.join(' '));
				}
				else if(cf7pdform_arr[x].name == 'values')
				{
					var cf7pdform_vlines = (cf7pdform_arr[x].value).split('\n');
					var cf7pdform_varr = [];
				
					for(var i = 0; i < cf7pdform_vlines.length; i++)
					{
						cf7pdform_varr.push('"'+cf7pdform_vlines[i]+'"');
					}
					cf7pdform_filtered.push(cf7pdform_varr);
				}
			}
		}	
		$(cf7pdform).find('.tag').val('['+cf7pdform_filtered.join(' ')+']');
	}

	function cf7pdform_tag(cf7pdfields, cf7pdform)
	{
		var cf7pdoption = $(cf7pdform).find('option:selected');
				
		if(cf7pdoption.attr('pipedrive_type') == 'dropdown')
		{
			$(cf7pdform).find('.showoptions').fadeIn();
			$(cf7pdform).find('.showdatepicker').hide();
			var cf7pdlines = [];
			for(var x = 0; x < cf7pdfields.length; x++)
			{
				if(cf7pdfields[x].id == cf7pdoption.attr('pipedrive_dv'))
				{
					if(cf7pdfields[x].hasOwnProperty('options'))
					{
						for(var i = 0; i < cf7pdfields[x].options.length; i++)
						{
							cf7pdlines.push(cf7pdfields[x].options[i].OPTION_VALUE);
						}						
					}	
				}
			}	
			$(cf7pdform).find("#tag-generator-panel-menu-values").val(cf7pdlines.join('\n'));
		}
		else if(cf7pdoption.attr('pipedrive_type') == 'date')
		{
			$(cf7pdform).find(".showdatepicker").fadeIn();
		}			
		else
		{
			$(cf7pdform).find('.showoptions, .showdatepicker').hide();
		}
	}
	
	function cf7pdform_type(cf7pdfields, cf7pdform)
	{
		var cf7pdoption = $(cf7pdform).find('option:selected');
		var output = null;
		

		if(cf7pdoption.attr('pipedrive_type') == 'number')
		{
			output = 'number';
		}
		else if(cf7pdoption.attr('pipedrive_type') == 'Phone')
		{
			output = 'text';
			
		}
		else if(cf7pdoption.attr('pipedrive_type') == 'email')
		{
			output = 'email';
		}
		else if(cf7pdoption.attr('pipedrive_type') == 'textarea')
		{
			output = 'textarea';
		}
		else if(cf7pdoption.attr('pipedrive_type') == 'date')
		{
			output = 'date';
		}
		else if(cf7pdoption.attr('pipedrive_type') == 'select')
		{
			output = 'select';
		}		
		else
		{
			output = 'text';			
		}
		console.log(cf7pdoption.attr('pipedrive_type'));
		return output;
	}		

})( jQuery );
