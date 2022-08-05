<?PHP
    // if(rand(1,30) == 1)
    //     include 'tweet-cron.php';

    $db = Database::getDatabase();
    $feedback_count = $db->getValue("SELECT COUNT(*) FROM shine_feedback WHERE new = 1");
    $tweet_count = $db->getValue("SELECT COUNT(*) FROM shine_tweets WHERE new = 1");
?>

    <nav class="navbar navbar-expand navbar-dark bg-dark static-top">

      <a class="navbar-brand mr-1" href="/">Shine</a>

      <button class="btn btn-link btn-sm text-white order-1 order-sm-0" id="sidebarToggle" href="#">
        <i class="fa fas fa-bars"></i>
      </button>

      <!-- Navbar Search -->
      <form class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-3 my-2 my-md-0" action="orders.php?id=<?PHP echo @$app_id; ?>" method="get">
        <div class="input-group">
          <input type="text" id="q" name="q" value="<?php echo @$q; ?>" class="form-control" placeholder="Search Orders..." aria-label="Search" aria-describedby="basic-addon2">
          <div class="input-group-append">
            <button class="btn btn-primary" type="button">
              <i class="fa fas fa-search"></i>
            </button>
          </div>
        </div>
      </form>

      <!-- Navbar -->
      <ul class="navbar-nav ml-auto ml-md-0">
        <li class="nav-item dropdown no-arrow mx-1">
          <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fas fa-bars"></i>
            <span class="badge badge-danger"></span>
          </a>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="alertsDropdown">
            <a class="dropdown-item" href="users.php"><i class="fa fa-user fa-fw"></i> Users</a>
            <a class="dropdown-item" href="settings.php"><i class="fa fa-gear fa-fw"></i> Settings</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="logout.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
          </div>
        </li>
      </ul>

    </nav>

    <div id="wrapper">

      <!-- Sidebar -->
      <ul class="sidebar navbar-nav">
        <li class="nav-item active">
          <a class="nav-link" href="#">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Menu</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="index.php">
            <i class="fa fa-home fa-fw"></i>
            <span>Home</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="orders.php">
            <i class="fa fa-paypal fa-fw"></i>
            <span>Orders</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="order-new.php">
            <i class="fa fa-money fa-fw"></i>
            <span>Create Order</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="activations.php">
            <i class="fa fa-check-circle fa-fw"></i>
            <span>Activations</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="feedback.php">
            <i class="fa fa-comments fa-fw"></i>
            <span>Feedback (<?PHP echo $feedback_count; ?>)</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="tweets.php">
            <i class="fa fa-twitter fa-fw"></i>
            <span>Tweets (<?PHP echo $tweet_count; ?>)</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="stats.php">
            <i class="fa fa-line-chart fa-fw"></i>
            <span>Sparkle Stats</span></a>
        </li>
<li class="nav-item active">
          <a class="nav-link" href="#">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Add New App</span>
          </a>
        </li>
        <form class="new-app" action="index.php" method="post">
            <li class="sidebar-search" style="line-height: 2.5em;">

<div class="input-group mb-3 custom-search-form">
  <input type="text" class="form-control" placeholder="App Name" name="name" id="appname">
  <div class="input-group-append">
    <button class="btn btn-primary" type="submit" name="btnNewApp" id="btnNewApp"><i class="fa fa-arrow-circle-right"></i></button>
  </div>
</div>

            </li>
            </form>
      </ul>
