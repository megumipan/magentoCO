<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
$installer = $this;
$installer->addAttribute(
        'customer', 'firstnamekana', array(
    'label' => 'First Name Kana',
    'is_required' => '0'
        )
);
$installer->addAttribute(
        'customer', 'lastnamekana', array(
    'label' => 'Last Name Kana',
    'is_required' => '0'
        )
);
$installer->addAttribute(
        'customer_address', 'firstnamekana', array(
    'label' => 'First Name Kana',
    'is_required' => '0'
        )
);
$installer->addAttribute(
        'customer_address', 'lastnamekana', array(
    'label' => 'Last Name Kana',
    'is_required' => '0'
        )
);
$installer->addAttribute(
        'quote', 'customer_firstnamekana', array(
    'visible' => false
        )
);
$installer->addAttribute(
        'quote', 'customer_lastnamekana', array(
    'visible' => false
        )
);
$installer->addAttribute(
        'quote_address', 'firstnamekana', array()
);
$installer->addAttribute(
        'quote_address', 'lastnamekana', array()
);
$installer->addAttribute(
        'order', 'customer_firstnamekana', array(
    'visible' => false
        )
);
$installer->addAttribute(
        'order', 'customer_lastnamekana', array(
    'visible' => false
        )
);
$installer->addAttribute(
        'order_address', 'firstnamekana', array()
);
$installer->addAttribute(
        'order_address', 'lastnamekana', array()
);


