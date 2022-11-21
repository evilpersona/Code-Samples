<?php
                $futures = array(
                  '@C`##' => 'Corn - CBOT',
                  '@S`##' => 'Soybeans - CBOT',
                  '@W`##' => 'Wheat - CBOT',
                  '@LE`##' => 'Live Cattle - CME',
                  '@GF`##' => 'Feeder Cattle - CME',
                  '@HE`##' => 'Lean Hogs - CME',
                  '@SM`##' => 'Soybean Meal - CBOT'
                );
                foreach ($futures as $key => $value) :
                  $keyE = urlencode($key);
                      
                        $futureURL = 'https://api.dtn.com/markets/symbols/'.$keyE.'/quotes?priceFormat=traditional&apikey='.$marketKey;
                        $futureD = callAPI($futureURL,$marketKey);
                        $futureJ = json_decode($futureD);
                        
                       //var_dump($futureJ);
                        
              ?>
              <div class="futures-section">
              <?php $keySTR = str_replace('`','',$key);
              $keySTR = str_replace('#','',$keySTR);
              ?>
                <div class="futures-section-title"><span><?php echo $value; ?></span></div>
                <div class="futures-section-content">
              
                <table class="dtn-quote">
                <thead>
                <tr>
                <th scope="col"><span class="label-delivery"></span></th>
						<th scope="col">Open</th>
						<th scope="col">High</th>
					  <th scope="col">Low</th>
						<th scope="col">Last</th>
						<th scope="col">Change</th>
						<th scope="col">Close</th>
						<th scope="col">Updated</th>
						<th scope="col">More</th></tr>
            </thead>
            <tbody>
            
                  <?php
                    
                      
                        
                        foreach($futureJ as $future):
                          $future_utc = strtotime($future->bidDateTime);
                  // Convert to local time zone.
                  $future_datetime = $future_utc + $offset;
                  $closeTime = date('g:i A', $future_datetime);
                  $changePos = strpos($future->change->text,'-');
                  $dash = substr($future->change->text, 0,1);
                  $changeClass = 'change-pos';
if ($dash === '-') $changeClass = 'change-neg';
echo '<tr>';
echo '<td class="month">'.$future->month.'</td>';
echo '<td>'.$future->open->text.'</td>';
echo '<td>'.$future->high->text.'</td>';
echo '<td>'.$future->low->text.'</td>';
echo '<td>'.$future->last->text.'</td>';
echo '<td class="'.$changeClass.'">'.$future->change->text.'</td>';
echo '<td>'.$future->settlePrice->text.'</td>';
echo '<td>'.$closeTime.'</td>';
echo '<td class="dtn-links1"><a href="'.get_permalink(518).'?symbol='.$future->symbol->tickerSymbol.'"><img src="'.get_stylesheet_directory_uri().'/images/chart@2x.png" width="17" height="17" /></a> <a href="'.get_permalink(516).'?symbol='.$future->symbol->tickerSymbol.'"><img src="'.get_stylesheet_directory_uri().'/images/options@2x.png" width="17" height="17" /></a></td>';
echo '</tr>';
                        endforeach;
                        
                        
                      
                  ?>
                  </tbody>
                  </table>
                      
                </div>
              </div>
              <?php endforeach; ?>
            </div>



function filter_dropdown_callback() {

  $value = $_POST['value'];
  $property = $_POST['property'];

  $product_cat = $_POST['product_cat'];
  $product_fin = $_POST['finish'];
  $product_pro = $_POST['profile'];


  #If the product category drop down is changed
  if($property == 'product_cat'){
    $objects = new WP_Query(
      array(
        'post_type'             => 'product',
        'post_status'           => 'publish',
        'posts_per_page'        => -1,
        'tax_query'             => array(
          array(
              'taxonomy'      => 'product_cat',
              'field'         => 'slug',
              'terms'         => $product_cat,
              'operator'      => 'IN',
              'include_children' => false,
          )
        )
      )
    );

    $object_ids = array();
    $objects = $objects->get_posts();

    foreach( $objects as $object ) {
      array_push($object_ids, $object->ID);
    }

    $finishes = wp_get_object_terms( $object_ids, 'finish' );

    $finish_dropdown = '<select name="finish" data-attr="finish" class="product_finish"><option value="">FINISH</option>';

    $finish_ids = array();
    foreach($finishes as $finish){
      if($finish->parent == 0){
        $finish_dropdown .= '<option value="'. $finish->slug .'">' . $finish->name . '</option>';
        array_push($finish_ids, $finish->ID);
      }
    }
    $finish_dropdown .= '</select>';

    $profiles = wp_get_object_terms( $object_ids, 'profiles');

    if( count($profiles) < 1 ){
      $profile_dropdown = false;
    } else {
      $profile_dropdown = '<select name="profile" data-attr="profile" class="product_profile" disabled><option value="">PROFILE</option>';

      foreach($profiles as $profile){
        $profile_dropdown .= '<option value="'. $profile->slug .'">' . $profile->name . '</option>';
      }
      $profile_dropdown .= '</select>';
    }
  }
  #If the product finish is changed make adjustments to the finish dropdown
  else if($property == 'finish') {
    $objects = new WP_Query(
      array(
        'post_type'             => 'product',
        'post_status'           => 'publish',
        'posts_per_page'        => -1,
        'tax_query'             => array(
          'relation'      => 'AND',
          array(
              'taxonomy'      => 'product_cat',
              'field'         => 'slug',
              'terms'         => $product_cat,
              'operator'      => 'IN',
              'include_children' => false,
          ),
          array(
              'taxonomy'      => 'finish',
              'field'         => 'slug',
              'terms'         => $value,
              'operator'      => 'IN',
              'include_children' => false,
          )
        )
      )
    );

    $object_ids = array();
    $objects = $objects->get_posts();

    foreach( $objects as $object ) {
      array_push($object_ids, $object->ID);
    }


    $profiles = wp_get_object_terms( $object_ids, 'profiles');

    if( count($profiles) < 1 ){
      $profile_dropdown = false;
    } else {
      $profile_dropdown = '<select name="profile" data-attr="profile" class="product_profile"><option value="">PROFILE</option>';

      foreach($profiles as $profile){
        $profile_dropdown .= '<option value="'. $profile->slug .'">' . $profile->name . '</option>';
      }
      $profile_dropdown .= '</select>';
    }
  }

	$response = array();
	$response['success'] = true;
  if(isset($finish_dropdown)){
    $response['finish_dd'] = $finish_dropdown;
  }
  $response['profile_dd'] = $profile_dropdown;

	echo json_encode($response);

  die();
}
