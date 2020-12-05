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

if ($action == 'upload_post_image') {
    if (empty($cl["is_logged"])) {
        $data['status'] = 400;
        $data['error']  = 'Invalid access token';
    }
    else {
        $data['err_code'] = "invalid_req_data";
        $data['status']   = 400;
        $post_data        = $me['draft_post'];

        if (not_empty($_FILES['image']) && not_empty($_FILES['image']['tmp_name'])) {
            if (empty($post_data)) {
                $post_id   = cl_create_orphan_post($me['id'], "image");
                $post_data = cl_get_orphan_post($post_id);

                cl_update_user_data($me['id'],array(
                    'last_post' => $post_id
                ));
            }
            
            if (not_empty($post_data) && $post_data["type"] == "image") {
                if (empty($post_data['media']) || count($post_data['media']) < 10) {
                    $file_info      =  array(
                        'file'      => $_FILES['image']['tmp_name'],
                        'size'      => $_FILES['image']['size'],
                        'name'      => $_FILES['image']['name'],
                        'type'      => $_FILES['image']['type'],
                        'file_type' => 'image',
                        'folder'    => 'images',
                        'slug'      => 'original',
                        'crop'      => array('width' => 300, 'height' => 300),
                        'allowed'   => 'jpg,png,jpeg,gif'
                    );

                    $file_upload = cl_upload($file_info);

                    if (not_empty($file_upload['filename'])) {
                        $post_id     =  $post_data['id'];
                        $img_id      =  $db->insert(T_PUBMEDIA, array(
                            "pub_id" => $post_id,
                            "type"   => "image",
                            "src"    => $file_upload['filename'],
                            "time"   => time(),
                            "json_data" => json(array(
                                "image_thumb" => $file_upload['cropped']
                            ),true)
                        ));

                        if (is_posnum($img_id)) {
                            $data['img']     = array("id" => $img_id, "url" => cl_get_media($file_upload['cropped']));
                            $data['status']  = 200;
                        }
                    }
                }
                else {
                    $data['err_code'] = "total_limit_exceeded";
                    $data['status']   = 400;
                }
            }
            else {
                cl_delete_orphan_posts($me['id']);
                cl_update_user_data($me['id'],array(
                    'last_post' => 0
                ));
            }
        }
    }
}

else if ($action == 'upload_post_video') {
    if (empty($cl["is_logged"])) {
        $data['status'] = 400;
        $data['error']  = 'Invalid access token';
    }
    else {
        $data['err_code'] = "invalid_req_data";
        $data['status']   = 400;
        $post_data        = $me['draft_post'];

        if (not_empty($_FILES['video']) && not_empty($_FILES['video']['tmp_name'])) {
            if (empty($post_data)) {
                $post_id   = cl_create_orphan_post($me['id'], "video");
                $post_data = cl_get_orphan_post($post_id);

                cl_update_user_data($me['id'],array(
                    'last_post' => $post_id
                ));
            }

            if (not_empty($post_data) && $post_data["type"] == "video") {
                if (empty($post_data['media'])) {
                    $file_info      =  array(
                        'file'      => $_FILES['video']['tmp_name'],
                        'size'      => $_FILES['video']['size'],
                        'name'      => $_FILES['video']['name'],
                        'type'      => $_FILES['video']['type'],
                        'file_type' => 'video',
                        'folder'    => 'videos',
                        'slug'      => 'original',
                        'allowed'   => 'mp4,mov,3gp,webm',
                    );

                    $file_upload = cl_upload($file_info);
                    $upload_fail = false;
                    $post_id     = $post_data['id'];

                    if (not_empty($file_upload['filename'])) {
                        try {
                            require_once(cl_full_path("core/libs/ffmpeg-php/vendor/autoload.php"));

                            $ffmpeg         = new FFmpeg(cl_full_path($config['ffmpeg_binary']));
                            $thumb_path     = cl_gen_path(array(
                                "folder"    => "images",
                                "file_ext"  => "jpeg",
                                "file_type" => "image",
                                "slug"      => "poster",
                            ));

                            $ffmpeg->input($file_upload['filename']);
                            $ffmpeg->set('-ss','3');
                            $ffmpeg->set('-vframes','1');
                            $ffmpeg->set('-f','mjpeg');
                            $ffmpeg->output($thumb_path)->ready();
                        } 

                        catch (Exception $e) {
                            $upload_fail = true;
                        }

                        if (empty($upload_fail)) {
                            $img_id      =  $db->insert(T_PUBMEDIA, array(
                                "pub_id" => $post_id,
                                "type"   => "video",
                                "src"    => $file_upload['filename'],
                                "time"   => time(),
                                "json_data" => json(array(
                                    "poster_thumb" => $thumb_path
                                ),true)
                            ));

                            if (is_posnum($img_id)) {
                                $data['status'] =  200;
                                $data['video']  =  array(
                                    "source"    => cl_get_media($file_upload['filename']),
                                    "poster"    => cl_get_media($thumb_path),
                                );
                            }
                        }
                    }
                }
                else {
                    $data['err_code'] = "total_limit_exceeded";
                    $data['status']   = 400;
                }
            }
            else {
                cl_delete_orphan_posts($me['id']);
                cl_update_user_data($me['id'],array(
                    'last_post' => 0
                ));
            }
        }
    }
}

