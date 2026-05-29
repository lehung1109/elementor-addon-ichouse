---
name: eai-rc-elementor-widget
description: >-
  Tạo hoặc migrate Elementor widget ICHouse gọi api-rc SSR + transient cache.
  Dùng khi thêm widget EAI-*, tích hợp React component từ api-rc vào WordPress,
  eai_rc_render_html, rc-files/version.json, hoặc thay template PHP tĩnh bằng API render.
---

# EAI Elementor Widget + api-rc

## Kiến trúc

```text
Elementor widget (PHP)  →  map settings → props JSON
       ↓
eai_rc_render_html(ComponentName, $props)  →  transient (1 tháng, key = version + props)
       ↓ miss
POST https://api-rc.ichouse.vn/api/render-rc  { component, props }
       ↓
Template PHP  →  echo $html  (không wp_kses_post — có script data-rct)
```

- **HTML + hydrate data** nằm trong một chuỗi `html` (SSR đã embed `<script data-rct="...">` qua `ReactSection`).
- **Cache version**: `wp-content/plugins/rc-files/version.json` → `components.{Name}.version`.
- **Helpers có sẵn**: `includes/rc-render.php`, `includes/helpers/bootstrap.php` (+ modules) — không duplicate logic transient/API trong widget.
- **Cursor rules**: `app/public/.cursor/rules/eai-elementor-widgets.mdc`, `eai-related-posts.mdc`.

## Trước khi code WordPress

1. Đọc component React trong `api-rc/src/components/...` và **Model** (props TypeScript).
2. Xác định **tên gọi API** (registry key = tên file `.tsx` default export, PascalCase):
   - Server component → gọi đúng tên file, vd. `HeaderTop`, `HeaderMenu`, `ProcessSection`.
   - **Client component (`"use client"`) → luôn luôn dùng `*Wrapper` server** — **không** gọi `Carousel`, `FeatureCardsCarousel`, v.v. Gọi API bằng tên wrapper: `CarouselWrapper`, `FeatureCardsCarouselWrapper`.
3. Props mock / fixture trong api-rc: file `src/data/<wrapper-kebab>.ts` (vd. `carousel-wrapper.ts`) — `version.json` chỉ version hóa component có file data tương ứng; client file không phải entry WordPress.
4. Sau build api-rc: copy `dist/version.json` + bundle vào `rc-files/`.

### Client component trong api-rc (bắt buộc wrapper — không ngoại lệ)

| Layer | File | WordPress / API |
|-------|------|-----------------|
| Client island | `Feature.tsx` (`"use client"`) | **Không** gọi |
| Server entry | `FeatureWrapper.tsx` | `eai_rc_render_html('FeatureWrapper', $props)` |
| Data canonical | `src/data/feature-wrapper.ts` | Khớp `version.json` → `components.FeatureWrapper` |
| Data hydrate | `src/data/feature.ts` | Re-export props; `data-rct="feature"` (camelCase **client**) |

```tsx
// Feature.tsx — "use client"
// FeatureWrapper.tsx — server, không "use client"
import ClientComponentWrapper from "../ClientComponentWrapper";
import ReactSection from "../ReactSection";
import Feature from "./Feature";

const FeatureWrapper = (model: FeatureModel) => (
  <ClientComponentWrapper>
    <Feature {...model} />
    <ReactSection type="featureCamelCase" data={model} />
  </ClientComponentWrapper>
);
export default FeatureWrapper;
```

- `ReactSection` `type` = camelCase **client** (`Carousel` → `carousel`), khớp `src/data/carousel.ts` + client registry — **không** dùng tên wrapper.
- Elementor widget: `component` argument **luôn** là `*Wrapper`; controls map props giống `FeatureWrapperModel`.

## Checklist tạo widget mới

```text
- [ ] api-rc: component (+ Wrapper nếu client)
- [ ] api-rc: build → cập nhật rc-files/version.json
- [ ] WP: includes/widgets/EAI-{slug}.php
- [ ] WP: includes/templates/EAI-{slug}.php
- [ ] WP: register trong includes/plugin.php
- [ ] Map props + helper (nếu lặp lại)
- [ ] Không markup HTML trong template (chỉ echo $html)
```

## Widget PHP (`includes/widgets/EAI-*.php`)

```php
protected function render(): void
{
  $settings = $this->get_settings_for_display();
  $props = [ /* khớp Model api-rc */ ];

  $result = eai_rc_render_html('ComponentOrWrapperName', $props);

  eai_render_template('templates/EAI-{slug}.php', [
    'html' => is_wp_error($result) ? '' : $result['html'],
    'error' => is_wp_error($result) ? $result : null,
    // 'empty' => true — chỉ khi không có dữ liệu bắt buộc (vd. chưa chọn menu)
  ]);
}
```

