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

function cl_translate($text = '', $data = array()) {
    global $langs, $cl;

    $langkey = cl_gen_lang_key($text);

    if (in_array($langkey, array_keys($langs))) {
        $text_val = $langs[$langkey];

        if (not_empty($data) && is_array($data)) {
            foreach ($data as $key => $val) {
                $text_val = preg_replace_callback("/\{\@(.+?)\@\}/", function($m) use ($data) {
                    return ((isset($data[$m[1]])) ? $data[$m[1]] : '');
                }, $text_val);
            }
        }

        return stripslashes($text_val);
    }

    if ($cl["server_mode"] == 'development') {
        try {
            $file_path = cl_full_path("core/langs/default.json");
            $def_lang  = file_get_contents($file_path);
            $def_lang  = json($def_lang);

            if (is_array($def_lang)) {
                $def_lang[$langkey] = $text;
                $def_lang           = json($def_lang,1);
                $save_lang          = file_put_contents($file_path,$def_lang);

                if (empty($save_lang)) {
                    die("Failed to save localization text: $text");
                }
            }
        } catch (Exception $e) { /*pass*/ }
    }

    return $text;
}

function cl_gen_lang_key($text = '') {
    if (empty($text) || is_string($text) != true) {
        return "";
    }

    $text    = trim($text);
    $langkey = preg_replace('/\{\@(.*?)\@\}/', '', $text);
    $langkey = strtolower(cl_slug($langkey));
    $langkey = cl_croptxt($langkey,65);
    return $langkey;
}

function cl_get_langs($lang = 'english') {
    global $cl;

    try {
        $language  = ($cl["server_mode"] == 'development') ? "default" : $lang;
        $file_path = cl_full_path("core/langs/$language.json");
        $usr_lang  = file_get_contents($file_path);
        $usr_lang  = json($usr_lang);

        if (is_array($usr_lang)) {
            foreach ($usr_lang as $key => $val) {
                $usr_lang[$key] = stripcslashes($usr_lang[$key]);
                $usr_lang[$key] = html_entity_decode($usr_lang[$key], ENT_QUOTES | ENT_HTML5);
                $usr_lang[$key] = htmlspecialchars_decode($usr_lang[$key]);
            }

            return $usr_lang;
        }
        else {
            throw new Exception("Failed to load the display language, the system cannot continue.");
        }
    } 

    catch (Exception $e) { 
        die($e->getMessage());
    }
}