else if ($action == 'delete_post_image') {
    if (empty($cl["is_logged"])) {
        $data['status'] = 400;
        $data['error']  = 'Invalid access token';
    }
    else {
        $data['err_code'] = "invalid_req_data";
        $data['status']   = 400;
        $image_id         = fetch_or_get($_POST['image_id'], 0);
        $post_data        = $me['draft_post'];

        if (not_empty($post_data) && is_posnum($image_id)) {
            $post_id    = $post_data['id'];
            $db         = $db->where('id', $image_id);
            $db         = $db->where('pub_id', $post_id);
            $image_data = $db->getOne(T_PUBMEDIA);

            if (cl_queryset($image_data)) {
                $json_data        = json($image_data['json_data']);
                $data['status']   = 200;
                $data['err_code'] = 0;
                $db               = $db->where('id', $image_id)->where('pub_id', $post_id);
                $qr               = $db->delete(T_PUBMEDIA);

                if (in_array($image_data['type'], array('image','video'))) {
                    cl_delete_media($image_data['src']);

                    if (not_empty($json_data['image_thumb'])) {
                        cl_delete_media($json_data['image_thumb']);
                    }
                }
            }

            if (count($post_data['media']) < 2) {
                cl_delete_orphan_posts($me['id']);
                cl_update_user_data($me['id'],array(
                    'last_post' => 0
                ));
            }
        }   
    }
}

else if ($action == 'delete_post_video') {
    if (empty($cl["is_logged"])) {
        $data['status'] = 400;
        $data['error']  = 'Invalid access token';
    }
    else {
        $data['err_code'] = "invalid_req_data";
        $data['status']   = 400;
        $post_data        = $me['draft_post'];

        if (not_empty($post_data)) {

            $data['err_code'] = "0";
            $data['status']   = 200;
            
            cl_delete_orphan_posts($me['id']);
            cl_update_user_data($me['id'],array(
                'last_post' => 0
            ));
        }   
    }
}

else if($action == 'import_og_data') {
    if (empty($cl["is_logged"])) {
        $data['status'] = 400;
        $data['error']  = 'Invalid access token';
    }

    else {
        $data['err_code'] = "invalid_req_data";
        $data['status']   = 400;

        if(empty($_POST['url']) || is_url($_POST['url'])) {
            $post_data = $me['draft_post'];
            $og_url    = fetch_or_get($_POST['url'], "");

            try {
                $og_data = get_meta_tags($og_url); 

                if (not_empty($og_data)) {
                    $og_data_vals     = array(
                        'title'       => cl_croptxt(fetch_or_get($og_data['title'], ""), 32, '..'),
                        'description' => cl_croptxt(fetch_or_get($og_data['description'], ""), 160, '..'),
                        'image'       => "",
                        'url'         => $og_url
                    );

                    if (not_empty($og_data['twitter:image']) && is_url($og_data['twitter:image'])) {
                        $og_data_vals['image'] = $og_data['twitter:image'];  
                    }

                    if (not_empty($og_data['twitter:url']) && is_url($og_data['twitter:url'])) {
                        $og_data_vals['url'] = $og_data['twitter:url'];  
                    }

                    if (empty($og_data_vals['title'])) {
                        $html_page = file_get_contents($og_url);

                        preg_match('/\<title\>(?P<title>.+?)\<\/?title\>/iu', $html_page, $matches);

                        if (not_empty($matches['title'])) {
                            $og_data_vals['title'] = cl_croptxt($matches['title'], 32, '..');
                        }
                    }

                    if (not_empty($og_data_vals['title']) && not_empty($og_data_vals['description']) ) {
                        $data['status']  = 200;
                        $data['og_data'] = $og_data_vals;
                    }
                }
            } 

            catch (Exception $e) {
                /*pass*/ 
            }
        }
    }
}

