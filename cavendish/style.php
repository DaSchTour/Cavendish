<?php header("Content-type: text/css");
$logoURL = $_GET['logo'];	
$logowidth = $_GET['logowidth'];
$logoheight = $_GET['logoheight'];
$logomargin=$GET['logomargin'];
$pagewidth=$GET['pagewidth'];
$sidebarsearch=$GET['$sidebarsearch'];
?>
#header h6 a { 
	background-color: transparent; 
	background-image: url("'.$logoURL.'"); 
	background-repeat: no-repeat; 
	width:<?php echo $logowidth ?> px;
	height:<?php echo $logoheight ?> px;
	margin-top:<?php echo $logomargin ?> px;
	}
<?php
if (isset($pagewith)) { ?>
#globalWrapper {
	width:<?php echo $pagewidth ?> px;
	}
<?php
}
if ($sidebarsearch)	{
	?>#nav #p-search {display:none;}<?php
}
?>
			