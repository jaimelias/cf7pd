(function( $ ) {
	'use strict';

	var htmllang = $("html").attr("lang");
	htmllang = htmllang.slice(0, -3);		
	var pipedrive_url = cf7pd_url();
	pipedrive_country_list(pipedrive_url, htmllang);
	pipedrive_lang(htmllang);
	pipedrive_param();
	pipedrive_id();
	pipedrive_cookies();
	responsive_datepicker();
	responsive_timepicker();
	
})( jQuery );

function pipedrive_cookies()
{
	$(window).on('load', function (e){
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
	});
}

function pipedrive_id(){
	
	$(window).on('load', function (e){
		$('.wpcf7-form').find('input.id').each(function(){
			if(this.id != '')
			{	
				if($('[data-id="'+this.id+'"]').text() != '')
				{
					var cleantext = $('<div></div>').html($('[data-id="'+this.id+'"]').html()).text();
					$(this).val($('[data-id="'+this.id+'"]').text());
					console.log(this.id+': '+$(this).val());
				}
				else
				{
					console.log('data-id '+ this.id +' not found in HTML.');
				}
				
			}
		});		
	});
}
function pipedrive_param()
{
	$(window).on('load', function (e){
		$('.wpcf7-form').find('input.param').each(function(){
			if(this.id != '')
			{
				if(getUrlParameter(this.id) != '')
				{
					$(this).val(getUrlParameter(this.id));
					console.log(this.id+': '+$(this).val());	
				}
				else
				{
					console.log('param '+ this.id +' not found in URL.');
				}
			}
		});		
	});
}

function pipedrive_lang(lang)
{
	$(window).on('load', function (e){
		$('.wpcf7-form').find('input.lang').each(function(){
			$(this).val(lang);
			console.log('lang: '+ $(this).val());
		});			
	});
}

function pipedrive_country_list(pluginurl, htmllang)
{
	$(window).on('load', function (e){
		if($('.countrylist').length > 0)
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
	});
}	

function pipedrive_country_options(data)
{
	$('.wpcf7-form').find('.countrylist').each(function() {
		for (var x = 0; x < data.length; x++) 
		{
			$(this).append('<option value=' + data[x][0] + '>' + data[x][1] + '</option>');
		}
	});
}

function responsive_datepicker()
{
	$(window).on('load', function (e){
		$('.wpcf7-form').find('input.datepicker').each(function()
		{	
			var args = {};
			args.format = 'yyyy-mm-dd';
			args.container = '#cf7pd-datepicker';
			
			if($(this).attr('type') == 'text')
			{
				$(this).pickadate(args);
			}
			else if($(this).attr('type') == 'date')
			{
				$(this).attr({'type': 'text'});
				$(this).pickadate(args);
			}
		});			
	});	
}


function responsive_timepicker()
{
	$(window).on('load', function (e){
		var args = {};
		args.container = '#cf7pd-timepicker';
		
		$('.wpcf7-form').find('input.timepicker').each(function()
		{
			$(this).pickatime(args);
		});			
	});	
}	


function pipedrive_submit($token)
{
	var exclude = ['country_code3', 'is_eu', 'country_tld', 'languages', 'country_flag', 'geoname_id', 'time_zone_current_time', 'time_zone_dst_savings', 'time_zone_is_dst'];
	
	$.getJSON('https://api.ipgeolocation.io/ipgeo?apiKey='+ipgeolocation_api(), function(data) {
	  
		var obj = {};

		for(var k in data)
		{
		  if(typeof data[k] !== 'object')
		  {
			  if(exclude.indexOf(k) == -1)
			  {
				obj[k] = data[k];
				$('input.'+k).val(data[k]);
			  }
		  }
		  else
		  {
			  for(var sk in data[k])
			  {
				  if(exclude.indexOf(k+'_'+sk) == -1)
				  {
					obj[k+'_'+sk] = data[k][sk];
					$('input.'+k+'_'+sk).val(data[k][sk]);
				  }	   
			  }
		  }
		}
	}).always(function(){

		$('input[name="response"]').val($token);
		console.log($('.wpcf7-form').serializeArray());
		$('.wpcf7-form').submit();
		grecaptcha.reset();
	});
}

function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
}