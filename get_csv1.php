<?php
// get_csv1.php: プログラムIDが無い項目のテキストリンクのテキストと掲載URLをCSVで出力する処理

include_once(plugin_dir_path(__FILE__) . 'get_posts.php'); // get_posts.phpの読み込み

$links = get_links_from_posts(); // get_posts.php内の関数を呼び出し

if (!empty($links)) {
    // HTMLテーブルを解析してプログラムIDが無い項目のテキストリンクのテキストと掲載URLを抽出する
    $dom = new DOMDocument();
    @$dom->loadHTML(mb_convert_encoding($links, 'HTML-ENTITIES', 'UTF-8'));

    $csv_data = array();
    $rows = $dom->getElementsByTagName('tr');
    foreach ($rows as $row) {
        $tds = $row->getElementsByTagName('td');
        if ($tds->length === 4) {
            $program_id = $tds->item(2)->nodeValue;
            if (empty($program_id)) {
                $text_link_text = $tds->item(0)->nodeValue;
                $page_url = $tds->item(3)->nodeValue;
                $csv_data[] = array($text_link_text, $page_url);
            }
        }
    }

    // CSVとして出力
    header('Content-Type: text/csv; charset=Shift_JIS');
    header('Content-Disposition: attachment; filename=text_links.csv');

    $output = fopen('php://output', 'w');

    // データをCSV形式で出力
    fwrite($output, "\xEF\xBB\xBF"); // BOMを追加（Shift_JISの場合）

    foreach ($csv_data as $row) {
        $encodedData = array_map(function($value) {
            return mb_convert_encoding($value, 'SJIS', 'UTF-8');
        }, $row);
        
        fputcsv($output, $encodedData);
    }

    fclose($output);
    exit;
} else {
    echo 'データが見つかりませんでした。';
    exit;
}
?>
