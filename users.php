<?PHP
	require 'includes/master.inc.php';
	$Auth->requireAdmin('login.php');

	$db = Database::getDatabase();
	
	if(isset($_GET['q']))
	{
		$q = $_GET['q'];
		$_q = $db->escape($q);
		$search_sql = " AND (username LIKE '%$_q%' OR email LIKE '%$_q%') ";
	}
	else
	{
		$q = '';
		$search_sql = '';
	}

	$users = DBObject::glob('User', "SELECT * FROM shine_users WHERE 1 = 1 $search_sql ORDER BY username");
?>
<?PHP include('inc/header.inc.php'); ?>

<div class="row">
<div class="col-lg-12">

 <h1 class="page-header">Users</h1>

<ul class="nav nav-pills">
    <li class="nav-link"><a class="nav-link active" href="users.php">Users</a></li>
    <li class="nav-link"><a class="nav-link" href="user-new.php">Create new user</a></li>
</ul>

</div>

</div>

<br>

<div class="row">
<div class="col-lg-12">

<div class="card">
                        <div class="card-header">
                            User Accounts
                        </div>
                        <!-- /.card-heading -->
 <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                          <th>Username</th>
										<th>Level</th>
										<th>Email</th>
                                          <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>

<?PHP foreach($users as $u) : ?>
                                        <tr>
                                          <td><?PHP echo $u->username; ?></td>
										<td><?PHP echo $u->level; ?></td>
										<td><?PHP echo $u->email; ?></td>
										<td>
                                            <a href="user-edit.php?id=<?PHP echo $u->id; ?>" class="btn btn-default btn-success">Edit</a>
                                            <a href="user-edit.php?id=<?PHP echo $u->id; ?>&amp;action=delete" onclick="return confirm('Are you sure?');" class="btn btn-default btn-danger">Delete</a></td>
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
</div></div>



					
<?PHP include('inc/footer.inc.php'); ?>
