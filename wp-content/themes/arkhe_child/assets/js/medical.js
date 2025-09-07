// assets/js/medical.js
/** 
 * Tự set value cho trường date của Contact Form 7 (name="your-date")
 * - Ngày mặc định: hôm nay + 1 tháng
 * - Không cho chọn ngày quá khứ
 */
document.addEventListener('DOMContentLoaded', function () {
  // Tự set value cho trường date của Contact Form 7 (name="your-date")
  document.querySelectorAll('.wpcf7 form').forEach(function (form) {
    var input = form.querySelector('input[name="your-date"]');
    if (!input || input.value) return;

    // Ngày mặc định: hôm nay + 1 tháng
    var d = new Date();
    d.setMonth(d.getMonth() + 1);
    var pad = n => String(n).padStart(2, '0');
    input.value = d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());

    // Không cho chọn ngày quá khứ
    var t = new Date();
    var min = t.getFullYear() + '-' + pad(t.getMonth() + 1) + '-' + pad(t.getDate());
    input.setAttribute('min', min);
  });
});
