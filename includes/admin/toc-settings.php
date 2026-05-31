<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_toc_register_settings')) {
  function eai_toc_register_settings(): void
  {
    register_setting(
      'eai_toc_settings_group',
      'eai_toc_settings',
      [
        'type' => 'array',
        'sanitize_callback' => 'eai_toc_sanitize_settings',
        'default' => eai_toc_get_default_settings(),
      ]
    );
  }
}

if (! function_exists('eai_toc_add_tools_page')) {
  function eai_toc_add_tools_page(): void
  {
    add_management_page(
      esc_html__('ICHouse TOC', 'eai'),
      esc_html__('ICHouse TOC', 'eai'),
      'manage_options',
      'eai-toc-settings',
      'eai_toc_render_settings_page'
    );
  }
}

if (! function_exists('eai_toc_render_settings_page')) {
  function eai_toc_render_settings_page(): void
  {
    if (! current_user_can('manage_options')) {
      return;
    }

    $settings = eai_toc_get_settings();
    $post_types = eai_toc_get_post_type_options();
    $taxonomies = eai_get_public_taxonomy_options();
    ?>
    <div class="wrap">
      <h1><?php echo esc_html__('ICHouse Table of Contents', 'eai'); ?></h1>
      <p>
        <?php echo esc_html__(
          'Tự động chèn bài viết liên quan và mục lục (api-rc) vào nội dung qua filter the_content. Bài viết liên quan luôn được chèn khi có kết quả; mục lục chỉ chèn khi đủ số heading tối thiểu.',
          'eai'
        ); ?>
      </p>
      <p class="description">
        <?php echo esc_html__(
          'Nếu dùng plugin easy-table-of-contents, hãy deactivate để tránh trùng mục lục và ID heading.',
          'eai'
        ); ?>
      </p>

      <form method="post" action="options.php">
        <?php settings_fields('eai_toc_settings_group'); ?>

        <table class="form-table" role="presentation">
          <tr>
            <th scope="row">
              <label for="eai_toc_title"><?php echo esc_html__('Tiêu đề mục lục', 'eai'); ?></label>
            </th>
            <td>
              <input
                type="text"
                id="eai_toc_title"
                name="eai_toc_settings[title]"
                value="<?php echo esc_attr($settings['title']); ?>"
                class="regular-text"
              />
            </td>
          </tr>
          <tr>
            <th scope="row"><?php echo esc_html__('Số heading tối thiểu', 'eai'); ?></th>
            <td>
              <input
                type="number"
                id="eai_toc_min_headings"
                name="eai_toc_settings[min_headings]"
                value="<?php echo esc_attr((string) $settings['min_headings']); ?>"
                min="1"
                step="1"
                class="small-text"
              />
              <p class="description">
                <?php echo esc_html__('Chỉ chèn TOC khi có ít nhất số heading h2–h6 này.', 'eai'); ?>
              </p>
            </td>
          </tr>
          <tr>
            <th scope="row"><?php echo esc_html__('Post types', 'eai'); ?></th>
            <td>
              <fieldset>
                <legend class="screen-reader-text"><?php echo esc_html__('Post types', 'eai'); ?></legend>
                <?php foreach ($post_types as $slug => $label) : ?>
                  <label style="display:block;margin-bottom:4px;">
                    <input
                      type="checkbox"
                      name="eai_toc_settings[enabled_post_types][]"
                      value="<?php echo esc_attr($slug); ?>"
                      <?php checked(in_array($slug, $settings['enabled_post_types'], true)); ?>
                    />
                    <?php echo esc_html($label . ' (' . $slug . ')'); ?>
                  </label>
                <?php endforeach; ?>
              </fieldset>
            </td>
          </tr>
        </table>

        <h2><?php echo esc_html__('Bài viết liên quan', 'eai'); ?></h2>
        <p class="description">
          <?php echo esc_html__(
            'Luôn chèn danh sách bài liên quan khi query có kết quả, không phụ thuộc số heading mục lục.',
            'eai'
          ); ?>
        </p>

        <table class="form-table" role="presentation">
          <tr>
            <th scope="row">
              <label for="eai_toc_related_posts_title"><?php echo esc_html__('Tiêu đề', 'eai'); ?></label>
            </th>
            <td>
              <input
                type="text"
                id="eai_toc_related_posts_title"
                name="eai_toc_settings[related_posts_title]"
                value="<?php echo esc_attr($settings['related_posts_title']); ?>"
                class="regular-text"
              />
            </td>
          </tr>
          <tr>
            <th scope="row">
              <label for="eai_toc_related_posts_count"><?php echo esc_html__('Số bài hiển thị', 'eai'); ?></label>
            </th>
            <td>
              <input
                type="number"
                id="eai_toc_related_posts_count"
                name="eai_toc_settings[related_posts_count]"
                value="<?php echo esc_attr((string) $settings['related_posts_count']); ?>"
                min="1"
                max="10"
                step="1"
                class="small-text"
              />
            </td>
          </tr>
          <tr>
            <th scope="row"><?php echo esc_html__('Taxonomies', 'eai'); ?></th>
            <td>
              <fieldset>
                <legend class="screen-reader-text"><?php echo esc_html__('Taxonomies', 'eai'); ?></legend>
                <p class="description">
                  <?php echo esc_html__(
                    'Không chọn taxonomy nào = dùng tất cả taxonomy public của post type bài hiện tại.',
                    'eai'
                  ); ?>
                </p>
                <?php foreach ($taxonomies as $slug => $label) : ?>
                  <label style="display:block;margin-bottom:4px;">
                    <input
                      type="checkbox"
                      name="eai_toc_settings[related_posts_taxonomies][]"
                      value="<?php echo esc_attr($slug); ?>"
                      <?php checked(in_array($slug, $settings['related_posts_taxonomies'], true)); ?>
                    />
                    <?php echo esc_html($label . ' (' . $slug . ')'); ?>
                  </label>
                <?php endforeach; ?>
              </fieldset>
            </td>
          </tr>
        </table>

        <?php submit_button(); ?>
      </form>
    </div>
    <?php
  }
}

add_action('admin_init', 'eai_toc_register_settings');
add_action('admin_menu', 'eai_toc_add_tools_page');