else if ($action == 'publish_new_post') {
    if (empty($cl["is_logged"])) {
        $data['status'] = 400;
        $data['error']  = 'Invalid access token';
    }
    else {
        $data['err_code'] = 0;
        $data['status']   = 400;
        $post_data        = $me['draft_post'];
        $curr_pn          = fetch_or_get($_POST['curr_pn'], "none");
        $post_text        = fetch_or_get($_POST['post_text'], "");
        $gif_src          = fetch_or_get($_POST['gif_src'], "");
        $og_data          = fetch_or_get($_POST['og_data'], array());
        $thread_id        = fetch_or_get($_POST['thread_id'], 0);
        $post_text        = cl_croptxt($post_text, 600);
        $thread_data      = array();

        if (not_empty($thread_id)) {
            $thread_data = cl_raw_post_data($thread_id);
        }

        if (not_empty($post_data) && not_empty($post_data["media"])) {
            $data['status'] =  200;
            $thread_id      =  ((is_posnum($thread_id)) ? $thread_id : 0);
            $post_id        =  $post_data['id'];
            $post_text      =  cl_upsert_htags($post_text);
            $mentions       =  cl_get_user_mentions($post_text);
            $up             =  cl_update_post_data($post_id, array(
                "text"      => cl_text_secure($post_text),
                "status"    => "active",
                "thread_id" => $thread_id,
                "time"      => time()
            ));

            if (empty($thread_id)) {
                $db->insert(T_POSTS, array(
                    "user_id"        => $me['id'],
                    "publication_id" => $post_id,
                    "time"           => time()
                ));

                $data['posts_total'] = ($me['posts'] += 1);
                
                cl_update_user_data($me['id'], array(
                    'posts' => $data['posts_total']
                ));
            }

            else {
                $data['replys_total'] = cl_update_thread_replys($thread_id, 'plus');

                cl_update_post_data($post_id, array(
                    "target" => "pub_reply"
                ));

                if ($thread_data['user_id'] != $me['id']) {
                    cl_notify_user(array(
                        'subject'  => 'reply',
                        'user_id'  => $thread_data['user_id'],
                        'entry_id' => $post_id
                    ));
                }
            }

            if (in_array($curr_pn, array('home','thread'))) {
                $post_data    = cl_raw_post_data($post_id);
                $cl['li']     = cl_post_data($post_data);
                $data['html'] = cl_template('timeline/post');
            }

            if (not_empty($mentions)) {
                cl_notify_mentioned_users($mentions, $post_id);
            }
        }

        else {
            if (not_empty($post_text) || not_empty($gif_src)) {
                $thread_id      = ((is_posnum($thread_id)) ? $thread_id : 0);
                $post_text      = cl_upsert_htags($post_text);
                $mentions       = cl_get_user_mentions($post_text);
                $insert_data    = array(
                    "user_id"   => $me['id'],
                    "text"      => cl_text_secure($post_text),
                    "status"    => "active",
                    "type"      => "text",
                    "thread_id" => $thread_id,
                    "time"      => time()
                );

                if (empty($gif_src) && not_empty($og_data)) {
                    $insert_data['og_data'] = json($og_data, true);
                }

                $post_id = $db->insert(T_PUBS, $insert_data);

                if (is_posnum($post_id)) {

                    $data['status'] = 200;

                    if (empty($thread_id)) {
                        $db->insert(T_POSTS, array(
                            "user_id" => $me['id'],
                            "publication_id" => $post_id,
                            "time" => time()
                        ));


                        $data['posts_total'] = ($me['posts'] += 1);

                        cl_update_user_data($me['id'], array(
                            'posts' => $data['posts_total']
                        ));
                    }

                    else {
                        $data['replys_total'] = cl_update_thread_replys($thread_id,'plus');

                        cl_update_post_data($post_id, array(
                            "target" => "pub_reply"
                        ));

                        if ($thread_data['user_id'] != $me['id']) {
                            cl_notify_user(array(
                                'subject'  => 'reply',
                                'user_id'  => $thread_data['user_id'],
                                'entry_id' => $post_id
                            ));
                        }
                    }

                    if (not_empty($gif_src) && is_url($gif_src)) {
                        $db->insert(T_PUBMEDIA, array(
                            "pub_id" => $post_id,
                            "type"   => "gif",
                            "src"    => $gif_src,
                            "time"   => time(),
                        ));

                        cl_update_post_data($post_id, array(
                            "type" => "gif"
                        ));
                    }

                    if (in_array($curr_pn, array('home','thread'))) {
                        $post_data    = cl_raw_post_data($post_id);
                        $cl['li']     = cl_post_data($post_data);
                        $data['html'] = cl_template('timeline/post');
                    }

                    if (not_empty($mentions)) {
                        cl_notify_mentioned_users($mentions, $post_id);
                    }
                }
            }
        }

        cl_delete_orphan_posts($me['id']);
        cl_update_user_data($me['id'], array(
            'last_post' => 0
        ));
    }
}

