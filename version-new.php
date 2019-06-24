<?PHP

	require 'includes/master.inc.php';
	$Auth->requireAdmin('login.php');
	$nav = 'applications';

	$app = new Application($_GET['id']);

	if(!$app->ok()) redirect('index.php');

	if(isset($_POST['btnCreateVersion']))
	{
		$Error->blank($_POST['version_number'], 'Version Number');
		$Error->blank($_POST['human_version'], 'Human Readable Version Number');
		$Error->upload($_FILES['file'], 'file');

		if($Error->ok())
		{
			$v = new Version();
			$v->app_id         = $app->id;
			$v->version_number = $_POST['version_number'];
			$v->human_version  = $_POST['human_version'];
			$v->release_notes  = $_POST['release_notes'];
			$v->dt             = dater();
			$v->downloads      = 0;
			$v->filesize       = filesize($_FILES['file']['tmp_name']);
			$v->signature      = sign_file($_FILES['file']['tmp_name'], $app->sparkle_pkey);

			$object = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $app->name)) . "_" . $v->version_number . "." . substr($_FILES['file']['name'], -3);
			$info   = parse_url($app->s3path);
			$object = slash($info['path']) . $object;

			chmod($_FILES['file']['tmp_name'], 0755);

			$radio_value = $_POST["exampleRadios"];

			if ($radio_value == 'local') {

			move_uploaded_file( $_FILES['file']['tmp_name'], $app->dirpath . basename( $_FILES['file']['name'] ) );
			$v->url = slash($app->link) . $_FILES['file']['name'];

			} else {

			$s3 = new S3($app->s3key, $app->s3pkey);
			$s3->uploadFile($app->s3bucket, $object, $_FILES['file']['tmp_name'], true);
			$v->url = slash($app->s3url) . $object;
			}
      
			$v->insert();

			redirect('versions.php?id=' . $app->id);
		}
		else
		{
			$version_number = $_POST['version_number'];
			$human_version  = $_POST['human_version'];
			$release_notes  = $_POST['release_notes'];
		}
	}
	else
	{
		$version_number = '';
		$human_version  = '';
		$release_notes  = '';
	}

	// It would be better to use PHP's native OpenSSL extension
	// but it's PHP 5.3+ only. Too early to force that requirement
	// upon users.


function sign_file($filename, $keydata)
    {
        $binary_hash = shell_exec('/usr/bin/openssl dgst -sha1 -binary < ' . $filename);
        $hash_tmp_file = tempnam($app->dirpath, 'foo');
        file_put_contents($hash_tmp_file, $binary_hash);

        $key_tmp_file = tempnam($app->dirpath, 'bar');
        if(strpos($keydata, '-----BEGIN DSA PRIVATE KEY-----') === false)
            $keydata = "-----BEGIN DSA PRIVATE KEY-----\n" . $keydata . "\n-----END DSA PRIVATE KEY-----\n";
        file_put_contents($key_tmp_file, $keydata);

        $signed_data = shell_exec("/usr/bin/openssl dgst -sha1 -sign $key_tmp_file < $hash_tmp_file");

        return base64_encode($signed_data);
    }
?>
<?PHP include('inc/header.inc.php'); ?>


<div class="row">
<div class="col-lg-12">

<h1 class="page-header">Applications</h1>

<ul class="nav nav-pills">
<li class="nav-link"><a class="nav-link" href="application.php?id=<?PHP echo $app->id; ?>"><?PHP echo $app->name; ?></a></li>
<li class="nav-link"><a class="nav-link" href="versions.php?id=<?PHP echo $app->id; ?>">Versions</a></li>
<li class="nav-link"><a class="nav-link active"href="version-new.php?id=<?PHP echo $app->id; ?>">Release New Version</a></li>
</ul>

</div>

</div>

<br>


            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            Create Version
                        </div>
                        <div class="card-body">
                            <div class="row">

                                <div class="col-lg-12">

							<form action="version-new.php?id=<?PHP echo $app->id; ?>" method="post" enctype="multipart/form-data">
								<p><label for="version_number">Sparkle Version Number</label> <input type="text" name="version_number" id="version_number" value="<?PHP echo $version_number;?>" class="form-control"></p>
								<p><label for="human_version">Human Readable Version Number</label> <input type="text" name="human_version" id="human_version" value="<?PHP echo $human_version;?>" class="form-control"></p>
								<p><label for="release_notes">Release Notes</label> <textarea class="form-control" name="release_notes" id="release_notes"><?PHP echo $release_notes; ?></textarea></p

								<p>
<div class="form-check">
  <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="amazon" checked>
  <label class="form-check-label" for="exampleRadios1">
    Are you using Amazon? Yes.
  </label>
</div>
<div class="form-check">
  <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="local">
  <label class="form-check-label" for="exampleRadios2">
    Are you uploading locally? Yes.
  </label>
</div>
								</p>			
								<!-- Make sure that when you set the upload directory you must set the path relative to your shine installation use ../ relative up / down directory path indicators in the text field -->
                                                                <p><label for="file">Application Archive</label> <input type="file" name="file" id="file" class="form-control"></p>
								<p><input type="submit" name="btnCreateVersion" value="Create Version" id="btnCreateVersion" class="btn btn-lg btn-success btn-block"></p>
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
