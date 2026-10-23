<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_ocean_park_faq_map_items')) {
  /**
   * Map FAQ items repeater to OceanParkFaqItemModel[].
   *
   * @param mixed $raw_rows
   * @return array<int,array{question:string,answerHtml:string,defaultOpen:bool}>
   */
  function eai_ocean_park_faq_map_items($raw_rows): array
  {
    if (! is_array($raw_rows)) {
      return [];
    }

    $items = [];
    foreach ($raw_rows as $row) {
      if (! is_array($row)) {
        continue;
      }

      $question = sanitize_text_field((string) ($row['question'] ?? ''));
      $answer_raw = (string) ($row['answer_html'] ?? $row['answer'] ?? '');
      $answer_html = trim(function_exists('wp_kses_post') ? wp_kses_post($answer_raw) : $answer_raw);

      if ($question === '' || $answer_html === '') {
        continue;
      }

      if ($question === 'Cư dân có thể xem trực tiếp nhà mẫu nội thất và xưởng sản xuất của IC HOUSE tại Vinhomes Ocean Park không?') {
        $question = 'Vì sao nên chọn IC HOUSE — đơn vị có Nhà mẫu trong Ocean Park, Văn phòng và Nhà xưởng ngay gần khu đô thị?';
        $answer_html = '<p>Khác với các công ty nội thất ở xa nội thành hoặc đơn vị thương mại phải thuê xưởng gia công bên ngoài, <strong>IC HOUSE sở hữu 3 cơ sở hiện hữu ngay tại Vinhomes Ocean Park</strong> giúp quý cư dân hoàn toàn an tâm về chất lượng, giá thành và bảo hành:</p><ul><li><strong>1. Có Nhà mẫu nội thất TRONG khu đô thị Vinhomes Ocean Park:</strong> Đặt tại <strong>Sao Biển 23, KĐT Vinhomes Ocean Park, Gia Lâm, Hà Nội</strong> (chỉ 3 phút di chuyển nội khu) để gia chủ trực tiếp chạm tay kiểm tra cốt gỗ An Cường, đá Vicostone, tủ bếp cánh kính và độ sắc nét của đường dán cạnh trước khi xuống tiền.</li><li><strong>2. Có Văn phòng công ty GẦN khu đô thị:</strong> Đặt tại <strong>Số 01 Voi Phục, Gia Lâm, Hà Nội</strong> (cách 5 phút), đội ngũ 30+ Kiến trúc sư trưởng sẵn sàng có mặt khảo sát đo đạc căn hộ chỉ sau 15 phút và giám sát công trình hàng ngày.</li><li><strong>3. Có Nhà xưởng sản xuất 2.000m² GẦN Ocean Park:</strong> Đặt tại <strong>Thôn Ngọc Trai, Xã Như Quỳnh, Tỉnh Hưng Yên</strong> (cách 10 phút vận chuyển tới chân tòa), giúp tiết kiệm 15–20% chi phí trung gian, mời cư dân nghiệm thu cốt mộc tại xưởng và xử lý bảo hành siêu tốc trong 2–24 giờ.</li></ul>';
      }

      $default_open = ($row['default_open'] ?? '') === 'yes' || ($row['default_open'] ?? false) === true;

      $items[] = [
        'question' => $question,
        'answerHtml' => $answer_html,
        'defaultOpen' => $default_open,
      ];
    }

    return $items;
  }
}

