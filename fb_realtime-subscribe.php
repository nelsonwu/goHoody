<?php

require_once('php/facebook.php');

$APPLICATION_ID = "192823134073322";
$APPLICATION_SECRET = "cadcff0ca1411b30f975499ba148b8d3";


$param = array('access_token' => '192823134073322|MhZpmMoo_bikrMpWuxoVfyfn7vE',
                'object' => 'user',
                'fields' => 'name,pic,email,activities,interests,music,tv,movies,books,profile_update_time',
                'callback_url' => 'http://gohoody.com/development/fb_realtime-callback.php',
                'verify_token' => 'blah'
                );

//var_dump($param);
 $facebook = new Facebook($APPLICATION_ID, $APPLICATION_SECRET);
 $subs = $facebook->api('/'.$APPLICATION_ID.'/subscriptions', 'POST', $param);

 var_dump($subs);

?>