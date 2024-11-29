<h1>
    Recent View Activities
</h1>
<?php
    $recent_views = get_option('recent_view_activities', array());
    
    $itemsPerPage = 15;
    $page = isset(($_POST['page']))?(int)$_POST['page']:1;
    $totalPages = ceil(count($recent_views)/$itemsPerPage);
    $startIndex = ($page-1)*$itemsPerPage;
    $updatedPageData = array_reverse($recent_views);
    $currentPageData = array_slice($updatedPageData, $startIndex, $itemsPerPage);

    

    if (!empty($currentPageData)) {
        echo '<table class="widefat fixed activity_table">';
        echo '<thead><tr><th>View Time</th><th>IP Address</th><th>Location</th><th>Page Title</th><th>View Count</th></tr></thead>';
        echo '<tbody>';

        foreach ($currentPageData as $view) {
            echo '<tr>';
            echo '<td>' . esc_html($view['view_time']) . '</td>';
            echo '<td>' . esc_html($view['ip_address']) . '</td>';
            echo '<td>' . esc_html($view['city']) . ' ' . esc_html($view['country'])  . '</td>';
            echo '<td>' . esc_html(get_the_title($view['post_id'])) . '</td>';
            echo '<td>' . esc_html($view['view_count']) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        ?>
            <form method="POST" style="margin-top: 20px; display: flex; justify-content: flex-end;">
                <div>
                    <button type="submit" name="page" class="prev-btn" style="cursor: pointer;" value="<?= $page - 1?>" <?= ($page == 1)? "disabled":"" ?>>&#10508;</button>
                    <button type="submit" name="page" class="next-btn" style="cursor: pointer;" value="<?= $page + 1 ?>" <?= ($page == $totalPages)? "disabled":"" ?>>&#10509;</button>
                </div>
            </form>
        <?php
    } else {
        echo '<p>No recent view activities found.</p>';
    }
?>