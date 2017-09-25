<?php
// blog_taxonomy : Taxonomy slug
//array('page','product','service') : post type array
add_action( 'save_post', 'wpse261307_set_cat_on_cpt' );
function wpse261307_set_cat_on_cpt( $post_id )
{
    global $wpdb;

    // Check for correct post type
    //if ( get_post_type() == 'our_company' ) // Set post or custom-post type name here
    if ( in_array(get_post_type(),array('page','product','service')) ) // Set post or custom-post type name here
    {  
        // Find the Category by slug
        $idObj = get_category_by_slug( 'blog_taxonomy' ); // Set your Category here
        // Get the Category ID
        $id = $idObj->term_id;

       // Set now the Category for this CPT
       wp_set_object_terms( $post_id, $id, 'category', true );
    }

}

function update_custom_terms($post_id) {

  // only update terms if it's a post-type-B post
  if ( !(in_array(get_post_type($post_id),array('page','product','service'))) ) {
    return;
  }

  // don't create or update terms for system generated posts
  if (get_post_status($post_id) == 'auto-draft') {
    return;
  }
    
  $term_title = get_the_title($post_id);
  $term_slug = get_post( $post_id )->post_name;

  $existing_terms = get_terms('blog_taxonomy', array(
    'hide_empty' => false
    )
  );

  foreach($existing_terms as $term) {
    if ($term->description == $post_id) {
      wp_update_term($term->term_id, 'blog_taxonomy', array(
        'name' => $term_title,
        'slug' => $term_slug
        )
      );
      return;
    }
  }

  wp_insert_term($term_title, 'blog_taxonomy', array(
    'slug' => $term_slug,
    'description' => $post_id
    )
  );
}

add_action('save_post', 'update_custom_terms');

add_action('delete_post', 'delete_custom_terms');
function delete_custom_terms($post_id) {
  $existing_terms = get_terms('blog_taxonomy', array(
    'hide_empty' => false
    )
  );

  foreach($existing_terms as $term) {
    if ($term->description == $post_id) {
      wp_delete_term($term->term_id, 'blog_taxonomy');
      return;
    }
  }
}

?>
