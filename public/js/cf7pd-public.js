$(function(){
	
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
		pipedrive_submit();
});