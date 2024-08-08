<?php
    $dailyVisitorCounts = $this->get_daily_view_count();
    $dataPoints = array();

    foreach($dailyVisitorCounts  as $count){
        $dataPoints[] = array(
            "x" => strtotime($count['date'])*1000, 
            "y" => $count['visitor_count']
        );
    };

    usort($dataPoints, function($a, $b){
        return $b['x'] - $a['x'];
    });

    $sortedDataPoints = $dataPoints; 

    $itemsPerPage = 5;
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $totalPages = ceil(count($sortedDataPoints)/$itemsPerPage);
    $startIndex = ($page-1) * $itemsPerPage;
    $currentDataPoints = array_slice($sortedDataPoints, $startIndex, $itemsPerPage);

?>
<style>
    .doremon-page-view-counter-main{
        margin: 50px 50px 50px 10px;
    }
    .doremon-counter-main-container{
        display: flex;
        justify-content: space-between;
        margin-left: 10px;
    }
    .doremon-counter-label-container{
        width: 50%;
        padding: 20px 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
    }
    .doremon-counter-container{
        width: 200px;
        color: white;
        padding: 24px;
        border-radius: 20px;  
    }
    .doremon-counter-total-visitor{
        background-color: #457F4F;
    }
    .doremon-counter-today-visitor{
        background-color: #3974DB;
    }
    .doremon-counter-container h1{
        display: flex;
        flex-direction: column-reverse;
        gap: 10px;
        color:white;
        text-align: end;
    }
    .doremon-counter-table-container{
        width: 50%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    } .doremon-counter-table-container h1{
        text-transform: capitalize;
    }
    .doremon-counter-table-container table{
        width: 100%; 
        font-size: 22px;
        border-collapse: collapse;
    }
    .doremon-counter-table-container table, tr, th, td{
        border: 1px solid black;
        padding: 5px
    }
    .doremon-counter-table-container tr{
        text-align: center;
    }
    .doremon-counter-table-container .pagination{
        width: 100%;
        margin-top: 10px;
        display: flex;
        justify-content: flex-end;
    }
    .doremon-counter-table-container .pagination form button{
        font-size: 20px;
        border-radius: 5px;
    }
    #chartContainer{
        margin: 50px 0 0 10px;
        min-height: 400px;
    }
    .canvasjs-chart-credit{
        display: none!important;
    }
    .changeSettings{
        margin: 20px 0 20px 20px; 
        display: flex; 
        justify-content: end;
        gap: 30px;
    }
    .changeSettings form{
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
        border: 0.5px solid gray;
        border-radius: 5px;
        padding: 15px 20px; 
        background: white;
        font-size: 16px;
    }
    .changeSettings form h1{
        text-transform: capitalize;
        font-size: 22px;
        font-weight: 500;
    }
    .changeSettings form div{
        display: flex;
        flex-direction: column;
        gap: 10px; 
    }
    .changeSettings form div div{
        display: flex;
        flex-direction: row;
        align-items: center;

    }
    .changeSettings label{
        text-transform: capitalize; 
    }
    .changeSettings .submitBtnContainer{
        width: 100%;
        display: flex;
        align-items: flex-end;
        margin-top: 10px;
    }
    .submitChanges{
        width: fit-content;
        background-color: #29527B;
        color: white;
        padding: 4px;
        border-radius: 5px; 
    }
</style>

<script>
    window.onload = function () {

        var chart = new CanvasJS.Chart("chartContainer", {
            animationEnabled: true,
            exportEnabled: true,
            theme: "light2", // "light1", "light2", "dark1", "dark2"
            title:{
                text: "Statistics"
            },
            axisX:{
                title: "Date",
                valueFormatString: "DD MMM YY"
            },
            axisY:{
                title: "Visitor",
                includeZero: true
            },
            data: [{
                type: "column",
                xValueType: "dateTime",
                dataPoints: <?php echo json_encode($dataPoints); ?>
            }]
        });
        chart.render();

    }
</script>


<div class="doremon-page-view-counter-main">
    <h1>
        Doremon View Counter
    </h1>
    <div class="changeSettings">
        <form action="" method="POST">
            <h1>
                show view count
            </h1>
            <div>
                <div>
                    <input type="checkbox" name="handlePagesTitleCheckbox" id="" <?php echo $pagesTitleChecked ?>>
                    <label for="handlePagesTitleCheckbox">after pages title</label>
                </div>
                <div>
                    <input type="checkbox" name="handleShowInHomePage" id="" <?php echo $showInHomePageChecked ?> >
                    <label for="handleShowInHomePage">allow to show in homepage</label>
                </div>
                <div>
                    <input type="checkbox" name="handlePostsTitleCheckbox" id="" <?php echo $postsTitleChecked ?> >
                    <label for="handlePostsTitleCheckbox">after posts title</label>
                </div>
                <div>
                    <input type="checkbox" name="handlePagesCheckbox" id="" <?php echo $pagesChecked ?>>
                    <label for="handlePagesCheckbox">pages list (Admin dashboard)</label>
                </div>
                <div>
                    <input type="checkbox" name="handlePostsCheckbox" id="" <?php echo $postsChecked ?>>
                    <label for="handlePostsCheckbox">posts list (Admin dashboard)</label>
                </div>
            </div>
            <div class="submitBtnContainer">
                <input type="submit" value="Save Changes" name="submit" class="submitChanges">
            </div>
        </form>        
    </div>
    
    <div class="doremon-counter-main-container">
        <div class="doremon-counter-label-container">
            <div class="doremon-counter-container doremon-counter-today-visitor">
                <h1>
                    <span>
                        Today View
                    </span>
                    <span>
                        <?php
                            if(!empty($dailyVisitorCounts)){
                                $date = date('Y-m-d');
                                $todayVisitor = array_search($date, array_column($dailyVisitorCounts, 'date'));
                                if($todayVisitor !== false){
                                    $todayVisitor = $dailyVisitorCounts[$todayVisitor]['visitor_count'];
                                    if(is_array($todayVisitor)){
                                        $todayVisitor = implode(", ", $todayVisitor);
                                        echo $todayVisitor;
                                    }
                                    else{
                                        echo $todayVisitor;
                                    }
                                }else{
                                    $todayVisitor = 0;
                                    echo $todayVisitor;
                                }
                            }
                        ?>
                    </span>
                </h1>
            </div>
    
            <div class="doremon-counter-container doremon-counter-total-visitor">
                <h1>
                    <span>
                        Total View
                    </span>
                    <span>
                        <?= $this->get_total_view_count(); ?>
                    </span>
                </h1>
            </div>
        </div>
        <div class="doremon-counter-table-container">
            <h1>Website View Table</h1>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Visitor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($currentDataPoints as $dataPoint):
                        $date = date('Y-m-d', $dataPoint['x'] / 1000);?>
                        <tr>
                            <td><?= $date?></td>
                            <td><?= is_array($dataPoint['y']) ? implode(", ", $dataPoint['y']) : $dataPoint['y']?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="pagination">
                <form method="POST">
                    <button type="submit" name="page" class="prev-btn" value="<?= $page - 1?>" <?= ($page == 1)? "disabled":"" ?>>&#10508;</button>
                    <button type="submit" name="page" class="next-btn" value="<?= $page + 1 ?>" <?= ($page == $totalPages)? "disabled":"" ?>>&#10509;</button>
                </form>
            </div>
        </div>
    </div>
    <div id="chartContainer"></div>
    
</div>
<script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>