else if($action == 'get_draft_post') {
    $data['status']   = 404;
    $data['err_code'] = 0;
    if (empty($cl["is_logged"])) {
        $data['status'] = 400;
        $data['error']  = 'Invalid access token';
    }
    else {
        if (not_empty($me['draft_post'])) {
            if ($me['draft_post']['type'] == "image") {
                if (not_empty($me['draft_post']['media'])) {
                    $data['images'] = array();
                    $data['status'] = 200;
                    $data['type']   = "image";

                    foreach ($me['draft_post']['media'] as $row) {
                        $data['images'][] = array(
                            "id" => $row["id"],
                            "url" => cl_get_media($row["src"]),
                        );
                    }
                }
            }
            else if($me['draft_post']['type'] == "video") {

                $video_src = fetch_or_get($me['draft_post']['media'][0],false);
               
                if (not_empty($video_src)) {
                    $data['video']  = array();
                    $data['status'] = 200;
                    $data['type']   = "video";

                    $data['video']  = array(
                        "poster"    => cl_get_media($video_src['x']['poster_thumb']),
                        "source"    => cl_get_media($video_src['src'])
                    );
                }
            }
        }
    }
}

else if($action == 'follow') {
    if (empty($cl["is_logged"])) {
        $data['status'] = 400;
        $data['error']  = 'Invalid access token';
    }
    else {
        $data['status']   = 404;
        $data['err_code'] = 0;
        $user_id          = fetch_or_get($_POST['id'],0);


        if (is_posnum($user_id) && $me['id'] != $user_id) {
            
            $udata = cl_raw_user_data($user_id);

            if (not_empty($udata) && cl_is_blocked($me['id'], $user_id) != true && cl_is_blocked($user_id, $me['id']) != true) {
                if (cl_is_following($me['id'], $user_id)) {
                    if (cl_unfollow($me['id'], $user_id)) {
                        
                        $me_followings           = ($me['following'] -= 1);
                        $udata_followers         = ($udata['followers'] -= 1);
                        $data['status']          = 200;
                        $data['total_following'] = $me_followings;

                        cl_update_user_data($me['id'], array(
                            'following' => (($me_followings < 0) ? 0 : $me_followings)
                        ));

                        cl_update_user_data($user_id, array(
                            'followers' => (($udata_followers < 0) ? 0 : $udata_followers)
                        ));

                        $db = $db->where('notifier_id', $me['id']);
                        $db = $db->where('recipient_id', $user_id);
                        $db = $db->where('subject', 'subscribe');
                        $db = $db->where('entry_id', $user_id);
                        $qr = $db->delete(T_NOTIFS);

                        if ($udata['profile_privacy'] == 'followers') {
                            $data['refresh'] = 1;
                        }
                    }
                }

                else{
                    if (cl_follow($me['id'], $user_id)) {

                        $data['status']          = 200;
                        $data['total_following'] = ($me['following'] += 1);

                        cl_update_user_data($me['id'], array(
                            'following' => $data['total_following']
                        ));

                        cl_update_user_data($user_id, array(
                            'followers' => ($udata['followers'] += 1)
                        ));

                        cl_notify_user(array(
                            'subject'  => 'subscribe',
                            'user_id'  => $user_id,
                            'entry_id' => $user_id,
                        ));

                        if ($udata['profile_privacy'] == 'followers') {
                            $data['refresh'] = 1;
                        }
                    }
                }
            }
        }
    }
}

