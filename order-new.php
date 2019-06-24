<?PHP
	require 'includes/master.inc.php';
	$Auth->requireAdmin('login.php');
	$nav = 'orders';

	if(isset($_POST['btnCreateOrder']))
	{
		$Error->blank($_POST['app_id'], 'Application');
		$Error->blank($_POST['first_name'], 'First Name');
		$Error->blank($_POST['last_name'], 'Last Name');
		$Error->email($_POST['email']);
		
		if($Error->ok())
		{
		    $app = new Application($_POST['app_id']);
		    
			$o = new Order();
			$o->first_name  = $_POST['first_name'];
			$o->last_name   = $_POST['last_name'];
			$o->payer_email = $_POST['email'];
			$o->app_id      = $_POST['app_id'];
			$o->type        = 'Manual';
			$o->dt          = dater();
			$o->item_name   = $app->name;
			$o->notes	= $_POST['notes'];

			$o->insert();
			$o->generateLicense();
			redirect('order.php?id=' . $o->id);
		}
		else
		{
			$first_name = $_POST['first_name'];
			$last_name  = $_POST['last_name'];
			$email      = $_POST['email'];
		}
	}
	else
	{
		$first_name = '';
		$last_name  = '';
		$email      = '';
	}
	
	$applications = DBObject::glob('Application', 'SELECT * FROM shine_applications ORDER BY name');
?>
<?PHP include('inc/header.inc.php'); ?>

        <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Create Manual Order</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            Create order
                        </div>
                        <div class="card-body">
                            <div class="row">
  
                                <div class="col-lg-12">
                                    
                                    <form action="order-new.php" method="post">
								<p><label for="app_id">Application</label> <select name="app_id" id="app_id" class="form-control"><?PHP foreach($applications as $a) : ?><option value="<?PHP echo $a->id; ?>"><?PHP echo $a->name; ?></option><?PHP endforeach; ?></select></p>
								<p><label for="first_name">First Name</label> <input type="text" name="first_name" id="first_name" value="<?PHP echo $first_name; ?>" class="form-control"></p>
								<p><label for="last_name">Last Name</label> <input type="text" name="last_name" id="last_name" value="<?PHP echo $last_name; ?>" class="form-control"></p>
								<p><label for="email">Email</label> <input type="text" name="email" id="email" value="<?PHP echo $email; ?>" class="form-control"></p>
								<p><label for="notes">Notes</label> <textarea name="notes" id="notes" class="form-control"></textarea></p>
								<br><p><input type="submit" name="btnCreateOrder" value="Create Order" id="btnCreateOrder" class="btn btn-lg btn-success btn-block"></p>
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
