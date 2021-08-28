<?PHP
	require 'includes/master.inc.php';
	$Auth->requireAdmin('login.php');
	$nav = 'feedback';
	
	$f = new Feedback($_GET['id']);
	if(!$f->ok()) redirect('feedback.php');

	if(isset($_POST['btnNew']))
	{
		$f->new = 1;
		$f->update();
		redirect('feedback.php');
	}
	elseif(isset($_POST['btnDelete']))
	{
		$f->delete();
		redirect('feedback.php');
	}
	else
	{
		$f->new = 0;
		$f->update();
	}
	
	if(isset($_POST['btnNotes']))
	{
		$f->notes = $_POST['notes'];
		$f->update();
		redirect('feedback-view.php?id=' . $f->id);
	}

	// Get related orders
	$db = Database::getDatabase();
	$email = $db->quote($f->email);
	$orders = DBObject::glob('Order', 'SELECT * FROM shine_orders WHERE payer_email = ' . $email .  ' ORDER BY dt DESC');

 	// Get related feedbacks
 	$email = $db->quote($f->email);
 	$feedbacks = DBObject::glob('Feedback', 'SELECT * FROM shine_feedback WHERE email = ' . $email .  ' AND id <> ' . $f->id . ' ORDER BY dt DESC');

	// Get related activations
	$order_ids = array(-1); // -1 prevents sql error when no orders are added to the array
	foreach($orders as $o)
		$order_ids[] = $o->id;
	$order_ids = implode(',', $order_ids);
	$activations = DBObject::glob('Activation', "SELECT * FROM shine_activations WHERE (order_id IN ($order_ids)) OR (ip = '{$f->ip}') ORDER BY dt DESC");
?>
<?PHP include('inc/header.inc.php'); ?>
<div class="row">
<div class="col-lg-12">

 <h1 class="page-header">Feedback</h1>

<ul class="nav nav-pills">

<?PHP if(!isset($_GET['id'])): ?>
    <li class="nav-link" data-toggle="pill"><a class="nav-link" href="feedback.php">All Feedback</a></li>
<?php else: ?>
    <li class="nav-link" data-toggle="pill"><a class="nav-link active" href="feedback.php">All Feedback</a></li>
<?php endif; ?>
								<li class="nav-link"><a href="feedback.php?type=support" class="nav-link">Support Questions</a></li>
                                                                <li class="nav-link"><a href="feedback.php?type=bug" class="nav-link" >Bug Reports</a></li>
                                                                <li class="nav-link"><a href="feedback.php?type=feature" class="nav-link">Feature Requests</a></li>

</ul>


<?PHP echo $Error; ?>


</div>

</div>

<br>


<div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            Feedback #<?PHP echo $f->id; ?>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tr>
									<th>App Name</th>
									<td><?PHP echo $f->appname . ' ' . $f->appversion;?></td>
								</tr>
								<tr>
									<th>System</th>
									<td><?PHP echo $f->systemversion;?></td>
								</tr>
								<tr>
									<th>Email</th>
									<td><a href="mailto:<?PHP echo $f->email;?>"><?PHP echo $f->email;?></a></td>
								</tr>
								<tr>
									<th>Type</th>
									<td><?PHP echo ucwords($f->type);?></td>
								</tr>
								<tr>
									<th>Message</th>
									<td><?PHP echo nl2br($f->__message);?></td>
								</tr>
								<?PHP if($f->type == "feature") : ?>
								<tr>
									<th>Importance</th>
									<td><?PHP echo $f->importance;?></td>
								</tr>
								<?PHP endif; ?>
								<?PHP if($f->type == "bug") : ?>
								<tr>
									<th>Critical</th>
									<td><?PHP echo ($f->critical == 0) ? "No" : "Yes!"; ?></td>
								</tr>
								<?PHP endif; ?>
								<tr>
									<th>Date Submitted</th>
									<td><?PHP echo dater('n/j/Y g:ia', $f->dt); ?></td>
								</tr>
								<tr>
									<th>IP</th>
									<td><?PHP echo $f->ip;?></td>
								</tr>
                                </table>


							<form action="feedback-view.php?id=<?PHP echo $f->id;?>" method="post">
								<p>
									<input type="submit" name="btnNew" value="Mark as New" id="btnnew"/ class="btn btn-lg btn-info">
									<input type="submit" name="btnDelete" value="Delete" id="btndelete" onclick="return confirm('Are you sure?');"/ class="btn btn-lg btn-danger">
								</p>
							</form>


                            </div>
                        </div>
                        <!-- /.panel-body -->
                    </div>

                    <!-- /.panel -->
                </div>