else if($action == 'delete_post') {
    if (empty($cl["is_logged"])) {
        $data['status'] = 400;
        $data['error']  = 'Invalid access token';
    }
    else {
        $data['err_code'] = 0;
        $data['status']   = 400;
        $post_id          = fetch_or_get($_POST['id'], 0);

        if (is_posnum($post_id)) {
            $post_data = cl_raw_post_data($post_id);

            if (not_empty($post_data) && ($post_data['user_id'] == $me['id'] || not_empty($cl['is_admin']))) {

                $post_owner = cl_raw_user_data($post_data['user_id']);

                if (not_empty($post_owner)) {
                    if ($post_data['target'] == 'publication') {

                        $data['posts_total'] = ($post_owner['posts'] -= 1);
                        $data['posts_total'] = ((is_posnum($data['posts_total'])) ? $data['posts_total'] : 0);

                        cl_update_user_data($post_data['user_id'], array(
                            'posts' => $data['posts_total']
                        ));

                        $db = $db->where('publication_id', $post_id);
                        $qr = $db->delete(T_POSTS);
                    }

                    else {
                        $data['url'] = cl_link(cl_strf("thread/%d", $post_data['thread_id']));

                        cl_update_thread_replys($post_data['thread_id'], 'minus');
                    }
                    
                    cl_recursive_delete_post($post_id);
                    
                    $data['status'] = 200;
                }
            }
        }
    }
}

else if($action == 'like_post') {
    if (empty($cl["is_logged"])) {
        $data['status'] = 400;
        $data['error']  = 'Invalid access token';
    }
    else {
        $data['err_code'] = 0;
        $data['status']   = 400;
        $post_id          = fetch_or_get($_POST['id'], 0);

        if (is_posnum($post_id)) {
            $post_data = cl_raw_post_data($post_id);

            if (not_empty($post_data)) {
                if (cl_has_liked($me['id'], $post_id) != true) {
                    $db->insert(T_LIKES, array(
                        'pub_id'  => $post_id,
                        'user_id' => $me['id'],
                        'time'    => time()
                    ));

                    $likes_count         = ($post_data['likes_count'] += 1);
                    $data['status']      = 200;
                    $data['likes_count'] = $likes_count;

                    cl_update_post_data($post_id, array(
                        'likes_count' => $likes_count
                    ));

                    if ($post_data['user_id'] != $me['id']) {
                        cl_notify_user(array(
                            'subject'  => 'like',
                            'user_id'  => $post_data['user_id'],
                            'entry_id' => $post_id,
                        ));
                    }
                }
                else {
                    $db                  = $db->where('pub_id', $post_id);
                    $db                  = $db->where('user_id', $me['id']);
                    $qr                  = $db->delete(T_LIKES);
                    $data['status']      = 200;
                    $likes_count         = ($post_data['likes_count'] -= 1);
                    $data['likes_count'] = $likes_count;

                    cl_update_post_data($post_id, array(
                        'likes_count' => $likes_count
                    ));

                    $db = $db->where('notifier_id', $me['id']);
                    $db = $db->where('recipient_id', $post_data['user_id']);
                    $db = $db->where('subject', 'like');
                    $db = $db->where('entry_id', $post_id);
                    $rq = $db->delete(T_NOTIFS);
                }
            }
        }
    }
}

else if($action == 'show_likes') {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $post_id          = fetch_or_get($_POST['id'], 0);

    if (is_posnum($post_id)) {
        $post_data = cl_raw_post_data($post_id);
   
        if (not_empty($post_data)) {
            $cl['liked_post']  = $post_id;
            $cl['post_likes']  = cl_get_post_likes($post_id, 30);
            $cl['likes_count'] = cl_number($post_data['likes_count']);
            $data['status']    = 200;
            $data['html']      = cl_template('timeline/modals/likes');
        }
    }
}

