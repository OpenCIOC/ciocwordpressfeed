<?php

class CIOC_RSD_Public {

	private $plugin_name;

	private $version;
	
	private $parent;
	
	protected $fields_with_email;
	
	protected $fields_with_web;
	
	protected $stype_values;
	
	protected $cmtype_values;
	
	protected $icon_mapping;
	
	const NO_GROUP = 'NO-GROUP';
	
	public function __construct( CIOC_RSD $parent ) {
		if ($parent) {
			$this->parent = $parent;
			$this->plugin_name = $parent->plugin_name;
			$this->version = $parent->version;
		}
		
		$this->fields_with_email = array (
				'CONTACT_1',
				'CONTACT_2',
				'E_MAIL',
				'EXEC_1',
				'EXEC_2',
				'SOURCE',
				'VOLCONTACT'
		);
		
		$this->fields_with_web = array (
				'WWW_ADDRESS',
				'WWW_ADDRESS_NW',
				'SUBMIT_CHANGES_TO',
				'MORE_INFO_LINK'
		);
		
		$this->stype_values = array ( 'A', 'O', 'T', 'S' );
		
		$this->cmtype_values = array ( 'L', 'S' );
		
		$this->icon_mapping = array (
				'ACCESSIBILITY' => 'fa fa-wheelchair',
				'APPLICATION' => 'fa fa-pencil-square-o',
				'AREAS_SERVED' => 'fa icon-globe-lines',
				'NO_UPDATE_EMAIL' => 'fa icon-no-email',
				'BOUNDARIES' => 'fa icon-border',
				'BUS_ROUTES' => 'fa fa-bus',
				'ORG_LOCATION_SERVICE' => 'fa fa-sitemap',
				'AFTER_HRS_PHONE' => 'fa icon-phone-time',
				'COMMENTS' => 'fa fa-comment',
				'CONTACT_1' => 'fa fa-user',
				'CONTACT_2' => 'fa icon-user-alt',
				'CRISIS_PHONE' => 'fa icon-phone-crisis',
				'DATES' => 'fa fa-calendar',
				'DESCRIPTION' => 'fa fa-info-circle',
				'DISTRIBUTION' => 'fa fa-share-alt',
				'E_MAIL' => 'fa fa-envelope-o',
				'ELECTIONS' => 'fa fa-calendar-check-o',
				'ELIGIBILITY' => 'fa fa-check-square-o',
				'ESTABLISHED' => 'fa fa-institution',
				'EXEC_1' => 'fa icon-user-exec',
				'EXEC_2' => 'fa icon-user-exec-alt',
				'FAX' => 'fa fa-fax',
				'FEES' => 'fa fa-money',
				'FUNDING' => 'fa icon-funding-alt',
				'HOURS' => 'fa fa-calendar-o',
				'INTERNAL_MEMO' => 'fa fa-comment-o',
				'INTERSECTION' => 'fa fa-map-signs',
				'LANGUAGES' => 'fa icon-language',
				'LOCATED_IN_CM' => 'fa fa-map-marker',
				'MAIL_ADDRESS' => 'fa fa-envelope',
				'MEETINGS' => 'fa fa-calendar-plus-o',
				'NAICS' => 'fa fa-industry',
				'OFFICE_PHONE' => 'fa fa-phone',
				'PRINT_MATERIAL' => 'fa fa-file-o',
				'PUBLIC_COMMENTS' => 'fa fa-warning',
				'PUBLICATION' => 'fa fa-list-alt',
				'PUBLICATION_FRIENDLY' => 'fa fa-list-alt',
				'RESOURCES' => 'fa fa-folder-open-o',
				'SITE_ADDRESS' => 'fa fa-map',
				'SITE_ADDRESS_MAPPED' => 'fa fa-map-o',
				'SITE_LOCATION' => 'fa fa-building-o',
				'SORT_AS' => 'fa fa-sort-alpha-asc',
				'SOURCE' => 'fa fa-user-plus',
				'SOURCE_DB' => 'fa fa-copyright',
				'TAXONOMY' => 'fa fa-tags',
				'TAXONOMY_STAFF' => 'fa fa-tags',
				'TDD_PHONE' => 'fa fa-tty',
				'TOLL_FREE_PHONE' => 'fa fa-phone-square',
				'USAGE_COUNT' => 'fa fa-bar-chart',
				'USAGE_COUNT_P' => 'fa fa-line-chart',
				'USAGE_COUNT_S' => 'fa fa-line-chart',
				'VOLCONTACT' => 'fa fa-users',
				'WWW_ADDRESS' => 'fa fa-link',
				'WWW_ADDRESS_NW' => 'fa fa-link'
		);
	}

