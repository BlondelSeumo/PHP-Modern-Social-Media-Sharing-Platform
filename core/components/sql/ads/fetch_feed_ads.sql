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

SELECT a.`id`, a.`user_id`, a.`cover`, a.`company`, a.`target_url`, a.`views`, a.`description`, a.`cta`, a.`time`, u.`avatar`, u.`username`, u.`verified`, CONCAT(u.`fname`, ' ', u.`lname`) AS name FROM `<?php echo($data['t_ads']); ?>` a
	
	INNER JOIN `<?php echo($data['t_users']); ?>` u ON a.`user_id` = u.`id`

	WHERE a.`status` = 'active'

	AND u.`active` = '1'

	AND u.`wallet` > 0

	<?php if(not_empty($data['udata'])): ?>
		AND (a.`audience` LIKE '%<?php echo($data["udata"]["country_id"]); ?>%')
	<?php endif; ?>

ORDER BY RAND() LIMIT 1;
