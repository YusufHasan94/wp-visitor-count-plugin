<?php
/*
Plugin Name: Doremon visitor count
Description: Count visitor using simple doremon visitor plugin.
Version: 1.0.0
Author: Yusuf
*/

class DoremonVisitorCount{
    public function __construct(){
        add_action( 'wp', array($this, 'increment_visitor_count'));
        add_action( 'admin_menu', array($this, 'add_visitor_menu_page'));
    }
    // Function to increment visitor count
    public function increment_visitor_count() {
        if ( ! is_admin() ) {
            $count = get_option( 'visitor_count', 0 );
            update_option( 'visitor_count', ++$count );

            $today = date('Y-m-d');
            $dailyCounts = get_option('daily_visitor_counts', array());
            if(isset($dailyCounts[$today])){
                $dailyCounts[$today]++;
            }else{
                $dailyCounts[$today] = 1;
            }
            update_option('daily_visitor_counts', $dailyCounts);
        }
    }
    // Function to display visitor count
    public function get_total_visitor_count() {
        $count = get_option( 'visitor_count', 0 );
        return $count;
    }

    public function get_daily_visitor_count(){
        $dailyCounts = get_option('daily_visitor_counts', array());
        $today = date('Y-m-d');
        $totalDayCounts = array();
        if(isset($dailyCounts[$today])){
            $totalDayCounts[] = array(
                'date'=> $today,
                'visitor_count'=> $dailyCounts[$today]
            );
        }
        return $totalDayCounts;
    } 

    // Function to add menu page
    public function add_visitor_menu_page() {
        add_menu_page(
            'Doremon Visitor Count',                         // Page title
            'Doremon Visitor Count',                         // Menu title
            'manage_options',                                // Capability required to access
            'doremon-visitor-count',                         // Menu slug
            array($this,'display_visitor_menu_page'),        // Callback function to display page content
            'dashicons-chart-bar',                           // Icon (optional)
        );
    }
    // Callback function to display menu page content
    public function display_visitor_menu_page() {
        require "page.view.php";
    }
}

$doremonvisitor = new DoremonVisitorCount();