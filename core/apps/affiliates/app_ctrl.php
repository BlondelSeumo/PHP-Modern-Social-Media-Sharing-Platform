<?php 
# @*************************************************************************@
# @ @author Mansur Altamirov (Mansur_TL)									@
# @ @author_url 1: https://www.instagram.com/mansur_tl                      @
# @ @author_url 2: http://codecanyon.net/user/mansur_tl                     @
# @ @author_email: highexpresstore@gmail.com                                @
# @*************************************************************************@
# @ ColibriSM - The Ultimate Modern Social Media Sharing Platform           @
# @ Copyright (c) 21.03.2020 ColibriSM. All rights reserved.                @
# @*************************************************************************@

function cl_get_affiliate_payouts($offset = false, $limit = false) {
	global $db, $cl;

	if (empty($cl['is_logged'])) {
		return array();
	}

	$data          = array();
	$sql           = cl_sqltepmlate('apps/affiliates/sql/fetch_history', array(
		't_affils' => T_AFF_PAYOUTS,
		'offset'   => $offset,
		'user_id'  => $cl['me']['id'],
		'limit'    => $limit
	));

	$history = $db->rawQuery($sql);
	
	if (cl_queryset($history)) {
		foreach ($history as $row) {
			$row['amount'] = cl_money($row['amount']);
			$row['time']   = date('d M, Y h:m', $row['time']);
			$data[]        = $row;
		}
	}

	return $data;
}