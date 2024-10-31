<?php
add_shortcode('newswallbtn', 'nw_btn_shortcode');
function nw_btn_shortcode($atts){
    if(isset($atts[0])){
        $atts[0] = strtolower($atts[0]);
        if($atts[0] != 'center' and $atts[0] != 'right' and $atts[0] != 'left'){
            $nw_section_align = 'center';
        } else{
            $nw_section_align = $atts[0];
        }
    } else{
        $nw_section_align = 'center';
    }
    if(isset($atts[1])){
        $nw_page_link = 'href="' . $atts[1] . '"';
    } else{
        $nw_page_link = '';
    }
    wp_register_style('nw_newswall_styles', plugin_dir_url(__FILE__) . 'news-wall.css', [], '1.36');
    wp_enqueue_style( 'nw_newswall_styles');
    wp_register_script('nw_newswall_js', plugin_dir_url(__FILE__) . 'news-wall.js');
    wp_enqueue_script( 'nw_newswall_js');
    $nw_news = nwap_get_private_rows(get_current_user_id());
    ?>
    <div  class="<?php echo 'nw_section_align_' . $nw_section_align ?>">
        <style>
            .nw_section_img{
                background-image: url("<?php echo plugin_dir_url(__FILE__) . '/images/announce.png'?>");
            }
            .nw_close_section_img{
                background-image: url("<?php echo plugin_dir_url(__FILE__) . '/images/close-section.png'?>");
            }
        </style>
        <a class="news_wall_section_btn"></a>
        <div class="news_wall_section news_wall_section_deactive">
            <div class="news_wall_section_header">
                <?php 
                global $wpdb;
                $table_name = $wpdb->prefix . NW_OPTIONS_TABLE_NAME;
                $sql = "SELECT * FROM {$table_name} WHERE id=2";
                $newswallbtnnum = $wpdb->get_results($sql)[0]->value;
                $nw_news_num = count($nw_news);
                $sql = "SELECT * FROM {$table_name} WHERE id=3";
                $nw_news_title = $wpdb->get_results($sql)[0]->strvalue;
                $sql = "SELECT * FROM {$table_name} WHERE id=5";
                $nw_nonews_text = $wpdb->get_results($sql)[0]->strvalue;
                ?>
                <span><?php echo esc_html($nw_news_title) ?></span>
            </div>
            <div class="news_wall_section_body">
                <?php
                if ($nw_news_num >= $newswallbtnnum) {
                    $nw_news_counter = $newswallbtnnum;
                } else{
                    $nw_news_counter = count($nw_news);
                }
                for ($i = 0; $i < $nw_news_counter; $i++) { ?>
                    <div class="news_wall_section_body_news">
                        <div class="news_wall_section_body_news_header">
                            <span class="news_wall_section_body_news_title"><?php echo esc_html($nw_news[$i]->title)?></span>
                            <span class="news_wall_section_body_news_date"><?php echo esc_html($nw_news[$i]->DATE) ?></span>
                        </div>
                        <div class="news_wall_section_body_news_body">
                            <p><?php echo esc_html(mb_substr($nw_news[$i]->body, 0 , 50)) . nwap_show_dots($nw_news[$i]->body, 50) ?></p>
                        </div>
                    </div>
                <?php }
                if($nw_news == [] or $nw_news == null){
                    ?> <span class="no-news-nwbtn"> <?php echo $nw_nonews_text ?> </span> <?php
                } elseif ($nw_page_link != '') {
                    ?><span class="nw_fullnews_link"> <a <?php echo $nw_page_link ?>><?php echo __('See all news', 'news-wall') ?></a> </span><?php
                }
                ?>
            </div>
        </div>
    </div>
    <?php
}
add_shortcode('newswallpage', 'nw_page_shortcode');
function nw_page_shortcode(){
    wp_register_style('nw_newswall_styles', plugin_dir_url(__FILE__) . 'news-wall.css', [], '1.1');
    wp_enqueue_style( 'nw_newswall_styles');
    global $wpdb;
    $table_name = $wpdb->prefix . NW_OPTIONS_TABLE_NAME;
    $nw_news = nwap_get_private_rows(get_current_user_id());
    $sql = "SELECT * FROM {$table_name} WHERE id=4";
    $nw_news_title = $wpdb->get_results($sql)[0]->strvalue;
    $sql = "SELECT * FROM {$table_name} WHERE id=5";
    $nw_nonews_text = $wpdb->get_results($sql)[0]->strvalue;
    ?>
    <div class="news_wall_page">
        <h2><?php echo esc_html($nw_news_title) ?></h2>
        <div class="news_wall_page_container">
            <?php 
            if($nw_news != null and $nw_news != []){
                foreach ($nw_news as $news) { ?>
                    <div class="news_wall_page_news">
                        <div class="news_wall_page_news_header">
                            <h3><?php echo esc_html($news->title) ?></h3>
                            <span><?php echo esc_html($news->DATE) ?></span>
                        </div>
                        <div class="news_wall_page_news_body">
                            <p><?php echo esc_html($news->body) ?></p>
                        </div>
                    </div>
                <?php }
            } else{ ?>
                <span class="no-news"> <?php echo  $nw_nonews_text ?> </span> 
            <?php }
            ?>
        </div>
    </div>
    <?php
}