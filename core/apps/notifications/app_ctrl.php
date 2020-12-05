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

function cl_get_notifications($args = array()) {
	global $db, $cl, $me;

	$args        = (is_array($args)) ? $args : array();
	$options     = array(
        "offset" => false,
        "limit"  => 10,
        "type"   => "notifs",
    );

    $args          = array_merge($options, $args);
    $offset        = $args['offset'];
    $limit         = $args['limit'];
    $type          = $args['type'];
	$sql           = cl_sqltepmlate('apps/notifications/sql/fetch_notifications', array(
		't_notifs' => T_NOTIFS,
		't_blocks' => T_BLOCKS,
		't_users'  => T_USERS,
		'offset'   => $offset,
		'user_id'  => $me['id'],
		'type'     => $type,
		'limit'    => $limit
	));

	$notifs = $db->rawQuery($sql);
	$data   = array();
	$update = array();

	if (cl_queryset($notifs)) {
		foreach ($notifs as $row) {
			$row['url']    = cl_link(cl_strf("@%s", $row['username']));
			$row['avatar'] = cl_get_media($row["avatar"]);
			$row['time']   = cl_time2str($row["time"]);

			if (in_array($row['subject'], array('reply','repost','like', 'mention'))) {
				$row['url'] = cl_link(cl_strf("thread/%d", $row['entry_id']));
			}

			if ($row['status'] == '0') {
				$update[] = $row['id'];
			}

			$data[] = $row;
		}

		if (not_empty($update)) {
			$db = $db->where('id', $update, 'IN');
			$qr = $db->update(T_NOTIFS, array('status' => '1'));
		}
	}

	return $data;
}