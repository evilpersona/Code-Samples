function de_gforms_confirmation_dynamic_redirect( $confirmation, $form, $entry, $ajax ) {
    if ( $form['id'] == 2 ) {
        $acfdata        = $_SESSION["ticketlink"];
        $confirmation   = array( 'redirect' => $acfdata );
        session_destroy();
    }
    return $confirmation;
}

function wpse61678_terms_changed($object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids){
	if($taxonomy == 'markets' || $taxonomy == 'project_type'){
			
		 $added_tt_ids = array_diff($tt_ids, $old_tt_ids);

		 if( $append ){
			 //If appending terms - nothing was removed
			 $removed_tt_ids = array();
		 }else{
			 //Removed terms will be old terms, that were not specified in $tt_ids
			 $removed_tt_ids = array_diff($old_tt_ids, $tt_ids);
		 }

		/*Note problem 2, we would preferably like the term objects / IDs, 
		  Currently we have the taxonomy term IDs*/

		//Get all terms
		$all_terms = get_terms( $taxonomy, array('hide_empty'=>0,'fields' => 'ids'));

		$removed_terms =array();
		$added_terms =array();
		foreach( $all_terms as $term ){
			 $tt_id = (int) $term;
			 if( in_array( $tt_id, $removed_tt_ids) ){
				 $removed_terms[] = $term;
			 }elseif( in_array( $tt_id, $added_tt_ids) ){
				 $added_terms[] = $term;
			 }
		}
		if($taxonomy == 'markets'){

			$services_tax = wp_get_post_terms( $object_id, 'services', array('fields' => 'ids') );
					
			$result_remove_terms = array();
			if($removed_terms){
				foreach($removed_terms as $rm){
					$term = get_term( $rm, 'markets' );
					if($term->count < 1){
						$result_remove_terms[] = $rm;
					}
				}
			}
			foreach($services_tax as $serv){
				$arr_list = get_field('service_childs','services_'.$serv);
				
				if($arr_list){
					$array_sum = array_unique (array_merge ($arr_list, $added_terms));
					$array = array_diff($array_sum, $result_remove_terms);
					update_field('service_childs',$array,'services_'.$serv);
				}else{
					update_field('service_childs',$added_terms,'services_'.$serv);
				}
			}
		}
		
		if($taxonomy == 'project_type'){
			$markets_tax = wp_get_post_terms( $object_id, 'markets', array('fields' => 'ids') );
			$result_remove_terms = array();
			if($removed_terms){
				foreach($removed_terms as $rm){
					$term = get_term( $rm, 'project_type' );
					if($term->count < 1){
						$result_remove_terms[] = $rm;
					}
				}
			}
			foreach($markets_tax as $mark){
				$arr_list = get_field('market_childs','markets_'.$mark);
				if($arr_list){
					$array_sum = array_unique (array_merge ($arr_list, $added_terms));
					$array = array_diff($array_sum, $result_remove_terms);
					update_field('market_childs',$array,'markets_'.$mark);
				}else{
					update_field('market_childs',$added_terms,'markets_'.$mark);
				}
			}
		}
	}
 }
                               
                               
                               <?php
add_action('wp_ajax_nopriv_filter_port', 'filter_port');
add_action('wp_ajax_filter_port', 'filter_port');
function filter_port(){
    $keywords = '';
    $trip = '';
    $tax_query = array();
    $tax_query['relation'] = 'AND';
    $flag = false;
    foreach($_POST as $key => $filter){
        if($filter && $key != 'page'){
            if($key == 'keywords'){
                $keywords = $filter;
                
            }else if($filter == 'all'){}else{
                $trip = '1';
                $tax_query[] = array(
                    'taxonomy' => $key,
                    'field'    => 'term_id',
                    'terms'    => $filter,
                );
            }
        }
    }
 
    $query1 = get_posts( array(
        'posts_per_page' => -1,
        
        'post_type' => 'portfolio',
        
        'tax_query' => $tax_query,
        
        's' => $keywords,
        
    ) );
    $query2 = get_posts(array(
        'posts_per_page' => -1,
        'post_type' => 'portfolio',
        'meta_query' => array(
            'relation' => 'OR',
        array('key' => 'extra_term_1',
            'value' => $keywords,
            'compare' => 'LIKE'),
            array('key' => 'extra_term_2',
            'value' => $keywords,
            'compare' => 'LIKE'),
            array('key' => 'extra_term_3',
            'value' => $keywords,
            'compare' => 'LIKE'),
            array('key' => 'extra_term_4',
            'value' => $keywords,
            'compare' => 'LIKE'),
            array('key' => 'extra_term_5',
            'value' => $keywords,
            'compare' => 'LIKE'),
        )
    ));
    if($trip != '1'){
    $merged = array_merge( $query1, $query2 );
    }
    else{
        $merged = $query1;
    }
$post_ids = array();
foreach( $merged as $item ) {
    $post_ids[] = $item->ID;
}
$unique = array_unique($post_ids);
if(!$unique){
    $unique=array('0');
}
$args = array(
    'posts_per_page' => 15,
        'paged'   => $_POST['page'],
        'post_type' => 'portfolio',
        'post_status'=> array('publish'),
    'post__in' => $unique,
   'paged'   => $_POST['page'],
   'order'      => 'DESC',
        'orderby' => 'date',
);
$query = new WP_Query($args);
    ob_start();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post(); ?>
            <a href="<?php the_permalink(); ?>" class="item">
            <div class="image">
                <?php the_post_thumbnail('pr_single_gal'); ?>
            </div>
            <div class="title">
                <h2>
                    <?php the_title(); ?>
                </h2>
                <span class="btn_see-more default-btn">Learn More  <i class="fas fa-chevron-right"></i></span>
            </div>
            </a>
        <?php }
    }else {
        echo 'Sorry, no projects could be found that match your query. Try adjusting the filters and searching again.';
    }
    $content = ob_get_contents();
    ob_end_clean();
    $result = array();
    $result['content'] = $content;
    $result['max_num_pages'] = $query->max_num_pages;
    $result['page'] = $_POST['page'] + 1;
    echo json_encode($result);
    exit();
}

