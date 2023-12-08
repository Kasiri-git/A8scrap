<?php
// get_posts.php: 記事抽出、リンク一覧取得処理

function get_links_from_posts() {
    $args = array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => -1 // すべての投稿を取得
    );

    $posts = get_posts($args);

    $all_links = '<table class="A8scrap-table"><thead><tr><th>リンクテキスト</th><th>リンクURL</th><th>プログラムID</th><th>掲載ページURL</th></tr></thead><tbody>';

    foreach ($posts as $post) {
        $content = $post->post_content;
        $pattern_text = '/<a\s[^>]*?href=[\'"]([^\'"]+)[\'"][^>]*?>(.*?)<\/a>/'; // テキストリンクの抽出用正規表現パターン
        $pattern_img = '/<img\s[^>]*?src=[\'"]([^\'"]+mid=s(\d{14}))/'; // 画像リンクの抽出用正規表現パターン

        $text_links = array();
        $img_links = array();

        // テキストリンクの抽出
        preg_match_all($pattern_text, $content, $matches_text, PREG_SET_ORDER);
        if (!empty($matches_text)) {
            foreach ($matches_text as $match) {
                $text_links[] = $match;
            }
        }

        // 画像リンクの抽出
        preg_match_all($pattern_img, $content, $matches_img, PREG_SET_ORDER);
        if (!empty($matches_img)) {
            foreach ($matches_img as $match) {
                // プログラムIDを左から15桁分取得する（sを含む）
                $program_id = 's' . $match[2];
                $img_links[] = array('url' => $match[1], 'program_id' => $program_id);
            }
        }

        // リンク情報の優先順位設定
        if (!empty($img_links)) {
            foreach ($img_links as $img_link) {
                $link = $img_link['url']; // 画像リンクURL部分
                $text = ''; // 画像リンクの場合、テキストは空になります
                $program_id = $img_link['program_id']; // プログラムID

                // 掲載ページURLの取得
                $page_url = get_permalink($post->ID);

                // テーブルに情報を追加
                $all_links .= '<tr><td>' . $text . '</td><td>' . $link . '</td><td>' . $program_id . '</td><td style="word-break: break-all;">' . $page_url . '</td></tr>';
            }
        } elseif (!empty($text_links)) {
            foreach ($text_links as $match) {
                $link = $match[1]; // テキストリンクURL部分
                $text = $match[2]; // テキストリンク部分
                $program_id = ''; // テキストリンクの場合、プログラムIDは空になります

                // 掲載ページURLの取得
                $page_url = get_permalink($post->ID);

                // テーブルに情報を追加
                $all_links .= '<tr><td>' . $text . '</td><td>' . $link . '</td><td>' . $program_id . '</td><td style="word-break: break-all;">' . $page_url . '</td></tr>';
            }
        }
    }

    $all_links .= '</tbody></table>';

    return $all_links;
}
