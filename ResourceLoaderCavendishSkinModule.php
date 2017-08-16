<?php
/**
 * ResourceLoader module for the Cavendish skin.
 *
 * @file
 */
class ResourceLoaderCavendishSkinModule extends ResourceLoaderModule {
	/**
	 * Get all CSS for this module for a given skin.
	 *
	 * @param ResourceLoaderContext $context
	 * @return array List of CSS strings or array of CSS strings keyed by media type.
	 *  like [ 'screen' => '.foo { width: 0 }' ];
	 *  or [ 'screen' => [ '.foo { width: 0 }' ] ];
	 */
	public function getStyles( ResourceLoaderContext $context ) {
		$config = ConfigFactory::getDefaultInstance()->makeConfig( 'cavendish' );
		$logoURL = $config->get( 'CavendishLogoURL' );
		$logoWidth = $config->get( 'CavendishLogoWidth' );
		$logoHeight = $config->get( 'CavendishLogoHeight' );
		$logoMargin = $config->get( 'CavendishLogoMargin' );
		$siteWidth = $config->get( 'CavendishSiteWidth' );
		$qrCodeMode = $config->get( 'CavendishQRCodeMode' );

		$styles = <<<STYLES
#header h6 a {
	background-color: transparent;
	background-image: url("{$logoURL}");
	background-repeat: no-repeat;
	width: {$logoWidth}px;
	height: {$logoHeight}px;
	margin-top: {$logoMargin}px;
}
STYLES;
		$styles .= "\n\n";

		if ( $siteWidth ) {
			$styles .= <<<STYLES
#globalWrapper {
	width: {$siteWidth}px;
}
STYLES;
			$styles .= "\n\n";
		}

		if ( $config->get( 'CavendishSidebarSearchbox' ) ) {
			$styles .= <<<STYLES
#nav #p-search {
	display: none;
}
STYLES;
			$styles .= "\n\n";
		}

		if ( $qrCodeMode == 'all' ) {
			$styles .= <<<STYLES
#f-poweredbyico {
	display: none;
}
STYLES;
			$styles .= "\n\n";
		}

		if ( $qrCodeMode == 'print' ) {
			$styles .= <<<STYLES
#qrcode {
	display: none;
}
STYLES;
			$styles .= "\n\n";
		}

		return [ 'screen' => $styles ];
	}
}