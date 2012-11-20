<?php

class ReviewPlugin extends Plugin
{
	public function action_plugin_activation( $plugin_file )
	{
		Post::add_new_type( 'review' );
	}

	public function action_plugin_deactivation( $plugin_file )
	{
		Post::deactivate_post_type( 'review' );
	}

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

	public function action_init()
	{
		$this->add_template('review', dirname($this->get_file()) . '/review.php');
	}

	public function action_form_publish_review( $form, $post )
	{
		$ratings = array(
			1 => '1 / 5',
			2 => '2 / 5',
			3 => '3 / 5',
			4 => '4 / 5',
			5 => '5 / 5',
		);
		$form->insert('content', new FormControlSelect('rating', $post, 'Rating', $ratings, 'admincontrol_select'));

		$form->insert('content', new FormControlText('asin', $post, 'ASIN', 'admincontrol_text'));

		$form->insert('content', new FormControlText('dcrowzip', $post, 'Data Crow Zip', 'admincontrol_text'));
	}

	public function filter_post_rating($rating, $post)
	{
		if(intval($post->info->rating) != 0) {
			$rating = $post->info->rating;
		}
		return $rating;
	}

	public function filter_post_asin($asin, $post)
	{
		if($post->info->asin != '') {
			$asin = $post->info->asin;
		}
		return $asin;
	}

	public function filter_post_dcrowzip($dcrowzip, $post)
	{
	     if($post->info->dcrowzip != '')
	     {
	        $dcrowzip = $post->info->dcrowzip;
             }
             return $dcrowzip;
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