else if($action == 'load_likes') {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $post_id          = fetch_or_get($_GET['id'], 0);
    $offset           = fetch_or_get($_GET['offset'], 0);

    if (is_posnum($post_id) && is_posnum($offset)) {
        $cl['post_likes'] = cl_get_post_likes($post_id, 30, $offset);
        $html_arr         = array();
   
        if (not_empty($cl['post_likes'])) {
            foreach ($cl['post_likes'] as $cl['li']) {
                $html_arr[] = cl_template('timeline/includes/like_li');
            }

            $data['status'] = 200;
            $data['html']   = implode('', $html_arr);
        }
    }
}

else if($action == 'bookmark_post') {
    if (empty($cl["is_logged"])) {
        $data['status'] = 400;
        $data['error']  = 'Invalid access token';
    }
    else {
        $data['err_code'] = 0;
        $data['status']   = 400;
        $post_id          = fetch_or_get($_POST['id'], 0);
        $a                = fetch_or_get($_POST['a'], 'none');

        if (is_posnum($post_id)) {
            $post_data = cl_raw_post_data($post_id);

            if (not_empty($post_data)) {
                if (cl_has_saved($me['id'], $post_id) != true) {
                    $db->insert(T_BOOKMARKS, array(
                        'publication_id' => $post_id,
                        'user_id'        => $me['id'],
                        'time'           => time()
                    ));

                    $data['status']      = 200;
                    $data['status_code'] = '1';
                }
                else {
                    $db                  = $db->where('publication_id', $post_id);
                    $db                  = $db->where('user_id', $me['id']);
                    $qr                  = $db->delete(T_BOOKMARKS);
                    $data['status']      = 200;
                    $data['status_code'] = '0';
                }
            }
        }
    }
}

else if($action == 'repost') {
    if (empty($cl["is_logged"])) {
        $data['status'] = 400;
        $data['error']  = 'Invalid access token';
    }
    else {
        $data['err_code'] = 0;
        $data['status']   = 400;
        $post_id          = fetch_or_get($_POST['id'], 0);

        if (is_posnum($post_id)) {
            $post_data = cl_raw_post_data($post_id);

            if (not_empty($post_data)) {
                if (cl_has_reposted($me['id'], $post_id) != true) {
                    $db->insert(T_POSTS, array(
                        'publication_id'  => $post_id,
                        'user_id'         => $me['id'],
                        'type'            => 'repost',
                        'time'            => time()
                    ));

                    $reposts_count         = ($post_data['reposts_count'] += 1);
                    $data['status']        = 200;
                    $data['reposts_count'] = $reposts_count;

                    cl_update_post_data($post_id, array(
                        'reposts_count' => $reposts_count
                    ));

                    if ($post_data['user_id'] != $me['id']) {
                        cl_notify_user(array(
                            'subject'  => 'repost',
                            'user_id'  => $post_data['user_id'],
                            'entry_id' => $post_id,
                        ));
                    }
                }
                else {
                    $db     = $db->where('publication_id', $post_id);
                    $db     = $db->where('user_id', $me['id']);
                    $db     = $db->where('type', 'repost');
                    $repost = $db->getOne(T_POSTS);

                    if (cl_queryset($repost)) {
                        $db                    = $db->where('publication_id', $post_id);
                        $db                    = $db->where('user_id', $me['id']);
                        $db                    = $db->where('type', 'repost');
                        $qr                    = $db->delete(T_POSTS);
                        $data['status']        = 200;
                        $data['repost_id']     = $repost['id'];
                        $reposts_count         = ($post_data['reposts_count'] -= 1);
                        $data['reposts_count'] = $reposts_count;

                        cl_update_post_data($post_id, array(
                            'reposts_count' => $reposts_count
                        ));

                        $db = $db->where('notifier_id', $me['id']);
                        $db = $db->where('recipient_id', $post_data['user_id']);
                        $db = $db->where('subject', 'repost');
                        $db = $db->where('entry_id', $post_id);
                        $rq = $db->delete(T_NOTIFS);
                    }
                }
            }
        }
    }
}

