<?php



add_action('wp_ajax_wowNow','wowNow');
add_action('wp_ajax_nopriv_wowNow','wowNow');

function wowNow(){
	$startDate = @$_POST['startDate'];
	$endDate = @$_POST['endDate'];
	$attraction = @$_POST['attractions'];
	$headers = [
			
		'Content-Type: application/json',
		'Ocp-Apim-Subscription-Key: 25f27806439943bc9c8e2386ddf67c5b'
	  ];
	//$session = ayudaLogin();

	//error_log(print_r($attraction,true));
//var_dump($session);
	//$sessionID = $session[0]->{'sessionID'};
		
		$avails = array();
		$faceID = array();
		$faceIDL = array();
		//$startDate = strtotime($startDate);
		//$endDate = strtotime($endDate);
		if($attraction != ''){

			
			
			
		$allbulletinsSL=get_posts(array('numberposts'=>-1, 'orderby' => array( 'menu_order' => 'ASC', 'title' => 'ASC', ) ,'post_type'=> 'location','tax_query' => array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'locations_categories',
				'field'    => 'slug',
				'terms'    => array( 'street-level-billboards' )
			),
			array(
				'taxonomy' => 'locations_attractions',
				'field' => 'slug',
				'terms' => $attraction,
			)
		
		), 'post_status'=>'publish'));
	}else{
		$allbulletinsSL=get_posts(array('numberposts'=>-1, 'orderby' => array( 'menu_order' => 'ASC', 'title' => 'ASC', ) ,'post_type'=> 'location','tax_query' => array(
			array(
				'taxonomy' => 'locations_categories',
				'field'    => 'slug',
				'terms'    => array( 'street-level-billboards' )
			)
		),'post_status'=>'publish'));
	}
		foreach( $allbulletinsSL as $sign ){
			if ($signID = get_field('location_id',$sign->ID)){
				
				$faceID[] = $signID;
			}
		}

		//error_log(print_r($faceID,true));
		if(count($faceID,1) > 0){
		$curl = curl_init();
		//$body = json_encode(array("faceIDs" => $faceID, "startDate"=> $startDate, "endDate"=> $endDate));
		array_walk($faceID, function(&$x) {$x = "'$x'";});
		$faceIDs = implode(',',$faceID);
		$body = '{
			"faceIds": [
			  '.$faceIDs.'
			],
			"availabilityPeriod": {
			  "start": "'.$startDate.'",
			  "end": "'.$endDate.'"
			},
			"spotLength": 15,
			"saturation": 1.0,
		  }';

		  
			
		//error_log($body);
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://ayudapreview.azure-api.net/booking/availabilities/faces/digital',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => $body,
			CURLOPT_HTTPHEADER => $headers,
		  ));
		  
		  $response = curl_exec($curl);
		  curl_close($curl);
		  //error_log(json_decode($response,true));
		  $avails[] = json_decode($response,true);
		}

		  
		
		

		  if($attraction != ''){
			  
			
			$allbulletinsFB=get_posts(array('numberposts'=>-1, 'orderby' => array( 'menu_order' => 'ASC', 'title' => 'ASC', ) ,'post_type'=> 'location','tax_query' => array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'locations_categories',
					'field'    => 'slug',
					'terms'    => array( 'freeway-billboards' )
				),
				array(
					'taxonomy' => 'locations_attractions',
					'field' => 'slug',
					'terms' => $attraction,
				)
			
			),'post_status'=>'publish'));
		  }else{
		$allbulletinsFB=get_posts(array('numberposts'=>-1, 'orderby' => array( 'menu_order' => 'ASC', 'title' => 'ASC', ) ,'post_type'=> 'location','tax_query' => array(
			array(
				'taxonomy' => 'locations_categories',
				'field'    => 'slug',
				'terms'    => array( 'freeway-billboards' )
			)
		),'post_status'=>'publish'));
	}
		foreach( $allbulletinsFB as $sign2 ){
			if ($signIDs = get_field('location_id',$sign2->ID)){
				
				$faceIDL[] = $signIDs;
				
			}
		}

		if (count($faceIDL) > 0){
			$curl2 = curl_init();
			//$body = json_encode(array("faceIDs" => $faceID, "startDate"=> $startDate, "endDate"=> $endDate));
			array_walk($faceIDL, function(&$x) {$x = "'$x'";});
			$faceIDsL = implode(',',$faceIDL);
			$body2 = '{
				"faceIds": [
				  '.$faceIDsL.'
				],
				"availabilityPeriod": {
				  "start": "'.$startDate.'",
				  "end": "'.$endDate.'"
				},
				"spotLength": 8,
				"saturation": 1.0,
			  }';
	
			  
			
			//error_log($body);
			curl_setopt_array($curl2, array(
				CURLOPT_URL => 'https://ayudapreview.azure-api.net/booking/availabilities/faces/digital',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => $body2,
				CURLOPT_HTTPHEADER => $headers,
			  ));
			  
			  $response2 = curl_exec($curl2);
			  curl_close($curl2);
	
			  $avails[] = json_decode($response2,true);
	
			}
			  
			  
			  $availOut = array();
			  //error_log('Avail Street: '.print_r($avails[0],true));
			 //error_log('Avail Free: '.print_r($avails[1],true));
			  $count = 0;
				foreach ($avails as $avail){
					foreach ($avail as $inv){
						foreach($inv as $v3){
							if($v3['availability'] == 'Available'){
								$availOut[] = $v3['faceId'];
								$count++;
							}
						}
					}
			 	 }
				  //error_log('COUNT: '.$count);
		  
		  
		  //$availOut = array();

		  //$availOut = array_merge($faceID,$faceIDL);
		  

	
		  
                    $argsSignAvail = array(
                  'posts_per_page'=> -1,
                    'orderby' => array( 'menu_order' => 'ASC', 'title' => 'ASC', ) ,
                    'post_type'=> 'location',
                    'tax_query' => array(
                      array(
                          'taxonomy' => 'locations_categories',
                          'field'    => 'slug',
                          'terms'    => array( 'street-level-billboards','freeway-billboards')
                      )
                  ),
                  'meta_query' => array(
                      array(
                          'key' => 'location_id',
                          'value' => $availOut,
                          'compare' => 'IN'
          
                      )
                      ),
                  'post_status'=>'publish');
				

				  //error_log('QUERY:'.print_r($argsSignAvail,true));
          
          
                    $allbulletinsAvail=new WP_Query($argsSignAvail);
          
					$mapMark = array();

					$attractions=get_posts(array('numberposts'=>-1, 'orderby' => array( 'menu_order' => 'ASC', 'title' => 'ASC', ) ,'post_type'=> 'location','tax_query' => array(
					  array(
						  'taxonomy' => 'locations_categories',
						  'field'    => 'slug',
						  'terms'    => array( 'attractions'),
					  )
				  ),'post_status'=>'publish'));

					foreach($attractions as $loc){ 
					  $location2=get_field('google_map_address',$loc->ID);
					  $hide = get_field('hide_on_full_map',$loc->ID);
					  $map_box_title=get_field('map_box_title',$loc->ID);
					  //$address3=get_field('address',$loc->ID);
					  $description=get_field('content',$loc->ID);
					  $map_box_image=get_field('map_box_image',$loc->ID);
					  $more_details_link=get_field('more_details_link',$loc->ID);
					  $map_box_title2=get_field('map_box_title2',$loc->ID);
					  $description2=get_field('content2',$loc->ID);
					  $map_box_image2=get_field('map_box_image2',$loc->ID);
					  $more_details_link2=get_field('more_details_link2',$loc->ID);
					  $custommarker=get_field('google_map_marker',$loc->ID);
					  $imag3=get_template_directory_uri().'/images/icons/freeway.svg';
					  $catname=array();
					  $cat2=get_the_terms( $loc->ID, 'locations_categories' );
					  $flagfoot=true;
					  $showlocation=false;
					  $classcat2=array();
				$class = '';
				$class2 = '';
					  $image="";
					  if(!empty($cat2)){
						  foreach($cat2 as $c){
							  $classcat2[]=$c->slug;
							  if($c->slug=='attractions' && !empty($custommarker)){
								  
								  $imag3=$custommarker;
					  $class2 = 'save';
							  }
							  if($c->slug=='attractions'){
								  $flagfoot=false;
							  }
							  
						  }
						  if(empty($imag3)){
							  if(in_array('freeway-billboards',$classcat2)){
								  
							  $imag3 = get_template_directory_uri().'/images/icons/freeway.svg';
							  }
							  if(in_array('the-donut',$classcat2)){
					  continue;
								  
								  $imag3=$custommarker;
	  
							  }
							  if(in_array('upcoming',$classcat2)){
								  
							  $imag3 = get_template_directory_uri().'/images/icons/coming-soon.svg';
							  }
		  
							  if(in_array('street-level-billboards',$classcat2)){
								  
								  $imag3 = get_template_directory_uri().'/images/icons/fullmotion.svg';
							  }
							  if(in_array('attractions',$classcat2)){
								  
								  $imag3 = get_template_directory_uri().'/images/marker.png';
					  
							  }
						  }
					  }
					  
				  
					  $mapMark[]='<div class="marker" data-attraction="yes" data-lat="'.esc_attr($location2['lat']).'" data-linkg="'.get_permalink($loc->ID).'" data-lng="'.esc_attr($location2['lng']).'" data-icon="'.$imag3.'"></div>';
				  }
                      
                      $result = array();
                          
                          $avail = array();
						  $availID = array();
                          foreach($allbulletinsAvail->posts as $post){
                              //var_dump($post);
							  $signID = get_field('location_id',$post->ID);
							  $content = apply_filters('the_content', get_post_field('post_content', $post->ID));
							  $address=get_field('address',$post->ID);
							  $address2 = explode('|',$address);
							  $address3 = strtolower($address2[0]);
							  $location = get_field('google_map_address',$post->ID);
							  $cat=get_the_terms($post->ID, 'locations_categories' );
							  $flagfoot=true;
							  $image2=get_template_directory_uri().'/images/icons/freeway.svg';
							  $classcat=array();
							  if(!empty($cat)){
								  foreach($cat as $c){
									  $classcat[]=$c->slug;
									  if($c->slug=='street-level-billboards'){
										  $steetway++;
										  $image2 = get_template_directory_uri().'/images/icons/fullmotion.svg';
										  $linkk = get_permalink($loc->ID);
									  }
									  if($c->slug=='attractions'){
										  $flagfoot=false;
										  $image2 = get_template_directory_uri().'/images/marker.png';
										  $linkk = '/locations';
									  }
									  if($c->slug=='attractions' && !empty($custommarker)){
										  $image2= $custommarker;
										  $linkk = '/locations';
				  
									  }
									  if($c->slug == 'the-donut'){
										  $image= $custommarker;
										  $linkk = get_permalink($post->ID);	
									  }
									  if($c->slug == 'freeway-billboards'){
										  $linkk = get_permalink($post->ID);	
									  }
									  if($c->slug == 'upcoming'){
										  $image2 = get_template_directory_uri().'/images/icons/coming-soon.svg';
										  $linkk = get_permalink($post->ID);	
									  }
								  }
							  }
							  $image=get_the_post_thumbnail_url($post->ID,'case-studies-image');
							  $contents = '<div class="availCont availLabel">';
							  $contents .='<a class="signLink" data-fancybox data-type="ajax" data-small-btn="false" href="https://wowmedianetwork.com/location-pop?id='.$post->ID.'" style="background-image:url('.$image.')"></a>';
							  $contents .= '<div class="postContent"><div class="checkMark"></div>';
                                $contents .= '<span class="signName">'.$post->post_title.'</span>';
								$contents .= '<span class="signAdd">'.$address3.'<br>Los Angeles</span>';
								if($post->post_title == 'WOW101N' || $post->post_title == 'WOW102S' ){
									$contents .= '<span class="signSOV"><span class="pricePer"></span><br /><ul><li>1 of 8 <div class="tooltip">SOV<span class="tooltiptext">Spots are
									sold as a Share of Voice (SOV). Each hour consists of 8 SOV. Each SOV is 7.5 minutes per hour</span></div></li><li> 8 Sec Spot </li><li>
									64 Sec Loop</li></ul></span>';
								}elseif($post->post_title == 'WOW103N' || $post->post_title == 'WOW104S' ){
									$contents .= '<span class="signSOV"><span class="pricePer"></span><br /><ul><li>1 of 8 <div class="tooltip">SOV<span class="tooltiptext">Spots are
									sold as a Share of Voice (SOV). Each hour consists of 8 SOV. Each SOV is 7.5 minutes per hour</span></div></li> <li>120 Sec
									Spot </li><li> 16 Min Loop</li></ul></span>';
								}else{
								$contents .= '<span class="signSOV"><span class="pricePer"></span><br /><ul><li>1 of 8 <div class="tooltip">SOV<span class="tooltiptext">Spots are
								sold as a Share of Voice (SOV). Each hour consists of 8 SOV. Each SOV is 7.5 minutes per hour</span></div></li><li>15, 30 or 60 Second Spot</li><li> 120
								Second Loop</li></ul></span>';
								}
								$contents .= '<div class="bottom"><label class="button buttonLabel" for="'.$post->ID.'">Select</label><input class="avail" type="checkbox" value="'.$signID.'" id="'.$post->ID.'" name="'.$post->post_title.'">';
								$contents .= '<a href="https://wowmedianetwork.com/location-pop?id='.$post->ID.'" data-small-btn="false" id="link-'.$post->ID.'" data-fancybox data-type="ajax" class="signDetails">Details</a></div></div></div>';


                              $contents .= '';
							  $contentMark = '<div class="marker" data-modal="https://wowmedianetwork.com/location-pop?id='.$post->ID.'" data-lat="'.esc_attr($location['lat']).'" data-linkg="'.get_permalink($post->ID).'" data-id="#link-'.$post->ID.'"  data-lng="'.esc_attr($location['lng']).'" data-icon="'.$image2.'"></div>';
							  $mapMark[] = $contentMark;

							  $availID[] = 'loc-'.$post->ID;
                              $avail[] = $contents;
                          }
						  
					$result['map'] = $mapMark;
                    $result['avail'] = $avail;
					$result['ids'] = $availID;
					
				
					
                        
		  //echo $body;
				
				echo json_encode($result);
		  die;

}


