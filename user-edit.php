<?PHP
	require 'includes/master.inc.php';
	$Auth->requireAdmin('login.php');

    $u = new User($_GET['id']);
    if(!$u->ok()) redirect('users.php');

    if(isset($_GET['action']) && $_GET['action'] == 'delete')
    {
        $u->delete();
        redirect('users.php');
    }

	if(isset($_POST['btnEditAccount']))
	{
		$Error->blank($_POST['username'], 'Username');
		$Error->blank($_POST['level'], 'Level');
        $Error->email($_POST['email']);
		
		if($Error->ok())
		{
			$u->username   = $_POST['username'];
			$u->email      = $_POST['email'];
			$u->level      = $_POST['level'];
			$u->twitter    = trim($_POST['twitter']);

            // Leave the password alone if it's not set
            if(!empty($_POST['password']))
                $u->setPassword($_POST['password']);

			$u->update();
            redirect('users.php');
		}
		else
		{
			$username = $_POST['username'];
			$email    = $_POST['email'];
			$level    = $_POST['level'];
			$twitter  = $_POST['twitter'];
		}
	}
	else
	{
		$username  = $u->username;
		$email     = $u->email;
		$level     = $u->level;
		$twitter   = $u->twitter;
	}
?>
<?PHP include('inc/header.inc.php'); ?>

<div class="row">
<div class="col-lg-12">

 <h1 class="page-header">Users</h1>

<ul class="nav nav-pills">
    <li class="nav-link"><a class="nav-link" href="users.php">Users</a></li>
    <li class="nav-link"><a class="nav-link" href="user-new.php">Create new user</a></li>
    <li class="nav-link"><a class="nav-link active" href="user-edit.php?id=<?PHP echo $u->id; ?>">Edit User</a></li>
</ul>

</div>

</div>

<br>


  <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            Edit User
                        </div>
                        <div class="card-body">
                            <div class="row">
  
                                <div class="col-lg-12">
                                    
                                   <form action="user-edit.php?id=<?PHP echo $u->id; ?>" method="post">
								<p><label for="username">Username</label> <input type="text" name="username" id="username" value="<?PHP echo $username; ?>" class="form-control"></p>
								<p><label for="password">Password</label> <input type="password" name="password" id="password" value="" class="form-control"><div class="alert alert-info"><span class="info">Leave the password blank if you do not wish to change it</span></div></p>
								<p><label for="twitter">Twitter Username</label> <input type="text" name="twitter" id="twitter" value="<?PHP echo $twitter; ?>" class="form-control"><div class="alert alert-info"><span class="info">We'll use your Twitter avatar throughout the site</span></div></p>
								<p><label for="email">Email</label> <input type="text" name="email" id="email" value="<?PHP echo $email; ?>" class="form-control"></p>
								<p><label for="level">Level</label>
								    <select name="level" id="level" class="form-control">
                                        <option <?PHP if($level == 'user') echo 'selected="selected"'; ?> value="user">User</option>
                                        <option <?PHP if($level == 'admin') echo 'selected="selected"'; ?> value="admin">Admin</option>
                                    </select>
                                </p><br>
								<p><input type="submit" name="btnEditAccount" value="Save" id="btnEditAccount" class="btn btn-lg btn-success btn-block"></p>
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
