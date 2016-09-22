<?php
/**
 * Body file for extension SectionHide.
 *
 * @file
 * @ingroup Extensions
 */

class SectionHideHooks {
	public static function onBeforePageDisplay( OutputPage &$out, Skin &$skin ) {
		$out->addModules( 'ext.sectionHide' );
		return true;
	}

    public static function onParserSectionCreate( $parser, $section, &$sectionContent, $showEditLinks ) {
        global $wgSectionHideImages, $wgSectionHideb4title;
        
        if ($section <= 0 || !$showEditLinks) {
                return true;
        }

        $headerLevel = (int) substr($sectionContent, 2, 1 );

        if ($wgSectionHideImages) {
            $img = Xml::Element( 'img', [
                'class'     => "sectionhide-image",
                'src'       => $wgSectionHideImages['hide'],
                'data-hide' => $wgSectionHideImages['hide'],
                'data-show' => $wgSectionHideImages['show']
            ]);
            // Right after the very first <h*> tag
            if ($wgSectionHideb4title) {
                preg_replace(">\K", $img, $sectionContent);
            }
        }
	
	// Insert the inner div around the section's contents so we can hide that
	// And an outer div around the entire section for hierarchical hiding
        $sectionContent = preg_replace( '/<\/h[2-6]>\K/', '<div class="sh-section">', $sectionContent);
        $sectionContent = Html::Rawelement("div",
                [ 'class' => "sectionblock blocklevel".$headerLevel ],
                $sectionContent . "</div>"
        );

        return true;
    }


    public static function onSkinEditSectionLinks( $skin, $title, $section, $tooltip, &$links, $lang ) {
        global $wgSectionHideHideText;

        if ($wgSectionHideHideText) return true;

        $hidetext = wfMessage( 'sectionhide-hide' )->text();
        $showtext = wfMessage( 'sectionhide-show' )->text();

	if ($section !== 0) {
            $links[] = [
                'targetTitle' => $title,
                'text' => $hidetext,
                'attribs' => [
                    "class" => "sectionhidelink",
                    "data-sectionhide-show" => $showtext,
                    "data-sectionhide-hide" => $hidetext,
                    "data-section" => $section,
                    "title" => "Hide this section",
                    ],
                'query' => array(),
                'options' => array(),
            ];
        }

	if ($section == 1) {
	    $showall = wfMessage( 'sectionhide-showall' )->text();
	    $hideall = wfMessage( 'sectionhide-hideall' )->text();
            $links[] = [
                'targetTitle' => $title,
                'text' => $hideall,
                'attribs' => [
                    "class" => "sectionhidelink",
                    "data-sectionhide-show" => $showall,
                    "data-sectionhide-hide" => $hideall,
                    "title" => "Hide all sections",
                    ],
                'query' => array(),
                'options' => array(),
            ];
	}

    }

    public static function onSkinTemplateOutputPageBeforeExec ( &$skin, &$template ) {
	global $wgSectionHideShowtop;
	if (1) {
	    $showall = wfMessage( 'sectionhide-showall' )->text();
	    $hideall = wfMessage( 'sectionhide-hideall' )->text();
	    $linkelem = Html::element('a', [
                    "class" => "sectionhidelink",
                    "data-sectionhide-show" => $showall,
                    "data-sectionhide-hide" => $hideall,
                    "title" => "Hide all sections",
		    "href" => "#`"
                ],
		$hideall
	    );
	    $hideelem = Html::Rawelement('span',
		 [ 'class' => 'sectionhide-head' ],
		'[' . $linkelem . ']' );

	    $template->data['title'] .= $hideelem;
	}
	return true;
    }


} # end of SectionHideHooks class


