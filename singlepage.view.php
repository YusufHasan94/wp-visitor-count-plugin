<style>
    .singlepage-main-container{
        margin: 50px 50px 50px 10px;
    }
    table, td, th{
        border: 1px solid black;
    }
    th, td{
        font-size: 18px;
        padding: 4px;
    }
</style>

<div class="singlepage-main-container">
    <h1 style="text-align:center;">
        Single Page view count
    </h1>
    <div>
        <table style="width:100%; border-collapse: collapse;"> 
            <thead> 
                <th>Sl</th>
                <th>Title</th>
                <th>visitor</th>
            </thead>
            <tbody>
                <?php
                    echo $this->title_table_content();
                ?>
            </tbody>
        </table>
    </div>
</div>