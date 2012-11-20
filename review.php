<article id="post-<?php echo $content->id; ?>" class="post review">
	<div class="postcontrols">
	<?php if($post->get_access(User::identify())->edit): ?>
	<a class="edit_post" href="<?php URL::out('admin', array('page'=>'publish', 'id'=>$content->id)); ?>">Edit Review</a>
	<?php endif; ?>
	</div>

	<header>
		<h2>Review</h2>
		<h1><a href="/az<?php echo $content->asin; ?>"><?php echo $content->title_out; ?></a></h1>
		<p>Rating: <?php echo $content->rating; ?></p>
	</header>

	<?php echo $content->content_out; ?>

	<?php if($request->display_entry): ?>
	<section class="comments" itemprop="comment">
		<h1 id="comments">Comments</h1>
		<?php if($content->comments->moderated->count == 0): ?>
			<p><?php _e('There are no comments on this post.'); ?>
		<?php else: ?>
			<?php foreach($content->comments->moderated->comments as $comment): ?>
				<?php echo $theme->content($comment); ?>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php if($post->info->comments_disabled): ?>
			<p><?php _e('Sorry, commenting on this post is disabled.'); ?>
		<?php else: ?>
		<?php $post->comment_form()->out(); ?>
		<?php endif; ?>
	</section>
	<?php endif; ?>

</article>
