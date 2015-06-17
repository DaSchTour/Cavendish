<?php
/**
 * Mozilla cavendish theme
 * Modified by DaSch for MW 1.19 and WeCoWi
 *
 * Loosely based on the cavendish style by Gabriel Wicke
 *
 * @todo document
 * @package MediaWiki
 * @subpackage Skins
 */


if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This is an extension to the MediaWiki package and cannot be run standalone.' );
}
 
$wgExtensionCredits['skin'][] = array(
	'path' => __FILE__,
	'name' => 'Cavendish', // name as shown under [[Special:Version]]
	'namemsg' => 'cavendish', // used since MW 1.24, see the section on "Localisation messages" below
	'version' => '2.3.4',
	'url' => 'https://www.mediawiki.org/wiki/Skin:Cavendish',
	'author' => '[https://mediawiki.org/wiki/User:DaSch DaSch]',
	'descriptionmsg' => 'cavendish-desc', // see the section on "Localisation messages" below
	'license' => 'GPL-2.0+',
);

$wgValidSkinNames['cavendish'] = 'Cavendish';
$wgAutoloadClasses['SkinCavendish'] = dirname(__FILE__).'/Cavendish.skin.php';
$wgExtensionMessagesFiles['Cavendish'] = dirname(__FILE__).'/Cavendish.i18n.php';

$wgResourceModules['skins.bootstrap'] = array(
	'styles' => array(
		'Cavendish/cavendish/print.css' => array( 'media' => 'print' ),
		'Cavendish/cavendish/cavendish.css' => array( 'media' => 'screen' ),
	),
	'remoteBasePath' => &$GLOBALS['wgStylePath'],
	'localBasePath' => &$GLOBALS['wgStyleDirectory'],
);