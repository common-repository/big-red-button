<?php

// Make sure uninstall was called from WP
if(!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

// Remove options from the database
delete_option('widget_mp_brb_widget_big_red_button');
delete_option('mp_brb_last_click_time');
    
?>