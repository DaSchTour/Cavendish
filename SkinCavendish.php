<?php

class SkinCavendish extends SkinTemplate {
	public $skinname = 'cavendish', $stylename = 'cavendish',
		$template = 'CavendishTemplate', $useHeadElement = true;

	/**
	 * @var Config
	 */
	private $cavendishConfig;

	public function __construct() {
		$this->cavendishConfig = ConfigFactory::getDefaultInstance()->makeConfig( 'cavendish' );
	}

	/**
	 * @param $out OutputPage object
	 */
	function setupSkinUserCss( OutputPage $out ) {
		parent::setupSkinUserCss( $out );

		$out->addModuleStyles( 'skins.cavendish' );

		$out->addStyle( 'Cavendish/resources/colors/' . $this->cavendishConfig->get( 'CavendishColor' ) . '.css', 'screen' );

		if ( $this->cavendishConfig->get( 'CavendishExtensionCSS' ) ) {
			$out->addStyle( 'Cavendish/resources/extensions.css', 'screen' );
		}

		/* README for details */
		include 'resources/config.php';

		$out->addStyle( 'Cavendish/resources/style.php', 'screen' );
	}
}

class CavendishTemplate extends MonoBookTemplate {
	public $skin;
	/**
	 * Template filter callback for Cavendish skin.
	 * Takes an associative array of data set from a SkinTemplate-based
	 * class, and a wrapper for MediaWiki's localization database, and
	 * outputs a formatted page.
	 */
	function execute() {
		$this->skin = $skin = $this->data['skin'];
		$QRURL = htmlentities( $skin->getTitle()->getFullURL() ) . $this->config->get( 'CavendishQRUrlAdd' );
		$styleversion = '2.3.5';
		$action = $skin->getRequest()->getText( 'action', 'view' );

		// HTML starts here
		$this->html( 'headelement' );
?>
<div id="internal"></div>
<!-- Skin version: <?php echo $styleversion ?> //Please leave this for bugtracking purpose//-->
<div id="globalWrapper" class="<?php echo htmlspecialchars( $action, ENT_QUOTES ) ?>">
	<div id="p-personal" class="portlet">
		<h5><?php $this->msg( 'personaltools' ) ?></h5>
		<div class="pBody">
			<ul<?php $this->html( 'userlangattributes' ) ?>>
			<?php
				$personalTools = $this->getPersonalTools();
				foreach ( $personalTools as $key => $item ) {
			?>
				<li id="<?php echo Sanitizer::escapeId( "pt-$key" ) ?>" class="<?php
					if ( $item['active'] ) { ?>active <?php } ?>top-nav-element">
					<span class="top-nav-left">&nbsp;</span>
					<?php
					// Crappy hack for Echo
					// Without this the Echo links will generate an E_NOTICE:
					// Notice: Array to string conversion in ../includes/skins/BaseTemplate.php on line 410
					if ( isset( $item['links'][0]['class'] ) && is_array( $item['links'][0]['class'] ) ) {
						$classHTML = '';
						foreach ( $item['links'][0]['class'] as $class ) {
							$classHTML .= $class . ' ';
						}
						$item['links'][0]['class'] = $classHTML;
					}
					echo $this->makeLink( $key, $item['links'][0], array( 'link-class' => 'top-nav-mid' ) ); ?>
					<span class="top-nav-right">&nbsp;</span></li>
					<?php
				}
			?>
			</ul>
		</div>
	</div>
	<div id="header">
		<a name="top" id="contentTop"></a>
		<h6>
		<a
		href="<?php echo htmlspecialchars( $this->data['nav_urls']['mainpage']['href'] ) ?>"
		title="<?php $this->msg( 'mainpage' ) ?>"><?php $this->text( 'pagetitle' ) ?></a></h6>
		<div id="p-cactions" class="portlet">
			<ul>
			<?php
			foreach ( $this->data['content_actions'] as $key => $tab ) {
					echo $this->makeListItem( $key, $tab );
				}
			?>
			</ul>
		</div>
		<?php
			// TODO Searchbox Handling
			$this->searchBox();
		?>
	</div>
	<div id="mBody">
		<div id="side">
			<div id="nav">
				<?php $this->renderPortals( $this->data['sidebar'] ); ?>
			</div>
		</div>
		</div><!-- end of SIDE div -->
		<div id="column-content">
			<div id="content">
				<a id="top"></a>
				<?php if ( $this->data['sitenotice'] ) { ?><div id="siteNotice"><?php $this->html( 'sitenotice' ) ?></div><?php } ?>
				<h1 id="firstHeading" class="firstHeading"><?php $this->html( 'title' ) ?></h1>
				<div id="bodyContent">
					<h3 id="siteSub"><?php $this->msg( 'tagline' ) ?></h3>
					<div id="contentSub"><?php $this->html( 'subtitle' ) ?></div>
					<?php if ( $this->data['undelete'] ) { ?><div id="contentSub2"><?php $this->html( 'undelete' ) ?></div><?php } ?>
					<?php if ( $this->data['newtalk'] ) { ?><div class="usermessage"><?php $this->html( 'newtalk' ) ?></div><?php } ?>
					<div id="jump-to-nav" class="mw-jump"><?php $this->msg( 'jumpto' ) ?> <a href="#column-one"><?php $this->msg( 'jumptonavigation' ) ?></a>, <a href="#searchInput"><?php $this->msg( 'jumptosearch' ) ?></a></div>
					<!-- start content -->
					<?php
						$this->html( 'bodytext' );
						if ( $this->data['catlinks'] ) {
							$this->html( 'catlinks' );
						}
					?>
					<!-- end content -->
					<?php
					if ( $this->data['dataAfterContent'] ) {
						$this->html( 'dataAfterContent' );
					}
					?>
				</div>
			</div><!-- end of MAINCONTENT div -->
		</div>
	</div><!-- end of MBODY div -->
	<div class="visualClear"></div>
	<div id="footer">
		<table>
			<tr>
				<td rowspan="2" class="f-iconsection">
		<?php // copyright icon
		if ( $this->data['copyrightico'] ) { ?><div id="f-copyrightico"><?php $this->skin->makeFooterIcon( $this->data['copyrightico'] ) ?></div><?php } ?>
				</td>
				<td align="center">
<?php	// Generate additional footer links
		$footerLinks = array(
			'lastmod', 'viewcount', 'credits', 'copyright',
			'privacy', 'about', 'disclaimer', 'tagline',
		);
		$validFooterLinks = array();
		foreach ( $footerLinks as $aLink ) {
			if ( isset( $this->data[$aLink] ) && $this->data[$aLink] ) {
				$validFooterLinks[] = $aLink;
			}
		}
		if ( count( $validFooterLinks ) > 0 ) {
?>			<ul id="f-list">
<?php
			foreach ( $validFooterLinks as $aLink ) {
				if ( isset( $this->data[$aLink] ) && $this->data[$aLink] ) {
?>					<li id="f-<?php echo $aLink ?>"><?php $this->html( $aLink ) ?></li>
<?php 			}
			}
		}
?></ul></td>
				<td rowspan="2" class="f-iconsection">
					<?php
					$validFooterIcons = $this->getFooterIcons( 'nocopyright' );
					foreach ( $validFooterIcons as $blockName => $footerIcons ) { ?>
							<div id="f-<?php echo htmlspecialchars( $blockName ); ?>ico"><?php
						foreach ( $footerIcons as $icon ) {
							echo $this->skin->makeFooterIcon( $icon );
						}
					}
					?></div>
					<?php
					// Show a Quick Response (QR) code if enabled in configuration
					if ( $this->config->get( 'CavendishQRCode' ) ) { ?>
					<div id="qrcode">
						<a href="http://goqr.me/" style="border:0 none;cursor:default;text-decoration:none;">
							<img src="http://api.qrserver.com/v1/create-qr-code/?data=<?php echo $QRURL; ?>&#38;size=160x160" height="80" width="80" alt="QR Code generator" title="" />
						</a>
					</div>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td>
					<div id="skin-info">
						<?php echo $skin->msg( 'cavendish-skin-info', $styleversion )->parse() ?>
					</div>
				</td>
			</tr>
		</table>
	</div><!-- end of the FOOTER div -->
</div><!-- end of the CONTAINER div -->
<!-- scripts and debugging information -->
<?php
		$this->printTrail();
		echo Html::closeElement( 'body' );
		echo Html::closeElement( 'html' );
	}

	/**
	 * @todo FIXME: this is literal copypasta from MonoBookTemplate.php just
	 * so that we can comment out one line. That sucks.
	 * @param array $sidebar
	 */
	protected function renderPortals( $sidebar ) {
		if ( !isset( $sidebar['SEARCH'] ) ) {
			$sidebar['SEARCH'] = true;
		}
		if ( !isset( $sidebar['TOOLBOX'] ) ) {
			$sidebar['TOOLBOX'] = true;
		}
		if ( !isset( $sidebar['LANGUAGES'] ) ) {
			$sidebar['LANGUAGES'] = true;
		}

		foreach ( $sidebar as $boxName => $content ) {
			if ( $content === false ) {
				continue;
			}

			// Numeric strings gets an integer when set as key, cast back - T73639
			$boxName = (string)$boxName;

			if ( $boxName == 'SEARCH' ) {
				// @todo FIXME: search box handling
				// $this->searchBox();
			} elseif ( $boxName == 'TOOLBOX' ) {
				$this->toolbox();
			} elseif ( $boxName == 'LANGUAGES' ) {
				$this->languageBox();
			} else {
				$this->customBox( $boxName, $content );
			}
		}
	}
} // end of class
