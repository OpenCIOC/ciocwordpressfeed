<?php

class CIOC_RSD {

	protected $loader;

	protected $plugin_name;
	
	protected $plugin_main_dir;

	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 */
	public function __construct($plugin_main_dir) {

		$this->plugin_name = 'cioc-rsd';
		$this->version = '1.0.0';
		$this->plugin_main_dir = $plugin_main_dir;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}
	
	public function __get($var) {
		if (isset($this->$var)) {
			return $this->$var;
		} else {
			return null;
		}
	}

	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/cioc-rsd-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/cioc-rsd-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cioc-rsd-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/cioc-rsd-public.php';

		$this->loader = new CIOC_RSD_Loader();
	}

	private function set_locale() {

		$plugin_i18n = new CIOC_RSD_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	private function define_admin_hooks() {

		$plugin_admin = new CIOC_RSD_Admin( $this );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
	}

	private function define_public_hooks() {

		$plugin_public = new CIOC_RSD_Public( $this );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_public, 'register_shortcodes' );
		$this->loader->add_filter( 'query_vars', $plugin_public, 'register_queryvars' );

	}

	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}

	public function full_fetch_url() {
		$options = get_option ( 'ciocrsd_settings' );
		
		$fetch_url = null;
		if ($options) {
			$fetch_url = $options['ciocrsd_cioc_url'];
			if (!filter_var ( $fetch_url, FILTER_VALIDATE_URL ) === FALSE) {
				$fetch_protocol = parse_url($fetch_url, PHP_URL_SCHEME);
				$fetch_protocol = empty($fetch_protocol) ? 'https' : $fetch_protocol;
				$fetch_host = parse_url($fetch_url, PHP_URL_HOST);
				
				if (!empty($fetch_protocol) && !empty($fetch_host)) {
					$fetch_url = $fetch_protocol . '://' . $fetch_host;
				}
			} else {
				$fetch_url = null;
			}
		}
		
		return $fetch_url;
	}

	public function fetch_auth_headers() {
		$options = get_option ( 'ciocrsd_settings' );

		$headers = array();

		if ($options) {
			$fetch_account = $options['ciocrsd_api_id'];
			$fetch_password = $options['ciocrsd_api_pw'];


			if (!empty($fetch_account) && !empty($fetch_password)) {
				$headers = array('Authorization' => 'Basic ' . base64_encode($fetch_account . ':' . $fetch_password));
			} else {
					echo "<!-- no auth -->";
			}
		}

		return $headers;
	}

	public function do_fetch_url_error() {
		echo __ ( '<div class="ciocrsd-alert">There is a problem with the data feed. Please contact the site administrator so they can check the settings.</div>' );
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

	public function check_domain(&$options, $default_domain) {
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

	public function count_all_in_view($atts) {
		$fetch_url = full_fetch_url();
		$fetch_headers = fetch_auth_headers();
		$return_html = '';
		
		if ($fetch_url && !empty($fetch_headers)) {
			$sc_options = shortcode_atts ( array (
					'viewtype' => NULL,
					'ln' => NULL,
					'domain' => NULL
			), $atts );
			
			check_domain($sc_options, 'cic');
			
			$fetch_url_params = process_fetch_url_params($sc_options);
			
			$response = wp_remote_get( $fetch_url . '/rpc/countall/' . $sc_options['domain'] . '?' . $fetch_url_params, array('headers' => $fetch_headers, 'timeout' => 30));
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
					$return_html = $json_data->{'RecordCount'};
				}
			}
		} else {
			do_fetch_url_error();
		}
		
		return $return_html;
	}
}