if (! function_exists('eai_ocean_park_faq_map_keyword_pills')) {
  /**
   * Map keyword pills repeater to OceanParkFaqKeywordPillModel[].
   *
   * @param mixed $raw_rows
   * @return array<int,array{label:string,url:string}>
   */
  function eai_ocean_park_faq_map_keyword_pills($raw_rows): array
  {
    if (! is_array($raw_rows)) {
      return [];
    }

    $pills = [];
    foreach ($raw_rows as $row) {
      if (! is_array($row)) {
        continue;
      }

      $label = sanitize_text_field((string) ($row['label'] ?? ''));
      $raw_url = $row['url'] ?? '';
      $url = is_array($raw_url)
        ? trim((string) ($raw_url['url'] ?? ''))
        : trim((string) $raw_url);
      if ($label === '' || $url === '') {
        continue;
      }

      $pills[] = [
        'label' => $label,
        'url' => $url,
      ];
    }

    if (
      count($pills) === 27 &&
      ($pills[0]['label'] ?? '') === 'Ocean Park 1' &&
      ($pills[26]['label'] ?? '') === 'Dự án căn hộ Ocean Park 3'
    ) {
      return array_merge([
        ['label' => 'Thiết kế nội thất Vinhomes Ocean Park', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Thi công nội thất Vinhomes Ocean Park trọn gói', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Nhà mẫu nội thất Sao Biển 23 Ocean Park', 'url' => '#dang-ky-tu-van'],
        ['label' => 'Xưởng sản xuất nội thất gần Ocean Park', 'url' => '#dang-ky-tu-van'],
        ['label' => 'Công ty nội thất gần Vinhomes Ocean Park', 'url' => '#dang-ky-tu-van'],
        ['label' => 'Báo giá thi công nội thất Vinhomes Ocean Park', 'url' => '#dang-ky-tu-van'],
      ], $pills, [
        ['label' => 'Nội thất gỗ An Cường căn hộ Ocean Park', 'url' => '#du-an-mau-ocean-park'],
      ]);
    }

    return $pills;
  }
}

if (! function_exists('eai_ocean_park_faq_get_rc_props')) {
  /**
   * Build OceanParkFaqModel props from Elementor widget settings.
   *
   * @param array<string,mixed> $settings
   * @return array<string,mixed>
   */
  function eai_ocean_park_faq_get_rc_props(array $settings, string $widget_id = ''): array
  {
    $section_id = sanitize_title((string) ($settings['section_id'] ?? ''));
    if ($section_id === '') {
      $section_id = 'ocean-park-faq';
    }

    $safe_widget = sanitize_html_class($widget_id, 'default');
    if ($safe_widget === '') {
      $safe_widget = 'default';
    }

    $eyebrow = sanitize_text_field((string) ($settings['eyebrow'] ?? ''));
    if ($eyebrow === 'GIẢI ĐÁP CHUYÊN SÂU TỪ KIẾN TRÚC SƯ TRƯỞNG IC HOUSE') {
      $eyebrow = 'GIẢI ĐÁP CHUYÊN SÂU TỪ KIẾN TRÚC SƯ TRƯỞNG IC HOUSE (VĂN PHÒNG VOI PHỤC & NHÀ MẪU SAO BIỂN 23)';
    }

    $title = sanitize_text_field((string) ($settings['title'] ?? ''));
    if ($title === 'Câu Hỏi Thường Gặp Khi Thiết Kế & Thi Công Nội Thất Cao Cấp Vinhomes Ocean Park 1 • 2 • 3') {
      $title = 'Câu Hỏi Thường Gặp Khi Thiết Kế & Thi Công Nội Thất Vinhomes Ocean Park Trọn Gói Cao Cấp 1 • 2 • 3';
    }

    return [
      'className' => sanitize_text_field((string) ($settings['class_name'] ?? '')),
      'sectionId' => $section_id,
      'checkboxIdPrefix' => 'eai-ocean-park-faq-' . $safe_widget,
      'eyebrow' => $eyebrow,
      'title' => $title,
      'description' => sanitize_text_field((string) ($settings['description'] ?? '')),
      'items' => eai_ocean_park_faq_map_items($settings['items'] ?? []),
      'keywordGroupTitle' => sanitize_text_field((string) ($settings['keyword_group_title'] ?? '')),
      'keywordPills' => eai_ocean_park_faq_map_keyword_pills($settings['keyword_pills'] ?? []),
    ];
  }
}

