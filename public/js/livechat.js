window.__lc = window.__lc || {};
window.__lc.license = livechat_license();
(function() {
  var lc = document.createElement('script'); lc.type = 'text/javascript'; lc.async = true;
  lc.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'cdn.livechatinc.com/tracking.js';
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(lc, s);
})();
var LC_API = LC_API || {};
LC_API.on_after_load = function() {
	var custom_variables = [
	  { name: 'landing_path', value: getCookie('landing_path') },
	  { name: 'landing_domain', value: getCookie('landing_domain') },
	  { name: 'channel', value: getCookie('channel') },
	  { name: 'device', value: getCookie('device') }
	];
	LC_API.set_custom_variables(custom_variables);
};