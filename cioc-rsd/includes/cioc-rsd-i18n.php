<?php

class CIOC_RSD_i18n {

	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'cioc-rsd',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

}