function getPricing($numberSigns,$days){

	$pricingTable = get_field('pricing','option');
	$pricePerDay = get_field('daily_increase','option');
	$price = '';


	$found = array_search($numberSigns,array_column($pricingTable, 'number_of_signs'));

	echo 'Found: '.$found.'<br />';

	if($found){
		$signs = $pricingTable[$found]['number_of_signs'];
		$onedayPrice = $pricingTable[$found]['price'];
		$discount = $pricingTable[$found]['discount'];
		$dec = $discount / 100.00;
		
		if($days < 14){
		$price = $onedayPrice + (($days - 1) * $pricePerDay);
		}else{
			$price = $onedayPrice + (($days - 1) * $pricePerDay) - $dec;
		}
	
		echo $price;
	}



	

	//var_dump($pricingTable);
	

}
function array_multi_search($needle,$haystack){
	foreach($haystack as $key=>$data){
	
	if(in_array($needle,$data))
	return $key;
	}
	}

add_action('wp_ajax_getProduct','getProduct');
add_action('wp_ajax_nopriv_getProduct','getProduct');
function getProduct(){

	$numberSigns = @$_POST['signs'];
	$days = @$_POST['days'];


	$product_id = get_field('sign_product','option');
	$product = wc_get_product($product_id);
	$variations = $product->get_available_variations();

	$result = array();

	$current_products = $product->get_children();
	$attributes = $product->get_attributes();
	foreach ($variations as $variation){
		//echo '<pre>' , var_dump($variation) , '</pre>';
		$attrib = $variation['attributes'];
		$found1 = array_search($numberSigns,array_column($attrib, 'attribute_pa_number-of-signs'));
		$found2 = array_search($days,array_column($attrib, 'attribute_pa_days'));

		if($attrib['attribute_pa_number-of-signs'] == $numberSigns && $attrib['attribute_pa_days'] == $days ){
			$variation_id = $variation['variation_id'];
			$price = $variation['display_price'];
		}

	
		
	}
	$formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);

		$result['var'] = $variation_id;
		$result['priceNF'] = $price;
		$result['price'] = $formatter->formatCurrency($price, 'USD');

		echo json_encode($result);
		die;
	
	//$found = array_search($numberSigns,array_column($variations, 'attribute_pa_number-of-signs'));
	
	//echo print_r($current_products,true);
}

