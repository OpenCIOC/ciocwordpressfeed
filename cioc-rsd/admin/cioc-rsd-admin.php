<?php
class CIOC_RSD_Admin {
	private $plugin_name;
	
	private $version;
	
	private $parent;
	
	public function __construct( CIOC_RSD $parent ) {
		if ($parent) {
			$this->parent = $parent;
			$this->plugin_name = $parent->plugin_name;
			$this->version = $parent->version;
		}
	}
	
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, $this->parent->plugin_main_dir . 'css/cioc-rsd.css', array(), $this->version, 'all');
	}
	
	public function add_admin_menu () {
		add_options_page ( 'CIOC Remote Search and Details Plugin Settings', 'CIOC API Settings', 'manage_options', 'cioc_rsd', array( $this, 'options_page' ) );
	}
	
	public function register_settings() {
		register_setting ( 'cioc_rsd_plugin', 'ciocrsd_settings' );
		
		add_settings_section ( 'ciocrsd_authentication_section', __ ( 'CIOC API Credentials' ), array( $this, 'authentication_settings_section_callback' ), 'cioc_rsd_plugin');
		
		add_settings_section ( 'ciocrsd_config_section', __ ( 'Data Formatting and Display' ), array( $this, 'config_settings_section_callback' ), 'cioc_rsd_plugin' );
		
		add_settings_section ( 'ciocrsd_mapping_section', __ ( 'Google Maps' ), array( $this, 'mapping_settings_section_callback' ), 'cioc_rsd_plugin' );
		
		add_settings_field ( 'ciocrsd_cioc_url', __ ( 'Database URL (e.g. https://mysite.cioc.ca)' ), array( $this, 'cioc_url_render' ), 'cioc_rsd_plugin', 'ciocrsd_authentication_section' );
		
		add_settings_field ( 'ciocrsd_api_id', __ ( 'API ID' ), array( $this, 'api_id_render' ), 'cioc_rsd_plugin', 'ciocrsd_authentication_section' );
		
		add_settings_field ( 'ciocrsd_api_pw', __ ( 'API Password' ), array( $this, 'api_pw_render' ), 'cioc_rsd_plugin', 'ciocrsd_authentication_section' );
		
		add_settings_field ( 'ciocrsd_has_fa', __ ( 'Do not add FontAwesome library components' ), array( $this, 'has_fa_render' ), 'cioc_rsd_plugin', 'ciocrsd_config_section' );
		
		add_settings_field ( 'ciocrsd_add_icons', __ ( 'Add icons automatically whenever possible' ), array( $this, 'add_icons_render' ), 'cioc_rsd_plugin', 'ciocrsd_config_section' );
		
		add_settings_field ( 'ciocrsd_has_bootstrap', __ ( 'Do not add Bootstrap library components' ), array( $this, 'has_bootstrap_render' ), 'cioc_rsd_plugin', 'ciocrsd_config_section' );
		
		add_settings_field ( 'ciocrsd_browse_count', __ ( 'Include Record Count on Browse Page(s)' ), array( $this, 'browse_count_render' ), 'cioc_rsd_plugin', 'ciocrsd_config_section' );
		
		add_settings_field ( 'ciocrsd_field_groups', __ ( 'Use Field Groups for Record Details Display' ), array( $this, 'field_groups_render' ), 'cioc_rsd_plugin', 'ciocrsd_config_section' );
		
		add_settings_field ( 'ciocrsd_google_maps_key', __ ( 'Google Maps API Key' ), array( $this, 'google_maps_key_render' ), 'cioc_rsd_plugin', 'ciocrsd_mapping_section' );
	}
	
	public function cioc_url_render() {
		$options = get_option ( 'ciocrsd_settings' );
		$field_error = null;
		if (filter_var ( $options ['ciocrsd_cioc_url'], FILTER_VALIDATE_URL ) === FALSE) {
			$field_error = '<span class="ciocrsd-alert">Not a valid URL!</span>';
		}
		?>
		<?php echo $field_error?>
		<input type='text' name='ciocrsd_settings[ciocrsd_cioc_url]' value="<?php echo $options['ciocrsd_cioc_url']; ?>" maxlength='150' size='90'>
		<?php
	}
	
	public function api_id_render() {
		$options = get_option ( 'ciocrsd_settings' );
		?>
		<input type='text' name='ciocrsd_settings[ciocrsd_api_id]' value="<?php echo $options['ciocrsd_api_id']; ?>" maxlength='38' size='38' autocomplete="off">
		<?php
	}
	
	public function api_pw_render() {
		$options = get_option ( 'ciocrsd_settings' );
		?>
		<input type='text' name='ciocrsd_settings[ciocrsd_api_pw]' value="<?php echo $options['ciocrsd_api_pw']; ?>" maxlength='38' size='38' autocomplete="off">
		<?php
	}

	public function has_fa_render() {
		$options = get_option ( 'ciocrsd_settings' );
		if (!isset($options['ciocrsd_has_fa'])) {
			$options['ciocrsd_has_fa'] = 0;
		}
		?>
		<input type='checkbox' name='ciocrsd_settings[ciocrsd_has_fa]' <?php checked( $options['ciocrsd_has_fa'], 1 ); ?> value='1'>
		<?php
	}
	
	public function add_icons_render() {
		$options = get_option ( 'ciocrsd_settings' );
		if (!isset($options['ciocrsd_add_icons'])) {
			$options['ciocrsd_add_icons'] = 0;
		}
		?>
			<input type='checkbox' name='ciocrsd_settings[ciocrsd_add_icons]' <?php checked( $options['ciocrsd_add_icons'], 1 ); ?> value='1'>
			<?php
		}
	
	public function has_bootstrap_render() {
		$options = get_option ( 'ciocrsd_settings' );
		if (!isset($options['ciocrsd_has_bootstrap'])) {
			$options['ciocrsd_has_bootstrap'] = 0;
		}
		?>
		<input type='checkbox' name='ciocrsd_settings[ciocrsd_has_bootstrap]' <?php checked( $options['ciocrsd_has_bootstrap'], 1 ); ?> value='1'>
		<?php
	}

	public function browse_count_render() {
		$options = get_option ( 'ciocrsd_settings' );
		if (!isset($options['ciocrsd_browse_count'])) {
			$options['ciocrsd_browse_count'] = 0;
		}
		?>
		<input type='checkbox' name='ciocrsd_settings[ciocrsd_browse_count]' <?php checked( $options['ciocrsd_browse_count'], 1 ); ?> value='1'>
		<?php
	}
	
	public function field_groups_render() {
		$options = get_option ( 'ciocrsd_settings' );
		if (!isset($options['ciocrsd_field_groups'])) {
			$options['ciocrsd_field_groups'] = 0;
		}
		?>
		<input type='checkbox' name='ciocrsd_settings[ciocrsd_field_groups]' <?php checked( $options['ciocrsd_field_groups'], 1 ); ?> value='1'>
		<?php
	}
	
	public function google_maps_key_render() {
		$options = get_option ( 'ciocrsd_settings' );
		?>
		<input type='text' name='ciocrsd_settings[ciocrsd_google_maps_key]' value="<?php echo $options['ciocrsd_google_maps_key']; ?>" maxlength='100' size='90'>
		<?php
	}
	
	public function config_settings_section_callback() {
		echo __ ( 'Please indicate which of the following apply to your site. In some cases, a particular 3rd party JavaScript or CSS library is required and will be added to pages that use this plug-in. If your site already supports these 3rd party libraries, you should indicate so here to prevent errors caused by adding the same library multiple times.' );
	}
	
	public function mapping_settings_section_callback() {
		echo __ ( 'The following section must be completed to enable mapping features' );
	}
	
	public function options_page() {
		?>
		<div class="wrap">
		<form action='options.php' method='post'>
	
		<h1>CIOC Remote Search and Details API Plug-in Adminstration</h1>
		<?php
		settings_fields ( 'cioc_rsd_plugin' );
		do_settings_sections ( 'cioc_rsd_plugin' );
		submit_button ();
		?>
		</form>
		</div>
		<?php
	}
	
	public function authentication_settings_section_callback() {
		$fetch_url = $this->parent->full_fetch_url();
		?>
		<p>Please indicate the CIOC site you are working against, and your API
		credentials. The database URL should support SSL. Include the protocol
		(https://) with the URL.</p>
		<p>Manage your API IDs and passwords through the CIOC Account management Page.
		If you believe your API Account ID and Password are compromised or you have lost
		the Password for this ID, you can disable this ID / Password pair and regenerate a 
		new API ID / Password pair for your user Account from your Account management Page.</p>
		<p><strong>You should not enter your actual CIOC account credentials here</strong>,
		and it is strongly recommended that you create API-only accounts for production use
		to ensure that data access permissions are safe and secure. Never allow an account
		with access to non-public information to be used for public API use.</p>
		
		<h4>Testing Account settings...</h4>
		<?php
		if ($fetch_url) {
			$response = wp_remote_get( $fetch_url . '/rpc/whoami' );
			if (wp_remote_retrieve_response_code($response) != 200) {
				?>
				<div class="ciocrsd-alert">WARNING: Authorization failed (<?= wp_remote_retrieve_response_message($response) ?>)</div>
				<?php
			} else {
				$content = wp_remote_retrieve_body($response);
				$json_data = json_decode ( $content );
				$cioc_user = $json_data->{'UserName'} ? $json_data->{'UserName'} : 'credentials rejected';
				$cioc_user_cic = (isset($json_data->{'CIC'}) && $json_data->{'CIC'}) ? 'yes' : 'no';
				$cioc_user_vol = (isset($json_data->{'VOL'}) && $json_data->{'VOL'}) ? 'yes' : 'no';
				?>
				<p>URL: <strong><?=$fetch_url?></strong>
				<br>CIOC User: <strong><?=$cioc_user?></strong>
				<br>Community Information API Permissions: <strong><?=$cioc_user_cic?></strong>
				<br>Volunteer Opportunity API Permissions: <strong><?=$cioc_user_vol?></strong>
				</p>
				<?php
				if ($json_data->{'CIC'} || $json_data->{'VOL'}) {
				?>
				<div class="ciocrsd-success">Account Successfully Verified</div>
				<?php
				} else {
				?>
				<div class="ciocrsd-alert">WARNING: Account settings are incorrect.</div>
				<?php
				}
			}
		} else {
			?>
			<div class="ciocrsd-alert">WARNING: URL not properly set</div>
			<?php
		}
			
	}

}
