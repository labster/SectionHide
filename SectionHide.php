<?php
if ( function_exists( 'wfLoadExtension' ) ) {
	wfLoadExtension( 'SectionHide' );
	return;
} else {
	die( 'This version requires MediaWiki 1.25+' );
}
