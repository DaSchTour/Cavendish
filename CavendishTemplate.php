<?php
class CavendishTemplate extends BaseTemplate {
	public $skin;

	/**
	 * Generate the toolbox, complete with all three old hooks
	 *
	 * @return string html
	 */
	protected function getToolboxBox() {
		$html = '';
		$template = $this;

		return $this->getBox( 'tb',
			$this->data['sidebar']['TOOLBOX'],
			'toolbox'
		);
	}

	/**
	 * Generate the languages box
	 *
	 * @return string html
	 */
	protected function getLanguageBox() {
		$html = '';

		if ( $this->data['language_urls'] !== false ) {
			$html .= $this->getBox( 'lang', $this->data['language_urls'], 'otherlanguages' );
		}

		return $html;
	}

	/**
	 * Helper function for getPortlet
	 *
	 * Merge all provided css classes into a single array
	 * Account for possible different input methods matching what Html::element stuff takes
	 *
	 * @param string|array $class base portlet/body class
	 * @param string|array $extraClasses any extra classes to also include
	 *
	 * @return array all classes to apply
	 */
	protected function mergeClasses( $class, $extraClasses ) {
		if ( !is_array( $class ) ) {
			$class = [ $class ];
		}
		if ( !is_array( $extraClasses ) ) {
			$extraClasses = [ $extraClasses ];
		}

		return array_merge( $class, $extraClasses );
	}

	/**
	 * Generates a block of navigation links with a header
	 *
	 * @param string $name
	 * @param array|string $content array of links for use with makeListItem, or a block of text
	 * @param null|string|array $msg
	 * @param array $setOptions random crap to rename/do/whatever
	 *
	 * @return string html
	 */
	protected function getPortlet( $name, $content, $msg = null, $setOptions = [] ) {
		// random stuff to override with any provided options
		$options = array_merge( [
			// handle role=search a little differently
			'role' => 'navigation',
			'search-input-id' => 'searchInput',
			// extra classes/ids
			'id' => 'p-' . $name,
			'class' => 'mw-portlet',
			'extra-classes' => '',
			'body-id' => null,
			'body-class' => 'mw-portlet-body',
			'body-extra-classes' => '',
			// wrapper for individual list items
			'text-wrapper' => [ 'tag' => 'span' ],
			// old toolbox hook support (use: [ 'SkinTemplateToolboxEnd' => [ &$skin, true ] ])
			'hooks' => '',
			// option to stick arbitrary stuff at the beginning of the ul
			'list-prepend' => ''
		], $setOptions );

		// Handle the different $msg possibilities
		if ( $msg === null ) {
			$msg = $name;
			$msgParams = [];
		} elseif ( is_array( $msg ) ) {
			$msgString = array_shift( $msg );
			$msgParams = $msg;
			$msg = $msgString;
		} else {
			$msgParams = [];
		}
		$msgObj = $this->getMsg( $msg, $msgParams );
		if ( $msgObj->exists() ) {
			$msgString = $msgObj->parse();
		} else {
			$msgString = htmlspecialchars( $msg );
		}

		$labelId = Sanitizer::escapeIdForAttribute( "p-$name-label" );

		if ( is_array( $content ) ) {
			$contentText = Html::openElement( 'ul',
				[ 'lang' => $this->get( 'userlang' ), 'dir' => $this->get( 'dir' ) ]
			);
			$contentText .= $options['list-prepend'];
			foreach ( $content as $key => $item ) {
				if ( is_array( $options['text-wrapper'] ) ) {
					$contentText .= $this->makeListItem(
						$key,
						$item,
						[ 'text-wrapper' => $options['text-wrapper'] ]
					);
				} else {
					$contentText .= $this->makeListItem(
						$key,
						$item
					);
				}
			}

			$contentText .= Html::closeElement( 'ul' );
		} else {
			$contentText = $content;
		}

		// Special handling for role=search
		$divOptions = [
			'role' => $options['role'],
			'class' => $this->mergeClasses( $options['class'], $options['extra-classes'] ),
			'id' => Sanitizer::escapeIdForAttribute( $options['id'] ),
			'title' => Linker::titleAttrib( $options['id'] )
		];
		if ( $options['role'] !== 'search' ) {
			$divOptions['aria-labelledby'] = $labelId;
		}
		$labelOptions = [
			'id' => $labelId,
			'lang' => $this->get( 'userlang' ),
			'dir' => $this->get( 'dir' )
		];
		if ( $options['role'] == 'search' ) {
			$msgString = Html::rawElement( 'label', [ 'for' => $options['search-input-id'] ], $msgString );
		}

		$bodyDivOptions = [
			'class' => $this->mergeClasses( $options['body-class'], $options['body-extra-classes'] )
		];
		if ( is_string( $options['body-id'] ) ) {
			$bodyDivOptions['id'] = $options['body-id'];
		}

		$html = Html::rawElement( 'div', $divOptions,
			Html::rawElement( 'h3', $labelOptions, $msgString ) .
			Html::rawElement( 'div', $bodyDivOptions,
				$contentText .
				$this->getSkin()->getAfterPortlet( $name )
			)
		);

		return $html;
	}

