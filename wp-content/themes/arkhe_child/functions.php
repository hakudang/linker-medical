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

/**
 * Enqueue JS riêng cho trang medical
 * - Chỉ nạp khi trang có block Arkhe Section (thường chỉ ở landing medical)
 * - Hoặc trang có shortcode CF7 (nếu bạn dùng CF7 ở nhiều trang
 *  và muốn nạp JS này ở tất cả các trang có CF7)
 * - Hoặc nạp đúng trang /medical và /ja/medical (nếu bạn chỉ dùng CF7 ở 2 trang này)
 * - Tệp JS này để tự set value cho trường date của Contact Form 7 (name="your-date")
 *  + Ngày mặc định: hôm nay + 1 tháng
 * + Không cho chọn ngày quá khứ
 *  
 */
add_action('wp_enqueue_scripts', function () {

  $path = ARKHE_CHILD_PATH . '/assets/js/medical.js';
  if ( ! file_exists($path) ) return;

  // Chỉ nạp ở các trang có nội dung /medical (tuỳ bạn chọn điều kiện)
  $should_load = false;

  // Cách A: nạp khi là trang có block Arkhe Section (thường chỉ ở landing medical)
  if ( is_page() && has_block('arkhe-blocks/section') ) {
    $should_load = true;
  }

  // Cách B (tuỳ chọn): nạp khi trang có shortcode CF7
  if ( is_page() && isset($GLOBALS['post']) && has_shortcode($GLOBALS['post']->post_content, 'contact-form-7') ) {
    $should_load = true;
  }

  // Cách C (tuỳ chọn): nạp đúng 2 trang VI/JA
  // if ( is_page('medical') || strpos($_SERVER['REQUEST_URI'], '/ja/medical') !== false ) {
  //   $should_load = true;
  // }

  if ( $should_load ) {
    wp_enqueue_script(
      'medical-js',
      ARKHE_CHILD_URI . '/assets/js/medical.js',
      [],                                  // dependencies (thường không cần jQuery)
      filemtime($path),                    // cache-busting khi sửa file
      true                                 // in_footer
    );
  }
}, 20);
