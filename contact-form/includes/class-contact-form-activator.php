<?php

/**
 * Fired during plugin activation
 *
 * @link       https://narek-dev.com
 * @since      1.0.0
 *
 * @package    Contact_Form
 * @subpackage Contact_Form/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Contact_Form
 * @subpackage Contact_Form/includes
 * @author     Narek <Malkhasyan>
 */
class Contact_Form_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'contact_form_data';

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			name text NOT NULL,
			email text NOT NULL,
			date text NOT NULL,
			multiple_choice text NOT NULL,
			file_name text NOT NULL,
			PRIMARY KEY  (id)
		);";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

}
