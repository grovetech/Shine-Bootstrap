<?PHP
	require 'includes/master.inc.php';
	$Auth->requireAdmin('login.php');
        $db = Database::getDatabase();
	$nav = 'applications';

        // Create a new application if needed
	if(isset($_POST['btnNewApp']) && strlen($_POST['name']))
	{
		$a = new Application();
		$a->name = $_POST['name'];
		$a->insert();
		redirect('application.php?id=' . $a->id);
	}


	// Get a list of our apps
	$apps   = DBObject::glob('Application', 'SELECT * FROM shine_applications WHERE hidden = 0 ORDER BY name');

	// Get our recent orders
	$orders = DBObject::glob('Order', 'SELECT * FROM shine_orders ORDER BY dt DESC LIMIT 10');

	// Downloads in last 24 hours
	$sel = "TIME_FORMAT(dt, '%Y%m%d%H')";
	$order_totals    = $db->getRows("SELECT $sel as dtstr, COUNT(*) FROM shine_downloads WHERE  DATE_ADD(dt, INTERVAL 24 HOUR) > NOW() GROUP BY dtstr ORDER BY $sel ASC");
	$opw24           = new googleChart(implode(',', gimme($order_totals, 'COUNT(*)')), 'bary');
	$opw24->showGrid   = 1;
	$opw24->dimensions = '280x100';
	$opw24->setLabelsMinMax(4,'left');
	$opw24_fb = clone $opw24;
	$opw24_fb->dimensions = '640x400';

	// Downloads in last 30 days
	$sel = "TO_DAYS(dt)";
	$order_totals2    = $db->getRows("SELECT $sel as dtstr, COUNT(*) FROM shine_downloads WHERE DATE_ADD(dt, INTERVAL 30 DAY) > NOW() GROUP BY $sel ORDER BY $sel ASC");
	$opw30           = new googleChart(implode(',', gimme($order_totals, 'COUNT(*)')), 'bary');
	$opw30->showGrid   = 1;
	$opw30->dimensions = '280x100';
	$opw30->setLabelsMinMax(4,'left');
	$opw30_fb = clone $opw30;
	$opw30_fb->dimensions = '640x400';
?>
<?PHP include('inc/header.inc.php'); ?>



<div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header"> Dashboard</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
             <div class="row">
                <div class="col-lg-12 margin-bottom-10">
                    <div class="card">
                        <div class="card-header">
                            Your Applications
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Current Version</th>
                                            <th>Last Release Date</th>
                                            <th>Downloads / Updates</th>
					    					  <th>Support Questions</th>
                                            <th>Bug Reports</th>
                                            <th>Feature Requests</th>
                                        </tr>
                                    </thead>
                                    <tbody>

<?PHP foreach($apps as $a) : ?>

                                        <tr>
                                            <td><a href="application.php?id=<?PHP echo $a->id;?>"><?PHP echo $a->name; ?></a></td>
                                            <td><?PHP echo $a->strCurrentVersion(); ?></td>
                                            <td><?PHP echo $a->strLastReleaseDate(); ?></td>
                                            <td><a href="versions.php?id=<?PHP echo $a->id; ?>"><?PHP echo number_format($a->totalDownloads()); ?></a> / <a href="versions.php?id=<?PHP echo $a->id; ?>"><?PHP echo number_format($a->totalUpdates()); ?></a></td>
                                            <td><?PHP echo $a->numSupportQuestions(); ?></td>
                                            <td><?PHP echo $a->numBugReports(); ?></td>
                                            <td><?PHP echo $a->numFeatureRequests(); ?></td>
                                        </tr>
<?PHP endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>






                <div class="col-lg-12 margin-bottom-10">
                    <div class="card">
                        <div class="card-header">
                            Recent Orders (<?PHP echo number_format(Order::totalOrders()); ?> total)
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>App Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?PHP foreach($orders as $o) : ?>
        							<tr>
        							    <td><?PHP echo time2str($o->dt); ?></td>
        							    <td><a href="order.php?id=<?PHP echo $o->id; ?>"><?PHP echo utf8_encode($o->first_name); ?> <?PHP echo utf8_encode($o->last_name); ?></a></td>
        							    <td><a href="mailto:<?PHP echo $o->payer_email; ?>"><?PHP echo $o->payer_email; ?></a></td>
        							    <td><?PHP echo $o->applicationName(); ?></td>
        							</tr>
        							<?PHP endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>

        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <i class="fa fa-bar-chart-o fa-fw"></i> Downloads 30 Days
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                     <div class="row" syle="margin: 20px;">

                   			 <canvas id="canvas"></canvas>

                                        <!-- /.col-lg-8 (nested) -->
                                    </div>

                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->


        </div>


        <div class="col-lg-6">
              <div class="card">
                     <div class="card-header">
                          <i class="fa fa-bar-chart-o fa-fw"></i> Downloads 24 Hours
                      </div>
                                <!-- /.card-header -->
                      <div class="card-body">
                            <div class="row" style="margin: 20px;">
			    	<canvas id="canvas2"></canvas>
                            </div>
		      </div>
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


var barChartData2 = {
		labels : [<?PHP print_r(implode(',', gimme($order_totals, 'COUNT(*)'))); ?>],
		datasets : [
			{
				fillColor : "rgba(220,220,220,0.5)",
				strokeColor : "rgba(220,220,220,0.8)",
				highlightFill: "rgba(220,220,220,0.75)",
				highlightStroke: "rgba(220,220,220,1)",
				data : [<?PHP print_r(implode(',', gimme($order_totals, 'COUNT(*)'))); ?>]
			}
		]

	}

	var barChartData = {
		labels : [<?PHP print_r(implode(',', gimme($order_totals2, 'COUNT(*)'))); ?>],
		datasets : [
			{
				fillColor : "rgba(220,220,220,0.5)",
				strokeColor : "rgba(220,220,220,0.8)",
				highlightFill: "rgba(220,220,220,0.75)",
				highlightStroke: "rgba(220,220,220,1)",
				data : [<?PHP print_r(implode(',', gimme($order_totals2, 'COUNT(*)'))); ?>]
			}
		]

	}

window.onload = function(){
		var ctx = document.getElementById("canvas").getContext("2d");
		window.myBar = new Chart(ctx).Bar(barChartData, {
			responsive : true
		});

var ctx2 = document.getElementById("canvas2").getContext("2d");
		window.myBar2 = new Chart(ctx2).Bar(barChartData2, {
			responsive : true
		});
	}

</script>