function compareByName($a, $b) {
  return strcmp($a["name"], $b["name"]);
}

add_action('wp_ajax_nopriv_change_select_options', 'change_select_options');
add_action('wp_ajax_change_select_options', 'change_select_options');
function change_select_options(){
    if($_POST['tax_id'] == 'all' && $_POST['name_tax'] == 'service_childs'){
        $childs = get_terms( [
            'taxonomy' => 'markets',
            'hide_empty' => true,
			'orderby' => 'name',
            'order' => 'ASC'
        ] );
    }else if($_POST['tax_id'] == 'all' && $_POST['name_tax'] == 'market_childs'){
        $childs = get_terms( [
            'taxonomy' => 'project_type',
            'hide_empty' => true,
			'orderby' => 'name',
             'order' => 'ASC'
        ] );
    }else{
        $skip = 'no';
        $childs = get_field($_POST['name_tax'],'term_'.$_POST['tax_id']);
    }
    
    $childs_arr = array();
    foreach($childs as $child){

        $term_child = get_term($child);
        if($skip == 'no'){
        if($_POST['name_tax'] == 'market_childs'){
            
                $args = array(
                    'post_type' => 'portfolio',
                    'posts_per_page' => -1,
                    'tax_query' => array(
                        'relationship' => 'AND',
                        array(
                            'taxonomy' => 'services',
                            'field' => 'term_id',
                            'terms' => $_POST['services'],
                        ),
                        array(
                            'taxonomy' => 'markets',
                            'field' => 'term_id',
                            'terms' => $_POST['tax_id'],
                        ),
                        array(
                            'taxonomy' => 'project_type',
                            'field' => 'term_id',
                            'terms' => $term_child->term_id,
                        )
                    )
                        );
                $postsff = get_posts($args);
                    
            
        if(0 == count($postsff)) {
            continue;
        }}else{
            $postsff = get_posts(array(
                'post_type' => 'portfolio',
                'posts_per_page' => -1,
                'tax_query' => array(
                    'relationship' => 'AND',
                    array(
                        'taxonomy' => 'services',
                        'field' => 'term_id',
                        'terms' => $_POST['tax_id'],
                    ),
                    array(
                        'taxonomy' => 'markets',
                        'field' => 'term_id',
                        'terms' => $term_child->term_id,
                    )
                )
                    ));
        
        
        if(0 == count($postsff)) {
            continue;
        }
    }}
        if(0 == $term_child->count){
            continue;
        }
        else{
            $childs_arr[] = array(
                "term_id" => $term_child->term_id,
                "name" => $term_child->name,
            );
        }
    }
	
	usort($childs_arr, 'compareByName');
    echo json_encode($childs_arr);
    exit();
}

add_action('wp_ajax_nopriv_filter_news', 'filter_news');
add_action('wp_ajax_filter_news', 'filter_news');
function filter_news(){
    $tax_query = array();
    $tax_query['relation'] = 'AND';
    if($_POST['category']){
        $tax_query[] = array(
            'taxonomy' => 'category',
            'field'    => 'term_id',
            'terms'    =>  $_POST['category'],
        );
    }
	

	$cur_page = $_POST['page'] > 1 ? $_POST['page'] : 1;
    $query = new WP_Query( array(
        'posts_per_page' => 6,
        'post_type' => array('post'),
        'post_status'=> array('publish'),
        'paged'   => $cur_page,
        'order' => 'DESC',
        'orderby' => 'date',
        'tax_query' => $tax_query,
        's' => $_POST['keywords']
      ) );
	  $max_pages = $query->max_num_pages;

      ob_start();
      if ($query->have_posts()) {
		  
		
		  
        while ($query->have_posts()) {
            $query->the_post(); 
			
			
			
		  
		
		  
			
			?>
             <div class="post" data-aos="fade-up"
                  data-aos-anchor-placement="top-bottom" data-id="<?php the_ID() ?>" >
                    <?php if (has_post_thumbnail()) { ?>
                        <a class="thumb" href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail(); ?>
                        </a>
                    <?php } ?>
                    <div class="post_text">
                        <span>
                            Posted <?php echo get_the_date('F jS'); ?>  In
                            <?php the_category(', '); ?> by <a href="<?php echo get_author_posts_url($post->post_author) ?>"><?php the_author(); ?></a>
                        </span>
                        <div class="wyz">
                            <a class="title-post" href="<?php the_permalink(); ?>">
                                <h2><?php the_title(); ?></h2>
                            </a>
                            <p><?php echo wp_trim_words(get_the_content(), 30, ' [...] '); ?></p>
                        </div>
                    </div>
                </div>
                
                
                <?php  ?>
                
    <?php } if( $max_pages == $cur_page ) {echo "<style>.btn_moreJS{display:none!important}</style>";}  
    } else {
        echo 'Not found';
        // no posts found
    }
    $content = ob_get_contents();
    ob_end_clean();
    wp_reset_postdata();
    $result = array();
    $result['content'] = $content;
    $result['max_num_pages'] = $query->max_num_pages;
    $result['page'] = $_POST['page'] + 1;
    echo json_encode($result);
    exit();
}
