function addSubscriber($email)
{
	$apiKey = get_field('mailchimp_api_key' , 'option');
	$listId = get_field('mailchimp_list_id' , 'option');
	$memberId = md5( strtolower( $email ) );
	$name = 'AGAPE';

	$dataCenter = substr( $apiKey, strpos( $apiKey, '-' ) + 1 );
	$url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listId . '/members/' . $memberId;

	/*$json = json_encode([
	'email_address' => $email,
	'status'        => "subscribed", // "subscribed","unsubscribed","cleaned","pending"
	'merge_fields'  => [
	  'FNAME'     => $name, 
	]
	]); */
	$arr_subscriber = array();
	$arr_subscriber['email_address'] = $email;
	$arr_subscriber['status'] = "subscribed"; // "subscribed","unsubscribed","cleaned","pending"
	if( !empty( $name ) ){
	$arr_subscriber['merge_fields'] = array( "FNAME" => $name );
	}
	$json = json_encode( $arr_subscriber );

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	$result = curl_exec($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	return $httpCode;
}



// Load Product Color
add_action('wp_ajax_nopriv_altispace_load_product_image', 'altispace_load_product_image');
add_action('wp_ajax_altispace_load_product_image', 'altispace_load_product_image');
function altispace_load_product_image(){
	$image = '';
	$product_id = intval($_POST['product_id']);
	$color = '';
	$shape = '';
	if (isset($_POST['color'])) $color = esc_attr($_POST['color']);
	if (isset($_POST['shape'])) $shape = esc_attr($_POST['shape']);
	
	if ($shape && $color) {
		$term_color = get_term_by( 'name', $color, 'pa_color' );
		$color_code = get_field('code', 'pa_color_' . $term_color->term_id);
		
		$term = get_term_by( 'name', $shape, 'pa_shape' );
		
        $images = get_field('images', 'pa_shape_' . $term->term_id);
        if (isset($images[$color_code]) && $images[$color_code] != '') {
            $image = $images[$color_code]['sizes']['large'];
        } else if (isset($images['default']) && $images['default'] != '') {
            $image = $images['default']['sizes']['large'];
        }
		
	} else if ($color) {
		$term = get_term_by( 'name', $color, 'pa_color' );

		if ($product_id == 28) { // BEAMS
			$images = get_field('images', 'pa_color_' . $term->term_id);
			if (isset($images['beams']) && $images['beams'] != '') {
				$image = $images['beams']['sizes']['large'];
			}
		}
	} else if ($shape) {
		$term = get_term_by( 'name', $shape, 'pa_shape' );
		
        $images = get_field('images', 'pa_shape_' . $term->term_id);
        if (isset($images['default']) && $images['default'] != '') {
            $image = $images['default']['sizes']['large'];
        }
	}
	
	wp_send_json(array('image' => $image));
	wp_die();
}

function alitpsace_show_fields_in_order($fields) {
	$meta = get_post_meta($_REQUEST['post'], '_raq_request');
	$product_attributes = '';
	foreach ($meta[0]['raq_content'] as $item) {
		$product = wc_get_product( $item['product_id'] );
		$title = $product->get_title();
		
		$product_attributes .= '<br><br>';
		$product_attributes .= '<strong>' . $title . '</strong><br>';
		$product_attributes .= $item['product_sku'] . '<br>';
		
		if (isset($item['product_shape']) && $item['product_shape'] != '') {
			$product_attributes .= __('Shape', 'altispace').': ' . urldecode($item['product_shape']) . ' in<br>';
				
			if (isset($item['product_size']) && $item['product_size'] != '') {
				$size = urldecode($item['product_size']);
				$size = explode('x', str_replace('"', '', $size));
				$product_attributes .= __('Size', 'altispace').': ' . $size[0] . ' in x ' . $size[1] . ' in<br>';
				$product_attributes .= __('Thickness', 'altispace').': ' . $size[2] . ' in<br>';
			}
		} else {
		
			if (isset($item['product_size']) && $item['product_size'] != '') {
				$size = urldecode($item['product_size']);
				$size = explode('x', str_replace('"', '', $size));
				$product_attributes .= __('Length', 'altispace').': ' . $size[0] . ' in<br>';
				$product_attributes .= __('Height', 'altispace').': ' . $size[1] . ' in<br>';
				$product_attributes .= __('Thickness', 'altispace').': ' . $size[2] . ' in<br>';
			}
			
		}

		$product_attributes .= __('Color', 'altispace').': '.urldecode($item['product_color']).'<br>'; //@phpcs:ignore
		if (isset($item['product_custom_color']) && $item['product_custom_color'] == '1' && isset($item['product_custom_color_request'])) {
			$cr = json_decode(urldecode($item['product_custom_color_request']), true);
			if ($cr) {
				$product_attributes .= __('Custom Color Request', 'altispace') . '<br>';
				foreach ($cr as $cr_item) {
					$l = '';
					if ($cr_item['name'] == 'cr_company') {
						$l = __('Company', 'altispace');
					}
					if ($cr_item['name'] == 'cr_number') {
						$l = __('Number', 'altispace');
					}
					if ($cr_item['name'] == 'cr_color') {
						$l = __('Color', 'altispace');
					}
					if ($cr_item['name'] == 'cr_finish') {
						$l = __('Finish', 'altispace');
					}
					if ($cr_item['value'] != '') {
						$product_attributes .= $l.': '.$cr_item['value'].'<br>';
					}
				}
				$product_attributes .= '<br>';
			}
		}
		$product_attributes .= __('Quantity', 'altispace').': '.$item['quantity'].'<br>'; //@phpcs:ignore
	}
	$fields['ywraq_customer_additional_email_content']['desc'] .= $product_attributes;
	return $fields;
}