$directory_country_region = $installer->getTable('directory/country_region');
$installer->run("
INSERT INTO `{$directory_country_region}` (country_id, code, default_name) VALUES 
('JP', 'Hokkaido', 'Hokkaido'),
('JP', 'Aomori', 'Aomori'),
('JP', 'Iwate', 'Iwate'),
('JP', 'Miyagi', 'Miyagi'),
('JP', 'Akita', 'Akita'),
('JP', 'Yamagata', 'Yamagata'),
('JP', 'Fukushima', 'Fukushima'),
('JP', 'Ibaragi', 'Ibaragi'),
('JP', 'Tochigi', 'Tochigi'),
('JP', 'Gunma', 'Gunma'),
('JP', 'Saitama', 'Saitama'),
('JP', 'Chiba', 'Chiba'),
('JP', 'Tokyo', 'Tokyo'),
('JP', 'Kanagawa', 'Kanagawa'),
('JP', 'Niigata', 'Niigata'),
('JP', 'Toyama', 'Toyama'),
('JP', 'Ishikawa', 'Ishikawa'),
('JP', 'Fukui', 'Fukui'),
('JP', 'Yamanashi', 'Yamanashi'),
('JP', 'Nagano', 'Nagano'),
('JP', 'Gifu', 'Gifu'),
('JP', 'Shizuoka', 'Shizuoka'),
('JP', 'Aichi', 'Aichi'),
('JP', 'Mie', 'Mie'),
('JP', 'Shiga', 'Shiga'),
('JP', 'Kyoto', 'Kyoto'),
('JP', 'Osaka', 'Osaka'),
('JP', 'Hyogo', 'Hyogo'),
('JP', 'Nara', 'Nara'),
('JP', 'Wakayama', 'Wakayama'),
('JP', 'Tottori', 'Tottori'),
('JP', 'Shimane', 'Shimane'),
('JP', 'Okayama', 'Okayama'),
('JP', 'Hiroshima', 'Hiroshima'),
('JP', 'Yamaguchi', 'Yamaguchi'),
('JP', 'Tokushima', 'Tokushima'),
('JP', 'Kagawa', 'Kagawa'),
('JP', 'Ehime', 'Ehime'),
('JP', 'Kochi', 'Kochi'),
('JP', 'Fukuoka', 'Fukuoka'),
('JP', 'Saga', 'Saga'),
('JP', 'Nagasaki', 'Nagasaki'),
('JP', 'Kumamoto', 'Kumamoto'),
('JP', 'Oita', 'Oita'),
('JP', 'Miyazaki', 'Miyazaki'),
('JP', 'Kagoshima', 'Kagoshima'),
('JP', 'Okinawa', 'Okinawa');
");
$directory_country_region_name_table = $installer->getTable('directory/country_region_name');
$installer->run("
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'北海道' FROM {$directory_country_region} AS a WHERE a.default_name = 'Hokkaido';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'青森県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Aomori';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'岩手県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Iwate';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'宮城県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Miyagi';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'秋田県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Akita';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'山形県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Yamagata';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'福島県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Fukushima';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'茨城県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Ibaragi';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'栃木県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Tochigi';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'群馬県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Gunma';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'埼玉県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Saitama';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'千葉県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Chiba';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'東京都' FROM {$directory_country_region} AS a WHERE a.default_name = 'Tokyo';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'神奈川県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Kanagawa';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'新潟県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Niigata';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'富山県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Toyama';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'石川県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Ishikawa';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'福井県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Fukui';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'山梨県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Yamanashi';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'長野県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Nagano';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'岐阜県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Gifu';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'静岡県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Shizuoka';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'愛知県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Aichi';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'三重県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Mie';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'滋賀県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Shiga';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'京都府' FROM {$directory_country_region} AS a WHERE a.default_name = 'Kyoto';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'大阪府' FROM {$directory_country_region} AS a WHERE a.default_name = 'Osaka';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'兵庫県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Hyogo';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'奈良県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Nara';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'和歌山県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Wakayama';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'鳥取県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Tottori';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'島根県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Shimane';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'岡山県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Okayama';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'広島県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Hiroshima';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'山口県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Yamaguchi';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'徳島県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Tokushima';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'香川県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Kagawa';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'愛媛県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Ehime';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'高知県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Kochi';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'福岡県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Fukuoka';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'佐賀県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Saga';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'長崎県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Nagasaki';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'熊本県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Kumamoto';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'大分県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Oita';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'宮崎県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Miyazaki';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'鹿児島県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Kagoshima';
INSERT INTO `{$directory_country_region_name_table}` (locale,region_id,name) SELECT 'ja_JP',a.region_id,'沖縄県' FROM {$directory_country_region} AS a WHERE a.default_name = 'Okinawa';
");


$attr_customer = array('website_id' => '登録ウェブサイト',
    'store_id' => '店舗ID',
    'created_in' => '作成場所',
    'prefix' => '称号',
    'firstname' => '名',
    'middlename' => 'ミドルネーム/イニシャル',
    'lastname' => '姓',
    'suffix' => '尊称',
    'email' => 'メールアドレス',
    'group_id' => 'グループ',
    'dob' => '誕生日',
    'default_billing' => '標準の請求先',
    'default_shipping' => '標準の配送先',
    'taxvat' => '納税者番号',
    'confirmation' => '確認済',
    'created_at' => '作成日',
    'gender' => '性別',
    'firstnamekana' => '名（カナ）',
    'lastnamekana' => '姓（カナ）'
);
$attr_customer_address = array('prefix' => '称号',
    'firstname' => '名',
    'middlename' => 'ミドルネーム/イニシャル',
    'lastname' => '姓',
    'suffix' => '尊称',
    'company' => '会社名',
    'street' => '住所',
    'city' => '市区町村',
    'country_id' => '国',
    'region' => '都道府県',
    'region_id' => '都道府県',
    'postcode' => '郵便番号',
    'telephone' => '電話番号',
    'fax' => 'Fax',
    'firstnamekana' => '名（カナ）',
    'lastnamekana' => '姓（カナ）'
);
$attr_catalog_category = array(
    'name' => 'カテゴリ名',
    'is_active' => '有効',
    'url_key' => 'URLキー',
    'description' => '概要',
    'image' => '画像',
    'meta_title' => 'タイトル',
    'meta_keywords' => 'Metaキーワード',
    'meta_description' => 'Metaディスクリプション',
    'display_mode' => '表示モード',
    'landing_page' => '静的ブロック',
    'is_anchor' => 'アンカー',
    'path' => 'パス',
    'position' => '表示位置',
    'custom_design' => 'カスタムデザイン',
    'custom_design_from' => '適用開始日',
    'custom_design_to' => '適用終了日',
    'page_layout' => 'ページレイアウト',
    'custom_layout_update' => 'カスタムレイアウト',
    'level' => 'レベル',
    'children_count' => '小カテゴリ数',
    'available_sort_by' => '利用可能なソート方法',
    'default_sort_by' => '標準ソート方法',
    'include_in_menu' => 'ナビゲーションメニューに表示',
    'custom_use_parent_settings' => '親カテゴリ設定を使用',
    'custom_apply_to_products' => '商品に適用',
    'filter_price_range' => '価格ナビゲーションの範囲',
    'thumbnail' => 'サムネイル画像'
);
$attr_catalog_product = array(
    'name' => '商品名',
    'description' => '概要',
    'short_description' => '短い概要',
    'sku' => 'SKU',
    'price' => '価格',
    'special_price' => '特価',
    'special_from_date' => '特価適用開始日',
    'special_to_date' => '特価適用終了日',
    'cost' => '原価',
    'weight' => '重量',
    'manufacturer' => '製造者',
    'meta_title' => 'Metaタイトル',
    'meta_keyword' => 'Metaキーワード',
    'meta_description' => 'Metaディスクリプション',
    'image' => '基本画像',
    'small_image' => '一覧画像',
    'thumbnail' => 'サムネイル画像',
    'media_gallery' => 'メディアギャラリー',
    'tier_price' => '階層価格',
    'color' => '色',
    'news_from_date' => '新着表示開始日',
    'news_to_date' => '新着表示終了日',
    'gallery' => 'イメージギャラリー',
    'status' => 'ステータス',
    'url_key' => 'URLキー',
    'minimal_price' => '最安価格',
    'is_recurring' => '継続課金有効',
    'recurring_profile' => '継続課金設定',
    'visibility' => '表示対象',
    'custom_design' => 'カスタムデザイン',
    'custom_design_from' => '適用開始日',
    'custom_design_to' => '適用終了日',
    'custom_layout_update' => 'カスタムレイアウト',
    'page_layout' => 'ページレイアウト',
    'options_container' => '商品オプション表示位置',
    'image_label' => '画像ラベル',
    'small_image_label' => '一覧画像ラベル',
    'thumbnail_label' => 'サムネイル画像ラベル',
    'country_of_manufacture' => '製造国',
    'msrp_enabled' => 'MAP適用',
    'msrp_display_actual_price_type' => '実売価格表示',
    'msrp' => "メーカー希望小売価格",
    'enable_googlecheckout' => 'Google Checkout可能',
    'tax_class_id' => '税区分',
    'gift_message_available' => 'ギフトメッセージ許可',
    'price_view' => '価格表示',
    'shipment_type' => '配送',
    'links_purchased_separately' => 'リンクを別に購入',
    'samples_title' => 'サンプルタイトル',
    'links_title' => 'リンクタイトル',
    'thumbnail' => 'サムネイル'
);
$attribute_set = array('customer', 'customer_address', 'catalog_category', 'catalog_product');

foreach ($attribute_set as $attribute) {
    foreach (${'attr_' . $attribute} as $entity => $label) {

        $id = $installer->getAttribute($attribute, $entity, 'attribute_id');
        $installer->updateAttribute(
                $attribute, $id, array(
            'frontend_label' => $label
                ), null
        );
    }
}

$installer->endSetup();