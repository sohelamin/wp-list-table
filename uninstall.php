<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();

function uninstall_wp_list_table_plugin() {
    global $wpdb;

    $table_name = $wpdb->prefix . "customers";
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}

uninstall_wp_list_table_plugin();
