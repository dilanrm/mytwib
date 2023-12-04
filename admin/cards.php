<?php require_once '../function.php' ?>
<?php require_once '../conn.php' ?>
<link rel="stylesheet" href="cards.css">
<div class="container my-4 pb-3">
    <div class="row">
        <div class="col-md-3">
            <div class="card-counter primary">
                <i class="fa fa-database"></i>
                <span class="count-numbers"><?php echo all_views($conn) ?></span>
                <span class="count-name">Total Visitiors</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-counter danger">
                <i class="fa fa-user"></i>
                <span class="count-numbers"><?php echo total_views($conn, 5) ?></span>
                <span class="count-name">Unique Visitors</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-counter success">
                <i class="fa fa-user-group"></i>
                <span class="count-numbers"><?php echo all_views($conn, 'DATE') ?></span>
                <span class="count-name">Today Visitors</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-counter info">
                <i class="fa fa-users"></i>
                <span class="count-numbers"><?php echo all_views($conn, 'MONTH') ?></span>
                <span class="count-name">This Month Visitors</span>
            </div>
        </div>
    </div>
</div>