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
        add_action('wp', array($this, 'track_page_visitor'));
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
        foreach($dailyCounts as $date=>$count){
            if($date === $today){
                continue;
            }
            $totalDayCounts [] = array(
                'date' => $date,
                'visitor_count'=>$count
            );
        }
        return $totalDayCounts;
    } 

    //allow to count single page view
    public function track_page_visitor(){
        if(is_singular()){
            global $post;
            $post_id = $post->ID;
            $count = get_post_meta($post_id, 'page_visits', true); 
            $count = (int)$count;
            if($count){
                $count++;
                update_post_meta($post_id, 'page_visits', $count);
            }else{
                add_post_meta($post_id, 'page_visits', 1, true);
            }
        }
    }

    public function display_page_visitor() {
        global $post;
        if ( isset($post->ID) ) {
            $post_id = $post->ID;
            $count = get_post_meta($post_id, 'page_visits', true);
            if ( ! empty($count) ) {
                return (int)$count;
            } else {
                return 0;
            }
        }
    } 

    public function title_table_content() {
        global $wpdb; 
        $args = array(
            'post_type' => array('page', 'post'),  // Fetch both pages and posts
            'posts_per_page' => 20,  // Number of titles to display
        );
        $query = new WP_Query($args);

        if ($query->have_posts()) {
            $count = 0;
            ?>
                <table style="width:100%; border-collapse: collapse;"> 
                    <thead> 
                        <th>Sl</th>
                        <th>Title</th>
                        <th>visitor</th>
                    </thead>
                    <tbody>
            <?php
                    while ($query->have_posts()) {
                        $query->the_post();
                        ++$count;
                        $visitor_count = $this->display_page_visitor();
                        ?>
                            <tr>
                                <td style="text-align:center;"><?=$count ?></td>
                                <td><?=get_the_title()?></td>
                                <td style="text-align:center;"><?=$visitor_count?></td>
                            </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            <?php
            wp_reset_postdata();  // Restore original post data
        } else {
            echo 'No posts/pages found.';
        }
    }


    // Function to add menu page
    public function add_visitor_menu_page() {
        add_menu_page(
            __('Doremon View Count', 'doremon-visitor-count'),              // Page title
            __('Doremon View Count', 'doremon-visitor-count'),              // Menu title
            'manage_options',                                               // Capability required to access
            'doremon_view_count_menu',                                      // Menu slug
            array($this,'display_visitor_general_page'),                    // Callback function to display page content
            'dashicons-chart-bar',                                          // Icon (optional)
            4                                                               //priority    
        );
        add_submenu_page(
            'doremon_view_count_menu',                                       // parent slug
            __('Single page view', 'doremon-visitor-count'),                 // Page title
            __('Single page view', 'doremon-visitor-count'),                 // Menu title
            'manage_options',                                                // Capability required to access
            'single_page_view',                                              // Menu slug
            array($this,'display_singular_view_page'),                       // Callback function to display page content
        );
    }
    // Callback function to display menu page content
    public function display_visitor_general_page() {
        require "page.view.php";
    }
    public function display_singular_view_page() {
        require "singlepage.view.php";
    }

}

$doremonvisitor = new DoremonVisitorCount();