	/**
	 * Generate a sidebar box using getPortlet(); prefill some common stuff
	 *
	 * @param string $name
	 * @param array|string $contents
	 * @param-taint $contents escapes_htmlnoent
	 * @param null|string|array|bool $msg
	 * @param array $setOptions
	 *
	 * @return string html
	 */
	protected function getBox( $name, $contents, $msg = null, $setOptions = [] ) {
		$options = array_merge( [
			'class' => 'portlet',
			'body-class' => 'pBody',
			'text-wrapper' => ''
		], $setOptions );

		// Do some special stuff for the personal menu
		if ( $name == 'personal' ) {
			$prependiture = '';

			// Extension:UniversalLanguageSelector order - T121793
			if ( array_key_exists( 'uls', $contents ) ) {
				$prependiture .= $this->makeListItem( 'uls', $contents['uls'] );
				unset( $contents['uls'] );
			}
			if ( !$this->getSkin()->getUser()->isLoggedIn() &&
				User::groupHasPermission( '*', 'edit' )
			) {
				$prependiture .= Html::rawElement(
					'li',
					[ 'id' => 'pt-anonuserpage' ],
					$this->getMsg( 'notloggedin' )->escaped()
				);
			}
			$options['list-prepend'] = $prependiture;
		}

		return $this->getPortlet( $name, $contents, $msg, $options );
	}

	/**
	 * Generate the search, using config options for buttons (?)
	 *
	 * @return string html
	 */
	protected function getSearchBox() {
		$html = '';

		$optionButtons = "\u{00A0} " . $this->makeSearchButton(
			'fulltext',
			[ 'id' => 'mw-searchButton', 'class' => 'searchButton' ]
		);
		$searchInputId = 'searchInput';
		$searchForm = Html::rawElement( 'form', [
			'action' => $this->get( 'wgScript' ),
			'id' => 'searchform'
		],
			Html::hidden( 'title', $this->get( 'searchtitle' ) ) .
			$this->makeSearchInput( [ 'id' => $searchInputId ] ) .
			$this->makeSearchButton( 'go', [ 'id' => 'searchGoButton', 'class' => 'searchButton' ] ) .
			$optionButtons
		);

		$html .= $this->getBox( 'search', $searchForm, null, [
			'search-input-id' => $searchInputId,
			'role' => 'search',
			'body-id' => 'searchBody'
		] );

		return $html;
	}

