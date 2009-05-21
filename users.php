<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
<?php
/* 
 * Inserts/Updates MLS Users
 */

    require_once "../wp-load.php";
    require_once "idx.php";
    global $wpdb;

    $file = dirname(__FILE__)."/temp/users.txt";
    if ( !file_exists($file) )
        wp_die("Data file doesn't exist!");
    $table = 'wp_rp_agents';
    $columns = array("mls_id", "last_name", "first_name", "address_num", "address_dir",
        "address_street", "address2", "city", "state", "zip_code",
        "area_code", "phone", "phone_ext");

    // Create table if it doesn't exist.
    $created = $wpdb->query("CREATE TABLE IF NOT EXISTS `$table` (
  `id` int(11) NOT NULL auto_increment,
  `mls_id` int(11) default NULL,
  `first_name` varchar(30) default NULL,
  `last_name` varchar(30) default NULL,
  `address_num` int(11) default NULL,
  `address_dir` varchar(10) default NULL,
  `address_street` varchar(30) default NULL,
  `address2` varchar(50) default NULL,
  `city` varchar(30) default NULL,
  `state` varchar(2) default NULL,
  `zip_code` int(11) default NULL,
  `area_code` int(11) default NULL,
  `phone` int(11) default NULL,
  `phone_ext` int(11) default NULL,
  `last_updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0;");

// Run the updater.
do_idx_update($file, $columns, $table);
?>
    </body>
</html>