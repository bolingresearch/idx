<?php
/* 
 * Tehama County, California IDX Data Import Script.
 */


require_once("../wp-load.php");


/**
 * FTP Server Credentials
 */
$host = "idx.fnismls.com";
$user = "tehamamls_djboling";
$pass = "2dk9j2ipcz";
$dir = "IDX/";


/**
 * Local directory details
 */
$local_temp_dir = dirname(__FILE__)."/temp/";
$local_images_dir = ABSPATH."wp-content/listing-images/";
// Database
$db_table = 'wp_rplistings';


/**
 * Connect to the FTP Server
 */
$conn = ftp_connect($host) or die("ERROR: Cannot connect to FTP server.");
ftp_login($conn, $user, $pass) or die("ERROR: Cannot login to FTP server");
ftp_chdir($conn, $dir);


/**
 * Retrieve list of files on the server.
 */
$list = ftp_nlist($conn, ".");
natsort($list);


/**
 * Separate image archives from data files.
 */
$listingdata_files = array();
$image_archives = array();
$i = $j = 0;
foreach ($list as $remote_file) {
    if (preg_match("/listings/", $remote_file)) {
        $listingdata_files[$i] = $remote_file;
        $i++;
    }
    else if (preg_match("/pics/", $remote_file)) {
            $image_archives[$j] = $remote_file;
            $j++;
        }
}


/**
 * Files to download.
 */
$date = new DateTime(date("Y-m-d"));  // today
$date->modify("-1 day");            // yesterday
$formatted_date = $date->format("Ymd");
$files2download = $listingdata_files;
foreach ($image_archives as $image_archive) {
    if ( preg_match("/$formatted_date/", $image_archive)) {
        $files2download[] = $image_archive;
    }
}


/**
 * Download and extract data files and image archives.
 */
get_idxfiles( $files2download );


/**
 * Disconnect from the FTP server.
 */
ftp_close($conn);


/**
 * Update the database with IDX listing data.
 */
update_database();
assign_images();


/**
 * Downloads and extracts data files and image archives from
 * the IDX server.
 *
 * @global FTP $conn
 * @global string $local_temp_dir
 * @global string $local_images_dir
 * @param string[] $files Filenames to download and extract.
 */
function get_idxfiles( $files = array() ) {

    global $conn;
    global $local_temp_dir;
    global $local_images_dir;


    // include Tar class
    require_once("Archive/Tar.php");


    echo "<div style='height:150px; width:400px; overflow:auto; border: solid 1px #222;'>";


    foreach ($files as $remote_file) {
        echo "<p>";

        $local_file = $local_temp_dir.$remote_file;

        ftp_get($conn, $local_file, $remote_file, FTP_BINARY) or die("ERROR: Cannot download $remote_file");
        echo "$remote_file successfully downloaded.<br />";

        // if text file
        if (preg_match("/txt.gz/", $remote_file) == 1) {
            if (file_exists(substr($local_file, 0, -3))) {
                unlink(substr($local_file, 0, -3));
            }
            `gunzip $local_file`;
            // check if file was successfully unzipped
            if (file_exists(substr($local_file, 0, -3))) {
                echo "$remote_file unzipped.";
            }
        }

        // if images archive
        if (preg_match("/.tar/", $remote_file) == 1) {

        // use tar file
            $tar = new Archive_Tar($local_file);
            if (is_writable($local_images_dir)) {
                if ($tar->extract($local_images_dir)) {
                    echo "Images extracted.<br />";
                }
                if(file_exists($local_file)) {
                    if (unlink($local_file)) {
                        echo 'Archive file deleted.<br />';
                    } else {
                        echo 'Check your folder permissions. The archive file could not be deleted.<br />';
                    }
                }
            } else {
                echo "Images directory is not writable.<br />";
            }
        }
        echo "</p>";
    }
    echo "</div>";
}


/**
 * Updates RealtyPress tables with IDX data. Scans image directory and
 * updates listing table with image date.
 *
 * @global <type> $local_temp_dir
 * @global <type> $local_images_dir
 * @global <type> $wpdb
 */