	/**
	 * Template filter callback for Cavendish skin.
	 * Takes an associative array of data set from a SkinTemplate-based
	 * class, and a wrapper for MediaWiki's localization database, and
	 * outputs a formatted page.
	 */
	function execute() {
		$this->skin = $skin = $this->data['skin'];
		$action = $skin->getRequest()->getText( 'action', 'view' );

		$this->data['pageLanguage'] =
			$this->getSkin()->getTitle()->getPageViewLanguage()->getHtmlCode();

		// HTML starts here
		$this->html( 'headelement' );
?>
<div id="internal"></div>
<div id="globalWrapper" class="<?php echo htmlspecialchars( $action, ENT_QUOTES ) ?>">
	<div id="p-personal" class="portlet">
		<h5><?php $this->msg( 'personaltools' ) ?></h5>
		<div class="pBody">
			<ul<?php $this->html( 'userlangattributes' ) ?>>
			<?php
				$personalTools = $this->getPersonalTools();
				foreach ( $personalTools as $key => $item ) {
			?>
				<li id="<?php echo Sanitizer::escapeIdForAttribute( "pt-$key" ) ?>" class="<?php
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
					echo $this->makeLink( $key, $item['links'][0], [ 'link-class' => 'top-nav-mid' ] ); ?>
					<span class="top-nav-right">&nbsp;</span>
				</li>
				<?php
				}
			?>
			</ul>
		</div>
	</div>
	<div id="header">
		<a name="top" id="contentTop"></a>
		<h6>
		<a href="<?php echo htmlspecialchars( $this->data['nav_urls']['mainpage']['href'] ) ?>"
			title="<?php $this->msg( 'mainpage' ) ?>"><?php $this->text( 'pagetitle' ) ?></a></h6>
		<div id="p-cactions" class="portlet" role="navigation">
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
			echo $this->getSearchBox();
		?>
	</div>
	<div id="mBody">
		<div id="side">
			<div id="nav">
				<?php echo $this->renderPortals( $this->data['sidebar'] ); ?>
			</div>
		</div>
	</div><!-- end of #mBody div -->
	<div id="column-content">
		<div id="content" class="mw-body">
			<a id="top"></a>
			<?php if ( $this->data['sitenotice'] ) { ?><div id="siteNotice"><?php $this->html( 'sitenotice' ) ?></div><?php } ?>
			<?php echo $this->getIndicators() ?>
			<h1 id="firstHeading" class="firstHeading" lang="<?php $this->text( 'pageLanguage' ); ?>"><?php $this->html( 'title' ) ?></h1>
			<div id="bodyContent" class="mw-body-content">
				<h3 id="siteSub"><?php $this->msg( 'tagline' ) ?></h3>
				<div id="contentSub"<?php $this->html( 'userlangattributes' ) ?>><?php $this->html( 'subtitle' ) ?></div>
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
		</div><!-- end of #content div -->
	</div>
</div><!-- end of #globalWrapper div -->
<div class="visualClear"></div>
<div id="footer" role="contentinfo"<?php $this->html( 'userlangattributes' ) ?>>
	<table>
		<tr>
			<td rowspan="2" class="f-iconsection">
			<?php if ( $this->data['copyrightico'] ) { ?>
				<div id="f-copyrightico"><?php echo $this->skin->makeFooterIcon( $this->data['copyrightico'] ) ?></div>
			<?php } ?>
			</td>
			<td align="center">
<?php	// Generate additional footer links
		$footerLinks = [
			'lastmod', 'viewcount', 'credits', 'copyright',
			'privacy', 'about', 'disclaimer', 'tagline',
		];

		$validFooterLinks = [];
		foreach ( $footerLinks as $aLink ) {
			if ( isset( $this->data[$aLink] ) && $this->data[$aLink] ) {
				$validFooterLinks[] = $aLink;
			}
		}

		if ( count( $validFooterLinks ) > 0 ) {
?>				<ul id="f-list">
<?php
			foreach ( $validFooterLinks as $aLink ) {
				if ( isset( $this->data[$aLink] ) && $this->data[$aLink] ) {
?>					<li id="f-<?php echo $aLink ?>"><?php $this->html( $aLink ) ?></li>
<?php 			}
			}
		}
?>
				</ul>
			</td>
			<td rowspan="2" class="f-iconsection">
				<?php
				$validFooterIcons = $this->get( 'footericons' );
				unset( $validFooterIcons['copyright'] );
				foreach ( $validFooterIcons as $blockName => $footerIcons ) { ?>
						<div id="f-<?php echo htmlspecialchars( $blockName ); ?>ico"><?php
					foreach ( $footerIcons as $icon ) {
						// Need to check emptiness before rendering to prevent core from throwing
						// an E_NOTICE:
						// PHP Notice:  Undefined index: alt in <path to MW>/includes/skins/Skin.php on line 1007
						if ( !empty( $icon ) ) {
							echo $this->skin->makeFooterIcon( $icon );
						}
					}
				?></div>
				<?php
				}
				// Show a Quick Response (QR) code if enabled in configuration
				if ( $this->config->get( 'CavendishQRCode' ) ) {
					$QRURL = htmlentities( $skin->getTitle()->getFullURL() ) . $this->config->get( 'CavendishQRUrlAdd' );
				?>
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
					<?php echo $skin->msg( 'cavendish-skin-info', '2.5.0' )->parse() ?>
				</div>
			</td>
		</tr>
	</table>
</div><!-- end of the FOOTER div -->
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
		$html = '';
		$languagesHTML = '';

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
				// ignore.
			} elseif ( $boxName == 'TOOLBOX' ) {
				$html .= $this->getToolboxBox( $content );
			} elseif ( $boxName == 'LANGUAGES' ) {
				$languagesHTML = $this->getLanguageBox( $content );
			} else {
				$html .= $this->getBox(
					$boxName,
					$content,
					null,
					[ 'extra-classes' => 'generated-sidebar' ]
				);
			}
		}

		// Output language portal last given it can be long
		// on articles which support multiple languages (T254546)
		return $html . $languagesHTML;
	}
} // end of class
