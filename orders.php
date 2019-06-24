<?PHP
	require 'includes/master.inc.php';
	$Auth->requireAdmin('login.php');
	$nav = 'orders';

	$applications = DBObject::glob('Application', 'SELECT * FROM shine_applications ORDER BY name');

	$db = Database::getDatabase();
	
	if(isset($_GET['q']))
	{
		$q = $_GET['q'];
		$_q = $db->escape($q);
		$search_sql = " AND (first_name LIKE '%$_q%' OR last_name LIKE '%$_q%' OR payer_email LIKE '%$_q%') ";
	}
	else
	{
		$q = '';
		$search_sql = '';
	}

	if(isset($_GET['id']) && !empty($_GET['id']))
	{
		$app_id = intval($_GET['id']);
		$total_num_orders = $db->getValue("SELECT COUNT(*) FROM shine_orders WHERE app_id = $app_id $search_sql ORDER BY dt DESC");
		$pager = new Pager(@$_GET['page'], 100, $total_num_orders);
		$orders = DBObject::glob('Order', "SELECT * FROM shine_orders WHERE app_id = $app_id $search_sql ORDER BY dt DESC LIMIT {$pager->firstRecord}, {$pager->perPage}");
		$where = " AND app_id = $app_id ";
		$app_name = $applications[$app_id]->name;
	}
	else
	{
		$total_num_orders = $db->getValue("SELECT COUNT(*) FROM shine_orders WHERE 1 = 1 $search_sql ");
		$pager = new Pager(@$_GET['page'], 100, $total_num_orders);
		$orders = DBObject::glob('Order', "SELECT * FROM shine_orders WHERE 1 = 1 $search_sql ORDER BY dt DESC LIMIT {$pager->firstRecord}, {$pager->perPage}");
		$where = '';
		$app_name = 'All';
	}

	$available_apps = $db->getValues("SELECT app_id FROM shine_orders GROUP BY app_id");

	// Orders Per Month
	$order_totals_month    = $db->getRows("SELECT DATE_FORMAT(dt, '%b') as dtstr, COUNT(*) FROM shine_orders WHERE type = 'PayPal' $where GROUP BY CONCAT(YEAR(dt), '-', MONTH(dt)) ORDER BY YEAR(dt) ASC, MONTH(dt) ASC");
	$opm             = new googleChart(implode(',', gimme($order_totals_month, 'COUNT(*)')), 'bary');
	$opm->showGrid   = 1;
	$opm->dimensions = '280x100';
	$opm->setLabelsMinMax(4,'left');
	$opm_fb = clone $opm;
	$opm_fb->dimensions = '640x400';

	// Orders Per Week
	$order_totals_week    = $db->getRows("SELECT WEEK(dt) as dtstr, COUNT(*) FROM shine_orders WHERE type = 'PayPal' $where GROUP BY CONCAT(YEAR(dt), WEEK(dt)) ORDER BY YEAR(dt) ASC, WEEK(dt) ASC");
	$opw             = new googleChart(implode(',', gimme($order_totals_week, 'COUNT(*)')), 'bary');
	$opw->showGrid   = 1;
	$opw->dimensions = '280x100';
	$opw->setLabelsMinMax(4,'left');
	$opw_fb = clone $opw;
	$opw_fb->dimensions = '640x400';

	// Orders Per Month Per Application
	$data = array();
	foreach($applications as $app)
		$data[$app->name] = $app->ordersPerMonth();
	$opma = new googleChart();
	$opma->smartDataLabel($data);
	$opma->showGrid   = 1;
	$opma->dimensions = '280x100';
	$opma->setLabelsMinMax(4,'left');
	$opma_fb = clone $opma;
	$opma_fb->dimensions = '640x400';
?>
<?PHP include('inc/header.inc.php'); ?>


<div class="row">
<div class="col-lg-12">

 <h1 class="page-header">Orders</h1>

<ul class="nav nav-pills">
<?PHP if(!isset($_GET['id'])): ?>
    <li class="nav-link"><a class="nav-link active" href="orders.php">All Orders</a></li>
<?php else: ?>
    <li class="nav-link"><a class="nav-link" href="orders.php">All Orders</a></li>
<?php endif; ?>
<?PHP foreach($applications as $a) : if(!in_array($a->id, $available_apps)) continue; ?>
    <?PHP if(@$_GET['id'] == $a->id): ?>
        <li class="nav-link"><a class="nav-link active" href="orders.php?id=<?PHP echo $a->id; ?>"><?PHP echo $a->name; ?></a></li>
    <?php else: ?>
        <li class="nav-link"><a class="nav-link" href="orders.php?id=<?PHP echo $a->id; ?>"><?PHP echo $a->name; ?></a></li>
    <?php endif; ?>
<?PHP endforeach; ?>
</ul>

</div>

</div>
<br>