else if($action == 'update_msb_indicators') {
    if (empty($cl["is_logged"])) {
        $data['status'] = 400;
        $data['error']  = 'Invalid access token';
    }
    else {
        $data['status']        = 200;
        $data['notifications'] = cl_total_new_notifs();
        $data['messages']      = cl_total_new_messages();
    }
}

else if($action == 'search') {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $search_query     = fetch_or_get($_GET['query'], false); 
    $type             = fetch_or_get($_GET['type'], false); 

    if (not_empty($search_query) && len_between($search_query,3, 32) && in_array($type, array('users','htags'))) {
        require_once(cl_full_path("core/apps/search/app_ctrl.php"));

        if ($type == "htags") {
            $search_query = cl_text_secure($search_query);
            $search_query = cl_croptxt($search_query, 32);
            $query_result = cl_search_hashtags($search_query, false, 150);
            $html_arr     = array();
            
            if (not_empty($query_result)) {
                foreach ($query_result as $cl['li']) {
                    $html_arr[] = cl_template('main/includes/search/htags_li');
                }

                $data['status'] = 200;
                $data['html']   = implode("", $html_arr);
            }  
        }
        else {
            $search_query = cl_text_secure($search_query);
            $search_query = cl_croptxt($search_query, 32);
            $query_result = cl_search_people($search_query, false, 150);
            $html_arr     = array();

            if (not_empty($query_result)) {
                foreach ($query_result as $cl['li']) {
                    $html_arr[] = cl_template('main/includes/search/users_li');
                }

                $data['status'] = 200;
                $data['html']   = implode("", $html_arr);
            }
        }
    }
}

else if($action == 'report_profile') {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $report_reason    = fetch_or_get($_POST['reason'], false); 
    $profile_id       = fetch_or_get($_POST['profile_id'], false); 
    $comment          = fetch_or_get($_POST['comment'], false); 
    $profile_data     = cl_raw_user_data($profile_id);

    if (not_empty($profile_data) && $profile_id != $me['id'] && in_array($report_reason, array_keys($cl['profile_report_types']))) {
        $data['status']  = 200;
        $db              = $db->where('user_id', $me['id']);
        $db              = $db->where('profile_id', $profile_id);
        $qr              = $db->delete(T_PROF_REPORTS);
        $comment         = (empty($comment)) ? "" : cl_croptxt($comment, 2900);
        $qr              = $db->insert(T_PROF_REPORTS, array(
            'user_id'    => $me['id'],
            'profile_id' => $profile_id,
            'reason'     => $report_reason,
            'comment'    => $comment,
            'seen'       => '0',
            'time'       => time()
        ));
    }
}

else if($action == 'block') {
    if (empty($cl["is_logged"])) {
        $data['status'] = 400;
        $data['error']  = 'Invalid access token';
    }
    else {
        $data['status']   = 404;
        $data['err_code'] = 0;
        $user_id          = fetch_or_get($_POST['id'], 0);


        if (is_posnum($user_id) && $me['id'] != $user_id) {
            
            $udata = cl_raw_user_data($user_id);

            if (not_empty($udata)) {
            
                if (cl_is_blocked($me['id'], $user_id)) {
                    $data['status'] = 200;
                    $db             = $db->where('user_id', $me['id']);
                    $db             = $db->where('profile_id', $user_id);
                    $qr             = $db->delete(T_BLOCKS);
                }

                else{
                    
                    cl_unfollow($user_id, $me['id']);

                    $data['status']  = 200;
                    $insert_id       = $db->insert(T_BLOCKS, array(
                        'user_id'    => $me['id'],
                        'profile_id' => $user_id,
                        'time'       => time()
                    ));

                    if (cl_is_following($me['id'], $user_id)) {
                        cl_unfollow($me['id'], $user_id);

                        $me_followings = ($me['following'] -= 1);
                        $me_followings = (empty($me_followings)) ? 0 : $me_followings;
     
                        cl_update_user_data($me['id'], array(
                            'following' => $me_followings
                        ));
                    }

                    if (cl_is_following($user_id, $me['id'])) {
                        cl_unfollow($user_id, $me['id']);

                        $user_followers = ($udata['followers'] -= 1);
                        $user_followers = (empty($user_followers)) ? 0 : $user_followers;

                        cl_update_user_data($user_id, array(
                            'followers' => $user_followers
                        ));
                    }
                }
            }
        }
    }
}