	public function enqueue_styles() {
		$options = get_option ( 'ciocrsd_settings' );
		
		if ($options) {
			$no_fa = isset($options['ciocrsd_has_fa']) ? $options['ciocrsd_has_fa'] : 0;
			$no_bs = isset($options['ciocrsd_has_bootstrap']) ? $options['ciocrsd_has_bootstrap'] : 0;
		}
		
		wp_enqueue_style( $this->plugin_name, $this->parent->plugin_main_dir . 'css/cioc-rsd.css', array(), $this->version);
		wp_enqueue_style( 'cioc-icon-font', 'https://d3byedob0d0n2o.cloudfront.net/fontello/c39f5861b48496b/fontello.css');
		if (!$no_fa) {
			wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css');
		}
		if (!$no_bs) {
			wp_enqueue_style( 'bs-glyphicons', 'https://d3byedob0d0n2o.cloudfront.net/bootstrap-3.3.5/glyphicons.css');
			wp_enqueue_style( 'bs-modal',  $this->parent->plugin_main_dir . 'css/bootstrap-modal.min.css', array(), $this->version);
		}
	}

	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cioc-rsd-public.js', array( 'jquery' ), $this->version, TRUE );
		wp_enqueue_script( 'bs-modal-script', plugin_dir_url( __FILE__ ) . 'js/bootstrap-modal.min.js', array( 'jquery' ), $this->version, TRUE );
	}
	
	public function register_shortcodes() {
		add_shortcode ( 'ciocrsd-countall', array( $this, 'count_all_in_view' ) );
		add_shortcode ( 'ciocrsd-quicklist-browse', array ( $this, 'quicklist_browse' ) );
		add_shortcode ( 'ciocrsd-displayrecord', array ( $this, 'display_record' ) );
		add_shortcode ( 'ciocrsd-displayresults', array ( $this, 'display_results' ) );
		add_shortcode ( 'ciocrsd-searchform', array ( $this, 'search_form' ) );
		add_shortcode ( 'ciocrsd-keyword', array ( $this, 'keyword_textbox_wrapper' ) );
		add_shortcode ( 'ciocrsd-community', array ( $this, 'community_dropdown_wrapper' ) );
		add_shortcode ( 'ciocrsd-quicklist', array ( $this, 'quicklist_dropdown_wrapper' ) );
		add_shortcode ( 'ciocrsd-quicklistbuttons', array ( $this, 'quicklist_buttons_wrapper' ) );
		add_shortcode ( 'ciocrsd-agegroup', array ( $this, 'agegroup_dropdown_wrapper' ) );
	}

	public function register_queryvars($vars) {
		$vars[] = 'num';
		$vars[] .= 'STerms';
		$vars[] .= 'SType';
		$vars[] .= 'CMType';
		$vars[] .= 'CMID';
		$vars[] .= 'PBID';
		$vars[] .= 'PBCode';
		$vars[] .= 'GHID';
		$vars[] .= 'AgeGroup';
		
		/* not responding to these yet
		$vars[] .= 'OL1';
		$vars[] .= 'OL2';
		$vars[] .= 'OL3';
		$vars[] .= 'OL4';
		$vars[] .= 'LL1';
		$vars[] .= 'SL1';
		*/
		
		$vars[] .= 'dosearch';
		return $vars;
	}
	
	private function is_field_with_email($field_name) {
		if (in_array ($field_name, $this->fields_with_email)
				|| substr($field_name, 0, 11) == 'EXTRA_EMAIL') {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	private function is_field_with_web($field_name) {
		if (in_array ($field_name, $this->fields_with_web)
				|| substr($field_name, 0, 11) == 'EXTRA_WWW') {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	private function clean_stype($val) {
		if (in_array ($val, $this->stype_values)) {
			return $val;
		} else {
			return NULL;
		}
	}
	
	private function clean_cmtype($val) {
		if (in_array ($val, $this->cmtype_values)) {
			return $val;
		} else {
			return NULL;
		}
	}
	
	private function clean_pubcode($val) {
		if (preg_match('/^[A-Z0-9\-]{1,20}$/', $val)) {
			return $val;
		} else {
			return NULL;
		}
	}
	
	private function clean_id($val) {
		if (is_numeric($val) && (intval($val) > 0) && (intval($val) < 2147483647)) {
			return $val;
		} else {
			return NULL;
		}
	}

	private function encode_field($field_name, $field_value, $allow_html) {
		if ($this->is_field_with_email($field_name)) {
			if (!$allow_html) {
				$field_value =  htmlspecialchars ( $field_value );
			}
			$field_value = str_replace ( '@', '<span class="atgoeshere"></span>', $field_value );
		} elseif ($this->is_field_with_web($field_name)) {
			if ($field_value) {
				if (substr( $field_value, 0, 4 ) === "http") {
					$web_link = $field_value;
				} else {
					$web_link = 'http://' . $field_value;
				}
				if (!filter_var ( $web_link, FILTER_VALIDATE_URL ) === FALSE) {
					$field_value = '<a href="' . $web_link . '" target="_blank">' . $field_value . '</a>';
				} else {
					$field_value = htmlspecialchars( $field_value );
				}
			}
		} elseif (!$allow_html) {
			$field_value =  htmlspecialchars ( $field_value );
		}
		return $field_value;
	}
	
	private function render_field($field_name, $field_label, $field_value, $allow_html, $add_icons) {
		/* All inputs to this function should be set */
		$field_value = $this->encode_field($field_name, $field_value, $allow_html);
		$return_html = '<div class="ciocrsd-field-group-row">'
				. '<div class="ciocrsd-field-label ciocrsd-label-' . $field_name . '">';
		if ($add_icons) {
			if (strpos($field_name,'HEADINGS') !== FALSE) {
				$return_html .= '<i class="fa fa-tag" aria-hidden="true"></i> ';
			} else {
				$return_html .= (isset($this->icon_mapping[$field_name]) ?
						'<i class="' . $this->icon_mapping[$field_name] . '" aria-hidden="true"></i> '
						: '<i class="add-icon-' . $field_name . '" aria-hidden="true"></i>');
			}
		}
		$return_html .= $field_label
				. '</div>'
				. '<div class="ciocrsd-field-content ciocrsd-' . $field_name . '">' . $field_value . '</div>'
				. '</div>';
		return $return_html;
	}
	
	private function process_fetch_url_params($options, $add_params = []) {
		$return_params = '';
		
		if (!is_array($add_params)) {
			$add_params = [];
		}
		
		if (! is_int ( $options ['viewtype'] ) === FALSE) {
			$options ['viewtype'] = NULL;
		} else {
			$add_params['UseCICVw'] = $options['viewtype'];
		}
		
		$culture_types = array (
				'en-CA',
				'fr-CA'
		);
		
		if ($options ['ln']) {
			if (! in_array ( $options ['ln'], $culture_types )) {
				$options ['ln'] = NULL;
			} else {
				$add_params['Ln'] = $options['ln'];
			}
		}
		
		$return_params = http_build_query($add_params);
		
		return $return_params;
	}
	
	private function check_domain(&$options, $default_domain) {
		if (!$default_domain) {
			$default_domain = 'cic';
		}
	
		$domain_types = array (
				'cic',
				'vol'
		);
	
		if ($options ['domain']) {
			if (! in_array ( $options ['domain'], $domain_types )) {
				$options ['domain'] = $default_domain;
			}
		} else {
			$options['domain'] = $default_domain;
		}
	}
	
	public function set_search_terms($sc_options) {
		$search_params = array();
				
		$search_terms = isset($sc_options ['sterms']) ? $sc_options ['sterms'] : NULL;
		if (!$search_terms) {
			$search_terms = get_query_var('STerms');
		}
		if ($search_terms) {
			$search_params['STerms'] =  $search_terms;
		}
		
		$search_type = isset($sc_options ['stype']) ? $sc_options ['stype'] : NULL;
		if (!$search_type) {
			$search_type = get_query_var('SType');
		}
		$search_type = $this->clean_stype($search_type);
		if ($search_type) {
			$search_params['SType'] =  $search_type;
		}
			
		$cm_type = NULL;
		if (isset($sc_options['location']) ? $sc_options['location'] : NULL) {
			$cm_type = 'L';
		} elseif (isset($sc_options['servicearea']) ? $sc_options['servicearea'] : NULL) {
			$cm_type = 'S';
		}
		if (!$cm_type) {
			$cm_type = get_query_var('CMType');
		}
		$cm_type = $this->clean_cmtype($cm_type);
		if ($cm_type) {
			$search_params['CMType'] =  $cm_type;
		}
		
		$cm_id = get_query_var('CMID');
		$cm_id = $this->clean_id($cm_id);
		if ($cm_id) {
			$search_params['CMID'] =  $cm_id;
		}
		
		$agegroup_id = get_query_var('AgeGroup');
		$agegroup_id = $this->clean_id($agegroup_id);
		if ($agegroup_id) {
			$search_params['AgeGroup'] =  $agegroup_id;
		}
		
		$pub_code = isset($sc_options['pubcode']) ? $sc_options ['pubcode'] : NULL;
		if (!$pub_code) {
			$pub_code = get_query_var('PBCode');
		}
		$pub_code = $this->clean_pubcode($pub_code);
		if ($pub_code) {
			$search_params['PBCode'] = $pub_code;
		}
		
		$pub_id = isset($sc_options['pbid']) ? $sc_options ['pbid'] : NULL;
		if (!$pub_id) {
			$pub_id = get_query_var('PBID');
		}
		$pub_id = $this->clean_id($pub_id);
		if ($pub_id) {
			$search_params['PBID'] = $pub_id;
		}
		
		$heading_id = isset($sc_options['ghid']) ? $sc_options ['ghid'] : NULL;
		if (!$heading_id) {
			$heading_id = get_query_var('GHID');
		}
		$heading_id = $this->clean_id($heading_id);
		if ($heading_id) {
			$search_params['GHID'] = $heading_id;
		}
		
		return $search_params;
	}
	
	public function count_all_in_view($atts) {
		$fetch_url = $this->parent->full_fetch_url();
		$fetch_headers = $this->parent->fetch_auth_headers();
		$return_html = '';
		
		if ($fetch_url && !empty($fetch_headers)) {
			$sc_options = shortcode_atts ( array (
					'viewtype' => NULL,
					'ln' => NULL,
					'domain' => NULL
			), $atts );
			
			$this->check_domain($sc_options, 'cic');
			
			$fetch_url_params = $this->process_fetch_url_params($sc_options);
			
			$response = wp_remote_get( $fetch_url . '/rpc/countall/' . $sc_options['domain'] . '?' . $fetch_url_params, array('headers' => $fetch_headers, 'timeout' => 30));
			if (wp_remote_retrieve_response_code($response) != 200) {
				?>
					<div class="ciocrsd-alert">WARNING: Authorization failed or content unavailable public (<?= wp_remote_retrieve_response_code( $response )?> <?= wp_remote_retrieve_response_message($response) ?>)</div>
				<?php
				if (is_wp_error($response)) {
					echo "<!-- " . $response->get_error_message() . " -->";
				}
			} else {
				$content = wp_remote_retrieve_body($response);		
				$json_data = json_decode ( $content );
			
				if (! json_last_error() == JSON_ERROR_NONE ) {
					$return_html = '<span class="ciocrsd-alert">Error: ' . json_last_error_msg() . '</span>';
				} elseif ($content === FALSE) {
					$return_html = '<span class="ciocrsd-alert">Error: Content not available</span>';
				} else {
					$return_html = $json_data->{'RecordCount'};
				}
			}
		} else {
			$this->parent->do_fetch_url_error();
		}
		
		return $return_html;
	}
	
	public function display_record($atts) {
		$options = get_option ( 'ciocrsd_settings' );
	
		if ($options) {
			$show_field_groups = isset($options['ciocrsd_field_groups']) ? $options['ciocrsd_field_groups'] : 0;
			if (isset($options['ciocrsd_google_maps_key'])) {
				$google_maps_key = ' data-maps-key="' . $options['ciocrsd_google_maps_key'] . '"';
			} else {
				$google_maps_key = "";
			}
			$add_icons = isset($options['ciocrsd_add_icons']) ? $options['ciocrsd_add_icons'] : 0;
		}
	
		$fetch_url = $this->parent->full_fetch_url();
		$fetch_headers = $this->parent->fetch_auth_headers();
		$return_html = '';
	
		if ($fetch_url && !empty($fetch_headers)) {
			$sc_options = shortcode_atts ( array (
					'viewtype' => NULL,
					'ln' => NULL,
					'domain' => NULL,
					'num' => NULL
			), $atts );
	
			$this->check_domain($sc_options, 'cic');
			$domain = $sc_options['domain'];
	
			$fetch_url_params = $this->process_fetch_url_params($sc_options);
	
			if ($domain === 'cic') {
				$num = $sc_options ['num'];
					
				if (!$num) {
					$num = get_query_var('num');
				}
					
				if (!preg_match('/^[A-Za-z]{3}[0-9]{4,5}$/', $num)) {
					$return_html = '<div class="ciocrsd-alert">Error: Invalid Record Number</div>' . $num;
				} else {
					$response = wp_remote_get($fetch_url . '/rpc/record/' . $num . '?texttohtml=1&' . $fetch_url_params, array('headers' => $fetch_headers, 'timeout' => 30)); 

					if (wp_remote_retrieve_response_code($response) != 200) {
						?>
							<div class="ciocrsd-alert">WARNING: Authorization failed or content unavailable (<?= wp_remote_retrieve_response_code( $response )?> <?= wp_remote_retrieve_response_message($response) ?>)</div>
						<?php
				if (is_wp_error($response)) {
					echo "<!-- " . $response->get_error_message() . " -->";
				}
					} else {
						$content = wp_remote_retrieve_body($response);		
						$json_data = json_decode ( $content );
		
						$return_html = '';
						$org_name_html = '';
						
						if (! json_last_error() == JSON_ERROR_NONE) {
							$return_html = '<div class="ciocrsd-alert">Error: ' . json_last_error_msg() . '</div>';
						} elseif ($content === FALSE) {
							$return_html = '<div class="ciocrsd-alert">Error: Record not available</div>';
						} else {
							$org_name = $json_data->{'ORG_LEVEL_1'};
							$org_name_full = $json_data->{'orgname'};
							$org_name_html = '<h2 class="ciocrsd-org-name">' . $org_name . '</h2>';
							if ($org_name != $org_name_full) {
								$org_name_remainder = str_replace($org_name . ', ', '', $org_name_full);
								$org_name_html .= '<h3 class="ciocrsd-org-name">' . $org_name_remainder . '</h3>';
							}
							$logo = NULL;

							foreach ( $json_data->{'field_groups'} as $field_group ) {
								$field_group_section_data = '';
								foreach ( $field_group->{'fields'} as $field ) {
									$field_value = $field->{'value'};
									$field_name = $field->{'name'};
									$field_label = $field->{'display_name'};
																
									if ($field_name == 'LOGO_ADDRESS' && $field_value) {
										$logo = '<div class="ciocrsd-logo-container">' . $field_value . '</div>';
									} else {
										$allow_html =  isset($field->{'allow_html'}) ? $field->{'allow_html'} : FALSE;
										if ($field_value) {
											$field_group_section_data .= $this->render_field($field_name, $field_label, $field_value, $allow_html, $add_icons);
										}	
									}
								}
								if ($field_group_section_data) {
									if ($show_field_groups) {
										$return_html .= '<h3 class="ciocrsd-field-group">' . $field_group->{'name'} . '</h3>';
									}
									$return_html .= $field_group_section_data;
								}
							}
							
							$return_html .= '<h3 class="ciocrsd-field-group">About this Information</h3>';
			
							$last_modified = $json_data->{'modified_date'};
							$last_updated = $json_data->{'update_date'};
							if ($last_modified || $last_updated) {
								$return_html .= '<div class="ciocrsd-field-group-row">'
										. '<div class="ciocrsd-last-mod">';
								if ($last_modified) {
									$return_html .= 'Last Modified: ' . $last_modified;
								}
								if ($last_modified && $last_updated) {
									$return_html .= ' | ';
								}
								if ($last_updated) {
									$return_html .= 'Last Full Update: ' . $last_updated;
								}
								$return_html .= '</div>'
										. '</div>';
							}
			
							$suggest_update = $json_data->{'feedback_link'};
							if (!filter_var ( $suggest_update, FILTER_VALIDATE_URL ) === FALSE) {
								$return_html .= '<div class="ciocrsd-field-group-row">' 
										. '<div class="ciocrsd-suggest-update"><a href="' . $suggest_update . '" target="_blank">Suggest a change to this information</a></div>'
										. '</div>';
							}

							if ($logo) {
								$return_html = '<div class="ciocrsd-record-header">'
									. $logo . '<div class="ciocrsd-org-name-container">' . $org_name_html . '</div>'
									. '</div>' . $return_html;
							} else {
								$return_html = '<div class="ciocrsd-record-header">' . $org_name_html . '</div>' . $return_html;
							}
							
							$return_html = '<div class="ciocrsd-record-detail"' . $google_maps_key . '>' .  $return_html . '</div>';
							
						}
					}
				}
			}
		} else {
			ciocrsd_do_fetch_url_error();
		}
	
		return $return_html;
	}
	
	public function quicklist_browse($atts) {
		$options = get_option ( 'ciocrsd_settings' );
		
		$sc_options = shortcode_atts ( array (
				'viewtype' => NULL,
				'ln' => NULL,
				'quicklist' => NULL,
				'ciocresults' => FALSE,
				'targetresults' => NULL,
				'count' => TRUE
		), $atts );
	
		$fetch_url = $fetch_url = $this->parent->full_fetch_url();
		$fetch_headers = $this->parent->fetch_auth_headers();
		$return_html = '';

		$target_results = $sc_options ['targetresults'];
		if (filter_var ( $target_results, FILTER_VALIDATE_URL ) === FALSE) {
			$target_results = NULL;
		}
		
		$form_action = '';
		if ($sc_options['ciocresults']) {
			$form_action = $options['ciocrsd_cioc_url'] . '/results.asp';
		} elseif ($target_results) {
			$form_action = $target_results;
		}
		
		$show_count = TRUE;
		if ($sc_options['count'] === 'off') {
			$show_count = FALSE;
		}
		
		if (!filter_var ( $fetch_url, FILTER_VALIDATE_URL ) === FALSE && !empty($fetch_headers)) {
			$quicklist_type = $sc_options['quicklist'];
			$pubcode = NULL;
			if ($quicklist_type && $quicklist_type != 'DEFAULT') {
				$pubcode = $this->clean_pubcode($quicklist_type);
			}
			$pubcode_path = '';
			if ($pubcode) {
				$pubcode_path = '/' . $pubcode;
			}
			
			$add_params = [];
			if ($show_count) {
				$add_params = array (
					'count' => 'on'
				);
			}
	
			$fetch_url_params = $this->process_fetch_url_params($sc_options, $add_params);
	
			$response = wp_remote_get( $fetch_url . '/rpc/quicklist' . $pubcode_path . '?' . $fetch_url_params, array('headers' => $fetch_headers, 'timeout' => 30));
			if (wp_remote_retrieve_response_code($response) != 200) {
				?>
						<div class="ciocrsd-alert">WARNING: Authorization failed or content unavailable (<?= wp_remote_retrieve_response_code( $response )?> <?= wp_remote_retrieve_response_message($response) ?>)</div>
						<?=$fetch_url . '/rpc/quicklist' . $pubcode_path . '?' . $fetch_url_params ?>
					<?php
				if (is_wp_error($response)) {
					echo "<!-- " . $response->get_error_message() . " -->";
				}
				} else {
					$content = wp_remote_retrieve_body($response);		
					$json_data = json_decode ( $content );
				
					if (! json_last_error() == JSON_ERROR_NONE ) {
						$return_html = '<span class="ciocrsd-alert">Error: ' . json_last_error_msg() . '</span>';
					} elseif ($content === FALSE) {
						$return_html = '<span class="ciocrsd-alert">Error: Content not available</span>';
					} else {
						$search_params = $this->set_search_terms($sc_options);
						
						$is_heading = $json_data->{'type'} == 'Headings';
						if ($is_heading) {
							$select_name = 'GHID';
							$id_type = 'GH_ID';
							$quicklist_name = 'GeneralHeading';
						} else {
							$select_name = 'PBID';
							$id_type = 'PB_ID';
							$quicklist_name = 'PubName';
						}
						$type = $select_name;

						$last_group = '';
						if (isset($json_data->{'quicklist'}) && !empty($json_data->{'quicklist'})) {
							$return_html = '<div class="ciocrsd-quicklist-browse">';
							foreach ( $json_data->{'quicklist'} as $record_row ) {
								$record_id = (isset($record_row->{$id_type}) ? $record_row->{$id_type} : NULL);
								$record_id = $this->clean_id($record_id);
								$record_count = TRUE;
								$record_count_display = '';
								if ($show_count && isset($record_row->{'RecordsInView'})) {
									$record_count =  $record_row->{'RecordsInView'} === '0' ? FALSE : $record_row->{'RecordsInView'};
									$record_count_display = ' <div class="ciocrsd-count-bubble">' . $record_count . '</div>';
								}
								if ($record_id && $record_count) {
									$this_group = (isset($record_row->{'Group'}) && $record_row->{'Group'}) ? $record_row->{'Group'} : $this::NO_GROUP;
									
									if ($last_group != $this_group) { 
										if ($last_group != '') {
											$return_html .= '</ul>';
										}
										if ($this_group != $this::NO_GROUP) {
											$cat_icon = NULL;
											if (isset($record_row->{'IconNameFullGroup'}) && $record_row->{'IconNameFullGroup'}) {
												$cat_icon = $record_row->{'IconNameFullGroup'};
												if (substr($cat_icon, 0, 3) === 'fa-' || substr($cat_icon, 0, 5) === 'icon-') {
													$cat_icon = 'fa ' . $cat_icon;
												} elseif (substr($cat_icon, 0, 10) === 'glyphicon-') {
													$cat_icon = 'glyphicon ' . $cat_icon;
												}
												$cat_icon = '<i class="' . $cat_icon . '" aria-hidden="true"></i>';
											}
											$return_html .= '<h3>' . $cat_icon . ' ' . $this_group . '</h3>';	
										}
										$return_html .= '<ul>';
									}
									$last_group = $this_group;
	
									$cat_icon = NULL;
									if (isset($record_row->{'IconNameFull'}) && $record_row->{'IconNameFull'}) {
										$cat_icon = $record_row->{'IconNameFull'};
										if (substr($cat_icon, 0, 3) === 'fa-' || substr($cat_icon, 0, 5) === 'icon-') {
											$cat_icon = 'fa ' . $cat_icon;
										} elseif (substr($cat_icon, 0, 10) === 'glyphicon-') {
											$cat_icon = 'glyphicon ' . $cat_icon;
										}
										$cat_icon = '<i class="' . $cat_icon . '" aria-hidden="true"></i>';
									}
									$return_html .= '<li>'
										. $cat_icon
										. ' <a href="' . $form_action . '?dosearch=on&' . $select_name . '=' . $record_id . '">' 
										. $record_row->{$quicklist_name} . '</a>' . $record_count_display . '</li>';
								}
							}
							$return_html .= '</ul></div>';
						}
					}
				}
			}
			return $return_html;
		}
	
	public function display_results($atts) {
		$options = get_option ( 'ciocrsd_settings' );
		
		$add_icons = 0;
		if ($options && $options['ciocrsd_add_icons']) {
			$add_icons = $options['ciocrsd_add_icons'];
		}
	
		$fetch_url = $this->parent->full_fetch_url();
		$fetch_headers = $this->parent->fetch_auth_headers();
		$search_params = [];
		$return_html = '';
	
		if ($fetch_url && !empty($fetch_headers)) {
			$sc_options = shortcode_atts ( array (
					'viewtype' => NULL,
					'ln' => NULL,
					'domain' => NULL,
					'dosearch' => FALSE,
					'ciocdetails' => FALSE,
					'targetdetails' => NULL,
					'nolink' => FALSE,
					'nocount' => FALSE,
					'sterms' => NULL,
					'stype' => NULL,				
					'pubcode' => NULL,
					
					/* not responding to these yet
					'heading' => NULL,
					'location' => NULL,
					'servicearea' => NULL,
					'minage' => NULL,
					'maxage' => NULL
					*/
			), $atts );
			
			$this->check_domain($sc_options, 'cic');
			$domain = $sc_options['domain'];
			
			$execute_search = TRUE;
			if ($sc_options['dosearch'] && !get_query_var('dosearch')) {
				$execute_search = FALSE;
			}
	
			if ($domain === 'cic') {
				$target_details = $sc_options ['targetdetails'];
				if (filter_var ( $target_details, FILTER_VALIDATE_URL ) === FALSE) {
					$target_details = NULL;
				}
				
				$search_params = $this->set_search_terms($sc_options);
					
				if (!$execute_search) { 
					$return_html = '';
				} elseif (!(
						isset($search_params['STerms']) 
						|| isset($search_params['PBCode'])
						|| isset($search_params['PBID'])
						|| isset($search_params['GHID'])
						|| isset($search_params['CMID'])
						|| isset($search_params['AgeGroup'])
						)) {
					$return_html = '<div class="ciocrsd-alert">Error: No Search Terms Provided</div>';
				} else {
					$fetch_url_params = $this->process_fetch_url_params($sc_options, $search_params);
					
					$response = wp_remote_get($fetch_url . '/rpc/orgsearch.asp?' . $fetch_url_params, array('headers' => $fetch_headers, 'timeout' => 30));
					if (wp_remote_retrieve_response_code($response) != 200) {
						?>
							<div class="ciocrsd-alert">WARNING: Authorization failed or content unavailable (<?= wp_remote_retrieve_response_code( $response )?> <?= wp_remote_retrieve_response_message($response) ?>)</div>
							<div style="display:none;"><?=$fetch_url . '/rpc/orgsearch.asp?' . $fetch_url_params ?></div> 
						<?php
				if (is_wp_error($response)) {
					echo "<!-- " . $response->get_error_message() . " -->";
				}
					} else {
						$content = wp_remote_retrieve_body($response);		
						$json_data = json_decode ( $content );
						
						if (! json_last_error() == JSON_ERROR_NONE) {
							$return_html = '<div class="ciocrsd-alert">Error: ' . json_last_error_msg() . '</div>';
						} elseif ($content === FALSE) {
							$return_html = '<div class="ciocrsd-alert">Error: No search data is being returned. Contact the site administrator.</div>';
						} elseif (! is_null ( $json_data->{'error'} )) {
							$return_html = '<p>' . htmlspecialchars ( $json_data->{'error'} ) . '</p>';
						} elseif (! isset($json_data->{'recordset'}) || ! is_array($json_data->{'recordset'})) {
							$return_html .= '<div class="ciocrsd-results-count">No results</div>';
						} else {
							if (!$sc_options['nocount']) {
								$return_html .= '<div class="ciocrsd-results-count">Returned ' . count($json_data->{'recordset'}) . ' results</div>';
							}
							foreach ( $json_data->{'recordset'} as $record_row ) {
								$num = isset($record_row->{'NUM'}) ? $record_row->{'NUM'} : NULL;
								if ($num) {
									$ignore_fields = array ( 
											'NUM', 
											'LATITUDE', 
											'LONGITUDE',
											'LOGO_ADDRESS',
											'ORG_NAME', 
											'RECORD_DETAILS', 
											'API_RECORD_DETAILS', 
											'LOCATED_IN_CM',
											'DESCRIPTION', 
											'DESCRIPTION_SHORT' 
									);
									
									$logo = isset($record_row->{'LOGO_ADDRESS'}) ? $record_row->{'LOGO_ADDRESS'} : NULL;
									$org_name = isset($record_row->{'ORG_NAME'}) ? $record_row->{'ORG_NAME'} : $num;
									$org_location = isset($record_row->{'LOCATED_IN_CM'}) ? $record_row->{'LOCATED_IN_CM'} : NULL;
									$org_desc = isset($record_row->{'DESCRIPTION_SHORT'}) ? $record_row->{'DESCRIPTION_SHORT'} : 
											(isset($record_row->{'DESCRIPTION'}) ? $record_row->{'DESCRIPTION'} : NULL);
									
									$record_link = NULL;
									
									$return_html .= '<div class="ciocrsd-search-result">';
									
									if ($logo) {
										$return_html .= '<div class="ciocrsd-logo-container">' . $logo . '</div>';	
									}
									
									if ($sc_options['nolink']) {
										$return_html .= '<h2 class="ciocrsd-org-name">' . $org_name . '</h2>';
									} else {
										if ($sc_options['ciocdetails'] && isset($record_row->{'RECORD_DETAILS'})) {
											$record_link = $record_row->{'RECORD_DETAILS'};
										} elseif ($target_details) {
											$record_link = $target_details . '?num=' . $num;
										} else {
											$record_link = $num;
										}
										$return_html .= '<h2 class="ciocrsd-org-name"><a href="' . $record_link . '">' . $org_name . '</a></h2>';
									}
									if ($org_location) {
										$return_html .= '<div class="ciocrsd-field-group-row">'
												. '<div class="ciocrsd-field-label ciocrsd-LOCATED_IN_CM">'
												. '<i class="fa fa-map-marker" aria-hidden="true"></i> '
												. htmlspecialchars_decode($org_location)
												. '</div>'
												. '</div>';
									}
									
									foreach ($record_row as $field_name => $field_value) {
										if (!in_array ($field_name, $ignore_fields)) {
											if ($field_value) {
												$allow_html =  TRUE;
												$field_label = (isset($json_data->fields->{$field_name}) ? $json_data->fields->{$field_name} : $field_name);
												$return_html .= $this->render_field($field_name, $field_label, $field_value, $allow_html, $add_icons);
											}
										}
									}
									
									if ($org_desc) {
										$return_html .= '<div class="ciocrsd-field-group-row">'
												. '<div class="ciocrsd-field-content-full ciocrsd-DESCRIPTION">'
												. (((substr($org_desc,-3) == '...') && $record_link) ? ($org_desc . ' <a href="' . $record_link . '">[ More Info ]</a>') : $org_desc)
												. '</div>'
												. '</div>';
									}
									
									$return_html .= '</div>';
								}
							}
							$return_html = '<div class="ciocrsd-search-results">' . $return_html . '</div>';
						}
					}
				}
			}
		} else {
			ciocrsd_do_fetch_url_error();
		}
	
		return $return_html;
	}
	
	public function agegroup_dropdown_wrapper($atts) {
		$sc_options = shortcode_atts ( array (
				'viewtype' => NULL,
				'ln' => NULL,
				'shortplaceholder' => FALSE
		), $atts );
	
		return $this->agegroup_dropdown($sc_options);
	}
	
	public function agegroup_dropdown($sc_options, &$type = NULL) {
		$options = get_option ( 'ciocrsd_settings' );
		
		$fetch_url = $this->parent->full_fetch_url();
		$fetch_headers = $this->parent->fetch_auth_headers();
		$return_html = '';
		

		if ($sc_options['shortplaceholder']) {
			$placeholder_text = 'Age Group';
		} else {
			$placeholder_text = 'Select an Age Group';
		}
		
		
		if (!filter_var ( $fetch_url, FILTER_VALIDATE_URL ) === FALSE && !empty($fetch_headers)) {
						
			$fetch_url_params = $this->process_fetch_url_params($sc_options);
			
			$response = wp_remote_get( $fetch_url . '/rpc/agegrouplist?' . $fetch_url_params, array('headers' => $fetch_headers, 'timeout' => 30) );
			if (wp_remote_retrieve_response_code($response) != 200) {
				?>
					<div class="ciocrsd-alert">WARNING: Authorization failed or content unavailable (<?= wp_remote_retrieve_response_code( $response )?> <?= wp_remote_retrieve_response_message($response) ?>)</div>
				<?php
				if (is_wp_error($response)) {
					echo "<!-- " . $response->get_error_message() . " -->";
				}
			} else {
				$content = wp_remote_retrieve_body($response);		
				$json_data = json_decode ( $content );
			
				if (! json_last_error() == JSON_ERROR_NONE ) {
					$return_html = '<span class="ciocrsd-alert">Error: ' . json_last_error_msg() . '</span>';
				} elseif ($content === FALSE) {
					$return_html = '<span class="ciocrsd-alert">Error: Content not available</span>';
				} else {
					$search_params = $this->set_search_terms($sc_options);

					if (isset($json_data->{'agegroups'}) && count($json_data->{'agegroups'}) > 0) {
						$return_html .= '<select name="AgeGroup" class="form-control">'
							. '<option value=""> -- ' . $placeholder_text . ' -- </option>'; 
						foreach ( $json_data->{'agegroups'} as $record_row ) {
							$record_id = (isset($record_row->{'AgeGroup_ID'}) ? $record_row->{'AgeGroup_ID'} : NULL);
							$record_id = $this->clean_id($record_id);
							if ($record_id) {
								$return_html .= '<option value="' . $record_id . '"' . ((isset($search_params['AgeGroup']) && $search_params['AgeGroup']==$record_id) ? ' selected' : '') . '>' 
									. $record_row->{'AgeGroupName'} . '</option>';
							}
						}
						$return_html .= '</select>';
					}
				}
			}
		}
		
		return $return_html;
	}
	
	public function keyword_textbox_wrapper($atts) {
		$sc_options = shortcode_atts ( array (
				'viewtype' => NULL,
				'ln' => NULL,
				'shortplaceholder' => FALSE
		), $atts );
		
		return $this->keyword_textbox($sc_options);
	}
	
	protected function keyword_textbox($sc_options) {
		$search_params = $this->set_search_terms($sc_options);
		
		if ($sc_options['shortplaceholder']) {
			$placeholder_text = 'Enter search terms';
		} else {
			$placeholder_text = 'Enter one or more search terms';
		}
		
		$return_html = '<input type="text" name="STerms"'
			. (isset($search_params['STerms']) ? ' value="' . htmlspecialchars($search_params['STerms']) . '"' : '')
			. ' placeholder="' . $placeholder_text . '" class="form-control">';
				
		return $return_html;
	}
	
	public function quicklist_dropdown_wrapper($atts) {
		$sc_options = shortcode_atts ( array (
				'viewtype' => NULL,
				'ln' => NULL,
				'quicklist' => NULL,
				'shortplaceholder' => FALSE
		), $atts );
	
		return $this->quicklist_dropdown($sc_options);
	}
	
	protected function quicklist_dropdown($sc_options, &$type = NULL) {
		$options = get_option ( 'ciocrsd_settings' );
	
		$fetch_url = $this->parent->full_fetch_url();
		$fetch_headers = $this->parent->fetch_auth_headers();
		$return_html = '';
		
		if ($sc_options['shortplaceholder']) {
			$placeholder_text = 'Category';
		} else {
			$placeholder_text = 'Select a Category';
		}
	
		if (!filter_var ( $fetch_url, FILTER_VALIDATE_URL ) === FALSE && !empty($fetch_headers)) {			
			$quicklist_type = $sc_options['quicklist'];
			$pubcode = NULL;
			if ($quicklist_type && $quicklist_type != 'DEFAULT') {
				$pubcode = $this->clean_pubcode($quicklist_type);
			}
			$pubcode_path = '';
			if ($pubcode) {
				$pubcode_path = '/' . $pubcode;
			}
				
			$fetch_url_params = $this->process_fetch_url_params($sc_options);
				
			$response = wp_remote_get( $fetch_url . '/rpc/quicklist' . $pubcode_path . '?' . $fetch_url_params, array('headers' => $fetch_headers, 'timeout' => 30) );
			if (wp_remote_retrieve_response_code($response) != 200) {
				?>
					<div class="ciocrsd-alert">WARNING: Authorization failed or content unavailable (<?= wp_remote_retrieve_response_code( $response )?> <?= wp_remote_retrieve_response_message($response) ?>)</div>
				<?php
				if (is_wp_error($response)) {
					echo "<!-- " . $response->get_error_message() . " -->";
				}
			} else {
				$content = wp_remote_retrieve_body($response);		
				$json_data = json_decode ( $content );
			
				if (! json_last_error() == JSON_ERROR_NONE ) {
					$return_html = '<span class="ciocrsd-alert">Error: ' . json_last_error_msg() . '</span>';
				} elseif ($content === FALSE) {
					$return_html = '<span class="ciocrsd-alert">Error: Content not available</span>';
				} else {
					$search_params = $this->set_search_terms($sc_options);
					
					$is_heading = $json_data->{'type'} == 'Headings';
					if ($is_heading) {
						$select_name = 'GHID';
						$id_type = 'GH_ID';
						$quicklist_name = 'GeneralHeading';
					} else {
						$select_name = 'PBID';
						$id_type = 'PB_ID';
						$quicklist_name = 'PubName';
					}
					$type = $select_name;
					if (isset($json_data->{'quicklist'}) && count($json_data->{'quicklist'}) > 0) {
						$return_html .= '<select name="' . $select_name . '" class="form-control">'
							. '<option value=""> -- ' . $placeholder_text . ' -- </option>'; 
						foreach ( $json_data->{'quicklist'} as $record_row ) {
							$record_id = (isset($record_row->{$id_type}) ? $record_row->{$id_type} : NULL);
							$record_id = $this->clean_id($record_id);
							if ($record_id) {
								$return_html .= '<option value="' . $record_id . '"' . ((isset($search_params[$select_name]) && $search_params[$select_name]==$record_id) ? ' selected' : '') . '>' 
									. $record_row->{$quicklist_name} . '</option>';
							}
						}
						$return_html .= '</select>';
					}
				}
			}
		}
		return $return_html;
	}

	public function quicklist_buttons_wrapper($atts) {
		$sc_options = shortcode_atts ( array (
				'viewtype' => NULL,
				'ln' => NULL,
				'quicklist' => NULL,
				'gridclass' => 'col-xs-12',
				'buttonclass' => NULL,
		), $atts );
	
		return $this->quicklist_buttons($sc_options);
	}

	public function quicklist_buttons($sc_options, &$type = NULL) {
		$options = get_option ( 'ciocrsd_settings' );
	
		$fetch_url = $this->parent->full_fetch_url();
		$fetch_headers = $this->parent->fetch_auth_headers();
		$return_html = '';
		
		if (!filter_var ( $fetch_url, FILTER_VALIDATE_URL ) === FALSE && !empty($fetch_headers)) {			
			$quicklist_type = $sc_options['quicklist'];
			$pubcode = NULL;
			if ($quicklist_type && $quicklist_type != 'DEFAULT') {
				$pubcode = $this->clean_pubcode($quicklist_type);
			}
			$pubcode_path = '';
			if ($pubcode) {
				$pubcode_path = '/' . $pubcode;
			}
				
			$fetch_url_params = $this->process_fetch_url_params($sc_options);
				
			$response = wp_remote_get( $fetch_url . '/rpc/quicklist' . $pubcode_path . '?' . $fetch_url_params, array('headers' => $fetch_headers, 'timeout' => 30) );
			if (wp_remote_retrieve_response_code($response) != 200) {
				?>
					<div class="ciocrsd-alert">WARNING: Authorization failed or content unavailable (<?= wp_remote_retrieve_response_code( $response )?> <?= wp_remote_retrieve_response_message($response) ?>)</div>
				<?php
				if (is_wp_error($response)) {
					echo "<!-- " . $response->get_error_message() . " -->";
				}
			} else {
				$content = wp_remote_retrieve_body($response);		
				$json_data = json_decode ( $content );
			
				if (! json_last_error() == JSON_ERROR_NONE ) {
					$return_html = '<span class="ciocrsd-alert">Error: ' . json_last_error_msg() . '</span>';
				} elseif ($content === FALSE) {
					$return_html = '<span class="ciocrsd-alert">Error: Content not available</span>';
				} else {
					$is_heading = $json_data->{'type'} == 'Headings';
					if ($is_heading) {
						$select_name = 'GHID';
						$id_type = 'GH_ID';
						$quicklist_name = 'GeneralHeading';
					} else {
						$select_name = 'PBID';
						$id_type = 'PB_ID';
						$quicklist_name = 'PubName';
					}
					$type = $select_name;
					$buttonclass = 'ciocrsd-category-button';
					if (isset($sc_options['buttonclass'])) {
						$buttonclass = $buttonclass . ' ' . $sc_options['buttonclass'];
					}
					$buttonclass = ' class="'. $buttonclass . '"';
					if (isset($json_data->{'quicklist'}) && count($json_data->{'quicklist'}) > 0) {
						$return_html .= '<div class="row ciocrsd-category-row">';
						$gridclass = $sc_options['gridclass'];
						foreach ( $json_data->{'quicklist'} as $record_row ) {
							$record_id = (isset($record_row->{$id_type}) ? $record_row->{$id_type} : NULL);
							$record_id = $this->clean_id($record_id);
							if ($record_id) {
								$return_html .= '<div class="ciocrsd-category-cell ' . $gridclass . '"><button type="submit" name="'. $type . '" value="' . $record_id . '" ' . $buttonclass . '>' 
									. $record_row->{$quicklist_name} . '</button></div>';
							}
						}
						$return_html .= '</div>';
					}
				}
			}
		}
		return $return_html;
	}
	
	
	public function community_dropdown_wrapper($atts) {
		$sc_options = shortcode_atts ( array (
				'viewtype' => NULL,
				'ln' => NULL,
				'limitcmtype' => NULL,
				'shortplaceholder' => FALSE,
				'cmpromptunfilled' => FALSE
		), $atts );
		
		return $this->community_dropdown($sc_options);
	}
	
	public function community_dropdown($sc_options) {
		$options = get_option ( 'ciocrsd_settings' );
	
		$fetch_url = $options['ciocrsd_cioc_url'];
		$fetch_headers = $this->parent->fetch_auth_headers();
		$return_html = '';
		
		if ($sc_options['shortplaceholder']) {
			$placeholder_text = '';
		} else {
			$placeholder_text = 'Select a ';
		}
	
		if (!filter_var ( $fetch_url, FILTER_VALIDATE_URL ) === FALSE && !empty($fetch_headers)) {

			$limit_type = $sc_options['limitcmtype'];
			$prompt_unfilled = $sc_options['cmpromptunfilled'];

			$fetch_url_params = $this->process_fetch_url_params($sc_options);
	
			$response = wp_remote_get( $fetch_url . '/jsonfeeds/community_generator.asp?' . $fetch_url_params, array('headers' => $fetch_headers, 'timeout' => 30) );
			if (wp_remote_retrieve_response_code($response) != 200) {
				?>
					<div class="ciocrsd-alert">WARNING: Authorization failed or content unavailable (<?= wp_remote_retrieve_response_code( $response )?> <?= wp_remote_retrieve_response_message($response) ?>)</div>
				<?php
				if (is_wp_error($response)) {
					echo "<!-- " . $response->get_error_message() . " -->";
				}
			} else {
				$content = wp_remote_retrieve_body($response);		
				$json_data = json_decode ( $content );
			
				if (! json_last_error() == JSON_ERROR_NONE ) {
					$return_html = '<span class="ciocrsd-alert">Error: ' . json_last_error_msg() . '</span>';
				} elseif ($content === FALSE) {
					$return_html = '<span class="ciocrsd-alert">Error: Content not available</span>';
				} else {
					$search_params = $this->set_search_terms($sc_options);				
					if (count($json_data) > 0) {
						$drop_down_title = "Community";
						if ($sc_options['limitcmtype']) {
							$return_html .= '<input type="hidden" name="CMType" value="' . $sc_options['limitcmtype'] . '">';
							if ($sc_options['limitcmtype'] === 'L') {
								$drop_down_title = "Location";
							} elseif ($sc_options['limitcmtype'] === 'S') {
								$drop_down_title = "Service Area";
							}
						}
						if($prompt_unfilled) {
							$prompt_unfilled = ' data-unfilled-prompt="true"';
						} else {
							$prompt_unfilled = '';
						}
						$return_html .= '<select name="CMID" class="form-control"' . $prompt_unfilled . '>'
							. '<option value=""> -- ' . $placeholder_text . $drop_down_title . ' -- </option>'; 
						foreach ( $json_data as $record_row ) {
							$record_id = (isset($record_row->{'chkid'}) ? $record_row->{'chkid'} : NULL);
							$record_id = $this->clean_id($record_id);
							if ($record_id) {
								$return_html .= '<option value="' . $record_id . '"' . ((isset($search_params['CMID']) && $search_params['CMID']==$record_id) ? ' selected' : '') . '>' 
									. $record_row->{'label'} . '</option>';
							}
						}
						$return_html .= '</select>';
					}
				}
			}
		}
		return $return_html;
	}
	
	public function search_form($atts) {
		$options = get_option ( 'ciocrsd_settings' );
	
		$fetch_url = $this->parent->full_fetch_url();
		$fetch_headers = $this->parent->fetch_auth_headers();
		$search_params = [];
		$return_html = '';
	
		if ($fetch_url && !empty($fetch_headers)) {
			$sc_options = shortcode_atts ( array (
					'viewtype' => NULL,
					'ln' => NULL,
					'domain' => NULL,
					'ciocresults' => FALSE,
					'targetresults' => NULL,
					'keywords' => TRUE,
					'agegroup' => FALSE,
					'quicklist' => NULL,
					'community' => TRUE,
					'limitcmtype' => NULL,
					'multiformid' => '',
					'shortplaceholder' => FALSE,
					'clearbutton' => FALSE,
					'cmpromptunfilled' => FALSE
			), $atts );
				
			$this->check_domain($sc_options, 'cic');
			$domain = $sc_options['domain'];
			
			$target_results = $sc_options ['targetresults'];
			if (filter_var ( $target_results, FILTER_VALIDATE_URL ) === FALSE) {
				$target_results = NULL;
			}
			$sc_options ['targetresults'] = $target_results;
			
			$multiform_id = $this->clean_id($sc_options['multiformid']);
			if (!$multiform_id) {
				$multiform_id = '';
			}
			$sc_options['multiformid'] = $multiform_id;
			
			if ($sc_options['keywords'] === 'off') {
				$sc_options['keywords'] = FALSE;
			}
			
			if ($sc_options['community'] === 'off') {
				$sc_options['community'] = FALSE;
			}
			
			$search_params = $this->set_search_terms($sc_options);
	
			if ($domain === 'cic') {
				$form_action = '';
				if ($sc_options['ciocresults']) {
					$form_action = $options['ciocrsd_cioc_url'] . '/results.asp';
				} elseif ($target_results) {
					$form_action = $target_results;
				}
				
				$return_html = '<form action="' . $form_action . '" method="GET" name="CIOCRSDSearch' . $multiform_id . '" class="form-horizontal ciocrsd-search-form">'
						. '<input type="hidden" name="dosearch" value="on">';
				
				if ($sc_options['keywords']) {
					$return_html .= '<div class="ciocrsd-form-input">' 
						. $this->keyword_textbox($sc_options)
						. '</div>';
				}
				
				if ($sc_options['community']) {
					$return_html .= '<div class="ciocrsd-form-input">'
						. $this->community_dropdown($sc_options)
						. '</div>';
				}
				
				if ($sc_options['quicklist']) {
					$return_html .= '<div class="ciocrsd-form-input">' 
						. $this->quicklist_dropdown($sc_options, $quicklist_type)
						. '</div>';	
				}
				
				if ($sc_options['agegroup']) {
					$return_html .= '<div class="ciocrsd-form-input">' .
							$this->agegroup_dropdown($sc_options)
							. '</div>';
				}
						
				$return_html .= '<div class="ciocrsd-search-buttons">'
							. ' <input type="submit" value="Search" class="ciocrsd-search-button" class="form-control">';
				
				if ($sc_options['clearbutton']) {
					$return_html .= ' <input type="button" value="Clear Form" class="ciocrsd-search-button"'
								. ' onClick="'
								. ($sc_options['keywords'] ? 'document.CIOCRSDSearch' . $multiform_id . '.STerms.value=\'\';' : '')
								. ($sc_options['community'] ? 'document.CIOCRSDSearch' . $multiform_id . '.CMID.value=\'\';' : '')
								. ($sc_options['quicklist'] ? 'document.CIOCRSDSearch' . $multiform_id . '.' . $quicklist_type . '.value=\'\';' : '')
								. ($sc_options['agegroup'] ? 'document.CIOCRSDSearch' . $multiform_id . '.AgeGroup.value=\'\';' : '')
								. '">';
				} else {
					$return_html .= ' <input type="reset" value="Reset" class="ciocrsd-search-button">';					
				}
					
				$return_html .= '</div>'
					. '</form>';
			}
		}
		
		return $return_html;
	}
}
