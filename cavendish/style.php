<?php header("Content-type: text/css");
include('config.php');
?>
#header h6 a { 
	background-color: transparent; 
	background-image: url("<?php echo $cavendishLogoURL ?>"); 
	background-repeat: no-repeat; 
	width: <?php echo $cavendishLogoWidth ?>px;
	height: <?php echo $cavendishLogoHeight ?>px;
	margin-top: <?php echo $cavendishLogoMargin ?>px;
	}
<?php
if ($cavendishSiteWith) { ?>
#globalWrapper {
	width: <?php echo $cavendishSiteWith ?>px;
	}
<?php
}
if ($cavendishSidebarSearchbox)	{ ?>
	#nav #p-search {
		display:none;
	}<?php
}
if ($cavendishQRCodeMode=='all'){ ?>
#f-poweredbyico {
	display:none;
}
<?php
}
if ($cavendishQRCodeMode=='print'){ ?>
#qrcode {
	display:none;
}
<?php
}
?>
			