<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
	
	
	<base href="{$baseurl}" />
    <title>{$title}</title>
    
    
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="content-language" content="de" />
    <meta name="author" content="Franz Wilding - www.franz-wilding.at" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta name="robots" content="index,follow" />

    <meta name="DC.Title" content="SAME AS TITLE" />
    <meta name="date" content="" />
	<meta name="generator" content="" />
	<meta name="keywords" content="" />
    
	<!--[if lt IE 9]>
		<script src="media/js/IEFIXES.js" type="text/javascript"></script>
	<![endif]-->
	

	
	<!-- ##### MAIN JAVASCRIPT ##### -->	
	<script src="{$mediapath}/js/jquery.js" type="text/javascript"></script>
	
	<script src="{$mediapath}/js/wymeditor/jquery.wymeditor.js" type="text/javascript"></script>
	<script src="{$mediapath}/js/wymeditor/plugins/hovertools/jquery.wymeditor.hovertools.js" type="text/javascript"></script>
	<script src="{$mediapath}/js/jqueryui/ui.jquery.js" type="text/javascript"></script>
	
	<script type="text/javascript" src="{$mediapath}/js/fancybox/jquery.mousewheel.js"></script>
	<script type="text/javascript" src="{$mediapath}/js/fancybox/jquery.fancybox.js"></script>

	<script type="text/javascript" src="{$mediapath}/js/uploadify/scripts/swfobject.js"></script>
	<script type="text/javascript" src="{$mediapath}/js/uploadify/scripts/jquery.uploadify.js"></script>
	
	<script src="{$mediapath}/js/functions.js" type="text/javascript"></script>

	<!-- ##### MAIN CSS FILE ##### -->
	<link href="{$mediapath}/css/main.css" rel="stylesheet" type="text/css" media="screen" />
	<link rel="stylesheet" type="text/css" href="{$mediapath}/js/fancybox/jquery.fancybox.css" media="screen" />
	<link href="{$mediapath}/js/uploadify/css/uploadify.css" rel="stylesheet" type="text/css" />

</head>
<body>

<div id="navigation">

<p id="logo">contentBox, von Franz Wilding</p><!-- logo ends here -->
<div id="ulWrapper">
	<ul>
		{foreach from=$contentareas item=contentarea}
		<li{if $contentarea.active} class="active"{/if}><a href="content/{$contentarea.id}">{$contentarea.name}</a></li>
		{/foreach}
	</ul>
</div>

<div id="runaway">
	<a href="javascript:popup($('#runaway > a'), $('#runaway > a + p'), 'below_right', 'top');">Metanavigation</a>
	<p class="popup">
		<a href="logout" class="big">Logout</a>
		<a href="settings" class="settings">Einstellungen</a>
		<a href="persones" class="user">User</a>
		<a href="" class="small">Hilfe</a>, <a href="http://contentbox.franz-wilding.at" target="_blank" class="small">Über contentBox</a>
	</p>
</div><!-- runaway ends here -->

</div><!-- navigation ends here -->

<div id="content">
	<p class="content_title">Content</p>
	
	{$content}
</div><!-- content ends here -->


</body>
</html>