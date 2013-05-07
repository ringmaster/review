<?php

namespace Habari;

class ReviewPlugin extends Plugin
{
	/**
	 * Execute when plugin is activated
	 */
	public function action_plugin_activation()
	{
		// Add the new post type "review"
		Post::add_new_type( 'review' );
	}

	/**
	 * Execute when plugin is deactivated
	 */
	public function action_plugin_deactivation( )
	{
		// deactivate the "review" post type
		Post::deactivate_post_type( 'review' );
	}

	/**
	 * Provide singular and plural translations for the "review" post type
	 * @param string $type The type that Habari seeks the display name for
	 * @param string $foruse The intended use of the display name
	 * @return string The singular or plural translation of "review" if requested
	 */
	public function filter_post_type_display($type, $foruse) 
	{ 
		$names = array( 
			'review' => array(
				'singular' => _t( 'Review', 'review' ),
				'plural' => _t( 'Reviews', 'review' ),
			)
		); 
		return isset($names[$type][$foruse]) ? $names[$type][$foruse] : $type; 
	}

	/**
	 * Execute when the plugin is initialized
	 */
	public function action_init()
	{
		// Make the default review.php template available to the theme system from this directory
		$this->add_template('review', dirname($this->get_file()) . '/review.php');
	}

	/**
	 * Alter the publication form for posts of type "review"
	 * @param FormUI $form The publication form
	 * @param Post $post The post being edited
	 */
	public function action_form_publish_review( $form, $post )
	{
		$ratings = array(
			1 => '1 / 5',
			2 => '2 / 5',
			3 => '3 / 5',
			4 => '4 / 5',
			5 => '5 / 5',
		);
		$form->insert('content', FormControlSelect::create('rating', $post)->set_options($ratings)->label( _t('Rating', 'review')));

		$form->insert('content', FormControlText::create('asin', $post)->label( _t('ASIN', 'review')));
	}

	/**
	 * Make the ->rating field available directly on the post object
	 * @param integer $rating The incoming rating value (usually 0)
	 * @param Post $post The rated post
	 * @return integer The rating value
	 */
	public function filter_post_rating($rating, $post)
	{
		if(intval($post->info->rating) != 0) {
			$rating = $post->info->rating;
		}
		return $rating;
	}

	/**
	 * Make the ->asin field available directly on the post object
	 * @param string $asin The incoming asin value (usually '')
	 * @param Post $post The rated post
	 * @return string The ASIN value
	 */
	public function filter_post_asin($asin, $post)
	{
		if($post->info->asin != '') {
			$asin = $post->info->asin;
		}
		return $asin;
	}

	public function filter_rewrite_rules( $rules )
	{
		$rules[] = new RewriteRule( array(
			'name' => 'display_reviews',
			'parse_regex' => '%^reviews(?:/page/(?P<page>\d+))?/?$%i',
			'build_str' => 'reviews(/page/{$page})',
			'handler' => 'PluginHandler',
			'action' => 'display_reviews',
			'priority' => 7,
			'is_active' => 1,
			'description' => 'Displays multiple reviews',
		));

		return $rules;
	}

	public function action_plugin_act_display_reviews( $handler )
	{
		$paramarray['fallback'] = array(
			'review.multiple',
			'entry.multiple',
			'multiple',
			'home',
		);

		$default_filters = array(
			'content_type' => Post::type( 'review' ),
		);
		$paramarray['user_filters'] = $default_filters;

		return $handler->theme->act_display( $paramarray );
	}
}

?>