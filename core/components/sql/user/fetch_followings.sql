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

SELECT c.`id` AS offset_id, u.`id`, u.`about`, u.`followers`, u.`posts`, u.`avatar`, u.`last_active`, u.`username`, u.`fname`, u.`lname`, u.`email`, u.`verified` FROM `<?php echo($data['t_conns']); ?>` c
	
	INNER JOIN `<?php echo($data['t_users']); ?>` u ON c.`following_id` = u.`id`

	WHERE c.`follower_id` = "<?php echo($data['user_id']); ?>"

	AND u.`active` IN ('1', '2')

	<?php if($data['offset']): ?>
		AND c.`id` < <?php echo($data['offset']); ?>
	<?php endif; ?>

	ORDER BY c.`id` DESC, u.`followers` DESC, u.`posts` DESC

	LIMIT <?php echo($data['limit']); ?>