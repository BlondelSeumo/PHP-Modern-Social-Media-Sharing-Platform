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

SELECT b.`id`, b.`user_id`, b.`profile_id`, u.`username`, u.`fname`, u.`lname`, u.`verified`, u.`avatar` FROM `<?php echo($data['t_blocks']); ?>` b

	INNER JOIN `<?php echo($data['t_users']); ?>` u ON b.`profile_id` = u.`id`

	WHERE b.`user_id` = <?php echo($data['user_id']); ?>

	AND u.`active` IN ('1', '2')

LIMIT 1000;