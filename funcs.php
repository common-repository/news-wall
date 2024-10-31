<?php

function nwap_get_rows(){
    global $wpdb;
    $table_name = $wpdb->prefix . NW_NEWS_TABLE_NAME;

    $sql = ("SELECT * FROM `$table_name` ORDER BY id DESC");
    return $wpdb->get_results($sql);	
}
function nwap_get_private_rows($id){
    global $wpdb;
    $table_name = $wpdb->prefix . NW_NEWS_TABLE_NAME;
    $sql = ("SELECT * FROM `$table_name` WHERE private = {$id} OR private = -1 ORDER BY id DESC");
    return $wpdb->get_results($sql);
}
function nwap_show_dots(string $text, int $max_len){
    if(strlen($text) > $max_len){
        return '...';
    } else{
        return '';
    }
}
function nwap_sanitize_input($input){
    $input = trim($input);
    $result = str_replace(['\'' , '"' , "'" , ';' , '#' , '$' , "%" , '=' , '{' , '}' , '/' , '-' , '_' , '?' , '~' , '`' , '!' , '*' , '|'] ,'' , $input);
    return $result;
}
function nwap_check_input($input){
    if($input == null){
        return false;
    } elseif($input == ''){
        return false;
    } elseif($input == ' '){
        return false;
    } else{
        return true;
    }
}