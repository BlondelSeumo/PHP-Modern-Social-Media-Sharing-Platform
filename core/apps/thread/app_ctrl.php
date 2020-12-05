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

function cl_get_thread_data($post_id = false) {
	global $db, $cl;

	if (not_num($post_id)) {
		return false;
	}

	$db   = $db->where('id', $post_id);
	$db   = $db->where('status', array('active','inactive','deleted'),'IN');
	$post = $db->getOne(T_PUBS);
	$data = array(
		'post'      => array(),
		'next'      => array(),
		'can_reply' => true
	);

	if (cl_queryset($post)) {
		$data['post'] = cl_post_data($post);
		$data['next'] = cl_get_thread_child_posts($post_id, 30);

		if (not_empty($cl['is_logged'])) {

			if (cl_is_blocked($data['post']['user_id'], $cl['me']['id'])) {
				$data['can_reply'] = false;
			}

			else if (cl_is_blocked($cl['me']['id'], $data['post']['user_id'])) {
				$data['can_reply'] = false;
			}
		}
	}

	return $data;
}

function cl_get_thread_parent_posts($post_obj = array()) {
    global $db, $cl;

    if (not_empty($post_obj['thread_id'])) {
        $db = $db->where('id', $post_obj['thread_id']);
        $db = $db->where('status', array('active','inactive','deleted'),'IN');
        $qr = $db->getOne(T_PUBS);

        if (cl_queryset($qr)) {
        	$cl['_'][] = cl_post_data($qr);

        	return cl_get_thread_parent_posts($qr);
        }
    }

    else {
    	return ((not_empty($cl['_'])) ? $cl['_'] : false);
    }
}

function cl_get_thread_child_posts($post_id = false, $limit = null, $offset = false, $offset_to = 'lt') {
    global $db, $cl;

    if (not_num($post_id)) {
    	return false;
    }

    $offset_to    = (($offset_to == 'gt') ? '>' : '<');
    $data         = array();
	$db           = $db->where('thread_id', $post_id);
	$db           = $db->where('status', array('active','inactive','deleted'),'IN');
	$db           = ((is_posnum($offset)) ? $db->where('id', $offset, $offset_to) : $db);
	$db           = $db->orderBy('id','DESC');
	$child_posts  = $db->get(T_PUBS, $limit);

	if (cl_queryset($child_posts)) {
		foreach ($child_posts as $row) {
			$row['replys'] = array();
			$db            = $db->where('thread_id', $row['id']);
			$db            = $db->where('status', array('active','inactive','deleted'),'IN');
			$db            = $db->orderBy('id','DESC');
			$replys        = $db->get(T_PUBS, 2);

			if (cl_queryset($replys)) {
				foreach ($replys as $reply) {
					$row['replys'][] = cl_post_data($reply);
				}
			}

			$data[] = cl_post_data($row);
		}
	}

	return $data;
}
