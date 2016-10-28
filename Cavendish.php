<?php

if ( function_exists( 'wfLoadSkin' ) ) {
	wfLoadSkin( 'Cavendish' );
	// Keep i18n globals so mergeMessageFileList.php doesn't break
	$wgMessagesDirs['Cavendish'] = __DIR__ . '/i18n';
	wfWarn(
		'Deprecated PHP entry point used for Cavendish skin. Please use wfLoadSkin instead, ' .
		'see https://www.mediawiki.org/wiki/Extension_registration for more details.'
	);
	return true;
} else {
	die( 'This version of the Cavendish skin requires MediaWiki 1.25+' );
}
