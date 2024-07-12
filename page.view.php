<style>
    .doremon-counter-main-container{
        padding: 20px 0;
        display: flex;
        justify-content: center;
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
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    } .doremon-counter-table-container h1{
        text-transform: capitalize;
    }
    .doremon-counter-table-container table{
        width: 50%; 
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
</style>

<div>
    <h1>
        Doremon Visitor counter
    </h1>
    <div class="doremon-counter-main-container">
        <div class="doremon-counter-container doremon-counter-today-visitor">
            <h1>
                <span>
                    Today Visitor
                </span>
                <span>
                    <?php foreach(($this->get_daily_visitor_count()) as $today_visitor)
                        echo $today_visitor['visitor_count'];
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
        <h1>visitor statistics</h1>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Visitor</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach(($this->get_daily_visitor_count()) as $daily_count):?>
                    <tr>
                        <td><?=$daily_count['date'];?></td>
                        <td><?=$daily_count['visitor_count'];?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


