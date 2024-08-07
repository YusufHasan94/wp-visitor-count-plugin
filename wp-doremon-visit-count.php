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
        add_action('init', array($this, 'handle_settings_changes'));
    }


    // show view count settings part start here
    public function handle_settings_changes() {
        $showPages = get_option('doremon_show_pages_view_count', false);
        $showPosts = get_option('doremon_show_posts_view_count', false);
        
        $showCountWithTitleInPages = get_option('display_view_count_pages_with_title', false);
        $showCountInHomePage = get_option('display_view_count_home_page', false);
        $showCountWithTitleInPosts = get_option('display_view_count_posts_with_title', false);

        if ($showPages) {
            $this->handle_pages_request();
        }

        if ($showPosts) {
            $this->handle_posts_request();
        }
        if($showCountWithTitleInPages){
            $this->handle_show_count_pages_with_title();
        }
        if($showCountInHomePage){
            $this->handle_show_count_home_page();
        }
        if($showCountWithTitleInPosts){
            $this->handle_show_count_posts_with_title();
        }
    }

    public function handle_pages_request() {
        add_filter('manage_pages_columns', array($this, 'add_viewcount_page_column'));
        add_action('manage_pages_custom_column', array($this, 'populate_viewcount_page_column'), 10, 2);
    }

    public function handle_posts_request() {
        add_filter('manage_posts_columns', array($this, 'add_viewcount_post_column'));
        add_action('manage_posts_custom_column', array($this, 'populate_viewcount_post_column'), 10, 2);
    }

    public function handle_show_count_pages_with_title(){
        add_filter('the_title', array($this, 'display_view_count_pages_with_title'), 10, 2);
    }
    public function handle_show_count_home_page(){
        add_filter('the_title', array($this, 'display_view_count_home_page'), 10, 2);
    }

    public function handle_show_count_posts_with_title(){
        add_filter('the_title', array($this, 'display_view_count_posts_with_title'), 10, 2);
    }

    public function display_view_count_pages_with_title($title, $id=null){
        if(is_singular('page') && ($title != "Home")){
            if(!$id){
                global $post;
                $id = $post->ID;
            }
            $view = get_post_meta($id, 'page_visits', true);
            if($view){
                $title =  "<h1 style='visibility:visible;'> $title ($view views)</h1>" ;
                
            }
        }
        return $title;
    }
    
    public function display_view_count_home_page($title, $id=null){
        if(is_singular('page') && ($title=="Home")){
            if(!$id){
                global $post;
                $id = $post->ID;
            }
            $view = get_post_meta($id, 'page_visits', true);
            if($view){
                $title =  "<h1 style='visibility:visible;'> $title ($view views)</h1>" ;
            }
        }
        return $title;
    }
    
    public function display_view_count_posts_with_title($title, $id=null){
        if(is_singular('post')){
            if(!$id){
                global $post;
                $id = $post->ID;
            }
            $view = get_post_meta($id, 'page_visits', true);
            if($view){
                $title =  "<h1 style='visibility:visible;'> $title ($view views)</h1>" ;                 
            }
        }
        return $title;
    }

    public function add_viewcount_page_column($columns){
        $columns['visitor_count'] = __('View Count', 'doremon-view-count');
        return $columns;
    }  

    public function populate_viewcount_page_column($column,$post_id){
        if($column == 'visitor_count'){
            $count = get_post_meta($post_id, 'page_visits', true);
            echo $count?$count:0;
        }
    }
    
    public function add_viewcount_post_column($columns){
        $columns['visitor_count'] = __('View Count', 'doremon-view-count');
        return $columns;
    }  

    public function populate_viewcount_post_column($column, $post_id){
        if($column == 'visitor_count'){
            $count = get_post_meta($post_id, 'page_visits', true);
            echo $count? $count:0;
        }
    }  

    public function save_settings_changes() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['submit'])) {
                update_option('doremon_show_pages_view_count', isset($_POST['handlePagesCheckbox']) ? true : false);
                update_option('doremon_show_posts_view_count', isset($_POST['handlePostsCheckbox']) ? true : false);
                update_option('display_view_count_pages_with_title', isset($_POST['handlePagesTitleCheckbox']) ? true : false);
                update_option('display_view_count_home_page', isset($_POST['handleShowInHomePage']) ? true : false);
                update_option('display_view_count_posts_with_title', isset($_POST['handlePostsTitleCheckbox']) ? true : false);
            }
        }
    }
    // show view count settings part end here

    //counting & display view starts here
    public function increment_view_count() {
        if (! is_admin() ) {
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

    public function get_total_view_count() {
        $count = get_option( 'visitor_count', 0 );
        return $count;
    }

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

    // track single page view 
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
            'post_type' => array('page', 'post'),  
            'posts_per_page' => 20, 
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
            wp_reset_postdata();  
        } else {
            echo 'No posts/pages found.';
        }
    }

    // display dashboard main page
    public function display_view_general_page() {
        $this->save_settings_changes();

        $pagesChecked = get_option('doremon_show_pages_view_count', false) ? "checked" : "";
        $postsChecked = get_option('doremon_show_posts_view_count', false) ? "checked" : "";
        $pagesTitleChecked = get_option('display_view_count_pages_with_title', false) ? "checked" : "";
        $showInHomePageChecked = get_option('display_view_count_home_page', false) ? "checked" : "";
        $postsTitleChecked = get_option('display_view_count_posts_with_title', false) ? "checked" : "";
            
        require "page.view.php"; 
    }

    // display dashboard table page
    public function display_singular_view_page() {
        require "singlepage.view.php";
    }
    
    // add menu page
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
            __('Single Page View', 'doremon-view-count'),                 // Page title
            __('Single Page View', 'doremon-view-count'),                 // Menu title
            'manage_options',                                             // Capability required to access
            'single_page_view',                                           // Menu slug
            array($this,'display_singular_view_page'),                    // Callback function to display page content
        );
    }
}

$doremonview = new DoremonviewCount();