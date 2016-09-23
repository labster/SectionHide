( function ( $, mw ) {
	'use strict';

	function hidesection (e) {
		e.preventDefault();

		var $link = $( this );
		var $this_block = $link.parents('.sh-block');
		var $textlink = $link.attr('class') == "sectionhide-link"  ? $link : $this_block.find('.sectionhide-link');
		var $imglink  = $link.attr('class') == "sectionhide-image" ? $link : $this_block.children('.sectionhide-image');
		// Toggle text
		if ( $textlink.html() == $link.data('hide') ) {
			$textlink.html( $textlink.data('show') );
		} else {
			$textlink.html( $textlink.data('hide') );
		}
		// Toggle image
		if ( $imglink.attr('href') == $link.data('hide') ) {
			$imglink.attr( 'href', $imglink.data('show') );
		} else {
			$imglink.attr( 'href', $imglink.data('hide') );
		}

		// Toggle this div visibility
		$this_block.children('.sh-section').toggle();

		// Toggle divs under this hierarchy
		var $level = $this_block.data('level');
		var $next_blocks = $this_block.nextAll('.sh-block');

		$next_blocks.each( function( index, element ) {
			var $block = $( element );
			if ($block.data('level') <= $level) {
				return false;
			}
			$block.toggle();
		});
	}

	function hideall (e) {
		e.preventDefault();

		var $link = $( '.sectionhide-all' );
		// Toggle text
		var $show = 0;
		if ( $link.html() == $link.data('hide') ) {
			$link.html( $link.data('show') );
		} else {
			$link.html( $link.data('hide') );
			$show = 1;
		}

		var $sections = $('.sh-block, .sh-section').not("[data-level='2']");
		var $textlink = $(".sectionhide-link");
		var $imglink  = $(".sectionhide-image");
		if ($show) {
			$sections.show();
			$textlink.html( $textlink.data('hide') );
			$imglink.attr( 'href', $imglink.data('hide') );
		} else {
			$sections.hide();
			$textlink.html( $textlink.data('show') );
			$imglink.attr( 'href', $imglink.data('show') );
		}
	}

	mw.hook( 'wikipage.content' ).add( function () {
		$('.sectionhide-link, .sectionhide-image').click( hidesection );
		$('.sectionhide-all').click( hideall );
	} );
}( jQuery, mediaWiki ) );
