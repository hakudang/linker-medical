# Linker Medical – Arkhe Child (README)

Trang đích **/medical** (VI) và **/ja/medical** (JA) xây dựng trên WordPress sử dụng:
- Theme: **Arkhe** + **arkhe_child**
- Blocks: **Arkhe Blocks** + Gutenberg
- Đa ngữ: **Polylang** (VI / JA)
- Form: **Contact Form 7**
- Tài nguyên tuỳ biến: `assets/css/medical.css`, `assets/js/medical.js`

> Tài liệu này có ghi chú triển khai (deploy) và **đoạn mã “Cố định URL trong wp-config.php”** cho site cài trong thư mục con `/medical`.

---

## 0) Cấu trúc (child theme)

arkhe_child/
├─ style.css # Header theme + tuỳ biến site-wide (menu, v.v.)
├─ functions.php # Enqueue style/js + versioning theo theme
├─ README.md # File này
└─ assets/
├─ css/
│ └─ medical.css # CSS cho trang /medical (hero, slider, CF7, v.v.)
└─ js/
└─ medical.js # JS nhỏ (vd: auto set ngày mặc định cho CF7)

---

## 1) Quản lý version & nạp (enqueue)

- Luôn giữ **Version** trong header `style.css` (SemVer) và dùng nó làm version cho asset.
- Ở dev có thể dùng `filemtime()`; ở staging/prod dùng `Version` để kiểm soát cache.

**Ví dụ (trích `functions.php`):**
```php
defined('ABSPATH') || exit;

define('ARKHE_CHILD_PATH', get_stylesheet_directory());
define('ARKHE_CHILD_URI',  get_stylesheet_directory_uri());

$__theme = wp_get_theme( get_stylesheet() );
define('ARKHE_CHILD_VER', $__theme->get('Version') ?: '0.0.0');

add_action('wp_enqueue_scripts', function () {
  // CSS child (nạp sau Arkhe nếu có)
  wp_enqueue_style(
    'arkhe-child-style',
    ARKHE_CHILD_URI . '/style.css',
    wp_style_is('arkhe-style','registered') ? ['arkhe-style'] : [],
    ARKHE_CHILD_VER
  );

  // Chỉ nạp asset Medical ở trang có Arkhe Section (có thể thu hẹp thêm điều kiện)
  if ( is_page() && has_block('arkhe-blocks/section') ) {
    $css = ARKHE_CHILD_PATH . '/assets/css/medical.css';
    if ( file_exists($css) ) {
      wp_enqueue_style('medical-css', ARKHE_CHILD_URI.'/assets/css/medical.css', ['arkhe-child-style'], ARKHE_CHILD_VER);
    }
    $js = ARKHE_CHILD_PATH . '/assets/js/medical.js';
    if ( file_exists($js) ) {
      wp_enqueue_script('medical-js', ARKHE_CHILD_URI.'/assets/js/medical.js', [], ARKHE_CHILD_VER, true);
    }
  }
}, 99);
```

## 2) Ghi chú dựng trang

Bọc toàn bộ nội dung trang bằng Group có class medical-page (để scope CSS).

Các section chính: Hero (Cover), Media Slider, Vấn đề (Columns), Giải pháp (Media & Text), Gói dịch vụ (Box Links/Card), Quy trình (Step), Bệnh viện (logos), FAQ (Accordion), CTA, Contact (CF7).

Biến CSS dùng trong medical.css:

--ml-accent, --ml-accent-600, --ml-bg-soft, --ml-text, --ml-muted, --ml-border

Hero + Slider “chia 50/50 màn hình” (đã trừ chiều cao header sticky) nằm trong medical.css.

Nút: dùng Gutenberg mặc định + .is-style-outline (đã tuỳ chỉnh trong medical.css).

## 3) Quy ước Contact Form 7

Tạo 2 form riêng: VI và JA. Mỗi trang dùng shortcode form đúng ngôn ngữ (ID khác nhau giữa môi trường).

Cấu trúc label đề xuất:
```
<label><span class="field-label">Họ tên</span>
  [text* your-name]
</label>
```

Với checkbox (ví dụ: Gói dịch vụ), không lồng <label> trong <label>. Dùng:
```
<div class="field-group">
  <span class="field-label">Gói dịch vụ</span>
  [checkbox* your-service use_label_element "1–2 ngày" "4–5 ngày du lịch"]
</div>

```
medical.css tự thêm dấu * đỏ vào .field-label khi control là bắt buộc.

medical.js ví dụ set giá trị mặc định cho [date* your-date] = hôm nay + 1 tháng, và min = hôm nay.
## 4) Menu (hover/active)

