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

function cl_admin_save_config($key = "none", $val = "none") {
	global $db, $cl;

    if (in_array($key, array_keys($cl['config']))) {
        $db = $db->where('name', $key);
        $qr = $db->update(T_CONFIGS, array(
        	'value' => $val
        ));
    }
    else{
        return false;
    }
}