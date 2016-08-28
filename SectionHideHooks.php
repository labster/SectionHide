<?php
/**
 * Body file for extension SectionHide.
 *
 * @file
 * @ingroup Extensions
 */

# hooks class
class SectionHideHooks
        {
        public static function onParserSectionCreate( $parser, $section, &$sectionContent, $showEditLinks, OutputPage &$out )
                {
                global $wgSectionHideUseImages, $wgSectionHideUseImagesOnly, $wgSectionHideHideImage, $wgSectionHideShowImage;
                global $wgSectionHideopenbracket, $wgSectionHideclosebracket, $wgSectionHideb4title;

		$out->addModules( 'ext.sectionHide' );

                if ($showEditLinks && $section > 0)
                        {
                        $hidetext = wfMessage( 'sectionhide-hide' )->text();
                        $showtext = wfMessage( 'sectionhide-show' )->text();
                        $initialtext = $hidetext;
                        if ($wgSectionHideUseImages != 0)
                                {
                                if ($wgSectionHideUseImagesOnly == 0)
                                        {
                                        $initialtext = '<img src="'.$wgSectionHideHideImage.'">'.$hidetext;
                                        $hidetext = $wgSectionHideHideImage.':'.$hidetext;
                                        $showtext = $wgSectionHideShowImage.':'.$showtext;
                                        }
                                else
                                        {
                                        $initialtext = '<img src="'.$wgSectionHideHideImage.'">';
                                        $hidetext = $wgSectionHideHideImage.':';
                                        $showtext = $wgSectionHideShowImage.':';
                                        }      
                                }
			$divstart = '<div class="sectionblocks" id="sectionblock'.$section.'">';
                        $divend = '</div><!-- id="sectionblock'.$section.'" -->';
                        if($wgSectionHideb4title == 1)
                        	{
	                        $hidelink = '<span class="visibilitytoggle">'.$wgSectionHideopenbracket.'<a href="#" onclick="toggleSectionVisibility(this, '.$section.", '".$showtext."','".$hidetext."'".');">'.$initialtext.'</a>'.$wgSectionHideclosebracket.'</span>';
                        	$sectionContent = preg_replace( '/<h[2-6]>/', "$0$hidelink", $sectionContent);
                        	$sectionContent = preg_replace( '/<\/h[2-6]>/', "$0\n$divstart\n", $sectionContent);
                        	}
                        else 
                        	{
	                        $hidelink = '<span class="mw-editsection visibilitytoggle">'.$wgSectionHideopenbracket.'<a href="#" onclick="toggleSectionVisibility(this, '.$section.", '".$showtext."','".$hidetext."'".');">'.$initialtext.'</a>'.$wgSectionHideclosebracket.'</span>';
                        	$sectionContent = preg_replace( '/<\/h[2-6]>/', "$hidelink$0\n$divstart\n", $sectionContent);
                        	}
                        $sectionContent = $sectionContent."\n$divend\n";                               
                        }
                return true;
                }

        public static function onParserAfterParse( &$parser, &$text, &$sstate )
                {
                global $wgSectionHideShowtop;
                // need to nest sections by levels by moving around the closing tags
                $numberofmatches = preg_match_all('#<h[2-6].*<\/h[2-6]>\n<div class="sectionblocks"#', $text, $matches, PREG_OFFSET_CAPTURE);
                $closingdivmatches = preg_match_all('#<\/div><!-- id=#', $text, $divmatches, PREG_OFFSET_CAPTURE);

                if($numberofmatches == $closingdivmatches && $numberofmatches > 1)
                        {
                        if( $wgSectionHideShowtop > $numberofmatches) $wgSectionHideShowtop = $numberofmatches; // cannot exceed the number of matches
                        $headlevel = array();
                        $i = 0;
                        foreach($matches[0] as $match)
                                {
                                $headlevel[$i] = substr($match[0],2,1); // heading level always third character
                                $i++;
                                }
                        $headlevel[$i] = 1; // make sure it terminates correctly
                        for($i = 0 ; $i < $numberofmatches - 1 ; $i++)
                                {
                                // the rule is:
                                // for each heading if the next level is lower, move the closing div to after the closing div of that level and re-test
                                // length of closing divs is </div><!-- id="sectionblock2" --> = 32 + number of digits of id (which is $i+1)
                                $divlength = 33 + ($i>98 ? 2 : ($i>8 ? 1 : 0)); //NB this should cope with up to 999 heading sections. Articles with more than that deserve to be broken
                                $j = 1;
                                while(($i+$j) < $numberofmatches && $headlevel[$i+$j] > $headlevel[$i])
                                        {
                                        // insert a closing div before the next heading, or at the end if no more headings
                                        $text = insertAtLoc($text
                                                        , '</div><!-- id="sectionblock'.($i+1).'" -->'
                                                        , $divmatches[0][$i+$j][1] + $divlength + (($i+$j>98 && $i <= 8) ? 2 : ((($i+$j>98 && $i <= 98) || ($i+$j > 8 && $i <= 8)) ? 1 : 0))); // deal with situations where sublevel div is longer than parent level
                                        $text = removeAtLoc($text, $divlength, $divmatches[0][$i][1]);  // remove the closing div from its previous location
                                        // update the current positions
                                        $divmatches[0][$i][1] = $divmatches[0][$i+$j][1]; // is now where the sublevel div was
                                        $divmatches[0][$i+$j][1] = $divmatches[0][$i+$j][1]-$divlength; // moves by divlength towards the start
                                        $matches[0][$i+$j][1] = $matches[0][$i+$j][1]-$divlength; // moves by sublevel heading towards the start
                                        $j++;
                                        }
                                }
                        // new hide all link
                        if ( $wgSectionHideShowtop > 0 )
                                {
                                // insert a section zero opening div before the first section heading
                                $text = insertAtLoc($text, '<div class="sectionblocks" id="sectionblock0">', $matches[0][($wgSectionHideShowtop-1)][1]);
                                $text = '<p><span class="visibilitytoggle">[<a href="#" onclick="toggleSectionVisibility(this, 0,'."'"
                                        .wfMessage( 'sectionhide-showall' )->text()."','".wfMessage( 'sectionhide-hideall' )->text()."'".')">'.wfMessage( 'sectionhide-hideall' )->text()
                                        .'</a>]</span></p>'
                                        .$text
                                        .'</div><!-- id="sectionblock0" -->';
                                }
                            elseif ( $wgSectionHideShowtop == 0 )
                                {
                                $text = '<p><span class="visibilitytoggle">[<a href="#" onclick="toggleSectionVisibility(this, 0,'."'"
                                        .wfMessage( 'sectionhide-showall' )->text()."','".wfMessage( 'sectionhide-hideall' )->text()."'".')">'.wfMessage( 'sectionhide-hideall' )->text()
                                        .'</a>]</span></p><div class="sectionblocks" id="sectionblock0">'
                                        .$text
                                        .'</div><!-- id="sectionblock0" -->';
                                }
                        }
                return true;
                }      
        } # end of SectionHideHooks class

function insertAtLoc($subject, $toinsert, $atloc)
        {
        return substr($subject,0,$atloc).$toinsert.substr($subject,$atloc);
        }
 
function removeAtLoc($subject, $remlength, $atloc)
        {
        return substr($subject,0,$atloc).substr($subject,$atloc+$remlength);
        }
