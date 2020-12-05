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

SELECT `id`,`followers`,`posts`,`avatar`,`about`,`last_active`,`username`,`fname`,`lname`,`email`,`verified`, `followers` FROM `<?php echo($data['t_users']); ?>`
	
	WHERE `active` = '1'

	<?php if($data['user_id']): ?>
		AND `id` NOT IN (SELECT `following_id` FROM `<?php echo($data['t_conns']); ?>` WHERE `follower_id` = "<?php echo($data['user_id']); ?>") 

		AND `id` != "<?php echo($data['user_id']); ?>"

		AND `id` NOT IN (SELECT b1.`profile_id` FROM `<?php echo($data['t_blocks']); ?>` b1 WHERE b1.`user_id` = <?php echo($data['user_id']); ?>)

		AND `id` NOT IN (SELECT b2.`user_id` FROM `<?php echo($data['t_blocks']); ?>` b2 WHERE b2.`profile_id` = <?php echo($data['user_id']); ?>)
	<?php endif; ?>

	ORDER BY `followers` DESC, `posts` DESC

<?php if($data['offset']): ?>
	LIMIT <?php echo($data['limit']); ?> OFFSET <?php echo($data['offset']); ?>;
<?php else: ?>
	LIMIT <?php echo($data['limit']); ?>;
<?php endif; ?>