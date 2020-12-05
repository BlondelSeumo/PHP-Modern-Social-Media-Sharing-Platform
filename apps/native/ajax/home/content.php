<?php 
# @*************************************************************************@
# @ @author Mansur Altamirov (Mansur_TL)                                    @
# @ @author_url 1: https://www.instagram.com/mansur_tl                      @
# @ @author_url 2: http://codecanyon.net/user/mansur_tl                     @
# @ @author_email: highexpresstore@gmail.com                                @
# @*************************************************************************@
# @ ColibriSM - The Ultimate Modern Social Media Sharing Platform           @
# @ Copyright (c) 21.03.2020 ColibriSM. All rights reserved.                @
# @*************************************************************************@

if (empty($cl["is_logged"])) {
    $data['status'] = 400;
    $data['error']  = 'Invalid access token';
}

else if ($action == 'load_more') {

    require_once(cl_full_path("core/apps/home/app_ctrl.php"));

    $data['err_code'] = 0;
    $data['status']   = 400;
    $offset           = fetch_or_get($_GET['offset'], 0);
    $html_arr         = array();

    if (is_posnum($offset)) {    

        $posts_ls = cl_get_timeline_feed(15, $offset);

        if (not_empty($posts_ls)) {
            foreach ($posts_ls as $cl['li']) {
                $html_arr[] = cl_template('timeline/post');
            }

            $data['status'] = 200;
            $data['html']   = implode("", $html_arr);
        }
    }
}