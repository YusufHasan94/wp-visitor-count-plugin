<?php
    $dailyVisitorCounts = $this->get_daily_visitor_count();
    $dataPoints = array();

    foreach($dailyVisitorCounts  as $count){
        $date = strtotime($count['date'])*1000;
        $dataPoints[] = array(
            "x" => $date, 
            "y" => $count['visitor_count']
        );
    }

?>
<style>
    .doremon-counter-main-container{
        display: flex;
        justify-content: space-between;
        margin: 0 50px;
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
    #chartContainer{
        margin: 50px 100px 100px 100px;
        min-height: 400px;
    }
    .canvasjs-chart-credit{
        display: none!important;
    }
</style>

<script>
    window.onload = function () {

        var chart = new CanvasJS.Chart("chartContainer", {
            animationEnabled: true,
            exportEnabled: true,
            theme: "light2", // "light1", "light2", "dark1", "dark2"
            title:{
                text: "Visitor Statistics"
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


<div>
    <h1>
        Doremon Visitor counter
    </h1>
    <div class="doremon-counter-main-container">
        <div class="doremon-counter-label-container">
            <div class="doremon-counter-container doremon-counter-today-visitor">
                <h1>
                    <span>
                        Today Visitor
                    </span>
                    <span>
                        <?php
                            if(!empty($dailyVisitorCounts)){
                                $date = date('Y-m-d');
                                $todayVisitor = array_search($date, array_column($dailyVisitorCounts, 'date'));
                                if($todayVisitor !== false){
                                    $todayVisitor = $dailyVisitorCounts[$todayVisitor]['visitor_count'];
                                    echo $todayVisitor;
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
                        Total Visitor
                    </span>
                    <span>
                        <?= $this->get_total_visitor_count(); ?>
                    </span>
                </h1>
            </div>
        </div>
        <div class="doremon-counter-table-container">
            <h1>visitor Table</h1>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Visitor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($dailyVisitorCounts as $dailyCount):?>
                        <tr>
                            <td><?=$dailyCount['date'];?></td>
                            <td><?=$dailyCount['visitor_count'];?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div id="chartContainer"></div>
    <div>
        <?php
            echo $this->title_table_content();
        ?>
    </div>
</div>
<script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>


