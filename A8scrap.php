<?php
/*
Plugin Name: A8scrap
Description: 投稿済み記事からリンクを抽出するプラグイン
Version: 1.0.0
Author: Kasiri
Author URI: https://kasiri.icu
Plugin URI: https://kasiri.icu/a8scrap-download
*/

// 管理ページのメニューを追加
add_action('admin_menu', 'a8scrap_menu');

function a8scrap_menu() {
    add_menu_page('A8scrap', 'A8scrap', 'manage_options', 'a8scrap-settings', 'a8scrap_settings_page');
}

// 管理ページのスタイルシートを読み込む
function a8scrap_load_styles() {
    wp_enqueue_style('A8scrap-style', plugins_url('../A8scrap/assets/A8scrap_style.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'a8scrap_load_styles');

// 管理ページの内容を表示
function a8scrap_settings_page() {
    ?>
    <div class="wrap">
        <h2>A8scrap</h2>
        <p>A8.netの広告リンクを抽出します。</p>
        <div class="button-section">
            <button id="start-scraping" class="start-scraping">スタート</button>
            <div class="download-buttons">
                <a href="<?php echo esc_url(admin_url('admin.php')); ?>?page=a8scrap-settings&action=download_csv_all" class="download-csv">プログラムIDアリ一覧（CSV）</a>
                <a href="<?php echo esc_url(admin_url('admin.php')); ?>?page=a8scrap-settings&action=download_csv_images" class="download-csv">プログラムID無し一覧（CSV）</a>
            </div>
        </div>
        <div id="scraping-results"></div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        $('.start-scraping').on('click', function() {
            // Ajaxを使用してWordPressからリンクを抽出して表示する
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'a8scrap_extract_links'
                },
                success: function(response) {
                    $('#scraping-results').html(response);
                },
                error: function(error) {
                    console.log(error);
                }
            });
        });
    });
    </script>
    <?php
}

// Ajax処理でリンクを抽出して表示する関数
add_action('wp_ajax_a8scrap_extract_links', 'a8scrap_extract_links');

function a8scrap_extract_links() {
    include_once(plugin_dir_path(__FILE__) . 'get_posts.php'); // get_posts.phpの読み込み
    $links = get_links_from_posts(); // get_posts.php内の関数を呼び出し
    echo $links;
    wp_die();
}

// ダウンロードCSV処理
add_action('admin_init', 'a8scrap_download_csv');
function a8scrap_download_csv() {
    if (isset($_GET['page']) && $_GET['page'] === 'a8scrap-settings') {
        if (isset($_GET['action']) && $_GET['action'] === 'download_csv_all') {
            include_once(plugin_dir_path(__FILE__) . 'get_csv.php');
            exit();
        } elseif (isset($_GET['action']) && $_GET['action'] === 'download_csv_images') {
            include_once(plugin_dir_path(__FILE__) . 'get_csv1.php');
            exit();
        } 
    }
}
