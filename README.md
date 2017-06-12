# Cavendish
Cavendish MediaWiki skin

## IMPORTANT NOTE
This fork is not yet stable. Check back later!

## Compatibility
This version of the skin is compatible with MW 1.28+

## Customization
There are different options that can be set, either in `LocalSettings.php` as usual or in `resources/config.php`.
The old configuration variables (which do not have the `wg` prefix) are being phased out.

`$cavendishLogoURL`
Link to the Logo that should be displayed in the header

`$cavendishLogoWidth`
width of the logo as a number in px

`$cavendishLogoHeight`
height of the logo as a number in px

`$cavendishLogoMargin`
offset on top of the logo

`$cavendishSiteWidth`
fixed width of the content area, if not set it's dynamic (default is false, that means dynamic)

The following options can be customized in `LocalSettings.php`:

`$wgCavendishExtensionCSS`
DEPRECATED. Set this to false if you do not want to use Cavendish CSS for certain extensions (default is true). As of 12 June 2017 Cavendish automatically uses its own CSS for the following extensions if and when said extensions are installed: Babel, Cite, InputBox, LiquidThread

`$wgCavendishQRCode`
Whether to add QR code to all pages (true) or not (false)

`$wgCavendishQRUrlAdd`
Used to track campaign for entry through QR Code in analytics software (e.g. Google Analytics, Piwik, ...); default is "?pk_campaign=qr-code"

`$wgCavendishColor`
Select color of the theme: blue, brown or green (default is blue)