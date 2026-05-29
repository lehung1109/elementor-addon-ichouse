# Reference — api-rc ↔ Elementor

## API render server

- **URL**: `https://api-rc.ichouse.vn/api/render-rc` (filter `eai_rc_api_render_url`)
- **Body**: `{ "component": "HeaderTop", "props": { ... } }`
- **Response**: `{ "html": "...", "hash": "..." }`
- **Registry**: `api-rc/scripts/generate-server-registry.ts` — mọi `.tsx` trong `src/components` (trừ `ui/`, `ClientComponentWrapper`, `ReactSection`, `client-components.tsx`), kể cả `"use client"` (SSR tĩnh).

## version.json

Path: `wp-content/plugins/rc-files/version.json`

```json
{
  "components": {
    "HeaderTop": { "version": "<sha256 sample html>", "source": "...", "data": "..." }
  }
}
```

Cache key PHP: `sha256(component|version|sha256(props_json))` → transient `eai_rc_*`, TTL `MONTH_IN_SECONDS`.

## Client hydrate

- Discover: `api-rc/src/lib/discover-client-components.ts` — file có `"use client"` + `src/data/{client-kebab}.ts` (vd. `carousel.ts`)
- `data-rct` = camelCase **client** export (vd. `carousel` ↔ `Carousel.tsx`) — không phải tên wrapper
- Bundle: `rc-files/react-loader.js`

## Client + wrapper (api-rc)

- WordPress / `version.json` / fixture: component = `*Wrapper`, data = `src/data/*-wrapper.ts`
- Client island: không gọi API trực tiếp; hydrate qua `ReactSection` + registry client

## MediaModel (TypeScript)

```ts
{
  url: string;
  alt: string;
  display_dimensions: { width: number; height: number };
  link?: { url: string; is_external: boolean; nofollow: boolean };
}
```

## LinkModel

```ts
{ url: string; is_external: boolean; nofollow: boolean }
```

## File layout plugin

```text
elementor-addon-ichouse/
├── elementor-addon-ichouse.php   # require helpers/bootstrap.php, rc-render.php
├── includes/
│   ├── helpers/bootstrap.php     # eai_render_template, require modules
│   ├── helpers/elementor-controls.php, media.php, footer.php, …
│   ├── rc-render.php             # eai_rc_render_html
│   ├── plugin.php                # register widgets + assets
│   ├── widgets/EAI-*.php
│   └── templates/EAI-*.php
```

Cursor rules (ưu tiên tham chiếu): workspace `app/public/.cursor/rules/eai-*.mdc`.

## Context-aware widgets (query PHP)

- Logic phụ thuộc post/URL hiện tại chạy trong `includes/helpers/{feature}.php`, không trong api-rc.
- Ví dụ planned: **Related posts** — `eai_related_posts_resolve()`, component `RelatedPostList`, slugs category bỏ qua: `un-category`, `uncategorized`.
- Rule: `app/public/.cursor/rules/eai-related-posts.mdc`.

## process-section

| Widget | API `component` | Ghi chú |
|--------|-----------------|--------|
| EAI-process-section | `ProcessSection` | Server; `backgroundImage`, `introContent` (WYSIWYG HTML), `steps` (`eai_rc_map_process_section_steps`) |