Tuỳ biến hover/active cho menu chính trong style.css (site-wide).

Active đã bắt đủ: current-menu-item, current-menu-ancestor, và aria-current="page" (Navigation block).

## 5) Đa ngữ (Polylang)

Hai trang:

VI: /medical

JA: /ja/medical (nếu WordPress đặt ở root). Nếu WordPress cài bên trong /medical, Polylang sẽ tạo /medical/ja/medical (xem ghi chú ở phần Deploy).

Tạo 2 menu (VI/JA) và gán location cho header Arkhe theo từng ngôn ngữ.

CF7: chèn shortcode đúng form ID của mỗi ngôn ngữ/môi trường.

## 6) Deploy thủ công (staging/production)
### 6.1 Files

Zip/unzip: arkhe_child, các plugins cần thiết, uploads (bỏ các thư mục cache).

Quyền: thư mục 755, file 644.

### 6.2 Database

Import SQL.

Sau đó thay URL trong DB (mục §7) bằng Better Search Replace.

### 6.3 Cố định URL trong wp-config.php (QUAN TRỌNG cho site cài trong /medical)

Thêm vào file /medical/wp-config.php trước dòng /* That's all, stop editing! */

```
// Cố định URL cho cài đặt trong thư mục con /medical
define('WP_HOME',    'https://linker-earth.com/medical');
define('WP_SITEURL', 'https://linker-earth.com/medical');

// Ép SSL trong admin (khuyên dùng)
define('FORCE_SSL_ADMIN', true);
```
### 6.4 .htaccess cho site trong thư mục con

Tạo/ghi đè /medical/.htaccess:
```
# WordPress trong thư mục /medical
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /medical/
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /medical/index.php [L]
</IfModule>
```
### 6.5 Sau khi deploy

Permalinks: Settings → Permalinks → Save (flush).

Menus theo ngôn ngữ (Polylang).

CF7: cập nhật shortcode dùng ID mới.

Cache/CDN: purge.

Robots: staging nên chặn index; production mở index.

## 7) Thay link bằng Better Search Replace (BSR)

Dùng Tools → Better Search Replace (by WP Engine). Chạy 2 lượt:

Thay local HTTP → prod HTTPS trong thư mục con

Search: http://linker-medical.local

Replace: https://linker-earth.com/medical

Thay local HTTPS → prod HTTPS trong thư mục con

Search: https://linker-medical.local

Replace: https://linker-earth.com/medical

Tùy chọn ép HTTPS nếu cần:

Search: http://linker-earth.com/medical

Replace: https://linker-earth.com/medical

Thiết lập khi chạy:

Select tables: chọn tất cả.

Run as dry run?: tick chạy thử, xem số lượng rows, sau đó bỏ tick để chạy thật.

Replace GUIDs?: KHÔNG tick.

## 8) Khắc phục sự cố nhanh

/medical/wp-admin redirect về domain local

Đảm bảo đã set WP_HOME/WP_SITEURL (mục §6.3).

Tạm đổi tên wp-content/plugins → plugins_off và mu-plugins → mu-plugins_off để loại trừ plugin ép URL.

Kiểm tra /medical/.htaccess (mục §6.4).

Flush Permalinks + cache.

Bật debug nếu cần:
```
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```
CF7 “フォームが見つかりません / form not found”

Shortcode đang trỏ tới ID cũ. Vào Contact → Forms copy shortcode mới và dán lại ở trang tương ứng.

Đường dẫn JA/VI “đẹp” khi WP cài trong /medical

Polylang sẽ dùng /medical/ja/.... Nếu muốn /ja/medical ở root domain, cần thêm redirect 301 tại .htaccess gốc domain (mang tính ánh xạ, canonical thật vẫn là /medical/ja/medical).

## 9) Ghi chú cho người đóng góp

Không chỉnh theme Arkhe gốc; chỉ sửa arkhe_child.

Giới hạn CSS site-wide ở style.css; CSS theo trang medical.css (scope .medical-page).

Ưu tiên Arkhe Blocks (Section, Step, FAQ, Slider, Box Links) khi thêm section.

JS nhỏ bỏ vào assets/js/medical.js (enqueue đã cấu hình).

## 10) Changelog (ví dụ)

1.2.0 – Hover/active menu, hero/slider 50–50, nhãn CF7 + dấu *, medical.js (ngày mặc định).

1.1.0 – Pricing + Timeline + Hospitals, trang JA.

1.0.0 – Khởi tạo Medical (VI), khung Arkhe Child.