- `register_controls()` giữ mapping Elementor; có thể tách `protected function get_rc_props(): array`.
- Class: `EAI_{Feature}_Widget`, file `EAI-{slug}.php`, `get_name()` → `eai_{slug}_widget`.

## Template PHP (`includes/templates/EAI-*.php`)

```php
<?php
if (! defined('ABSPATH')) { exit; }

$args = isset($args) && is_array($args) ? $args : [];
$html = $args['html'] ?? '';
$error = $args['error'] ?? null;

if (! empty($args['empty'])) {
  echo '<div class="eai-...-empty">' . esc_html__('...', 'eai') . '</div>';
  return;
}

if ($error instanceof WP_Error) {
  eai_rc_render_error_message($error);
  return;
}

echo $html;
```

- Lỗi API: comment HTML chỉ khi `WP_DEBUG` hoặc user đăng nhập (`eai_rc_render_error_message`).
- **Không** `wp_kses_post` trên `$html`.

## Map props thường dùng (helpers)

| Elementor / WP | api-rc prop | Helper |
|----------------|-------------|--------|
| URL control | `{ url, is_external, nofollow }` | inline hoặc `eai_rc_map_link()` |
| MEDIA + dimensions + link? | `MediaModel` | `eai_rc_map_media_model($media, $dims, $link, $size)` |
| Repeater slides | `slides[].image` | `eai_rc_map_carousel_slides()` |
| Repeater feature cards | `items[]` | `eai_rc_map_feature_cards_carousel_items()` |
| Repeater partner logos | `logos[]` | `eai_rc_map_partner_logos()` — no link |
| Footer menu / social / contact / fanpage | `menuColumns`, `social.links`, `contact.blocks`, `embeds` | `eai_rc_map_footer_*()` trong `helpers.php` |
| Repeater info_list | `info_list[]` | `eai_rc_map_header_inner_info_list()` |
| Nav menu ID | `items[]` tree | `eai_get_menu_tree_with_active()` → `eai_rc_map_header_menu_items()` |

Thêm mapper mới vào `includes/helpers/{feature}.php` + `bootstrap.php` khi pattern lặp ≥2 widget.

## Nhóm Elementor

- Category slug: `eai-ichouse` (`eai_get_widget_category_slug()` / `eai_get_widget_categories()`).
- Panel title: **ICHouse React** — đăng ký trong `plugin.php` → `register_widget_categories()` với `position` **1** (gần đầu danh sách).
- Widget title: prefix `ICHouse — …` để search và sort trong nhóm.

## Widget đã migrate (tham chiếu)

| Widget | API `component` | Ghi chú |
|--------|-----------------|--------|
| EAI-header | `Header` | Full header (mobile overlay + desktop) trong một SSR |
| EAI-carousel | `CarouselWrapper` | Client `Carousel.tsx`; data `carousel-wrapper.ts` |
| EAI-feature-cards-carousel | `FeatureCardsCarouselWrapper` | Client `FeatureCardsCarousel.tsx`; data `feature-cards-carousel-wrapper.ts` |
| EAI-partner-logos | `PartnerLogosWrapper` | Client `PartnerLogos.tsx`; repeater logos (no link); data `partner-logos-wrapper.ts` |
| EAI-process-section | `ProcessSection` | Server; `backgroundImage`, `introContent`, `steps` |
| EAI-design-consultation-cta | `DesignConsultationCta` | Server; `backgroundImage`, `heading`, `subheading`, `cta`, `ctaLabel` |
| EAI-footer | `Footer` | Server; `top` (3 menu cột + payment + social), `bottom` (brand, contact, fanpages) |
| EAI-related-posts | `RelatedPostList` | Query PHP theo taxonomy post hiện tại; xem rule `eai-related-posts.mdc` |

## Header conventions (api-rc)

Khi chỉnh header, đọc: `api-rc/src/components/header/HEADER.md`.

## Đăng ký plugin

Trong `includes/plugin.php` → `register_widgets()`:

```php
require_once __DIR__ . '/widgets/EAI-{slug}.php';
$widgets_manager->register(new \EAI_{Feature}_Widget());
```

## Không làm

- Duplicate `wp_remote_post` / transient trong từng widget.
- Template PHP copy markup từ React (trừ empty state đơn giản).
- Gọi API / render PHP với tên client (`Carousel`, `FeatureCardsCarousel`) khi đã có `*Wrapper` server.
- Dùng `src/data/<client-kebab>.ts` làm nguồn props WordPress khi đã có wrapper data — dùng file `*-wrapper.ts`.
- Commit `version.json` cũ sau khi đổi component (cache miss theo version mới).

## Kiểm tra

1. View source: markup + `data-rct` nếu client.
2. Reload trang: lần 2 dùng transient (cùng props + version).
3. Đổi setting Elementor → HTML đổi.
4. `react-loader.js` / CSS đã enqueue (`plugin.php` → `register_frontend_assets`).

Chi tiết api-rc component: [reference.md](reference.md)