if (! function_exists('eai_ocean_park_faq_props_are_empty')) {
  /**
   * Whether FAQ props are empty.
   *
   * @param array<string,mixed> $props
   */
  function eai_ocean_park_faq_props_are_empty(array $props): bool
  {
    return empty($props['items']) && empty($props['keywordPills']);
  }
}

if (! function_exists('eai_ocean_park_faq_get_editor_sample_props')) {
  /**
   * Editor sample props matching `src/data/ocean-park-faq.ts`.
   *
   * @param array<string,mixed> $settings
   * @return array<string,mixed>
   */
  function eai_ocean_park_faq_get_editor_sample_props(array $settings = [], string $widget_id = ''): array
  {
    return eai_ocean_park_faq_get_rc_props([
      'class_name' => $settings['class_name'] ?? '',
      'section_id' => $settings['section_id'] ?? 'ocean-park-faq',
      'eyebrow' => 'GIẢI ĐÁP CHUYÊN SÂU TỪ KIẾN TRÚC SƯ TRƯỞNG IC HOUSE (VĂN PHÒNG VOI PHỤC & NHÀ MẪU SAO BIỂN 23)',
      'title' => 'Câu Hỏi Thường Gặp Khi Thiết Kế & Thi Công Nội Thất Vinhomes Ocean Park Trọn Gói Cao Cấp 1 • 2 • 3',
      'description' => 'Tổng hợp kinh nghiệm thực chiến từ hơn 1.500+ công trình thiết kế và thi công nội thất chung cư trọn gói tại Vinhomes Ocean Park 1 (The London, The Beverly, The Paris), Ocean Park 2 (LUMIÈRE SpringBay, Masteri Trinity Square) và Dự án căn hộ Ocean Park 3.',
      'items' => [
        [
          'question' => 'Chi phí thiết kế và thi công nội thất trọn gói cao cấp tại Vinhomes Ocean Park 1, 2, 3 là bao nhiêu?',
          'answer_html' => '<p>Tại <strong>IC HOUSE</strong>, đơn giá thiết kế 3D cá nhân hóa (Bespoke) dao động từ <strong>250.000đ – 350.000đ/m²</strong> và được <strong>miễn phí 100%</strong> khi khách hàng ký hợp đồng thi công nội thất trọn gói. Tổng mức đầu tư hoàn thiện nội thất cao cấp khởi điểm từ <strong>120 triệu đồng/căn</strong>; trong đó gói <strong>Bespoke Luxury phổ biến từ 180 – 260 triệu đồng/căn</strong> sử dụng cốt gỗ MDF lõi xanh chống ẩm An Cường, Plywood phủ Veneer tự nhiên, sơn Inchem 5 lớp, cánh kính khung nhôm Anode, đá thạch anh Vicostone và phụ kiện giảm chấn Blum/Hafele chính hãng.</p>',
          'default_open' => 'yes',
        ],
        [
          'question' => 'Vì sao nên chọn IC HOUSE — đơn vị có Nhà mẫu trong Ocean Park, Văn phòng và Nhà xưởng ngay gần khu đô thị?',
          'answer_html' => '<p>Khác với các công ty nội thất ở xa nội thành hoặc đơn vị thương mại phải thuê xưởng gia công bên ngoài, <strong>IC HOUSE sở hữu 3 cơ sở hiện hữu ngay tại Vinhomes Ocean Park</strong>:</p><ul><li><strong>1. Có Nhà mẫu nội thất TRONG khu đô thị Vinhomes Ocean Park:</strong> Sao Biển 23, KĐT Vinhomes Ocean Park, Gia Lâm, Hà Nội.</li><li><strong>2. Có Văn phòng công ty GẦN khu đô thị:</strong> Số 01 Voi Phục, Gia Lâm, Hà Nội.</li><li><strong>3. Có Nhà xưởng sản xuất 2.000m² GẦN Ocean Park:</strong> Thôn Ngọc Trai, Xã Như Quỳnh, Tỉnh Hưng Yên.</li></ul>',
          'default_open' => '',
        ],
        [
          'question' => 'Thiết kế nội thất Căn hộ The London Ocean Park 1 (Tòa LD1, Tòa LD2, Tòa LD3) cần lưu ý gì?',
          'answer_html' => '<p>Phân khu <strong>The London</strong> (<strong>The London Ocean Park 1</strong>) bao gồm <strong>Tòa LD1</strong>, <strong>Tòa LD2</strong> và <strong>Tòa LD3</strong> sở hữu mặt bằng vuông vắn cùng tiêu chuẩn bàn giao hiện đại. Khi thiết kế <strong>Căn hộ The London</strong>, KTS IC HOUSE tập trung tối ưu hệ tủ kịch trần đồng bộ màu sắc với sàn và cửa bàn giao, bố trí đảo bếp kết hợp bàn ăn thông minh và giải pháp chiếu sáng kiến trúc giúp không gian rộng thoáng hơn 20–25%.</p>',
          'default_open' => '',
        ],
        [
          'question' => 'Căn hộ The Beverly Ocean Park 1 (Tòa BE1, Tòa BE2, Tòa BE3, Tòa BE4) bàn giao có sẵn bếp thì nên hoàn thiện thế nào?',
          'answer_html' => '<p>Tại phân khu <strong>The Beverly</strong> (<strong>The Beverly Ocean Park 1</strong>) gồm <strong>Tòa BE1</strong>, <strong>Tòa BE2</strong>, <strong>Tòa BE3</strong> và <strong>Tòa BE4</strong>, chủ đầu tư đã bàn giao sẵn hệ tủ bếp, thiết bị vệ sinh và điều hòa âm trần. Do đó, gói hoàn thiện <strong>Căn hộ The Beverly</strong> tại IC HOUSE sẽ <strong>khấu trừ toàn bộ hạng mục đã có sẵn</strong> (giúp gia chủ tiết kiệm 35 – 60 triệu đồng), tập trung ngân sách nâng cấp vách ốp phòng khách, hệ tủ quần áo cánh kính kịch trần, quầy bar rượu và không gian đa năng +1 đẳng cấp.</p>',
          'default_open' => '',
        ],
        [
          'question' => 'IC HOUSE có mặt bằng thiết kế mẫu cho phân khu The Paris Ocean Park 1 (Chung cư Ocean Park 1) không?',
          'answer_html' => '<p>Đội ngũ IC HOUSE đã hoàn thiện trọn bộ hồ sơ mặt bằng 2D và concept 3D cho các tòa thuộc phân khu <strong>The Paris</strong> (<strong>The Paris Ocean Park 1</strong>) tại <strong>Chung cư Ocean Park 1</strong>. Các phong cách được ưa chuộng nhất tại The Paris là <em>Parisian Chic</em>, <em>Modern Luxury</em> và <em>Japandi</em> thanh lịch, khai thác tối đa tầm nhìn hướng hồ San Hô và quảng trường trung tâm.</p>',
          'default_open' => '',
        ],
        [
          'question' => 'Tiêu chuẩn thiết kế thi công cho Căn hộ LUMIÈRE SpringBay và Dự án Masteri Trinity Square tại Ocean Park 2 có gì đặc biệt?',
          'answer_html' => '<p>Tại đại đô thị <strong>Ocean Park 2</strong>, hai phân khu cao tầng hạng sang là <strong>LUMIÈRE SpringBay</strong> (<strong>LUMIÈRE SpringBay Ocean Park 2</strong>) và <strong>Masteri Trinity Square</strong> (<strong>Masteri Trinity Square Ocean Park 2</strong>) có hệ kính Low-E kịch trần cong/phẳng cùng tiêu chuẩn bàn giao Masterise Homes khắt khe. Khi thi công <strong>Căn hộ LUMIÈRE SpringBay</strong> và <strong>Dự án Masteri Trinity Square</strong>, IC HOUSE sử dụng hệ liên kết âm giấu vít, nẹp đồng/nhôm Anode chuẩn xác từng milimet để bảo toàn tuyệt đối hệ trần thạch cao và sàn gỗ nguyên bản.</p>',
          'default_open' => '',
        ],
        [
          'question' => 'Phương án hoàn thiện nội thất cho Căn hộ cao tầng Ocean Park 3 (Dự án căn hộ Ocean Park 3) được tối ưu ra sao?',
          'answer_html' => '<p>Đối với các tòa thuộc <strong>Ocean Park 3</strong> (bao gồm <strong>Căn hộ cao tầng Ocean Park 3</strong> và <strong>Dự án căn hộ Ocean Park 3</strong>), IC HOUSE phát triển 2 dòng giải pháp chuyên biệt: (1) Gói <em>Bespoke Resort Living</em> dành cho gia đình ở thực hoặc nghỉ dưỡng cuối tuần với vật liệu chống ẩm tuyệt đối, và (2) Gói <em>Luxury Homestay Signature</em> có độ bền bề mặt cao, thu hút khách thuê chuyên gia quốc tế.</p>',
          'default_open' => '',
        ],
        [
          'question' => 'Quy trình đăng ký giấy phép thi công với Ban quản lý Vinhomes và chế độ bảo hành của IC HOUSE như thế nào?',
          'answer_html' => '<p>IC HOUSE hỗ trợ <strong>trọn gói 100% thủ tục hồ sơ kỹ thuật</strong> (bản vẽ cấp thoát nước, sơ đồ điện, biện pháp thi công an toàn) để xin cấp phép tại Ban quản lý Vinhomes Ocean Park 1, 2, 3. Toàn bộ công trình được cam kết tiến độ bằng hợp đồng (15 – 25 ngày làm việc), <strong>bảo hành 24 tháng</strong> và <strong>bảo trì định kỳ 5 năm</strong>; kỹ thuật viên trực tại Sao Biển 23 có mặt hỗ trợ trong vòng 2 giờ khi cư dân cần.</p>',
          'default_open' => '',
        ],
      ],
      'keyword_group_title' => 'Từ khóa tìm kiếm phổ biến: Thiết kế & Thi công nội thất Vinhomes Ocean Park 1 • 2 • 3 trọn gói cao cấp',
      'keyword_pills' => [
        ['label' => 'Thiết kế nội thất Vinhomes Ocean Park', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Thi công nội thất Vinhomes Ocean Park trọn gói', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Nhà mẫu nội thất Sao Biển 23 Ocean Park', 'url' => '#dang-ky-tu-van'],
        ['label' => 'Xưởng sản xuất nội thất gần Ocean Park', 'url' => '#dang-ky-tu-van'],
        ['label' => 'Công ty nội thất gần Vinhomes Ocean Park', 'url' => '#dang-ky-tu-van'],
        ['label' => 'Báo giá thi công nội thất Vinhomes Ocean Park', 'url' => '#dang-ky-tu-van'],
        ['label' => 'Ocean Park 1', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Chung cư Ocean Park 1', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'The Paris', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'The Paris Ocean Park 1', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'The London', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'The London Ocean Park 1', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Căn hộ The London', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Tòa LD1', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Tòa LD2', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Tòa LD3', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'The Beverly', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'The Beverly Ocean Park 1', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Căn hộ The Beverly', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Tòa BE1', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Tòa BE2', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Tòa BE3', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Tòa BE4', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Ocean Park 2', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'LUMIÈRE SpringBay', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'LUMIÈRE SpringBay Ocean Park 2', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Căn hộ LUMIÈRE SpringBay', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Masteri Trinity Square', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Masteri Trinity Square Ocean Park 2', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Dự án Masteri Trinity Square', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Ocean Park 3', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Căn hộ cao tầng Ocean Park 3', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Dự án căn hộ Ocean Park 3', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Nội thất gỗ An Cường căn hộ Ocean Park', 'url' => '#du-an-mau-ocean-park'],
      ],
    ], $widget_id);
  }
}
