<?PHP
	require 'includes/master.inc.php';
	$Auth->requireAdmin('login.php');
	$nav = 'stats';

	$db = Database::getDatabase();

	$applications = DBObject::glob('Application', 'SELECT * FROM shine_applications ORDER BY name');

	$chart_app_activity = new Chart();
	$chart_app_activity->id          = 'chart_app_activity';
	$chart_app_activity->type        = 'column';
	$chart_app_activity->title       = 'App Activity by Week';
	$chart_app_activity->yAxisTitle  = '# Sparkle Updates';
	$chart_app_activity->xColumnName = 'YEARWEEK(dt)';
	$chart_app_activity->yColumnName = 'COUNT(*)';
	$chart_app_activity->query       = 'SELECT COUNT(*), YEARWEEK(dt) FROM `shine_sparkle_reports` GROUP BY YEARWEEK(dt) ORDER BY YEARWEEK(dt) ASC';

	Class Chart
	{
		public $id;
		public $type;
		public $title;
		public $xColumnName;
		public $yColumnName;
		public $query;
		public $appID;
		public $yAxisTitle;

		private $data;

		public function run()
		{
			$db = Database::getDatabase();
			$rows = $db->getRows($this->query);

			$this->data = array();
			foreach($rows as $row)
			{
				$x = $row[$this->xColumnName];
				$y = $row[$this->yColumnName];
				$this->data[$x] = $y;
			}
		}

		public function render()
		{
			$this->run();

			$categories = array_keys($this->data);
			$categories = "'" . implode(',', $categories) . "'";
			$data = implode(',', $this->data);


			$out1  = "var barChartData3 = {\n"; 
			$out1 .= "		labels : [$data],\n"; 
			$out1 .=  "		datasets : [\n"; 
			$out1 .=  "			{\n"; 
			$out1 .=  "				fillColor : \"rgba(220,220,220,0.5)\",\n"; 
			$out1 .=  "				strokeColor : \"rgba(220,220,220,0.8)\",\n"; 
			$out1 .=  "				highlightFill: \"rgba(220,220,220,0.75)\",\n"; 
			$out1 .=  "				highlightStroke: \"rgba(220,220,220,1)\",\n"; 
			$out1 .=  "				data : [$data]\n"; 
			$out1 .=  "			}\n"; 
			$out1 .=  "		]\n"; 
			$out1 .=  "\n"; 
			$out1 .=  "	}\n";

			$out  = "var ctx3 = document.getElementById(\"canvas3\").getContext(\"2d\");";
			$out .= "window.myBar3 = new Chart(ctx3).Bar(barChartData3, {";
			$out .= "responsive : true";
			$out .= "});";

			echo $out1;
			echo $out;
		}
	}
?>
<?PHP include('inc/header.inc.php'); ?>


<div class="row">
<div class="col-lg-12">

 <h1 class="page-header">Sparkle Stats</h1>

<ul class="nav nav-pills">
    <?PHP if(!isset($_GET['id'])): ?>
        <li class="nav-link"><a class="nav-link active" href="stats.php">All Apps</a></li>
    <?php else: ?>
        <li class="nav-link"><a class="nav-link" href="stats.php">All Apps</a></li>
    <?php endif; ?>
    <?PHP foreach($applications as $a) : ?>
        <?PHP if(@$_GET['id'] == $a->id): ?>
            <li class="nav-link"><a class="nav-link active" href="stats.php?id=<?PHP echo $a->id; ?>"><?PHP echo $a->name; ?></a></li>
        <?php else: ?>
            <li class="nav-link"><a class="nav-link" href="stats.php?id=<?PHP echo $a->id; ?>"><?PHP echo $a->name; ?></a></li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>

</div>

</div>

<br><br>

<div class="row">
<div class="col-lg-12">
                   <div class="card">
                        <div class="card-header">
                            <i class="fa fa-bar-chart-o fa-fw"></i> Stats
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row" id="chart" style="margin: 20px;" >

				<canvas id="canvas3"></canvas>

                                <!-- /.col-lg-8 (nested) -->
                            </div>

</div>

	</div>
<?PHP include('inc/footer.inc.php'); ?>

<script type="text/javascript" charset="utf-8">
var chartwidth = $('#chart').width();
var options = {
        scaleFontColor: "#fa0",
        datasetStrokeWidth: 1,
        scaleShowLabels : false,
        animation : false,
        bezierCurve : true,
        scaleStartValue: 0,
	showXLabels: 1,
    };

	$(document).ready(function() {
		<?PHP $chart_app_activity->render(); ?>
	});
</script>