add_action('wp_ajax_getSavings','getSavings');
add_action('wp_ajax_nopriv_getSavings','getSavings');
function getSavings(){

	$numberSigns = @$_POST['signs'];
	$days = @$_POST['days'];


	$product_id = get_field('sign_product','option');
	$product = wc_get_product($product_id);
	$variations = $product->get_available_variations();

	$result = array();
	$result['origDays'] = $days;

	$origDays = $days;

	$current_products = $product->get_children();
	$attributes = $product->get_attributes();
	foreach ($variations as $variation){
		$days = $origDays;
		//echo '<pre>' , var_dump($variation) , '</pre>';
		$attrib = $variation['attributes'];
		$found1 = array_search($numberSigns,array_column($attrib, 'attribute_pa_number-of-signs'));
		$found2 = array_search($days,array_column($attrib, 'attribute_pa_days'));

		if($attrib['attribute_pa_number-of-signs'] == $numberSigns && $attrib['attribute_pa_days'] == $days ){
			$variation_id = $variation['variation_id'];
			$price = $variation['display_price'];
		}


		if($attrib['attribute_pa_number-of-signs'] == 1 && $attrib['attribute_pa_days'] == $days ){
			
				$priceDayO = $variation['display_price'];
			}

			
		if($attrib['attribute_pa_number-of-signs'] == 1 && $attrib['attribute_pa_days'] == $days ){
			
			$priceOne = $variation['display_price'];
		}		

		if($days >= 8 ){

			


			$days = 8;
			if($attrib['attribute_pa_number-of-signs'] == 1 && $attrib['attribute_pa_days'] == 8 ){
			
				$priceDay = $variation['display_price'];
			}
		}else{
			if($attrib['attribute_pa_number-of-signs'] == 1 && $attrib['attribute_pa_days'] == 1 ){
			
				$priceDay = $variation['display_price'];
			}
		}

		

	
	}
	$formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);

		$result['var'] = $variation_id;
		$result['priceNF'] = $price;
		$result['priceOne'] = $priceOne;
		$result['priceDay'] = $priceDay;
		$result['priceF'] = $priceDayO;
		$result['price'] = $formatter->formatCurrency($price, 'USD');
		$result['newDays'] = $days;

		echo json_encode($result);
		die;
	
	//$found = array_search($numberSigns,array_column($variations, 'attribute_pa_number-of-signs'));
	
	//echo print_r($current_products,true);
}


