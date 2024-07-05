<?php
/*
Plugin Name: Doremon visitor count
Description: Count visitor using simple doremon visitor plugin.
Version: 1.0.0
Author: Yusuf
*/

// Function to increment visitor count
function increment_visitor_count() {
    if ( ! is_admin() ) {
        $count = get_option( 'visitor_count', 0 );
        update_option( 'visitor_count', ++$count );
    }
}
add_action( 'wp', 'increment_visitor_count' );

// Function to display visitor count
function display_visitor_count() {
    $count = get_option( 'visitor_count', 0 );
    return $count;
}

// Function to add menu page
function add_visitor_menu_page() {
    add_menu_page(
        'Doremon Visitor Count',            // Page title
        'Doremon Visitor Count',            // Menu title
        'manage_options',                   // Capability required to access
        'doremon-visitor-count',            // Menu slug
        'display_visitor_menu_page',        // Callback function to display page content
        'dashicons-chart-bar',              // Icon (optional)
    );
}
add_action( 'admin_menu', 'add_visitor_menu_page' );

// Callback function to display menu page content
function display_visitor_menu_page() {
    echo '<div>
            <h1>
                Total Visitor:'. display_visitor_count().'
            </h1>
          </div>';
}
