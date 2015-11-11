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
			'token' => NULL,
			'key' => 'missing'
	), $atts );
	
	if (filter_var($options['url'], FILTER_VALIDATE_URL) === FALSE) {
    	$options['url']=$default_url;
    }
    
    if (!is_int($options['viewtype']) === FALSE) {
    	$options['viewtype']=NULL;
    }
    
    $action_types = array('popular_orgs','popular_interests','newest');
   
    if (! in_array($options['type'], $action_types)) {
    	$options['type'] = 'newest';
    }
    
    $culture_types = array('en-CA','fr-CA');

    if (! in_array($options['ln'], $culture_types)) {
    	$options['ln'] = NULL;
    }
    
 	$fetch_url = $options['url'] . '/jsonfeeds/volunteer/' . $options['type'] . '?key=' . $options['key'];
 	
 	if ($options['ln']) {
 		$fetch_url .= '&Ln=' . $options['ln'];
 	}
 		
 	if ($options['viewtype']) {
 		$fetch_url .= $fetch_url_con . '&UseVOLVw=' . $options['viewtype'];
 	}
    
 	$content = file_get_contents($fetch_url);
 	$json_data = json_decode($content);
 	
 	if (!is_null($json_data->{'error'})) {
 		$list_html = '<p>' . htmlspecialchars($json_data->{'error'}) . '</p>';
 	} elseif ($options['type'] == 'newest') {
 		$list_html = '<dl'
 				. ($options['list_class'] ? ' class="' . esc_attr($options['list_class']) . '"' : '')
 				. ($options['list_id'] ? ' id="' . esc_attr($options['list_id']) . '"' : '')
 				. '>';
 				foreach ($json_data->{'recordset'} AS $list_entry) {
 					$list_html .= '<dt class="position_title"><a href="' . $options['url'] . urldecode($list_entry->{'search'}) . '">' . htmlspecialchars($list_entry->{'title'}) . '</a> (' . htmlspecialchars($list_entry->{'date'}) . ')</dt>'
 						. '<dd class="organization_name">' . htmlspecialchars($list_entry->{'name'}) . '</dd>';
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