add_action('wp_ajax_getUser','getUser');
add_action('wp_ajax_nopriv_getUser','getUser');

function getUser(){

	$current_user = wp_get_current_user();

	$response = array();

	$response['email'] = $current_user->user_email;
	$response['first'] = $current_user->user_firstname;
	$response['last'] = $current_user->user_lastname;
	$response['userID'] = $current_user->ID;
	$response['phone'] = get_user_meta( $current_user->ID, 'billing_phone', true );
	$response['company'] = get_user_meta( $current_user->ID, 'billing_company', true );

	echo json_encode($response);
	die;

}

function getEvents(){
	$today = date("Y-m-d\Th:i:s\Z");
	$apiUrl = 'https://app.ticketmaster.com/discovery/v2/events.json?dmaId=324&size=100&startDateTime='.$today.'&apikey=36bVFLctZdMLxNJA9aemuR6uwg9bwWO1&venueId=KovZ917ACh0,KovZ917AJe0,KovZpZAEkn6A&sort=date,name,asc';

	$curl3 = curl_init();
		//error_log($body);
		curl_setopt_array($curl3, array(
			CURLOPT_URL => $apiUrl,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
		  ));
		  
		  $response3 = curl_exec($curl3);
		  curl_close($curl3);
		  $responseArray = json_decode($response3,true);
		  	$eventName = '';
			  
		  foreach($responseArray['_embedded']['events'] as $event){?>
			<div class="eventRow">
			<div class="eventName"><?php echo $event['name']; ?></div>
                <div class="eventVenue"><?php echo $event['_embedded']['venues'][0]['name']; ?></div>
                <div class="eventDate"><?php echo date("m/d/Y",strtotime($event['dates']['start']['localDate'])); ?></div>
                
			  
			  
			  </div>
			  <?php 
			  
			  //echo '<pre>'.print_r($event,true).'</pre>';
		  }

		 
}