<div class="row">
                <div class="col-lg-12 margin-bottom-10">
                    <div class="card">
                        <div class="card-header">
                            Your Applications
                        </div>
                        <!-- /.card-header-->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
										<th>Application</th>
										<th>Buyer</th>
										<th>Email</th>
										<th>Type</th>
										<th>Order Date</th>
										<th>Amount</th>
										<th>Edit</th>
                                        </tr>
                                    </thead>
                                    <tbody>

<?PHP foreach($orders as $o) : ?>

                                        <tr>
										<td><?PHP echo $o->applicationName(); ?></td>
										<td><?PHP echo $o->first_name; ?> <?PHP echo $o->last_name; ?></td>
										<td><a href="mailto:<?PHP echo utf8_encode($o->payer_email); ?>"><?PHP echo utf8_encode($o->payer_email); ?></a></td>
										<td><?PHP echo $o->type; ?></td>
										<td><?PHP echo dater($o->dt, 'm/d/Y g:ia') ?></td>
										<td><?PHP echo $o->intlAmount(); ?></td>
										<td><a href="order.php?id=<?PHP echo $o->id; ?>" class="btn btn-sm btn-success">Edit</a></td>
                                        </tr>
<?PHP endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                
<hr>            <!-- /.table-responsive -->
<div class="text-center">
<nav aria-label="Page navigation example">
    <ul class="pagination">
    <li class="page-item"><a class="page-link" href="orders.php?page=<?PHP echo $pager->prevPage(); ?>&amp;id=<?PHP echo @$app_id; ?>">&#171; Prev</a></li>
    <?PHP for($i = 1; $i <= $pager->numPages; $i++) : ?>
    <?PHP if($i == $pager->page) : ?>
    <li class="page-item active"><a class="page-link" href="orders.php?page=<?PHP echo $i; ?>&amp;id=<?PHP echo @$app_id; ?>"><?PHP echo $i; ?></a></li>
    <?PHP else : ?>
    <li class="page-item"><a class="page-link" href="orders.php?page=<?PHP echo $i; ?>&amp;id=<?PHP echo @$app_id; ?>"><?PHP echo $i; ?></a></li>
    <?PHP endif; ?>
    <?PHP endfor; ?>
    <li class="page-item"><a class="page-link" href="orders.php?page=<?PHP echo $pager->nextPage(); ?>&amp;id=<?PHP echo @$app_id; ?>">Next &#187;</a></li>
    </ul>
</nav>
</div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.panel -->
                </div>



<div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-bar-chart-o fa-fw"></i> Weekly Orders
                        </div>
                        <!-- /.panel-heading -->
                        <div class="card-body">
                             <div class="row">
                                <div style="margin: 20px;">

            <canvas id="canvas5"></canvas>

                                </div>
                                <!-- /.col-lg-8 (nested) -->
                            </div>
                    

                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.panel -->
                    


</div>



<div class="col-lg-6">
                   <div class="card">
                        <div class="card-header">
                            <i class="fa fa-bar-chart-o fa-fw"></i> Monthly Orders
                        </div>
                        <!-- /.panel-heading -->
                        <div class="card-body">
                            <div class="row">
                                <div style="margin: 20px;">

            <canvas id="canvas6"></canvas>

                                </div>
                                <!-- /.col-lg-8 (nested) -->
                            </div>
                    


</div>



       

<?PHP include('inc/footer.inc.php'); ?>
<script>
	var options = {
        scaleFontColor: "#fa0",
        datasetStrokeWidth: 1,
        scaleShowLabels : false,
        animation : false,
        bezierCurve : true,
        scaleStartValue: 0,
		showXLabels: 1,
    };


var barChartData5 = {
		labels : [<?PHP print_r(implode(',', gimme($order_totals_week, 'COUNT(*)'))); ?>],
		datasets : [
			{
				fillColor : "rgba(220,220,220,0.5)",
				strokeColor : "rgba(220,220,220,0.8)",
				highlightFill: "rgba(220,220,220,0.75)",
				highlightStroke: "rgba(220,220,220,1)",
				data : [<?PHP print_r(implode(',', gimme($order_totals_week, 'COUNT(*)'))); ?>]
			}
		]

	}

	var barChartData6 = {
		labels : [<?PHP print_r(implode(',', gimme($order_totals_month, 'COUNT(*)'))); ?>],
		datasets : [
			{
				fillColor : "rgba(220,220,220,0.5)",
				strokeColor : "rgba(220,220,220,0.8)",
				highlightFill: "rgba(220,220,220,0.75)",
				highlightStroke: "rgba(220,220,220,1)",
				data : [<?PHP print_r(implode(',', gimme($order_totals_month, 'COUNT(*)'))); ?>]
			}
		]

	}

window.onload = function(){
		var ctx5 = document.getElementById("canvas5").getContext("2d");
		window.myBar5 = new Chart(ctx5).Bar(barChartData5, {
			responsive : true
		});

var ctx6 = document.getElementById("canvas6").getContext("2d");
		window.myBar6 = new Chart(ctx6).Bar(barChartData6, {
			responsive : true
		});
	}

</script>