</div>


<div class="row">
                <div class="col-lg-12">
                    <div class="card panel-default">
                        <div class="card-header">
                            Feedback Notes
                        </div>
                        <!-- /.panel-heading -->
                        <div class="card-body">
                            <form action="feedback-view.php?id=<?PHP echo $f->id; ?>" method="post" class="bd">
								<textarea style="width:100%;" name="notes" id="notes" class="form-control"><?PHP echo $f->notes; ?></textarea><br>
								<input type="submit" name="btnNotes" value="Save Notes" id="btnNotes" class="btn btn-lg btn-success"><br><br>
								<div class="alert alert-info"><span class="info">Notes will NOT be sent or made visible to customers.</span></div>
							</form>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
</div>	
</div>


<div class="row">
                <div class="col-lg-12">
                    <div class="card panel-default">
                        <div class="card-header">
                            Customer Info
                        </div>
                        <!-- /.panel-heading -->
                        <div class="card-body">
                            <div id="rapportive" class="bd"></div>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
</div>	
</div>



<div class="row">
                <div class="col-lg-12">
                    <div class="card card-default">
                        <div class="card-header">
                            Related Feedback
                        </div>
                        <!-- /.panel-heading -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
 					            <tr>
 					                <th>Date</th>
 					                <th>App Name</th>
 					                <th>Type</th>
 					            </tr>
 					        </thead>
 					        <tbody>
     							<?PHP foreach($feedbacks as $f) : ?>
     							<tr>
     							    <td><?PHP echo time2str($f->dt); ?></td>
     							    <td><?PHP echo $f->appname . ' ' . $f->appversion;?></td>
     							    <td><a href="feedback-view.php?id=<?PHP echo $f->id; ?>"><?PHP echo ucwords($f->type); ?></a></td>
     							</tr>
     							<?PHP endforeach; ?>
 					        </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.panel-body -->
                    </div>

                    <!-- /.panel -->
                </div>
</div>


<div class="row">
                <div class="col-lg-12">
                    <div class="card card-default">
                        <div class="card-header">
                            Related Orders
                        </div>
                        <!-- /.panel-heading -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
					            <tr>
					                <th>Date</th>
					                <th>Name</th>
					                <th>App Name</th>
					            </tr>
					        </thead>
					        <tbody>
    							<?PHP foreach($orders as $o) : ?>
    							<tr>
    							    <td><?PHP echo time2str($o->dt); ?></td>
    							    <td><a href="order.php?id=<?PHP echo $o->id; ?>"><?PHP echo utf8_encode($o->first_name); ?> <?PHP echo utf8_encode($o->last_name); ?></a></td>
    							    <td><?PHP echo $o->applicationName(); ?></td>
    							</tr>
    							<?PHP endforeach; ?>
					        </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.panel-body -->
                    </div>

                    <!-- /.panel -->
                </div>
</div>

<div class="row">
                <div class="col-lg-12">
                    <div class="card card-default">
                        <div class="card-header">
                            Related Activations
                        </div>
                        <!-- /.panel-heading -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
					            <tr>
					                <th>Date</th>
					                <th>App Name</th>
					                <th>IP</th>
					            </tr>
					        </thead>
					        <tbody>
    							<?PHP foreach($activations as $a) : ?>
    							<tr>
    							    <td><?PHP echo time2str($a->dt); ?></td>
    							    <td><?PHP echo $a->applicationName(); ?></td>
    							    <td><a href="activations.php?q=<?PHP echo $a->ip; ?>"><?PHP echo $a->ip; ?></a></td>
    							</tr>
    							<?PHP endforeach; ?>
					        </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.panel-body -->
                    </div>

                    <!-- /.panel -->
                </div>
</div>
										

				
<?PHP include('inc/footer.inc.php'); ?>
<script type="text/javascript" charset="utf-8">
	$(function() {
		$('#rapportive').load('rapportive.php?email=<?PHP echo $f->email; ?>');
	});
</script>