add_action('wp_ajax_getSelectedSigns','getSelectedSigns');
add_action('wp_ajax_nopriv_getSelectedSigns','getSelectedSigns');

function getSelectedSigns(){

	$signNames =  @$_POST['signNames'];
	

	$argsSignAvail = array(
		'posts_per_page'=> -1,
		  'orderby' => array( 'menu_order' => 'ASC', 'title' => 'ASC', ) ,
		  'post_type'=> 'location',
		  'tax_query' => array(
			array(
				'taxonomy' => 'locations_categories',
				'field'    => 'slug',
				'terms'    => array( 'street-level-billboards','freeway-billboards')
			)
		),
		'meta_query' => array(
			array(
				'key' => 'location_id',
				'value' => $signNames,
				'compare' => 'IN'

			)
			),
		'post_status'=>'publish');

		$allbulletinsAvail=new WP_Query($argsSignAvail);
          
                      $allbulletinsNotAvail = new WP_Query($argsSignNotAvail);
                      
                      $result = array();
                          
                          $avail = array();
						  $mapMark = array();
						  $attractions=get_posts(array('numberposts'=>-1, 'orderby' => array( 'menu_order' => 'ASC', 'title' => 'ASC', ) ,'post_type'=> 'location','tax_query' => array(
							array(
								'taxonomy' => 'locations_categories',
								'field'    => 'slug',
								'terms'    => array( 'attractions'),
							)
						),'post_status'=>'publish'));

						  foreach($attractions as $loc){ 
							$location2=get_field('google_map_address',$loc->ID);
							$hide = get_field('hide_on_full_map',$loc->ID);
							$map_box_title=get_field('map_box_title',$loc->ID);
							//$address3=get_field('address',$loc->ID);
							$description=get_field('content',$loc->ID);
							$map_box_image=get_field('map_box_image',$loc->ID);
							$more_details_link=get_field('more_details_link',$loc->ID);
							$map_box_title2=get_field('map_box_title2',$loc->ID);
							$description2=get_field('content2',$loc->ID);
							$map_box_image2=get_field('map_box_image2',$loc->ID);
							$more_details_link2=get_field('more_details_link2',$loc->ID);
							$custommarker=get_field('google_map_marker',$loc->ID);
							$imag3=get_template_directory_uri().'/images/icons/freeway.svg';
							$catname=array();
							$cat2=get_the_terms( $loc->ID, 'locations_categories' );
							$flagfoot=true;
							$showlocation=false;
							$classcat2=array();
					  $class = '';
					  $class2 = '';
							$image="";
							if(!empty($cat2)){
								foreach($cat2 as $c){
									$classcat2[]=$c->slug;
									if($c->slug=='attractions' && !empty($custommarker)){
										
										$imag3=$custommarker;
							$class2 = 'save';
									}
									if($c->slug=='attractions'){
										$flagfoot=false;
									}
									
								}
								if(empty($imag3)){
									if(in_array('freeway-billboards',$classcat2)){
										
									$imag3 = get_template_directory_uri().'/images/icons/freeway.svg';
									}
									if(in_array('the-donut',$classcat2)){
							continue;
										
										$imag3=$custommarker;
			
									}
									if(in_array('upcoming',$classcat2)){
										
									$imag3 = get_template_directory_uri().'/images/icons/coming-soon.svg';
									}
				
									if(in_array('street-level-billboards',$classcat2)){
										
										$imag3 = get_template_directory_uri().'/images/icons/fullmotion.svg';
									}
									if(in_array('attractions',$classcat2)){
										
										$imag3 = get_template_directory_uri().'/images/marker.png';
							
									}
								}
							}
							
						
							$mapMark[]='<div class="marker" data-lat="'.esc_attr($location2['lat']).'" data-linkg="'.get_permalink($loc->ID).'" data-lng="'.esc_attr($location2['lng']).'" data-icon="'.$imag3.'"></div>';
						}


                          foreach($allbulletinsAvail->posts as $post){
                              //var_dump($post);
							  $signID = get_field('location_id',$post->ID);
							  $location = get_field('google_map_address',$post->ID);
							  $address=get_field('address',$post->ID);
							  $address2 = explode('|',$address);
							  $address3 = strtolower($address2[0]);
							  $cat=get_the_terms($post->ID, 'locations_categories' );
							  $flagfoot=true;
							  $image2=get_template_directory_uri().'/images/icons/freeway.svg';
							  $classcat=array();
							  if(!empty($cat)){
								  foreach($cat as $c){
									  $classcat[]=$c->slug;
									  if($c->slug=='street-level-billboards'){
										  $steetway++;
										  $image2 = get_template_directory_uri().'/images/icons/fullmotion.svg';
										  $linkk = get_permalink($loc->ID);
									  }
									  if($c->slug=='attractions'){
										  $flagfoot=false;
										  $image2 = get_template_directory_uri().'/images/marker.png';
										  $linkk = '/locations';
									  }
									  if($c->slug=='attractions' && !empty($custommarker)){
										  $image2= $custommarker;
										  $linkk = '/locations';
				  
									  }
									  if($c->slug == 'the-donut'){
										  $image= $custommarker;
										  $linkk = get_permalink($post->ID);	
									  }
									  if($c->slug == 'freeway-billboards'){
										  $linkk = get_permalink($post->ID);	
									  }
									  if($c->slug == 'upcoming'){
										  $image2 = get_template_directory_uri().'/images/icons/coming-soon.svg';
										  $linkk = get_permalink($post->ID);	
									  }
								  }
							  }
							  
							  
							  
							  $image=get_the_post_thumbnail_url($post->ID,'case-studies-image');
							  $contents = '<div class="selectedSign" data-signName="'.$post->post_title.'" data-signID="'.$signID.'"><a class="signLink"href="https://wowmedianetwork.com/location-details?id='.$post->ID.'" data-small-btn="false" data-fancybox data-type="ajax" style="background-image:url('.$image.');" /></a><div class="postContent">';
                                $contents .= '<div class="colFlex"><span class="signName">'.$post->post_title.'</span>';
								$contents .= '<span class="address">'.$address3.'</span></div>';
								$contents .= '<span class="space"><strong>Space:</strong><br />1 of 8 <div class="tooltip">SOV<span class="tooltiptext">Spots are
								sold as a Share of Voice (SOV). Each hour consists of 8 SOV. Each SOV is 7.5 minutes per hour</span></div></span>';
								$contents .= '<span class="dates"><strong>Dates Booked:</strong><br /><span></span></span>';
								$contents .= '<span class="days"><strong>Total Days:</strong><br /><span></span></span>';
								$contents .= '<span class="pricePer pricePers"></span>';
								$contents .= '<span class="remove" id="'.$signID.'"><svg xmlns="http://www.w3.org/2000/svg" width="11.305" height="11.306" viewBox="0 0 11.305 11.306">
								<path id="Union_2" data-name="Union 2" d="M-1591.348,82.066l-4.239,4.24L-1597,84.893l4.239-4.24L-1597,76.414l1.413-1.414,4.239,4.24,4.24-4.24,1.413,1.414-4.239,4.239,4.239,4.24-1.413,1.413Z" transform="translate(1597 -75)" fill="#7c7c7c"/>
							  </svg></span>';
								$contents .= '</div>';
                              
                              $contents .= '';
                              $avail[] = $contents;
							  
							  $contentMark = '<div class="marker" data-lat="'.esc_attr($location['lat']).'" data-linkg="'.get_permalink($loc->ID).'"  data-lng="'.esc_attr($location['lng']).'" data-icon="'.$image2.'"></div>';
							  $mapMark[] = $contentMark;
                          }
						  $result['avail'] = $avail;
						  $result['map'] = $mapMark;

						  echo json_encode($result);
						  die;
						}

