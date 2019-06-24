<?PHP
	require 'includes/master.inc.php';
	$Auth->requireAdmin('login.php');
	$nav = 'applications';
	
	$app = new Application($_GET['id']);
	if(!$app->ok()) redirect('index.php');
	$versions = $app->versions();
?>
<?PHP include('inc/header.inc.php'); ?>

<div class="row">
<div class="col-lg-12">

 <h1 class="page-header">Applications</h1>

<ul class="nav nav-pills">
<li class="nav-link"><a class="nav-link" href="application.php?id=<?PHP echo $app->id; ?>"><?PHP echo $app->name; ?></a></li>
<li class="nav-link"><a class="nav-link active" href="versions.php?id=<?PHP echo $app->id; ?>">Versions</a></li>
<li class="nav-link"><a class="nav-link" href="version-new.php?id=<?PHP echo $app->id; ?>">Release New Version</a></li>
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
                        <!-- /.card-header -->
 <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                           <th>Human Readable Version</th>
										<th>Sparkle Version Number</th>
										<th>Release Date</th>
										<th>Downloads</th>
										<th>Updates</th>
                                        </tr>
                                    </thead>
                                    <tbody>

<?PHP foreach($versions as $v) : ?>
                                        <tr>
                                           <td><a href="version-edit.php?id=<?PHP echo $v->id; ?>"><?PHP echo $v->human_version; ?></a></td>
										<td><?PHP echo $v->version_number; ?></td>
										<td><?PHP echo dater($v->dt, 'n/d/Y g:ia'); ?></td>
										<td><?PHP echo number_format($v->downloads); ?></td>
										<td><?PHP echo number_format($v->updates); ?></td>
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
