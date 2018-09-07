(function( $ ) {
	'use strict';

	$(function(){
		var htmllang = $("html").attr("lang");
		htmllang = htmllang.slice(0, -3);		
		var pipedrive_url = cf7pd_url();
		pipedrive_country_list(pipedrive_url, htmllang);
		pipedrive_lang(htmllang);
		pipedrive_param();
		pipedrive_id();
		pipedrive_cookies();
	});

	function pipedrive_cookies()
	{
		var landing = ['channel', 'device', 'landing_domain', 'landing_path'];
		var warnings = 0;		
		
		$('.wpcf7-form').each(function(){
			
			var this_form = $(this);
			
			for(var x = 0; x < landing.length; x++)
			{
				var this_field = $(this_form).find('input.'+landing[x]);
				
				$(this_field).each(function(){
					$(this).val(getCookie(landing[x]));
				});
				
				if($(this_field).length == 0)
				{
					console.warn('input.'+landing[x]+' not found');
					warnings++;
				}
			}
		});
		
		if(warnings > 0)
		{
			console.warn('You can create custom fields with Pipedrive and track metrics.');
		}
	}
	
	function pipedrive_id(){
		
		$('input.id').each(function(){
			if(this.id != '')
			{	
				if($('[data-id="'+this.id+'"]').text() != '')
				{
					$(this).val($('[data-id="'+this.id+'"]').text());
					console.log(this.id+': '+$(this).val());
				}
				else
				{
					console.log('data-id '+ this.id +' not found in HTML.');
				}
				
			}
		});			
	}
	function pipedrive_param()
	{
		var urlParams = new URLSearchParams(window.location.search);
		
		$('input.param').each(function(){
			if(this.id != '')
			{
				if(urlParams.get(this.id) != '')
				{
					$(this).val(urlParams.get(this.id));
					console.log(this.id+': '+$(this).val());	
				}
				else
				{
					console.log('param '+ this.id +' not found in URL.');
				}
			}
		});	
	}
	
	function pipedrive_lang(lang)
	{
		$('input.lang').each(function(){
			$(this).val(lang);
			console.log('lang: '+ $(this).val());
		});	
	}
	
	function pipedrive_country_list(pluginurl, htmllang)
	{
		$.getJSON( pluginurl + 'countries/'+htmllang+'.json')
			.done(function(data) 
			{
				pipedrive_country_options(data);
			})
			.fail(function()
			{
				$.getJSON(pluginurl + 'public/countries/en.json', function(data) {

					pipedrive_country_options(data);
				});				
			});			
	}	
	
	function pipedrive_country_options(data)
	{
		$('.countrylist').each(function() {
			for (var x = 0; x < data.length; x++) 
			{
				$(this).append('<option value=' + data[x][0] + '>' + data[x][1] + '</option>');
			}
		});		
	}
	
})( jQuery );

function pipedrive_submit($token)
{
	$('input[name="response"]').val($token);
	console.log($('.wpcf7-form').serializeArray());
	$('.wpcf7-form').submit();
	grecaptcha.reset();
}