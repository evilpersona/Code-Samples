add_action( 'wp_ajax_addDonate', 'addDonate');
add_action( 'wp_ajax_nopriv_addDonate', 'addDonate');
function addDonate(){
    extract($_POST);

    global $woocommerce;
    $custom_price = $donate_total;
    $cart_item_data = array('nyp' => $custom_price);
    $woocommerce->cart->add_to_cart( 302, 1, 0, array(), $cart_item_data );
    $woocommerce->cart->calculate_totals();
    $woocommerce->cart->set_session();
    $woocommerce->cart->maybe_set_cart_cookies();

    if (is_ajax()) {
        exit();
    }
}


function get_date_time( $form ) {
    foreach ( $form['fields'] as &$field ) {
        if ( $field->type != 'select') {
            continue;
        }
        foreach ( get_field('date_and_time', 239) as $date ) {
            if(($date["registered_clients_count"] < get_field("maximum_per_date", 239)) && strtotime($date["datetime"]) > time()){
                $choices[] = array( 'text' => date("F j, Y \a\\t g:i A", strtotime($date["datetime"])), 'value' => $date["datetime"] );
            }
        }
        // $field->placeholder = 'Select a Date';
        $field->choices = $choices;
 
    }
    return $form;
}

// update Pages
add_action( 'gform_after_submission', 'set_post_content', 10, 2 );
function set_post_content( $entry, $form ) {
    $form = GFFormsModel::get_form_meta( $entry['form_id'] );
    $values= array();
    if($entry['form_id'] == 1)
    {
        $gfn = rgar($entry, '3');
        $gln = rgar($entry, '4');
        $date_time = rgar($entry, '8');
        $add_places =  $gfn || $gln ? 2 : 1;
        if($dates = get_field("date_and_time", 239)){
			foreach($dates as $key => $date){
				if($date["datetime"] == $date_time){
					$dates[$key] = array("datetime" => $date["datetime"], "registered_clients_count" => (int)($date["registered_clients_count"] + $add_places));
				}
			}

			update_field( 'date_and_time', $dates , 239 );
		}
    }
}
