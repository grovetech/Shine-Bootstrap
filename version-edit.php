<?PHP
	require 'includes/master.inc.php';
	$Auth->requireAdmin('login.php');
	$nav = 'applications';
	
	$v = new Version($_GET['id']);
	if(!$v->ok()) redirect('index.php');
	
	$app = new Application($v->app_id);
	if(!$app->ok()) redirect('index.php');
	
	if(isset($_POST['btnDelete']))
	{
		$v->delete();
		redirect('versions.php?id=' . $app->id);
	}
	
	if(isset($_POST['btnSave']))
	{
		$v->version_number = $_POST['version_number'];
		$v->human_version  = $_POST['human_version'];
		$v->url = $_POST['url'];
		$v->release_notes  = $_POST['release_notes'];
		$v->filesize = $_POST['filesize'];
		$v->signature = $_POST['signature'];
		$v->update();	
	}

	$version_number = $v->version_number;
	$human_version  = $v->human_version;
	$release_notes  = $v->release_notes;
	$url            = $v->url;
	$signature      = $v->signature;
	$filesize       = $v->filesize;	
?>
<?PHP include('inc/header.inc.php'); ?>


<div class="row">
<div class="col-lg-12">

<h1 class="page-header">Applications</h1>

<ul class="nav nav-pills">
<li class="nav-link"><a href="application.php?id=<?PHP echo $app->id; ?>" class="nav-link"><?PHP echo $app->name; ?></a></li>
<li class="nav-link"><a href="versions.php?id=<?PHP echo $app->id; ?>" class="nav-link active">Versions</a></li>
<li class="nav-link"><a href="version-new.php?id=<?PHP echo $app->id; ?>" class="nav-link">Release New Version</a></li>
</ul>

</div>

</div>

<br><br>

<div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            Edit Version
                        </div>
                        <div class="card-body">
                            <div class="row">
  
                                <div class="col-lg-12">
                                    
                                    <form action="version-edit.php?id=<?PHP echo $v->id; ?>" method="post">
								<p><label for="version_number">Version Number</label> <input type="text" name="version_number" id="version_number" value="<?PHP echo $version_number;?>" class="form-control"></p>
								<p><label for="human_version">Human Readable Version Number</label> <input type="text" name="human_version" id="human_version" value="<?PHP echo $human_version;?>" class="form-control"></p>
								<p><label for="url">Download URL</label> <input type="text" name="url" id="url" value="<?PHP echo $url;?>" class="form-control"></p>
								<p><label for="release_notes">Release Notes</label> <textarea class="form-control" name="release_notes" id="release_notes"><?PHP echo $release_notes; ?></textarea></p>
								<p><label for="filesize">Filesize</label> <input type="text" name="filesize" id="filesize" value="<?PHP echo $filesize; ?>" class="form-control"></p>
								<p><label for="signature">Sparkle Signature</label> <input type="text" name="signature" id="signature" value="<?PHP echo $signature; ?>" class="form-control"></p>
<br>
								<p><input type="submit" name="btnDelete" value="Delete Version" id="btnDelete" onclick="return confirm('Are you sure?');" class="btn btn-lg btn-danger">   <input type="submit" name="btnSave" value="Save" id="btnSave" class="btn btn-lg btn-success"></p>
							</form>
                                </div>
                                <!-- /.col-lg-6 (nested) -->
                            </div>
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->


<?PHP include('inc/footer.inc.php'); ?>
