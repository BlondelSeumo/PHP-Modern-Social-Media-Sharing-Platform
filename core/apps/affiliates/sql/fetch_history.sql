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

SELECT * FROM `<?php echo($data['t_affils']); ?>`
	
	WHERE `user_id` = <?php echo($data['user_id']); ?>

	<?php if(not_empty($data['offset'])): ?>
		AND `id` < <?php echo($data['offset']); ?>
	<?php endif; ?>

	ORDER BY `id` DESC 

<?php if(not_empty($data['limit'])): ?>
	LIMIT <?php echo($data['limit']); ?>;
<?php endif; ?>