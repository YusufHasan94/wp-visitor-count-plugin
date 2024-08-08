<h1>
    Recent View Activities
</h1>
<?php
    $recent_views = get_option('recent_view_activities', array());

    if (!empty($recent_views)) {
        echo '<table class="widefat fixed">';
        echo '<thead><tr><th>Title</th><th>User Id</th><th>View Time</th><th>View Count</th></tr></thead>';
        echo '<tbody>';

        foreach ($recent_views as $view) {
            echo '<tr>';
            echo '<td>' . esc_html(get_the_title($view['post_id'])) . '</td>';
            // echo '<td>' . esc_html($view['user_id']) . '</td>';
            echo '<td>' . (isset($view['user_id']) ? esc_html($view['user_id']) : 'N/A') . '</td>';
            echo '<td>' . esc_html($view['view_time']) . '</td>';
            echo '<td>' . esc_html($view['view_count']) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>No recent view activities found.</p>';
    }
?>