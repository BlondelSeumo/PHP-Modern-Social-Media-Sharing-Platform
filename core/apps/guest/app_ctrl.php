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

function cl_get_guest_feed($limit = false, $offset = false) {
	global $db, $cl;

	$data         =  array();
	$sql          =  cl_sqltepmlate("apps/guest/sql/fetch_guest_feed",array(
		"t_posts" => T_POSTS,
		"t_pubs"  => T_PUBS,
		"limit"   => $limit,
		"offset"  => $offset
 	));

	$query_res = $db->rawQuery($sql);

	if (cl_queryset($query_res)) {
		foreach ($query_res as $row) {
			$post_data = cl_raw_post_data($row['publication_id']);
			if (not_empty($post_data) && in_array($post_data['status'], array('active'))) {
				$post_data['offset_id'] = $row['offset_id'];
				$data[]                 = cl_post_data($post_data);
			}
		}
	}

	return $data;
}