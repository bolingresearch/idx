<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once "../wp-load.php";

// array of lines from data file
$file = dirname(__FILE__)."/temp/listings-manufactured.txt";
if (!file_exists($file)) {
    wp_die('<div class="updated fade"><p>The file: '.$file.' doesn\'t exist.</p></div>');
}
$lines = file($file, FILE_IGNORE_NEW_LINES);
$listing_count = 0;
foreach($lines as $line) {
// explode columns into $fields array
    $fields = explode("\t", $line);
    $count = count($fields);
    for ($i = 0; $i < $count; $i++) {
        $fields[$i] = $wpdb->escape($fields[$i]);
    }
    echo $fields[1]."<br />";
    echo $fields[2]."<br />";
    echo $fields[3]."<br />";
    echo $fields[4]."<br />";
    echo $fields[5]."<br />";
    echo $fields[6]."<br />";
    echo $fields[7]."<br />";
    echo $fields[8]."<br />";
    echo $fields[9]."<br />";
    echo $fields[10]."<br />";
    echo $fields[11]."<br />";
    echo "Status: ".$fields[12]."<br />";
    echo $fields[13]."<br />";
    echo $fields[14]."<br />";
    echo $fields[15]."<br />";
    echo $fields[16]."<br />";
    echo $fields[17]."<br />";
    echo $fields[18]."<br />";
    echo $fields[20]."<br />";

}
?>
