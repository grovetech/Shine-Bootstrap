<?php
	require 'includes/master.inc.php';
	$Auth->requireAdmin('login.php');
	$nav = 'feedback';

	if(isset($_GET['type']))
	{
		$db = Database::getDatabase();
		$tabType = mysqli_real_escape_string($db->db,$_GET['type']);
		$feedback = DBObject::glob('Feedback', "SELECT * FROM shine_feedback WHERE type = '$tabType' ORDER BY dt DESC");
	}
	else
	{
		$feedback = DBObject::glob('Feedback', "SELECT * FROM shine_feedback ORDER BY dt DESC");
	}
    $tabType = "";
    if (isset($_GET['type'])) {
        $tabType = $_GET['type'];
    }
?>
<?php include('inc/header.inc.php'); ?>
<div class="row">
<div class="col-lg-12">

 <h1 class="page-header">Feedback</h1>

<ul class="nav nav-pills">
    <?php if($tabType==''): ?>
			<li class="nav-link"><a class="nav-link active" href="feedback.php">All Feedback</a></li>
    <?php else: ?>
			<li class="nav-link"><a class="nav-link" href="feedback.php">All Feedback</a></li>
    <?php endif; ?>
    <?php if($tabType=='support'): ?>
            <li class="nav-link"><a class="nav-link active" href="feedback.php?type=support">Support Questions</a></li>
    <?php else: ?>
            <li class="nav-link"><a class="nav-link" href="feedback.php?type=support">Support Questions</a></li>
    <?php endif; ?>
    <?php if($tabType=='bug'): ?>
	        <li class="nav-link"><a class="nav-link active" href="feedback.php?type=bug">Bug Reports</a></li>
    <?php else: ?>
	        <li class="nav-link"><a class="nav-link" href="feedback.php?type=bug">Bug Reports</a></li>
    <?php endif; ?>
    <?php if($tabType=='feature'): ?>
	        <li class="nav-link"><a  class="nav-link active" href="feedback.php?type=feature">Feature Requests</a></li>
    <?php else: ?>
	        <li class="nav-link"><a  class="nav-link" href="feedback.php?type=feature">Feature Requests</a></li>
    <?php endif; ?>
</ul>

</div>

</div>

<br>

<div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            Your Applications
                        </div>
                        <!-- /.card-heading -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
										<th>ID</th>
										<th>Application</th>
										<th>Type</th>
										<th>Email</th>
										<th>Wants Reply?</th>
										<th>Date</th>
										<th>View</th>
                                        </tr>
                                    </thead>
                                    <tbody>

<?php foreach($feedback as $f) : ?>
									<tr class="<?php if($f->new == 1) echo "new"; ?>">
										<td><?php echo $f->id; ?></td>
										<td><?php echo $f->appname; ?> <?php echo $f->appversion; ?></td>
										<td><?php echo $f->type; ?></td>
										<td><a href="mailto:<?php echo $f->email; ?>"><?php echo $f->email; ?></a></td>
										<td><?php echo ($f->reply == 1) ? '<strong>Yes</strong>' : 'No'; ?></td>
										<td><?php echo time2str($f->dt); ?></td>
										<td><a href="feedback-view.php?id=<?php echo $f->id; ?>" class="btn btn-sm btn-info">View</a></td>
                                        </tr>
<?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>

<div class="alert alert-info"><span>Use <a href="http://github.com/tylerhall/OpenFeedback/">OpenFeedback</a> to collect feedback from your users.</span></div>
                    <!-- /.card -->
                </div>
</div>


<?php include('inc/footer.inc.php'); ?>
