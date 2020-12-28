<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
</head>

<body>
<form id="start3d" name="start3d" action="<?=$ACSURL?>" method="post">
<input type="hidden" name="MD" value="<?=$MD?>" />
<input type="hidden" name="PaReq" value="<?=$PAReq?>" />
<input type="hidden" name="TermUrl" value="<?=URL::site('frontend/sagepay/3dcallback')?>" />
<button type="submit" id="send">click here if you are not automatically redirected</button>
</form>
<script>
document.forms.start3d.onsubmit = function(){
	document.getElementById('send').disabled = true;
};
document.forms.start3d.submit();
</script>
</body>
</html>