function update_database() {

    global $local_temp_dir;
    global $local_images_dir;
    global $wpdb;
    global $db_table;

    // array of lines from data file
    $file = $local_temp_dir."listings-residential.txt";
    if (!file_exists($file)) {
        wp_die('<div class="updated fade"><p>The file: '.$file.' doesn\'t exist.</p></div>');
    }
    $lines = file($file, FILE_IGNORE_NEW_LINES);
    $listing_count = 0;
    foreach($lines as $line) {
    // explode columns into $fields array
        $fields = explode("\t", $line);

        // make data safe
        $fields[1] = $wpdb->escape(ucwords(strtolower($fields[1])));
        $fields[2] = $wpdb->escape(ucwords(strtolower($fields[2])));
        $fields[3] = $wpdb->escape(ucwords(strtolower($fields[3])));
        $fields[4] = $wpdb->escape($fields[4]);
        $fields[5] = $wpdb->escape($fields[5]);
        $fields[6] = $wpdb->escape($fields[6]);
        $fields[7] = $wpdb->escape(ucwords(strtolower($fields[7])));
        $fields[8] = $wpdb->escape(ucwords(strtolower($fields[8])));
        $fields[9] = $wpdb->escape(ucwords(strtolower($fields[9])));
        $fields[10] = $wpdb->escape($fields[10]);
        $fields[11] = $wpdb->escape($fields[11]);
        $fields[12] = $wpdb->escape($fields[12]);
        $fields[13] = $wpdb->escape($fields[13]);
        $fields[14] = $wpdb->escape($fields[14]);
        $fields[15] = $wpdb->escape($fields[15]);
        $fields[16] = $wpdb->escape($fields[16]);
        $fields[17] = $wpdb->escape($fields[17]);
        $fields[19] = $wpdb->escape(ucwords(strtolower($fields[19])));
        $fields[22] = $wpdb->escape(ucwords(strtolower($fields[22])));
        $fields[24] = $wpdb->escape($fields[24]);
        $fields[26] = $wpdb->escape(ucwords(strtolower($fields[26])));
        $fields[27] = $wpdb->escape(ucwords(strtolower($fields[27])));
        $fields[49] = $wpdb->escape($fields[49]);
        $fields[54] = $wpdb->escape(ucwords(strtolower($fields[54])));
        $fields[59] = $wpdb->escape($fields[59]);
        $fields[101] = $wpdb->escape($fields[101]);
        $fields[108] = $wpdb->escape(sentence_case($fields[108]));
        $fields[123] = $wpdb->escape($fields[123]);
        $fields[125] = $wpdb->escape($fields[125]);
        $fields[126] = $wpdb->escape($fields[126]);
        $price_change = $fields[101] - $fields[4];

        // format date
        $fields[47] = date("Y-m-d", strtotime($fields[47]));

        // does listing already exist?
        $wpdb->show_errors();

        $found = $wpdb->get_results("SELECT listing_id FROM " . $db_table . " WHERE listing_id='$fields[0]'");
        // if found, listing exists. UPDATE it.
        $arr_size = count($found);

        if ($arr_size != 0) {
            $rows_affected = $wpdb->query(
                "UPDATE ".$db_table." SET class='$fields[1]',
type='$fields[2]',
area='$fields[3]',
price='$fields[4]',
street_num='$fields[5]',
street_dir='$fields[6]',
street_name='$fields[7]',
street_line2='$fields[8]',
city='$fields[9]',
state='$fields[10]',
zipcode='$fields[11]',
status='$fields[12]',
sale_or_rent='$fields[13]',
beds='$fields[14]',
baths_full='$fields[15]',
baths_partial='$fields[16]',
garage_cap='$fields[17]',
stories='$fields[19]',
water_src='$fields[22]',
acres='$fields[24]',
lot_dimensions='$fields[26]',
lot_description='$fields[27]',
list_date='$fields[47]',
year_built='$fields[49]',
subdivision='$fields[54]',
total_rooms='$fields[59]',
orig_price='$fields[101]',
public_remarks='$fields[108]',
agent_id='$fields[123]',
office_id='$fields[125]',
sqft='$fields[126]',
price_change='$price_change'
WHERE listing_id='$fields[0]'");

            if($rows_affected === 0) {
            //echo "Listing #$fields[0] doesn't need updating.<br />";
            //echo '<div class="updated fade"><p>Plugin settings saved.</p></div>';
            } elseif($rows_affected === FALSE) {
                echo '<div class="error fade"><p>There has been an error querying the database.</p></div>';
            } else {
                $listing_count++;
            }
            unset($found);
        } else {
            $affected = $wpdb->query("
INSERT INTO " . $db_table . " (listing_id,
class,
type,
area,
price,
street_num,
street_dir,
street_name,
street_line2,
city,
state,
zipcode,
status,
sale_or_rent,
beds,
baths_full,
baths_partial,
garage_cap,
stories,
water_src,
acres,
lot_dimensions,
lot_description,
list_date,
year_built,
subdivision,
total_rooms,
orig_price,
public_remarks,
agent_id,
office_id,
sqft,
price_change)
VALUES (
'$fields[0]',
'$fields[1]',
'$fields[2]',
'$fields[3]',
'$fields[4]',
'$fields[5]',
'$fields[6]',
'$fields[7]',
'$fields[8]',
'$fields[9]',
'$fields[10]',
'$fields[11]',
'$fields[12]',
'$fields[13]',
'$fields[14]',
'$fields[15]',
'$fields[16]',
'$fields[17]',
'$fields[19]',
'$fields[22]',
'$fields[24]',
'$fields[26]',
'$fields[27]',
'$fields[47]',
'$fields[49]',
'$fields[54]',
'$fields[59]',
'$fields[101]',
'$fields[108]',
'$fields[123]',
'$fields[125]',
'$fields[126]',
'$price_change')
                ");

        } // end else
        flush();
        $wpdb->flush();
    } // end foreach
    echo '<div class="updated fade"><p>The listings database was updated successfully. Count: '.$listing_count.'</p></div>';
} // end rp_update_listing_data


/*
* Converts a string to sentence case.
* @returns string
*/
function sentence_case($string) {
    $sentences = preg_split('/([.?!]+)/', $string, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
    $new_string = '';
    foreach ($sentences as $key => $sentence) {
        $new_string .= ($key & 1) == 0?
            ucfirst(strtolower(trim($sentence))) :
            $sentence.' ';
    }
    return trim($new_string);
}


/**
 *
 * @global WPDB $wpdb
 */
function assign_images() {
    global $wpdb;
    global $db_table;
    global $local_images_dir;
    $wpdb->show_errors();
    $listings = $wpdb->get_results("SELECT listing_id FROM $db_table");
    $count = 0;
    foreach($listings as $listing) {

    // Grab all images associated with listing id.
        $images = image_search($local_images_dir, $listing->listing_id);

        if(!empty($images)) {
            natsort($images);
            // insert image data into database.
            $img_string = "";
            foreach($images as $image) {
                $img_string .= $image . "\t";
            }
            $img_string = trim($img_string);
            $affected = $wpdb->query("UPDATE $db_table SET images='$img_string' WHERE listing_id={$listing->listing_id}");
            $count++;
        }
    }
    echo '<div class="updated fade"><p>'.$count.' listings updated with images.</p></div>';
}


/* 
* Searches recursively for filenames matching a given pattern.
* Outputs an array of the matching filenames.
*/
function image_search($dir, $id) {
// check if argument is a valid directory
    if(!is_dir($dir)) {
        wp_die("Argument '$dir' is not a valid directory!");
    }
    
    // open directory handle
    $dh = opendir($dir) or wp_die("Cannot open directory '$dir'!");
    
    $matches = array();
    // iterate over files in directory
    $i = 0;
    while (($file = readdir($dh)) !== false) {
    // filter out "." and ".."
        if ($file != "." && $file != "..") {
        // if this is a file
        // check for a match
        // add to $matchList if found
            if (preg_match("/$id/", $file) == 1) {
                $matches[$i] = "$file";
                $i++;
            }
        }
    }
    // return the final list to the caller
    return $matches;
}
?>