<?php

/*
Plugin Name: [MTW] Shortcodes
Description: Require Muse to Wordpress theme. Shortcodes collection.
Author: Muse to Wordpress
Author URI: http://musetowordpress.com
Version: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

function mtw_title( $atts ) {
	$atts = shortcode_atts( array(
		'id' => ''
	), $atts );
	
	global $wp_query;

	
	if( ( is_tax() || is_category() ) && !in_the_loop() )
	{
		$term = $wp_query->get_queried_object();
    	return $term->name;
	}
	elseif( is_archive() && !in_the_loop() )
	{
		return get_post_type_object( get_post_type() )->label;
	}
	elseif ( is_home() && is_front_page()  && !in_the_loop()  ) 
	{
		return get_bloginfo('name');
	}
	elseif( is_home() && !is_front_page()  && !in_the_loop() )
	{
		return wp_title('', false);
	}
	elseif( !is_home() && is_front_page()  && !in_the_loop() )
	{
		return get_bloginfo('name');
	}
	else
	{
		return do_shortcode( get_the_title($atts['id']) );
	}
}
add_shortcode( 'mtw_title','mtw_title' );

function mtw_permalink( $atts ) {

	global $post;

	return get_permalink();
}
add_shortcode( 'mtw_permalink','mtw_permalink' );

function ttr_wp_content( $atts ) {

	$atts = shortcode_atts( array(
		'default' => 'values',
		'max' => ''
	), $atts );
	$post = get_post();

	$content = do_shortcode( apply_filters('the_content',  $post->post_content ) );

	if( $atts['max'] != '' )
	{
		$content = strip_tags($content);
		if( mb_strlen($content) > $atts['max'])
		{
			$content = mb_substr($content, 0, $atts['max']) . '...';
		}
	}

	return '<div class="wp-content">' . $content . '</div>';
}
add_shortcode( 'mtw_content','ttr_wp_content' );

function mtw_post_date()
{
	return get_the_date();
}
add_shortcode( 'mtw_date','mtw_post_date' );


function ttr_wp_thumbnail( $atts ) {
	$atts = shortcode_atts( array(
		'id' => '',
		'size'=>'',
		'w' => 0,
		'h' => 0
	), $atts );

	if($atts['id'] == '')
	{
		$atts['id'] = get_the_id();
	}

	if($atts['size'] == '')
	{
		$atts['size'] =  'thumbnail';
	}

	$size = $atts['size'];

	if( $atts['w'] != 0 && $atts['h'] != 0 )
	{
		$size = array( $atts['w'], $atts['h'], 1 );

		$get_size = apply_filters( 'mtw_thumbnail_get_size', $size );

		$src = apply_filters( 'mtw_thumbnail_src', wp_get_attachment_image_src(  get_post_thumbnail_id( $atts['id'] ) , $get_size ) );

		$src =  $src[0] ;

		ob_start();
		?>
		<div style="
			width:100%;
			height:<?php echo $size[1] ?>px;
			background-image:url(<?php echo $src ?>);
			background-size: cover;
		"></div>
		<?php
		return ob_get_clean();
	}

	return get_the_post_thumbnail($atts['id'], $size );
}
add_shortcode( 'mtw_thumbnail','ttr_wp_thumbnail' );




function ttr_wp_comment( $atts ) {
	$atts = shortcode_atts( array(
		'default' => 'values'
	), $atts );

	if(comments_open(the_id())){
		comment_form();
	}
}
add_shortcode( 'mtw_comment','ttr_wp_thumbnail' );

function ttr_wp_sidebar( $atts ) {
	$atts = shortcode_atts( array(
		'name' => ''
	), $atts );

	get_sidebar($atts['name']);
}
add_shortcode( 'mtw_sidebar','ttr_wp_sidebar' );


function mtw_categories( $atts ) {
	
	global $post;

	$terms = wp_get_post_terms( $post->ID, 'category' );
	$names = array();

	foreach ($terms as $key => $term) 
	{
		$names[] = $term->name;
	}

	return implode(', ', $names);
}
add_shortcode( 'mtw_cat','mtw_categories' );



function mtw_taxonomy( $atts ) {
	
	$atts = shortcode_atts( array(
		'tax' => 'category'
	), $atts );

	global $post;

	$terms = wp_get_post_terms( $post->ID, $atts['tax'] );
	$names = array();

	foreach ($terms as $key => $term) 
	{
		$names[] = '<a href="'. get_term_link( $term ) . '">' . $term->name . "</a>";
	}

	return implode(', ', $names);
}
add_shortcode( 'mtw_tax','mtw_taxonomy' );

function mtw_get_term_link( $atts )
{
	$atts = shortcode_atts( array(
		'term' => '',
		'taxonomy' => 'category'
	), $atts );
	$url = get_term_link( $atts['term'], $atts['taxonomy'] );

	if( is_string( $url ) )
	{
		return $url;
	}

}
add_shortcode( 'mtw_get_term_link' , 'mtw_get_term_link' );



function mtw_tags( $atts ) {
		
	global $post;

	$terms = wp_get_post_terms( $post->ID, 'post_tag' );
	$names = array();

	foreach ($terms as $key => $term) 
	{
		$names[] = $term->name;
	}

	if( $names )
	{
		return implode(', ', $names);
	}
}
add_shortcode( 'mtw_tags','mtw_tags' );


function mtw_archive_title( $atts ) {
	
	if( is_home() )
	{
		$title = get_post( get_option( 'page_for_posts' ) )->post_title;
	}
	else
	{
		$title = post_type_archive_title();	
	}

	return apply_filters( 'mtw_archive_title', $title );
	

	// do shortcode actions here
}
add_shortcode( 'mtw_archive_title','mtw_archive_title' );
?>