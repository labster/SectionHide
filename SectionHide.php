<?php
/**
 * SectionHide extension - implements a hide/show link on sections on any ordinary page.
 * @version 1.7 - 2014/08/26
 * version 1.1 added a hide all/show all link for the entire article 
 * version 1.2 added an option on the hide all/show all link to show the initial text
 * version 1.3 added opt out error if installed alongside header tabs
 * version 1.4 added options for images as well as/instead of text
 * version 1.5 tweaks to improve appearance under vector skin, class change
 * version 1.6 options to show the link before the title and change the brackets
 * version 1.7 changed the closing tags to adhere to strict html
 *
 * @link https://www.mediawiki.org/wiki/Extension:SectionHide Documentation
 *
 * @file
 * @ingroup Extensions
 * @package MediaWiki
 * @author Simon Oliver
 * @copyright © 2013 Simon Oliver (Hoggle42)
 * @licence http://www.gnu.org/copyleft/gpl.html GNU General Public Licence 2.0 or later
 *
 * add the following line to localsettinge.php to use
 * require_once("$IP/extensions/SectionHide/SectionHide.php");
 * // Set this option to 1 to show the text before the first section when hiding all
 * // Set to X to show the top x-1 sections (use with caution - some browsers may complain)
 * // Set to -1 to disable
 * $wgSectionHideShowtop = 0; //default
 * // Set the first option to 1 to use images and add a relative path from the wiki root to the two images
 * // Set the second option to 1 to not include the text as well as the image
 * $wgSectionHideUseImages = 0; //default
 * $wgSectionHideUseImagesOnly = 0; //default
 * $wgSectionHideHideImage = ""; //default - set to e.g. "images/minus.gif" NB direction of slash
 * $wgSectionHideShowImage = ""; //default - e.g. "images/plus.gif"
 * // Use this option to show the link before the title (set to 1) 
 * $wgSectionHideb4title = 0; //default
 * // These options allow you to change or hide the brackets
 * $wgSectionHideopenbracket = "["; //default 
 * $wgSectionHideclosebracket = "]"; //default 
 */
 
if( !defined( 'MEDIAWIKI' ) ) {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( 1 );
}
 
if( isset( $htUseHistory ) ) {
	echo( "This extension should not be used in conjunction with the Header Tabs extension.\n" );
	die( 1 );
}
 
$wgExtensionCredits['other'][] = array( 
	'name' => 'SectionHide', 
	'author' => 'Simon Oliver',
	'version' => '1.7',
	'url' => 'https://www.mediawiki.org/wiki/Extension:SectionHide',
	'descriptionmsg' => 'sectionhide-desc',
); 
 
$wgHooks['ParserAfterParse'][] = 'SectionHideHooks::onParserAfterParse';
$wgHooks['ParserSectionCreate'][] = 'SectionHideHooks::onParserSectionCreate';
 
$wgAutoloadClasses[ 'SectionHideHooks' ] = __DIR__ . '/SectionHideHooks.php';
$wgExtensionMessagesFiles[ 'SectionHide' ] = __DIR__ . '/SectionHide.i18n.php';
$wgExtensionMessagesFiles[ 'SectionHideAlias' ] = __DIR__ . '/SectionHide.alias.php';
