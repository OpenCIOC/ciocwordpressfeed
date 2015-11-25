<?php
/*
* Plugin Name: CIOC Volunteer Feeds
* Description: This plugin provides integration shortcodes for feeds from the CIOC Software volunteer module
* Version: 1.0.0
* Author: Katherine Lambacher
* Author URI: http://www.kclsoftware.com
* License: Apache 2.0
*/

function cioc_vol_search_feed_list($atts) {
	$default_url = 'https://test.cioc.ca/';

	$options = shortcode_atts( array(
			'url' => $default_url,
			'type' => 'popular_orgs',
			'viewtype' => NULL,
			'ln' => NULL,
			'list_class' => NULL,
			'list_id' => NULL,
			'style_me' => NULL,
			'has_fa' => NULL,
			'org' => 'on',
			'duties' => NULL,
			'location' => NULL,
			'num' => NULL,
			'code' => NULL,
			'key' => 'missing'
	), $atts );
	
	if (filter_var($options['url'], FILTER_VALIDATE_URL) === FALSE) {
    	$options['url']=$default_url;
    }
    
    if (!is_int($options['viewtype']) === FALSE) {
    	$options['viewtype']=NULL;
    }
    
    $action_types_list = array('popular_orgs','popular_interests');
    $action_types_records = array('newest','org','interest');
    $action_types = array_merge($action_types_list, $action_types_records);   
   
    if (! in_array($options['type'], $action_types)) {
    	$options['type'] = 'newest';
    }
    
    $culture_types = array('en-CA','fr-CA');
    
 	$fetch_url = $options['url'] . '/jsonfeeds/volunteer/' . $options['type'] . '?key=' . $options['key'];
 	
 	if ($options['ln']) {
 		if (! in_array($options['ln'], $culture_types)) {
 			$options['ln'] = NULL;
 		} else {
 			$fetch_url .= '&Ln=' . $options['ln'];
 		}
 	}
 		
 	if ($options['viewtype']) {
 		$fetch_url .= '&UseVOLVw=' . $options['viewtype'];
 	}
 	
 	if (in_array($options['type'], $action_types_records)) {
 		if ($options['duties'] == 'on') {
 			$fetch_url .= '&duties=on';
 		}
 		if ($options['location'] == 'on') {
 			$fetch_url .= '&loc=on';
 		}
 		if ($options['num']) {
 			$fetch_url .= '&num=' . $options['num'];
 		}
 		if ($options['code']) {
 			$fetch_url .= '&code=' . $options['code'];
 		}
 	}
     
 	$content = file_get_contents($fetch_url);
 	$json_data = json_decode($content);
 	
 	if (!is_null($json_data->{'error'})) {
 		$list_html = '<p>' . htmlspecialchars($json_data->{'error'}) . '</p>';
 	} elseif (in_array($options['type'], $action_types_records)) {
 		$list_html = '<dl'
 				. ($options['list_class'] ? ' class="' . esc_attr($options['list_class']) . '"' : '')
 				. ($options['list_id'] ? ' id="' . esc_attr($options['list_id']) . '"' : '')
 				. '>';
 				foreach ($json_data->{'recordset'} AS $list_entry) {
 					$list_html .= '<dt' . ($options['style_me'] == 'on' ? ' style="margin-top:1em; margin-bottom:0.5em; font-size: 110%;"' : '')
 						. ' class="position_title"><a href="' . $options['url'] . urldecode($list_entry->{'search'}) . '">' 
 						. htmlspecialchars($list_entry->{'title'}) . '</a> (' . htmlspecialchars($list_entry->{'date'}) . ')</dt>';
 					if ($options['location'] == 'on' and $list_entry->{'location'}) {
 						$list_html .= '<dd' . ($options['style_me'] == 'on' ? ' style="margin-left:1.5em; margin-bottom:0.5em;"' : '') . ' class="duties">'
 							. ($options['has_fa'] == 'on' ? '<i class="fa fa-map-marker" aria-hidden="true"></i> ' : '')
 							. htmlspecialchars($list_entry->{'location'}) . '</dd>';
 					}
 					if ($options['org'] == 'on') {
 						$list_html .= '<dd' . ($options['style_me'] == 'on' ? ' style="margin-left:1.5em; margin-bottom:0.5em; font-style:italic;"' : '') . ' class="organization_name">'
	 						. htmlspecialchars($list_entry->{'name'}) . '</dd>';
 					}
 					if ($options['duties'] == 'on' and $list_entry->{'duties'}) {
 						$list_html .= '<dd' . ($options['style_me'] == 'on' ? ' style="margin-left:1.5em; margin-bottom:0.5em;"' : '') . ' class="duties">'
 							. $list_entry->{'duties'} . '</dd>';
 					}
 				}
 				$list_html .= '</dl>';
 	} else {
 		$list_html = '<ul' 
 			. ($options['list_class'] ? ' class="' . esc_attr($options['list_class']) . '"' : '')
 			. ($options['list_id'] ? ' id="' . esc_attr($options['list_id']) . '"' : '')
 			. '>';
 		foreach ($json_data->{'recordset'} AS $list_entry) {
	    	$list_html .= '<li><a href="' . $options['url'] . urldecode($list_entry->{'search'}) . '">' . htmlspecialchars($list_entry->{'name'}) . '</a> <span class="badge">' . htmlspecialchars($list_entry->{'count'}) . '<span></li>';
 		}
	    $list_html .= '</ul>';
 	} 
   
    return $list_html;
}

add_shortcode('ciocvolunteer', 'cioc_vol_search_feed_list');
?>