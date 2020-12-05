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

SELECT r.`id`, r.`profile_id`, r.`comment`, r.`user_id`, r.`reason`, r.`seen`, r.`time`, CONCAT(u.`fname`, ' ', u.`lname`) AS name, u.`email`, u.`username`, u.`verified`, u.`avatar` FROM `<?php echo($data['t_reps']);?>` r
	
	INNER JOIN `<?php echo($data['t_users']);?>` u ON r.`user_id` = u.`id`

	WHERE r.`id` = <?php echo($data['rep_id']); ?>

LIMIT 1;