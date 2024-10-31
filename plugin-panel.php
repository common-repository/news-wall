<?php
include_once(plugin_dir_path(__FILE__) . '/funcs.php');
function nwap_newslist_page(){
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die('You do not have access to this section');
    } else{
        // ADD Styles
        wp_register_style('nw_admin_styles', plugin_dir_url(__FILE__) . 'admin-styles.css');
        wp_enqueue_style( 'nw_admin_styles');
        ?>
        <div class="nwa_container">
            <?php global $wp; ?>
            <?php 
            if(isset($_GET['action'])){
                if($_GET['action'] == 'add'){ ?>
                    <span>
                        <?php echo __('All inputs are required(Except for the user ID)
                        News title must be less than 50 characters', 'news-wall') ?>
                    </span>
                    <form method="POST" class="nwa_new_news_form">
                        <label for="nwa_newnews_title"><?php echo __('News Title', 'news-wall') ?></label>
                        <input type="text" placeholder="<?php echo __('News title', 'news-wall') ?>" name="nwa_newnews_title" id="nwa_newnews_title">
						<label><?php echo __('News Text'); ?></label>
						<?php wp_editor(__('News Text', 'news-wall'), '', ['wpautop', 'textarea_name' => 'nwa_newnews_body', 'media_buttons' => false, 'textarea_rows' => 10]); ?>
                        <small><?php echo __('If you have a problem with the text editor above, do a Google search for the wordpress visual editor not working', 'news-wall') ?> </small>
						<label for="nwa_newnews_private"><?php echo __('Send Private News', 'news-wall') ?></label>
                        <input type="number" placeholder="<?php echo __('The ID of the user who receives the private message', 'news-wall') ?>" name="nwa_newnews_private" id="nwa_newnews_private">
                        <small><?php echo __('If this is public news, do not fill out this input', 'news-wall') ?></small>
                        <button class="nwa_new_news_submit" name="nwa_newnews_submit"><?php echo __('Send News', 'news-wall') ?></button>
                    </form>
                    <?php 
                        if(isset($_POST['nwa_newnews_submit'])){
                            $nwa_newnews_title = sanitize_text_field($_POST['nwa_newnews_title']);
                            $nwa_newnews_body = sanitize_textarea_field($_POST['nwa_newnews_body']);
                            if(nwap_check_input($_POST['nwa_newnews_private'])){
                                $nwa_newnews_private = (int) $_POST['nwa_newnews_private'];
                            } else{
                                $nwa_newnews_private = -1;
                            }
                            if(nwap_check_input($nwa_newnews_title) and nwap_check_input($nwa_newnews_body)){
                                global $wpdb;
                                $table_name = $wpdb->prefix . NW_NEWS_TABLE_NAME;
								$wpdb->show_errors();
								$insert_new_news = $wpdb->insert($table_name , array('title' => $nwa_newnews_title, 'body' => $nwa_newnews_body , 'private' => $nwa_newnews_private));
								if($insert_new_news == false){
                                    ?><span class="nwa_new_news_error"><?php echo __('An error occurred', 'news-wall') ?></span><?php
                                } else{
                                    unset($_POST['nwa_new_news_submit']);
                                    ?><span class="nwa_new_news_error"><?php echo __('News sent successfully', 'news-wall') ?></span>
                                    <a class="nwa_link" href="<?php echo home_url( $wp->request ) . '/wp-admin/admin.php?page=nw_newslist_page'?>"><?php echo __('Back to home') ?></a><?php
                                }
                            } else{ ?>
                                <span class="nwa_new_news_error"><?php echo esc_html(__('One or more incorrect inputs have been filled', 'news-wall')); ?> <br>
                                <?php echo __('Text: ', 'news-wall') . esc_html($nwa_newnews_title); ?> <br>
                                <?php echo __('Title: ', 'news-wall') . esc_html($nwa_newnews_body) ?></span>
                            <?php }
                        }
                    ?>
                <?php } elseif($_GET['action'] == 'del' and isset($_GET['id']) and ! isset($_GET['confirm'])){ ?>
                    <span class="nwa_message"><?php echo __('Do you really want to delete this news?', 'news-wall') ?></span>
                    <div class="nwa_remove_btns">
                        <a class="nwa_remove_btn" href="<?php echo home_url( $wp->request ) . '/wp-admin/admin.php?page=nw_newslist_page&action=del&id=' . $_GET['id'] . '&confirm' ?>"><?php echo __('Yes', 'news-wall') ?></a>
                        <a class="nwa_remove_btn" href="<?php echo home_url( $wp->request ) . '/wp-admin/admin.php?page=nw_newslist_page'?>"><?php echo __('No', 'news-wall') ?></a>
                    </div>
                <?php } elseif($_GET['action'] == 'del' and isset($_GET['id']) and isset($_GET['confirm'])){
                    global $wpdb;
                    $table_name = $wpdb->prefix . NW_NEWS_TABLE_NAME;
                    $news_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
                    $news_id = str_replace(['-', '+'], "", $news_id);
                    $news_id = (int) $news_id;
                    $sql = "SELECT * FROM {$table_name} WHERE `id`={$news_id}";
                    $sql_result = $wpdb->get_results($sql);
                    if($sql_result != [] or $sql_result != null){
                        $sql = "DELETE FROM {$table_name} WHERE `id`={$news_id}";
                        $wpdb->query($sql);
                        ?> <span class="nwa_message"><?php echo __('News deleted', 'news-wall') ?></span> <?php
                    } else{
                        ?> <span class="nwa_message"><?php echo __('The desired news was not found', 'news-wall') ?></span>
                        <a href="<?php echo home_url( $wp->request ) . '/wp-admin/admin.php?page=nw_newslist_page' ?>"><?php echo __('Back to home', 'news-wall') ?></a> <?php
                    }
                } elseif($_GET['action'] == 'edit' and isset($_GET['id'])){
                    global $wpdb;
                    $table_name = $wpdb->prefix . NW_NEWS_TABLE_NAME;
                    $news_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
                    $news_id = str_replace(['-', '+'], "", $news_id);
                    $news_id = (int) $news_id;
                    $sql = "SELECT * FROM {$table_name} WHERE `id`={$news_id}";
                    $sql_result = $wpdb->get_results($sql);
                    if($sql_result != [] or $sql_result != null){
                        $nwa_post_title = $sql_result[0]->title;
                        $nwa_post_body = $sql_result[0]->body;
                        $nwa_post_private = $sql_result[0]->private; ?>
                        <span>
                        <?php echo __("One or more incorrect inputs have been filled \n News title must be less than 50 characters", 'news-wall') ?>
                        </span>
                        <form method="POST" class="nwa_new_news_form">
                            <label for="nwa_newnews_title"><?php echo __('News Title', 'news-wall') ?></label>
                            <input value="<?php echo $nwa_post_title ?>" type="text" placeholder="<?php echo __('News title', 'news-wall') ?>" name="nwa_newnews_title" id="nwa_newnews_title">
                        <?php wp_editor(($nwa_post_body), '', ['wpautop', 'textarea_name' => 'nwa_newnews_body', 'media_buttons' => false, 'textarea_rows' => 10]); ?>
                            <label for="nwa_newnews_private"><?php echo __('Send Private News', 'news-wall') ?></label>
                            <input type="text" value="<?php echo esc_html($nwa_post_private) ?>" placeholder="<?php echo __('The ID of the user who receives the private message', 'news-wall') ?>" name="nwa_newnews_private" id="nwa_newnews_private">
                            <small><?php echo __('If this is public news, do not fill out this input', 'news-wall') ?></small>
                            <button class="nwa_new_news_submit" name="nwa_newnews_submit"><?php echo __('Send News', 'news-wall') ?></button>
                        </form>
                        <?php 
                            if(isset($_POST['nwa_newnews_submit'])){
                                $nwa_newnews_title = sanitize_text_field($_POST['nwa_newnews_title']);
                                $nwa_newnews_body = sanitize_textarea_field($_POST['nwa_newnews_body']);
                                if(nwap_check_input($_POST['nwa_newnews_private'])){
                                    $nwa_newnews_private = (int) $_POST['nwa_newnews_private'];
                                } else{
                                    $nwa_newnews_private = -1;
                                }
                                if(nwap_check_input($nwa_newnews_title) and nwap_check_input($nwa_newnews_body)){
                                    global $wpdb;
                                    $table_name = $wpdb->prefix . NW_NEWS_TABLE_NAME;
                                    if(! $wpdb->update($table_name, ['title' => $nwa_newnews_title, 'body' => $nwa_newnews_body, 'private' => $nwa_newnews_private] , ['id' => $news_id])){
                                        ?><span class="nwa_new_news_error"><?php echo __('An error occurred', 'news-wall') ?></span><?php
                                    } else{
                                        unset($_POST['nwa_new_news_submit']);
                                        ?><span class="nwa_new_news_error"><?php echo __('News sent successfully', 'news-wall') ?></span>
                                        <a class="nwa_link" href="<?php echo home_url( $wp->request ) . '/wp-admin/admin.php?page=nw_newslist_page'?>"><?php echo __('Back to home', 'news-wall') ?></a><?php
                                    }
                                } else{ ?>
                                    <span class="nwa_new_news_error"><?php echo esc_html(__('One or more incorrect inputs have been filled', 'news-wall')); ?> <br>
                                    <?php echo __('Text: ', 'news-wall') . esc_html($nwa_newnews_title); ?> <br>
                                    <?php echo __('Title: ', 'news-wall') . esc_html($nwa_newnews_body) ?></span>
                                <?php }
                                }
                    } else{
                        ?> <span class="nwa_message"><?php echo __('The desired news was not found', 'news-wall') ?></span>
                        <a href="<?php echo home_url( $wp->request ) . '/wp-admin/admin.php?page=nw_newslist_page' ?>"><?php echo __('Back to home', 'news-wall') ?></a> <?php
                    }
                    ?>
                    
                <?php }
            } else{ ?>
                <a class="nwa_new_news" href="<?php echo home_url( $wp->request ) . '/wp-admin/admin.php?page=nw_newslist_page&action=add' ?>"><?php echo __('New News', 'news-wall') ?></a>
                <div class="nwa_newslist_container">
                    <?php
                    $nwa_news = nwap_get_rows();
                    if($nwa_news == [] or $nwa_news == null){ ?>
                        <span class="nwa_message"><?php echo __('There is no news', 'news-wall') ?></span>
                    <?php } else{ ?>
                        <table class="nwa_newslist_table">
                            <tr>
                                <th><?php echo __('ID', 'news-wall') ?></th>
                                <th><?php echo __('Title', 'news-wall') ?></th>
                                <th><?php echo __('Date', 'news-wall') ?></th>
                                <th><?php echo __('abstract', 'news-wall') ?></th>
                                <th><?php echo __('Private', 'news-wall') ?></th>
                                <th><?php echo __('processes', 'news-wall') ?></th>
                            </tr>
                            <?php
                            foreach ($nwa_news as  $news) { ?>
                                <tr>
                                    <td><?php echo esc_html($news->id) ?></td>
                                    <td><?php echo esc_html($news->title) ?></td>
                                    <td><?php echo esc_html($news->DATE) ?></td>
                                    <td><?php echo mb_substr($news->body , 0 , 120) ?><?php echo nwap_show_dots($news->body, 120)?></td>
                                    <td><?php echo esc_html($news->private); ?></td>
                                    <td><a class="nwa_newslist_action" href="<?php echo home_url( $wp->request ) . '/wp-admin/admin.php?page=nw_newslist_page&action=edit&id=' . $news->id?>"><?php echo __('edit', 'news-wall') ?></a>
                                    <a class="nwa_newslist_action" href="<?php echo home_url( $wp->request ) . '/wp-admin/admin.php?page=nw_newslist_page&action=del&id=' . $news->id?>"><?php echo __('delete', 'news-wall') ?></a></td>
                                </tr>
                            <?php }
                            ?>
                        </table>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
        <?php
    }
}
function nwap_help_page(){
    wp_register_style('nw_admin_styles', plugin_dir_url(__FILE__) . '/admin-styles.css' , [] , '6');
    wp_enqueue_style( 'nw_admin_styles'); ?>
    <div class="nwa_help">
        <h1><?php echo __('Help and introduction', 'news-wall') ?></h1>
        <p><?php echo __('Welcome to the News Wall Help page', 'news-wall') ?></p> <br>
        <p><?php echo __('News Wall is a one-way communication plugin between webmasters and users. Using this plugin, you can inform your users about site news or news related to your sites field of activity.', 'news-wall')?></p>
        <h2><?php echo __('How to use', 'news-wall') ?></h2>
        <br><p><?php echo __('This plugin has two short codes.', 'news-wall') ?></p>
        <ol>
            <li>[newswallbtn] <?php echo __('To insert a button to display the news summary', 'news-wall') ?></li>
            <li>[newswallpage] <?php echo __('To display the full news', 'news-wall') ?></li>
        </ol>
        <h3><?php echo __('guide' , 'news-wall') ?> [newswallbtn]</h3>
        <p><?php echo __('By inserting this code, a button will appear that you can click on to see some of the latest news (you can change some things in the plugin settings)', 'news-wall') ?></p>
        <p><?php echo __('This short code takes two inputs. The first input is for button arrangement. Left, right and center', 'news-wall');  ?></p>
        <br><p><?php echo __('The second entry is a link that is suggested to be a page where the second code shortcode is used', 'news-wall'); ?></p>
        <br><p><?php echo __('It is not mandatory to enter these inputs', 'news-wall') ?></p>
        <img src="<?php echo plugin_dir_url(__FILE__) . 'images/nw-help1.jpg' ?>">
        <br><p><?php echo __('If you enter entries, your code should end up with something like this: [newswallbtn center google.com]', 'news-wall'); ?></p>
        <br><p><?php echo __('Sometimes you may save and refresh the page to see changes', 'news-wall'); ?></p>
        <img src="<?php echo plugin_dir_url(__FILE__) . 'images/nw-help2.jpg' ?>">
        <h3><?php echo __('guide') ?> [newswallpage]</h3>
        <p><?php echo __('This short code is for displaying news in full. Does not take input', 'news-wall'); ?></p>
        <p><?php echo __('You can create a page and put this shortcode in it.', 'news-wall'); ?></p>
    </div>
<?php }
function nwap_options_page(){
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die('You do not have access to this section');
    } else{
    wp_register_style('nw_admin_styles', plugin_dir_url(__FILE__) . '/admin-styles.css' , [] , '6.2');
    wp_enqueue_style( 'nw_admin_styles'); 
    global $wpdb;
    global $wp;
    $table_name = $wpdb->prefix . NW_OPTIONS_TABLE_NAME;
    $sql = "SELECT * FROM {$table_name} WHERE id=2";
    $newswallbtnnum = $wpdb->get_results($sql)[0]->value;
    $sql = "SELECT * FROM {$table_name} WHERE id=3";
    $newswallbtntitle = $wpdb->get_results($sql)[0]->strvalue;
    $sql = "SELECT * FROM {$table_name} WHERE id=4";
    $newswallsectitle = $wpdb->get_results($sql)[0]->strvalue;
    $sql = "SELECT * FROM {$table_name} WHERE id=5";
    $newswallthereisnonews = $wpdb->get_results($sql)[0]->strvalue ?>
    <div class="nw_setting">
        <form method="POST" class="nw_setting_form">
            <label for="nw_btn_newsnum"><?php echo __('Number of news in [newswallbtn]', 'news-wall'); ?></label>
            <input type="number" id="nw_btn_newsnum" name="nw_btn_newsnum" max="9" min="1" value="<?php echo $newswallbtnnum ?>" required>
            <label for="nw_btn_title"><?php echo __('The title of the news summary section([newswallbtn])', 'news-wall'); ?></label>
            <input type="text" id="nw_btn_title" name="nw_btn_title" value="<?php echo $newswallbtntitle ?>" required>
            <label for="nw_sec_title"><?php echo __('Title of all news section([newswallpage])', 'news-wall'); ?></label>
            <input type="text" id="nw_sec_title" name="nw_sec_title" value="<?php echo $newswallsectitle ?>" required>
            <label for="nw_nonews_text"><?php echo __('Text that displays when there is no news', 'news-wall'); ?></label>
            <input type="text" id="nw_nonews_text" name="nw_nonews_text" value="<?php echo $newswallthereisnonews ?>" required>
            <button type="submit" name="nw_option_submit"><?php echo __('Submit changes', 'news-wall'); ?></button>
            <?php 
            if(isset($_POST['nw_option_submit'])){
                $nw_newsnum = nwap_sanitize_input($_POST['nw_btn_newsnum']);
                $nw_btntitle = sanitize_text_field($_POST['nw_btn_title']);
                $nw_sectitle = sanitize_text_field($_POST['nw_sec_title']);
                $nw_nonewstext = sanitize_text_field($_POST['nw_nonews_text']);
                $table_name = $wpdb->prefix . NW_OPTIONS_TABLE_NAME;
                if (nwap_check_input($nw_newsnum) AND nwap_check_input($nw_btntitle) AND nwap_check_input($nw_sectitle) AND nwap_check_input($nw_nonewstext)) {
                    $nw_newsnum = (int) $nw_newsnum;
                    if ($wpdb->update($table_name, ['value' => $nw_newsnum] , ['id' => '2']) OR $wpdb->update($table_name, ['strvalue' => $nw_btntitle] , ['id' => '3']) OR $wpdb->update($table_name, ['strvalue' => $nw_sectitle] , ['id' => '4'])  OR $wpdb->update($table_name, ['strvalue' => $nw_nonewstext] , ['id' => '5'])) {
                        unset($_POST['nw_option_submit']);
                        ?>
                        <span class="nwa_new_news_error"><?php echo __('The desired news was updated. Refresh the page to see changes', 'news-wall') ?></span>
                        <a class="nwa_link" href="<?php echo home_url( $wp->request ) . '/wp-admin/admin.php?page=nw_newslist_page'?>"><?php echo __('Back to home', 'news-wall') ?></a><?php
                    } else{
                        ?> <span class="nwa_new_news_error"><?php echo __('there was a problem. In general, there are three possibilities: 1- The information entered is duplicate 2- There is a problem in the database 3- There is a problem with the plugin code.', 'news-wall') ?> </span> <?php
                    }
                } else{
                    ?> <span class="nwa_new_news_error"><?php echo __('One or more incorrect inputs have been filled', 'news-wall') ?> </span> <?php
                }
            }
            ?>
        </form>
    </div>
    <?php   }
}
function nwap_panel()
{
    add_menu_page(
        __('News Wall', 'news-wall'),
        __('News Wall', 'news-wall'),
        'manage_options',
        'nw_newslist_page',
        'nwap_newslist_page',
        'dashicons-email'
    );
    add_submenu_page(
        'nw_newslist_page',
        __('News - News Wall', 'news-wall'),
        __('News', 'news-wall'),
        'manage_options',
        'nw_newslist_page',
        'nwap_newslist_page'
    );
    add_submenu_page(
        'nw_newslist_page',
        __('Settings - Newswall', 'news-wal'),
        __('Settings',  'news-wall'),
        'manage_options',
        'nw_options',
        'nwap_options_page'
    );
    add_submenu_page(
        'nw_newslist_page',
        __('Help and introduction - Newswall', 'news-wall'),
        __('Help and introduction', 'news-wall'),
        'manage_options',
        'nw_help',
        'nwap_help_page'
    );
}
add_action('admin_menu', 'nwap_panel');