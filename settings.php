<?PHP
	require 'includes/master.inc.php';
	$Auth->requireAdmin('login.php');
	
	if(isset($_POST['btnPayPal']))
	{
		set_option('paypal_url', $_POST['ipn_url']);
		set_option('of_email', $_POST['of_email']);
		redirect('settings.php');
	}

	$ipn_url = get_option('paypal_url', DEFAULT_IPN_URL);
	$of_email = get_option('of_email', '');
?>
<?PHP include('inc/header.inc.php'); ?>


  <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Misc Settings</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            General Settings
                        </div>
                        <div class="card-body">
                            <div class="row">
  
                                <div class="col-lg-12">
                                    
                                    <form action="settings.php" method="post">
								<p>
									<label for="ipn_url">PayPal IPN URL</label> <input type="text" name="ipn_url" id="ipn_url" value="<?PHP echo $ipn_url;?>" class="form-control">

									<div class="alert alert-info">
									<span class="info">This is where PayPal will post back to when it receives an <a href="https://www.paypal.com/ipn">instant payment notification</a>.
										<br>Default is: <a href="<?PHP echo DEFAULT_IPN_URL; ?>"><?PHP echo DEFAULT_IPN_URL; ?></a>
										<br>Testing is: <a href="<?PHP echo SANDBOX_IPN_URL; ?>"><?PHP echo SANDBOX_IPN_URL; ?></a></span>
									</div>
								</p>
								<p>
									<label for="of_email">OpenFeedback Email</label> <input type="text" name="of_email" id="of_email" value="<?PHP echo $of_email;?>" class="form-control">
									<div class="alert alert-info"><span class="info">If you have <a href="https://github.com/tylerhall/OpenFeedback">OpenFeedback</a> enabled, this is where those reports will be sent.</span></div>
								</p>
								<p><input type="submit" name="btnPayPal" value="Save PayPal Settings" id="btnPayPal" class="btn btn-lg btn-success btn-block"></p>
							</form>
                                </div>
                                <!-- /.col-lg-6 (nested) -->
                            </div>
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->


<?PHP include('inc/footer.inc.php'); ?>