function getSignNames($selected){

							$signNames =  $selected;
							
						
							$argsSignAvail = array(
								'posts_per_page'=> -1,
								  'orderby' => array( 'menu_order' => 'ASC', 'title' => 'ASC', ) ,
								  'post_type'=> 'location',
								  'tax_query' => array(
									array(
										'taxonomy' => 'locations_categories',
										'field'    => 'slug',
										'terms'    => array( 'street-level-billboards','freeway-billboards')
									)
								),
								'meta_query' => array(
									array(
										'key' => 'location_id',
										'value' => $signNames,
										'compare' => 'IN'
						
									)
									),
								'post_status'=>'publish');
						
								$allbulletinsAvail=new WP_Query($argsSignAvail);
								  
											  
											  
											  $result = array();
												  
												  
												  foreach($allbulletinsAvail->posts as $post){
													  //var_dump($post);
													  
						
													  $result[] = $post->post_title;

										
												  }
						
												  return $result;
												}



add_action( "woocommerce_email_after_order_table", "custom_woocommerce_checkout_before_order_review", 10, 1);
function custom_woocommerce_checkout_before_order_review( $order ) {
$note = get_field('note',$order->id );
$sign = get_field('signs_selected',$order->id );
$signa = explode(',',$sign);
$count = count($signa);
$start = get_field('start_date',$order->id );
$end = get_field('end_date',$order->id );
echo '<h3>Order Details</h3><br /><strong>Number of Signs:</strong> '.$count.'<br /><strong>Signs Selected:</strong> '.$sign.'<br /><strong>Booking Dates:</strong> '.$start.' - '.$end.'<br /><strong>Order Note:</strong> '.$note;
}

