<?PHP
    require_once('includes/master.inc.php');
    require_once('TwitterAPIExchange.php');

    $db = Database::getDatabase();
    $tweet_apps = $db->getRows('SELECT id, tweet_terms FROM shine_applications');

    foreach($tweet_apps as $tweet_app)
    {
        $terms = explode(',', $tweet_app['tweet_terms']);
        foreach($terms as $term)
        {
            $term = trim($term);
            if(strlen($term) > 0)
            {

$url = 'https://api.twitter.com/1.1/search/tweets.json';
$getfield = '?q=#' . urlencode($term) . '&result_type=recent';
$requestMethod = 'GET';

$twitter = new TwitterAPIExchange($settings);
$twitter->setGetfield($getfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest();

$string = json_decode($twitter->setGetfield($getfield)
->buildOauth($url, $requestMethod)
->performRequest(),$assoc = TRUE);
if($string["errors"][0]["message"] != "") {echo "<h3>Sorry, there was a problem.</h3><p>Twitter returned the following error message:</p><p><em>".$string[errors][0]["message"]."</em></p>";exit();}

foreach($string['statuses'] as $items)
    {
	    
    }
		if(!is_object($twitter)) continue;

			   foreach($string['statuses'] as $result)
                {
                    $t = new Tweet();
                    $t->tweet_id    = $result['id_str'];
                    $t->username    = $result['user']['name'];
                    $t->app_id      = $tweet_app['id'];
                    $t->dt          = dater($result['created_at']);
                    $t->body        = $result['text'];
                    $t->profile_img = $items['user']['profile_image_url'];
                    $t->new         = 1;
                    $t->replied_to  = 0;
                    $t->insert();
                }

            }
        }
    }
