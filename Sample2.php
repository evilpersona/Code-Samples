function getFootTraffic(){
    $pwch = curl_init();
    $auth = array('Content-Type: application/x-www-form-urlencoded');
    $body ='grant_type=password&username=christopher.collareta%40loungelizard.com&password=8KtJMD0V7o';
    curl_setopt($pwch, CURLOPT_URL, 'https://data.springboardanalyser.com/token');
    curl_setopt($pwch, CURLOPT_HTTPHEADER, $auth);
    curl_setopt($pwch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($pwch, CURLOPT_HTTPGET, true);
curl_setopt( $pwch, CURLOPT_CUSTOMREQUEST, 'GET' );
curl_setopt( $pwch, CURLOPT_POSTFIELDS, $body );
//return the transfer as a string
curl_setopt($pwch, CURLOPT_RETURNTRANSFER, 1);

// $output contains the output string
$output = curl_exec($pwch);
// close curl resource to free up system resources
curl_close($pwch);

$json = [];
$json = json_decode($output, true);

$getch = curl_init();
$authget = array('Authorization: Bearer '.$json['access_token']);
$yest = 'Yesterday';
$date = date("Y-m-d",strtotime($yest));
curl_setopt($getch, CURLOPT_URL, 'https://data.springboardanalyser.com/api/footfalloutput/historic?startDate=2022-01-01&endDate='.$date);
    curl_setopt($getch, CURLOPT_HTTPHEADER, $authget);
    curl_setopt($getch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($getch, CURLOPT_HTTPGET, true);
//return the transfer as a string
curl_setopt($getch, CURLOPT_RETURNTRANSFER, 1);
$out = curl_exec($getch);
// close curl resource to free up system resources
curl_close($getch);

$json2 = [];
$json2 = json_decode($out, true);

$people = 0;
foreach($json2 as $day){
    $people = $people + $day['AcceptedIn'];
}


$oldcount = get_field('pedestrian_foot_counter', 'options');

$newCount = $people + $oldcount;
//echo '<pre>'.print_r($json2,true).'</pre>';
//$year = $json['yearToDate'] ?? '';
update_field('pedestrian_foot_counter_new', $newCount , 'options');
}


function getFootTrafficDaily(){
    $pwch = curl_init();
    $auth = array('Content-Type: application/x-www-form-urlencoded');
    $body ='grant_type=password&username=christopher.collareta%40loungelizard.com&password=8KtJMD0V7o';
    curl_setopt($pwch, CURLOPT_URL, 'https://data.springboardanalyser.com/token');
    curl_setopt($pwch, CURLOPT_HTTPHEADER, $auth);
    curl_setopt($pwch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($pwch, CURLOPT_HTTPGET, true);
curl_setopt( $pwch, CURLOPT_CUSTOMREQUEST, 'GET' );
curl_setopt( $pwch, CURLOPT_POSTFIELDS, $body );
//return the transfer as a string
curl_setopt($pwch, CURLOPT_RETURNTRANSFER, 1);

// $output contains the output string
$output = curl_exec($pwch);
// close curl resource to free up system resources
curl_close($pwch);

$json = [];
$json = json_decode($output, true);

$getch = curl_init();
$authget = array('Authorization: Bearer '.$json['access_token']);
$yest = 'Yesterday';
$date = date("Y-m-d",strtotime($yest));
$tod = 'Today';
$date2 = date("Y-m-d",strtotime($tod));
curl_setopt($getch, CURLOPT_URL, 'https://data.springboardanalyser.com/api/footfalloutput/historic?startDate='.$date.'&endDate='.$tod);
    curl_setopt($getch, CURLOPT_HTTPHEADER, $authget);
    curl_setopt($getch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($getch, CURLOPT_HTTPGET, true);
//return the transfer as a string
curl_setopt($getch, CURLOPT_RETURNTRANSFER, 1);
$out = curl_exec($getch);
// close curl resource to free up system resources
curl_close($getch);

$json2 = [];
$json2 = json_decode($out, true);

$people = 0;
foreach($json2 as $day){
    $people = $people + $day['AcceptedIn'];
}


$oldcount = get_field('pedestrian_foot_counter_new', 'options');

$newCount = $people + $oldcount;
//echo '<pre>'.print_r($json2,true).'</pre>';
//$year = $json['yearToDate'] ?? '';
update_field('pedestrian_foot_counter_new', $newCount , 'options');
}
add_action( 'get_foot_traffic_daily', 'getFootTrafficDaily', 10 );
wp_schedule_event( time(), 'daily', 'getFootTrafficDaily' );

