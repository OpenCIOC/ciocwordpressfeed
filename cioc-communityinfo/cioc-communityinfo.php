<?php
/*
 * Plugin Name: CIOC Community Information Feeds
 * Description: This plugin provides integration shortcodes for feeds from the CIOC Software Community Information module
 * Version: 1.0.1
 * Author: Katherine Lambacher
 * Author URI: http://www.kclsoftware.com
 * License: Apache 2.0
 */
function cioc_cominfo_search_feed_list($atts) {
	$default_url = 'https://test.cioc.ca/';
	$default_type = 'newest';
	
	$options = shortcode_atts ( array (
			'url' => $default_url,
			'type' => $default_type,
			'viewtype' => NULL,
			'ln' => NULL,
			'list_class' => NULL,
			'list_id' => NULL,
			'style_me' => NULL,
			'has_fa' => NULL,
			'description' => NULL,
			'address' => NULL,
			'email' => NULL,
			'web' => NULL,
			'officephone' => NULL,
			'hours' => NULL,
			'code' => NULL,
			'location' => NULL,
			'servicearea' => NULL,
			'debug' => NULL,
			'key' => 'missing' 
	), $atts );
	
	if (filter_var ( $options ['url'], FILTER_VALIDATE_URL ) === FALSE) {
		$options ['url'] = $default_url;
	}
	
	if (! is_int ( $options ['viewtype'] ) === FALSE) {
		$options ['viewtype'] = NULL;
	}
	
	$action_types = array (
			'newest',
			'pub',
			'taxonomy'
	);
	
	if (! in_array ( $options ['type'], $action_types )) {
		$options ['type'] = $default_type;
	}
	
	$culture_types = array (
			'en-CA',
			'fr-CA' 
	);
	
	$fetch_url = $options ['url'] . '/jsonfeeds/cominfo/' . $options ['type'] . '?key=' . $options ['key'];
	
	if ($options ['ln']) {
		if (! in_array ( $options ['ln'], $culture_types )) {
			$options ['ln'] = NULL;
		} else {
			$fetch_url .= '&Ln=' . $options ['ln'];
		}
	}
	
	if ($options ['viewtype']) {
		$fetch_url .= '&UseCICVw=' . $options ['viewtype'];
	}
	
	if ($options ['description'] == on) {
		$fetch_url .= '&description=on';
	}
	if ($options ['address'] == 'on') {
		$fetch_url .= '&address=on';
	}
	if ($options ['email'] == 'on') {
		$fetch_url .= '&email=on';
	}
	if ($options ['web'] == 'on') {
		$fetch_url .= '&web=on';
	}
	if ($options ['officephone'] == 'on') {
		$fetch_url .= '&officephone=on';
	}
	if ($options ['hours'] == 'on') {
		$fetch_url .= '&hours=on';
	}
	
	if (in_array ( $options ['type'], array (
			'pub',
			'taxonomy'
	) )) {
		if ($options ['code']) {
			$fetch_url .= '&code=' . urlencode($options ['code']);
		}
		if ($options ['location']) {
			$fetch_url .= '&location=' . urlencode($options ['location']);
		}
		if ($options ['servicearea']) {
			$fetch_url .= '&servicearea=' . urlencode($options ['servicearea']);
		}
	}
	
	$content = file_get_contents ( $fetch_url );
	$json_data = json_decode ( $content );
	
	if (! is_null ( $json_data->{'error'} )) {
		$list_html = '<p>' . htmlspecialchars ( $json_data->{'error'} ) . '</p>';
	} else {
		if ($options ['style_me'] == 'on') {
			$list_html = '<style type="text/css">'
				. '.fa-cioc {width:1em; margin-right:0.25em; text-align:center;}'
				. '.dt-cioc {margin-top:1.5em; margin-bottom:0.5em; font-size: 110%;}'
				. '.dd-cioc {margin-left:1.5em; margin-bottom:0.5em;}'
				. '.org-description {margin-bottom:0.75em;}'
				. '.atgoeshere::before {content:\' [at] \';}'
				. '</style>';
		} else {
			$list_html = '';
		}
		
		if ($options ['debug'] == 'on') {
			$list_html .= '<a href="' . $fetch_url . '">' . $fetch_url . '</a>';
		}
		
		$list_html .= '<dl' . ($options ['list_class'] ? ' class="' . esc_attr ( $options ['list_class'] ) . '"' : '') . ($options ['list_id'] ? ' id="' . esc_attr ( $options ['list_id'] ) . '"' : '') . '>';
		
		foreach ( $json_data->{'recordset'} as $list_entry ) {
			$list_html .= '<dt class="org-name dt-cioc">'
				. '<a href="' . $options ['url'] . urldecode ( $list_entry->{'search'} ) . '">' . htmlspecialchars ( $list_entry->{'name'} ) . '</a>'
				. ($options ['type'] == 'newest' ? ' (' . htmlspecialchars ( $list_entry->{'date'} ) . ')' : '') . '</dt>';
			if ($options ['description'] == 'on' and $list_entry->{'description'}) {
				$list_html .= '<dd class="org-description dd-cioc">'
					. htmlspecialchars ( $list_entry->{'description'} )
					. '</dd>';
			}
			if ($options ['address'] == 'on' and ($list_entry->{'address'} || $list_entry->{'location'})) {
				$list_html .= '<dd class="org-address dd-cioc">' 
					. ($options ['has_fa'] == 'on' ? '<i class="fa fa-map-marker fa-cioc" aria-hidden="true"></i> ' : '')
					. htmlspecialchars ( $list_entry->{'address'} ? $list_entry->{'address'} : $list_entry->{'location'})
					. '</dd>';
			} elseif ($list_entry->{'location'}) {
				$list_html .= '<dd class="org-address dd-cioc">'
						. ($options ['has_fa'] == 'on' ? '<i class="fa fa-map-marker fa-cioc" aria-hidden="true"></i> ' : '')
						. htmlspecialchars ( $list_entry->{'location'} )
						. '</dd>';
			}
			if ($options ['email'] == 'on' and $list_entry->{'email'}) {
				$list_html .= '<dd class="org-email dd-cioc">'
					. ($options ['has_fa'] == 'on' ? '<i class="fa fa-envelope fa-cioc" aria-hidden="true"></i> ' : '')
					. str_replace ( '@', '<span class="atgoeshere"></span>', htmlspecialchars ( $list_entry->{'email'} ) )
					. '</dd>';
			}
			if ($options ['web'] == 'on' and $list_entry->{'web'}) {
				$list_html .= '<dd class="org-web dd-cioc">'
					. ($options ['has_fa'] == 'on' ? '<i class="fa fa-link fa-cioc" aria-hidden="true"></i> ' : '')
					. '<a href="http://' . $list_entry->{'web'} . '">' . $list_entry->{'web'} . '</a>'
					. '</dd>';
			}
			if ($options ['officephone'] == 'on' and $list_entry->{'officephone'}) {
				$list_html .= '<dd class="org-phone dd-cioc">'
					. ($options ['has_fa'] == 'on' ? '<i class="fa fa-phone fa-cioc" aria-hidden="true"></i> ' : '')
					. $list_entry->{'officephone'}
					. '</dd>';
			}
			if ($options ['hours'] == 'on' and $list_entry->{'hours'}) {
				$list_html .= '<dd class="org-hours dd-cioc">'
					. ($options ['has_fa'] == 'on' ? '<i class="fa fa-calendar-o fa-cioc" aria-hidden="true"></i> ' : '')
					. $list_entry->{'hours'}
					. '</dd>';
			}
		}
		$list_html .= '</dl>';
	}
	
	return $list_html;
}

add_shortcode ( 'cioccominfo', 'cioc_cominfo_search_feed_list' );
?>