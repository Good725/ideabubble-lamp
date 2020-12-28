/*
ts:2018-03-29 21:39:00
*/

update engine_site_templates
	set header = replace(header, '</head>', '<link href="/engine/shared/css/jquery.datetimepicker.css" rel="stylesheet" /><script src="/engine/shared/js/daterangepicker/jquery.datetimepicker.js"></script><script>$(document).ready(function(){    $(".datepicker").datetimepicker({format:"d/m/Y",timepicker:false});});</script></head>');
	