function wpo_wcpdf_thank_you_link( $text, $order ) {
    if ( is_user_logged_in() ) {
        $pdf_url = wp_nonce_url( admin_url( 'admin-ajax.php?action=generate_wpo_wcpdf&template_type=invoice&order_ids=' . $order . '&my-account'), 'generate_wpo_wcpdf' );
        $text .= '<p><a href="'.esc_attr($pdf_url).'">Download WOWNOW Invoice</a></p>';
    }
    echo $text;
}


add_action('wp_ajax_getSavedOrders','getSavedOrders');
add_action('wp_ajax_nopriv_getSavedOrders','getSavedOrders');

function getSavedOrders(){

	if (is_user_logged_in() ){
		global $current_user;
		$content = array();
    	wp_get_current_user();
		$args = array(
			'author'        =>  $current_user->ID,
			'posts_per_page'=> -1,
			  'orderby' => array( 'menu_order' => 'ASC', 'title' => 'ASC', ) ,
			  'post_type'=> 'saved_orders',
				'post_status'=>'publish');
		$savedQuery = new WP_Query( $args );
		if ($savedQuery->have_posts()) {
			while ($savedQuery->have_posts()) {
				$savedQuery->the_post();
				$post = $savedQuery->post;
				$content[]= '<a href="/order-review?orderNumber='.$post->ID.'" target="_blank">'.$post->post_title.'</a><br />';
			}
			wp_reset_postdata();
			echo json_encode($content);
			die;
		}
		else{
			$content = 'No Saved Orders.';
			echo json_encode($content);
			die;
		}
	}else{
		
		$content = 'Please Log In To use This functionality';
		echo json_encode($content);
		die;
		}
}

