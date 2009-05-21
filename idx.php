<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Inserts/Updates RealtyPress Database with MLS feed.
 *
 * @global WPDB $wpdb
 * @param string $data_file
 * @param array $field_names
 * @param string $db_table
 */
function do_idx_update($data_file, $field_names, $db_table) {

    global $wpdb;
    $wpdb->flush();
    $wpdb->show_errors();

    $updated = 0;
    $inserted = 0;

    // array of lines from data file

    if (!file_exists($data_file)) {
        wp_die('<div class="updated fade"><p>The file: '.$file.' doesn\'t exist.</p></div>');
    }

    // Insert multiple rows at once, when possible.
    $insert_queries = "";

    // Get Rows (Agent Data)
    $lines = file($data_file, FILE_IGNORE_NEW_LINES);
    foreach($lines as $line) {
        $fields = explode("\t", $line);
        $count = count($fields);

        // Sanitize the Data
        for ($i = 0; $i < $count; $i++) {
            if (!is_numeric($fields[$i]))
                $fields[$i] = $wpdb->escape(ucwords(strtolower(trim($fields[$i]))));
            else
                $fields[$i] = $wpdb->escape($fields[$i]);
        }

        // UPDATE or INSERT
        $exists = $wpdb->get_var("SELECT mls_id FROM $db_table WHERE mls_id=$fields[0]");
        if ($exists != NULL) {
            // UPDATE
            $update_query = "UPDATE ".$db_table." SET ";
            for ($i = 0; $i < $count; $i++) {
                $update_query .= $field_names[$i]."='".$fields[$i]."'";
                if ($i < $count - 1) {
                    $update_query .= ", ";
                }
            }
            $update_query .= " WHERE mls_id='$fields[0]';";

            // Do UPDATE Query
            $result = 0;
            $result = $wpdb->query($update_query);
            if ($result === FALSE )
                wp_die("Error writing changes to database for $fields[2] $fields[1]:</p>$update_query</p>".$wpdb->print_error());
            if ($result > 0 )
                $updated++;
        }
        else
        {
            // INSERT
            $insert_fields = "INSERT INTO ".$db_table ."(";
            $insert_values = " VALUES (";
            for ($i = 0; $i < $count; $i++) {
                $insert_fields .= $field_names[$i];
                $insert_values .= "'".$fields[$i]."'";
                if ($i < $count - 1) {
                    $insert_fields .= ", ";
                    $insert_values .= ", ";
                } else {
                    $insert_fields .= ")";
                    $insert_values .= "); ";
                }
            }// END for
            // Do INSERT Query
            $insert_query = $insert_fields.$insert_values;
            $result = 0;
            $result = $wpdb->query($insert_query);
            if ($result === FALSE)
                wp_die("Error performing INSERT query:</p>$insert_query</p>".$wpdb->print_error());
            else if ($result > 0 )
                $inserted++;
        }// else
    }// END foreach

    wp_die("Updated: $updated, Inserted: $inserted");
}
?>
