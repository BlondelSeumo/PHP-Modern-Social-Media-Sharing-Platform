/*
@*************************************************************************@
@ @author Mansur Altamirov (Mansur_TL)									  @
@ @author_url 1: https://www.instagram.com/mansur_tl                      @
@ @author_url 2: http://codecanyon.net/user/mansur_tl                     @
@ @author_email: highexpresstore@gmail.com                                @
@*************************************************************************@
@ ColibriSM - The Ultimate Modern Social Media Sharing Platform           @
@ Copyright (c) 21.03.2020 ColibriSM. All rights reserved.                @
@*************************************************************************@
 */

SELECT posts.`id` as offset_id, posts.`publication_id`, posts.`type`, posts.`user_id` FROM `<?php echo($data['t_posts']); ?>` posts
	
	INNER JOIN `<?php echo($data['t_pubs']); ?>` pubs ON posts.`publication_id` = pubs.`id`

	WHERE posts.`type` = 'post'

	AND pubs.`status` = 'active'

	<?php if($data['offset']): ?>
		AND posts.`id` < <?php echo($data['offset']); ?>
	<?php endif; ?>

	ORDER BY posts.`id` DESC, pubs.`likes_count` DESC, pubs.`replys_count` DESC, pubs.`reposts_count` DESC

<?php if($data['limit']): ?>
	LIMIT <?php echo($data['limit']); ?>
<?php endif; ?>