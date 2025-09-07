<?php
/**
 * Arkhe用子テーマ用 function.php
 */
defined( 'ABSPATH' ) || exit;


/**
 * 子テーマのパス, URI
 */
define( 'ARKHE_CHILD_PATH', get_stylesheet_directory() );
define( 'ARKHE_CHILD_URI', get_stylesheet_directory_uri() );


/**
 * style.css nạp vào
 */
// add_action( 'wp_enqueue_scripts', function() {
//   // ngăn cache trình duyệt khi phát triển bằng cách thêm ngày sửa đổi cuối cùng làm truy vấn 
// 	$time_stamp = date( 'Ymdgis', filemtime( ARKHE_CHILD_PATH . '/style.css' ) );
// 	wp_enqueue_style( 'arkhe-child-style', ARKHE_CHILD_URI . '/style.css', [], $time_stamp );
// } );

add_action( 'wp_enqueue_scripts', function () {
  $ver  = filemtime( ARKHE_CHILD_PATH . '/style.css' );
  // thử phụ thuộc vào handle parent nếu parent đã đăng ký
  $deps = [];
  if ( wp_style_is( 'arkhe-style', 'registered' ) || wp_style_is( 'arkhe-style', 'enqueued' ) ) {
    $deps[] = 'arkhe-style';     // handle parent phổ biến của Arkhe
  }
  wp_enqueue_style(
    'arkhe-child-style',
    ARKHE_CHILD_URI . '/style.css',
    $deps,
    $ver
  );
}, 99 ); // load thật muộn để ghi đè


/**
 * nạp CSS /medical nếu có block section
 */
add_action('wp_enqueue_scripts', function () {
  if (is_page() && has_block('arkhe-blocks/section')) {
    // ví dụ: /assets/css/medical.css chứa 2 block CSS bạn có
    wp_enqueue_style(
      'medical-css',
      ARKHE_CHILD_URI. '/assets/css/medical.css',
      ['arkhe-child-style','contact-form-7'], // load sau cf7 để dễ override
      filemtime(ARKHE_CHILD_PATH. '/assets/css/medical.css')
    );
  }
});
