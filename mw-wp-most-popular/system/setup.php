<?php
class MW_WMP_setup {
	private static $table = "mw_wp_most_popular";
	public static function install() {
		// Create table
		global $wpdb;
		$table = $wpdb->prefix . self::$table;
		if ( $wpdb->get_var( "SHOW TABLES LIKE '".$table."'" ) != $table ) {
			$sql = "CREATE TABLE ".$table." (
						id BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
						post_id BIGINT NOT NULL,
						last_updated DATETIME NOT NULL,
						1_day_stats MEDIUMINT NOT NULL,
						7_day_stats MEDIUMINT NOT NULL,
						30_day_stats MEDIUMINT NOT NULL,
						all_time_stats BIGINT NOT NULL,
						raw_stats text NOT NULL);
					";
			$wpdb->query( $sql );
		}
	}
	
	public static function uninstall() {
		// Remove Table
		global $wpdb;
		$table = $wpdb->prefix . self::$table;
		$sql = "DROP TABLE ".$table.";";
		$wpdb->query( $sql );
	}
}