# Cavendish
Cavendish MediaWiki skin version 2.4.0

## Compatibility
This version of the skin is compatible with MW 1.28+

## Dependencies
The Cavendish skin requires the [MonoBook skin](https://www.mediawiki.org/wiki/Skin:MonoBook) to be installed as the `CavendishTemplate` class extends MonoBook's `MonoBookTemplate` class.

## Customization
There are different options that can be set in `LocalSettings.php`.

`$wgCavendishLogoURL`
URL to the logo image that should be displayed in the header

`$wgCavendishLogoWidth`
width of the logo as a number in px

`$wgCavendishLogoHeight`
height of the logo as a number in px

`$wgCavendishLogoMargin`
offset on top of the logo

`$wgCavendishSiteWidth`
fixed width of the content area, if not set it's dynamic (default is false, that means dynamic)

`$wgCavendishExtensionCSS`
DEPRECATED. Set this to false if you do not want to use Cavendish CSS for certain extensions (default is true). As of 12 June 2017 Cavendish automatically uses its own CSS for the following extensions if and when said extensions are installed: Babel, Cite, InputBox, LiquidThread, WikiEditor

`$wgCavendishQRCode`
Whether to add QR code to all pages (true) or not (false)

`$wgCavendishQRUrlAdd`
Used to track campaign for entry through QR Code in analytics software (e.g. Google Analytics, Piwik, ...); default is "?pk_campaign=qr-code"

`$wgCavendishColor`
Select color of the theme: blue, brown or green (default is blue)