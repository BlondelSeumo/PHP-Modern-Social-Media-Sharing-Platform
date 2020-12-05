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

SELECT * FROM `<?php echo($data['t_msgs']); ?>` 

	WHERE ((`sent_by` = <?php echo($data['user_one']); ?> AND `sent_to` = <?php echo($data['user_two']); ?> AND `deleted_fs1` = 'N') OR (`sent_to` = <?php echo($data['user_one']); ?> AND `sent_by` = <?php echo($data['user_two']); ?> AND `deleted_fs2` = 'N'))

ORDER BY `id` <?php echo($data['order']); ?>;