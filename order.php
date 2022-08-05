<?PHP
	require 'includes/master.inc.php';
	$Auth->requireAdmin('login.php');
	$nav = 'orders';

	$o = new Order(@$_GET['id']);
	if(!$o->ok()) redirect('orders.php');

	if(isset($_GET['act']) && $_GET['act'] == 'email')
		$o->emailLicense();
	
	if(isset($_GET['act']) && $_GET['act'] == 'download')
		$o->downloadLicense();

	if(isset($_GET['act']) && $_GET['act'] == 'upgrade')
	{
		$upgraded_order = $o->upgradeLicense();
		redirect('order.php?id=' . $upgraded_order->id);
		exit;
	}
	
	if(isset($_GET['act']) && $_GET['act'] == 'deactivate')
	{
		$o->deactivated = 1;
		$o->update();
		redirect('order.php?id=' . $o->id);
	}

	if(isset($_GET['act']) && $_GET['act'] == 'delete')
	{
		$o->delete();
		redirect('orders.php');
	}

	if(isset($_POST['submit']))
	{
		$o->notes = $_POST['notes'];
		$o->update();
		redirect('order.php?id=' . $o->id);
	}

	$app = new Application($o->app_id);

	// Get related orders
	$db = Database::getDatabase();
	$orders = DBObject::glob('Order', 'SELECT * FROM shine_orders WHERE payer_email = ' . $db->quote($o->payer_email) .  ' AND id <> ' .  $o->id .  ' ORDER BY dt DESC');
?>
<?PHP include('inc/header.inc.php'); ?>

<div class="row">
<div class="col-lg-12">

 <h1 class="page-header">Order #<?PHP echo $o->id; ?><?PHP if($o->deactivated == 1) : ?> (Deactivated) <?PHP endif; ?></h1>

</div>

</div>


<div class="row">


                <div class="col-lg-12">
                    <div class="card card-default">
                        <div class="card-header">
                            Order #<?PHP echo $o->id; ?><?PHP if($o->deactivated == 1) : ?> (Deactivated) <?PHP endif; ?>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">

								<?PHP foreach($o->columns as $k => $v) : ?>
								<?PHP if(strlen(trim($v)) > 0) : ?>
                                        <tr>
										<th><strong><?PHP echo $k; ?></strong></th>
										<td><?PHP echo $v; ?></td>
                                        </tr>
								<?PHP endif; ?>
								<?PHP endforeach; ?>
                                </table>
                            </div>
           <!-- /.table-responsive -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
</div></div>

<div class="row">
                <div class="col-lg-12">
                    <div class="card card-default">
                        <div class="card-header">
                            Order Notes
                        </div>
                        <!-- /.panel-heading -->
                        <div class="card-body">
                            <form action="order.php?id=<?PHP echo $o->id; ?>" method="post" class="bd">
								<textarea  name="notes" id="notes" class="form-control"><?PHP echo $o->notes; ?></textarea>
<br>
								<input type="submit" name="btnNotes" value="Save Notes" id="btnNotes" class="btn btn-lg btn-success btn-block">
<br>
</form>								<div class="alert alert-info"><span class="info">Notes will NOT be sent or made visible to customers.</span></div>
							
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
                            Related Orders and Activations
                        </div>
                        <!-- /.panel-heading -->
                        <div class="card-body">
                            <p><a href="activations.php?q=<?PHP echo $o->payer_email; ?>">Activated <?PHP echo $o->activationCount(); ?> times</a></p>
					    <table class="table table-striped">
					        <thead>
					            <tr>
					                <th>Date</th>
					                <th>App Name</th>
					            </tr>
					        </thead>
					        <tbody>
    							<?PHP foreach($orders as $o2) : ?>
    							<tr>
    							    <td><a href="order.php?id=<?PHP echo $o2->id; ?>"><?PHP echo time2str($o2->dt); ?></a></td>
    							    <td><?PHP echo $o2->applicationName(); ?></td>
    							</tr>
    							<?PHP endforeach; ?>
					        </tbody>
					    </table>
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
                            License Options
                        </div>
                        <!-- /.panel-heading -->
                        <div class="card-body">
                            <ul class="biglist">
							<li><a class="btn btn-secondary btn-block" href="order.php?id=<?PHP echo $o->id; ?>&amp;act=email" id="email">Email to User</a></li><br>
							<li><a class="btn btn-secondary btn-block" href="<?PHP echo $o->getDownloadLink(); ?>">Download Link (does not expire)</a></li><br>
							<li><a class="btn btn-secondary btn-block" href="<?PHP echo $o->getDownloadLink(86400); ?>">Download Link (1 day)</a></li><br>
							<li><a class="btn btn-secondary btn-block" href="<?PHP echo $o->getDownloadLink(86400 * 3); ?>">Download Link (3 days)</a></li><br>
							<li><a class="btn btn-secondary btn-block" href="<?PHP echo $o->getDownloadLink(86400 * 7); ?>">Download Link (1 week)</a></li><br>
							<li><a class="btn btn-secondary btn-block" href="order.php?id=<?PHP echo $o->id; ?>&amp;act=deactivate" id="deactivate">Deactivate License</a></li>
						</ul>
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
                            Order Options
                        </div>
                        <!-- /.panel-heading -->
                        <div class="card-body">
                            <ul class="biglist">
							<?PHP if($app->upgrade_app_id > 0) : ?>
							<li><a class="btn" href="order.php?id=<?PHP echo $o->id; ?>&amp;act=upgrade" id="upgrade">Upgrade Order</a></li>
							<?PHP endif; ?>
							<li><a href="order.php?id=<?PHP echo $o->id; ?>&amp;act=delete" id="delete" class="btn btn-danger">Delete Order</a></li>
						</ul>
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
                            Cut &amp; Paste License
                        </div>
                        <!-- /.panel-heading -->
                        <div class="card-body">
                            <?PHP if($app->engine_class_name == 'aquaticprime') : ?>
						<textarea style="width:100%;" class="form-control"><?PHP echo $o->license; ?></textarea>
						<?PHP elseif($app->engine_class_name == 'dual') : ?>
						<textarea style="width:100%;" class="form-control"><?PHP echo "Email: {$o->payer_email}\nSerial Number: {$o->serial_number}"; ?></textarea>
						<?PHP else : ?>
						<textarea style="width:100%;" class="form-control"><?PHP echo "Email: {$o->payer_email}\nReg Key: {$o->license}"; ?></textarea>
						<?PHP endif; ?>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
</div>	
</div>





<?PHP include('inc/footer.inc.php'); ?>
<script type="text/javascript" charset="utf-8">
	$(function() {
		$('#rapportive').load('rapportive.php?email=<?PHP echo $o->payer_email; ?>');
	});
</script>
