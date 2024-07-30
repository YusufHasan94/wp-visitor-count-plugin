<?php
/*
Plugin Name: Doremon view count
Description: Count view using simple doremon view plugin.
Version: 1.0.0
Author: Yusuf
*/

class DoremonviewCount{
    public function __construct(){
        add_action( 'wp', array($this, 'increment_view_count'));
        add_action('wp', array($this, 'track_page_view'));
        add_action( 'admin_menu', array($this, 'add_view_menu_page'));
            
        add_action('init', array($this, 'handle_pages_request'));
        add_action('init', array($this, 'handle_posts_request'));
        
    }

    public function handle_pages_request(){
        add_filter('manage_pages_columns', array($this, 'add_viewcount_page_column'));
        add_action('manage_pages_custom_column', array($this, 'populate_viewcount_page_column'), 10, 2);  
    }

    public function handle_posts_request(){
        add_filter('manage_posts_columns', array($this, 'add_viewcount_post_column'));
        add_action('manage_posts_custom_column', array($this, 'populate_viewcount_post_column'), 10, 2);
    }

    // increment view count
    public function increment_view_count() {
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
    // display view count
    public function get_total_view_count() {
        $count = get_option( 'visitor_count', 0 );
        return $count;
    }

    // fetching daily view count
    public function get_daily_view_count(){
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
    public function track_page_view(){
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

    //display page wise number of view
    public function display_page_view() {
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

    //display all posts and pages title
    public function title_table_content() {
        global $wpdb; 
        $args = array(
            'post_type' => array('page', 'post'),  // Fetch both pages and posts
            'posts_per_page' => 20,  // Number of titles to display
        );
        $query = new WP_Query($args);

        if ($query->have_posts()) {
            $count = 0;
                while ($query->have_posts()) {
                    $query->the_post();
                    ++$count;
                    $view_count = $this->display_page_view();
                    ?>
                        <tr>
                            <td style="text-align:center;"><?=$count ?></td>
                            <td><?=get_the_title()?></td>
                            <td style="text-align:center;"><?=$view_count?></td>
                        </tr>
                    <?php
                }
            wp_reset_postdata();  // Restore original post data
        } else {
            echo 'No posts/pages found.';
        }
    }

    // adding view count column at pages page. 
    public function add_viewcount_page_column($columns){
        $columns['visitor_count'] = __('view count', 'doremon-view-count');
        return $columns;
    }  

    // display view count for each page
    public function populate_viewcount_page_column($column,$post_id){
        if($column == 'visitor_count'){
            $count = get_post_meta($post_id, 'page_visits', true);
            echo $count?$count:0;
        }
    }
    
    // adding view count column at posts page.
    public function add_viewcount_post_column($columns){
        $columns['visitor_count'] = __('view count', 'doremon-view-count');
        return $columns;
    }  

    // display view count for each post
    public function populate_viewcount_post_column($column, $post_id){
        if($column == 'visitor_count'){
            $count = get_post_meta($post_id, 'page_visits', true);
            echo $count? $count:0;
        }
    }  

    // Function to add menu page
    public function add_view_menu_page() {
        add_menu_page(
            __('Doremon View Count', 'doremon-view-count'),              // Page title
            __('Doremon View Count', 'doremon-view-count'),              // Menu title
            'manage_options',                                            // Capability required to access
            'doremon_view_count_menu',                                   // Menu slug
            array($this,'display_view_general_page'),                    // Callback function to display page content
            'dashicons-chart-bar',                                       // Icon (optional)
            4                                                            //priority    
        );
        add_submenu_page(
            'doremon_view_count_menu',                                    // parent slug
            __('Single page view', 'doremon-view-count'),                 // Page title
            __('Single page view', 'doremon-view-count'),                 // Menu title
            'manage_options',                                             // Capability required to access
            'single_page_view',                                           // Menu slug
            array($this,'display_singular_view_page'),                    // Callback function to display page content
        );
    }

    // Callback function to display menu page content
    public function display_view_general_page() {
        $pagesChecked = "";
        $postsChecked = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['submit'])) {
                if (isset($_POST['handlePagesCheckbox'])) {
                    echo "<br>tring to visible<br>";
                    $pagesChecked = "checked";
                    // add_action('init', array($this, 'handle_pages_request'));
                } else {
                    echo "not checked<br>";
                    
                }
                
                if (isset($_POST['handlePostsCheckbox'])) {
                    echo "<br>tring to visible on posts<br>";
                    $postsChecked = "checked";
                    // $this->handle_post_request();
                } else {
                    echo "posts not checked<br>";
                    
                }
                
            }
        }
        require "page.view.php"; 
    }

    //callback function to display single page view content
    public function display_singular_view_page() {
        require "singlepage.view.php";
    }

}

$doremonview = new DoremonviewCount();