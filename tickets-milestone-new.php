<?PHP
	require 'includes/master.inc.php';
	$Auth->requireAdmin('login.php');
	$nav = 'tickets';

	$app = new Application($_GET['app_id']);
	if(!$app->ok()) redirect('tickets.php');

	if(isset($_POST['btnNew']))
	{
		if($Error->ok())
		{
			$m = new Milestone();
			$m->app_id        = $app->id;
			$m->title         = trim($_POST['title']);
			$m->description   = $_POST['description'];
			$m->status        = 'open';
			$m->dt_due        = dater($_POST['due']);
			
			if(strlen($m->title) == 0)
				$m->title = 'Untitled Milestone';

			$m->insert();
			redirect('tickets-milestones.php?app_id=' . $app->id);
		}
		else
		{
			$title        = $_POST['title'];
			$description  = $_POST['description'];
			$due          = $_POST['due'];
		}
	}
	else
	{
		$title        = '';
		$description  = '';
		$due          = '';
	}
?>
<?PHP include('inc/header.inc.php'); ?>

        <div id="bd">
            <div id="yui-main">
                <div class="yui-b"><div class="yui-g">

                    <div class="block tabs spaces">
                        <div class="hd">
                            <h2><?PHP echo $a->name; ?> Ticket Summary</h2>
							<ul>
								<li><a href="tickets-app-summary.php?id=<?PHP echo htmlspecialchars($app->id); ?>"><?PHP echo htmlspecialchars($app->name); ?> Summary</a></li>
								<li><a href="tickets-tickets.php?app_id=<?PHP echo htmlspecialchars($app->id); ?>">Tickets</a></li>
								<li class="active"><a href="tickets-milestones.php?app_id=<?PHP echo htmlspecialchars($app->id); ?>">Milestones</a></li>
							</ul>
							<div class="clear"></div>
                        </div>
                        <div class="bd">
							<form action="tickets-milestone-new.php?app_id=<?PHP echo htmlspecialchars($app->id);?>" method="post">
								<p><label for="title">Title</label> <input type="text" name="title" id="title" value="<?PHP echo htmlspecialchars($title);?>" class="text"></p>
								<p><label for="description">Description</label><br><textarea name="description" id="description" class="text"><?PHP echo htmlspecialchars($description) ?></textarea><span class="info">Markdown is allowed</span></p>
								<p><label for="due">Due Date:</label> <input type="text" name="due" id="due" value="<?PHP echo htmlspecialchars($due);?>" class="text"></p>
								<p><input type="submit" name="btnNew" value="Create Milestone" id="btnNew"></p>
							</form>
						</div>
					</div>
              
                </div></div>
            </div>
            <div id="sidebar" class="yui-b">
				<div class="block">
					<div class="hd"><h3>Create a New Item</h3></div>
					<div class="bd">
						<p class="text-center"><a href="tickets-new.php?app_id=<?PHP echo htmlspecialchars($app->id); ?>" class="big-button">New Ticket</a></p>
						<p class="text-center"><a href="tickets-milestone-new.php?app_id=<?PHP echo htmlspecialchars($app->id); ?>" class="big-button">New Milestone</a></p>
					</div>
				</div>
            </div>
        </div>
		<script type="text/javascript" charset="utf-8">
			window.onload = function() {
				document.getElementById('title').focus();
			}
		</script>
<?PHP include('inc/footer.inc.php'); ?>