function getAuthorizationURL($scope, $state) {
	$redirectURI = get_field('redirect_url','options');
	$clientId = get_field('api_key','options');
    // Create authorization URL
    $baseURL = "https://authz.constantcontact.com/oauth2/default/v1/authorize";
    $authURL = $baseURL . "?client_id=" . $clientId . "&scope=" . $scope . "+offline_access&response_type=code&state=" . $state . "&redirect_uri=" . $redirectURI;

    return $authURL;

}

function getAccessToken($code) {
	$redirectURI = get_field('redirect_url','options');
	$clientId = get_field('api_key','options');
	$clientSecret = get_field('client_secret','options');

    // Use cURL to get access token and refresh token
    $ch = curl_init();

    // Define base URL
    $base = 'https://authz.constantcontact.com/oauth2/default/v1/token';

    // Create full request URL
    $url = $base . '?code=' . $code . '&redirect_uri=' . $redirectURI . '&grant_type=authorization_code';
    curl_setopt($ch, CURLOPT_URL, $url);

    // Set authorization header
    // Make string of "API_KEY:SECRET"
    $auth = $clientId . ':' . $clientSecret;
    // Base64 encode it
    $credentials = base64_encode($auth);
    // Create and set the Authorization header to use the encoded credentials, and set the Content-Type header
    $authorization = 'Authorization: Basic ' . $credentials;
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($authorization, 'Content-Type: application/x-www-form-urlencoded'));

    // Set method and to expect response
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Make the call
    $result = curl_exec($ch);
    curl_close($ch);
	$res2 = json_decode($result,true);

	//echo print_r($res2,true);
	//echo print_r($res2['access_token'],true);
	update_field('auth_token',$res2['access_token'],'options');
	update_field('refresh_token',$res2['refresh_token'],'options');
    return $result;
	//echo print_r($result,true);
}

 

    if (! wp_next_scheduled ( 'wowNowCron' )) {
    wp_schedule_event(time(), 'daily', 'wowNowCron');
    }


add_action( 'wowNowCron', 'refreshToken' );

function refreshToken() {
	$refreshToken = get_field('refresh_token','options');
	$clientId = get_field('api_key','options');
	$clientSecret = get_field('client_secret','options');
    // Use cURL to get a new access token and refresh token
    $ch = curl_init();

    // Define base URL
    $base = 'https://authz.constantcontact.com/oauth2/default/v1/token';

    // Create full request URL
    $url = $base . '?refresh_token=' . $refreshToken . '&grant_type=refresh_token';
    curl_setopt($ch, CURLOPT_URL, $url);

    // Set authorization header
    // Make string of "API_KEY:SECRET"
    $auth = $clientId . ':' . $clientSecret;
    // Base64 encode it
    $credentials = base64_encode($auth);
    // Create and set the Authorization header to use the encoded credentials, and set the Content-Type header
    $authorization = 'Authorization: Basic ' . $credentials;
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($authorization, 'Content-Type: application/x-www-form-urlencoded'));

    // Set method and to expect response
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Make the call
    $result = curl_exec($ch);

	$res2 = json_decode($result,true);
	update_field('auth_token',$res2['access_token'],'options');
	update_field('refresh_token',$res2['refresh_token'],'options');
    curl_close($ch);
	
    return $result;
}


// add a link to the WP Toolbar
function custom_toolbar_link($wp_admin_bar) {
    $args = array(
        'id' => 'wpbeginner',
        'title' => 'Constant Contact Auth', 
        'href' => getAuthorizationURL('contact_data','new'), 
        'meta' => array(
            'class' => 'wpbeginner', 
            'title' => 'Auth Constant Contact'
            )
    );
    $wp_admin_bar->add_node($args);
}
add_action('admin_bar_menu', 'custom_toolbar_link', 999);


function sendToCC($email,$first,$last,$company,$phone){
$authToken = 'Bearer '.get_field('auth_token','options');

$ch = curl_init();

$url = 'https://api.cc.email/v3/contacts/sign_up_form';


$authorization = [
  'Authorization: '.$authToken,
  'Content-Type: application/json'
];
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $authorization);
curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$request = '
{
  "email_address": "'.$email.'",
  "first_name": "'.$first.'",
  "last_name": "'.$last.'",
  
  "company_name": "'.$company.'",
  "create_source": "WOWNOW",
  "phone_number": "'.$phone.'",
  "list_memberships": [
    "84167362-d517-11ec-8ee2-fa163e6b8330"
  ]
}
';
curl_setopt($ch,CURLOPT_POSTFIELDS,$request);
$result = curl_exec($ch);

	$res2 = json_decode($result,true);
	curl_close($ch);
	
    //echo print_r($result,true);
	



}



