<?php

declare(strict_types=1);

namespace Findkit\Settings;

if (!defined('ABSPATH')) {
	exit();
}

class Section
{
	private $section = null;
	private $page = null;
	private $title = null;
	private $fields = null;

	function __construct(string $page, string $section_name, array $options)
	{
		$this->fields = [];
		$this->section = $section_name;
		$this->page = $page;

		$this->title = $options['title'];

		\add_action('admin_init', [$this, '__action_admin_init']);
	}

	function get_section_name(): string
	{
		return $this->section;
	}

	function __action_admin_init()
	{
		$this->title = is_callable($this->title)
			? call_user_func($this->title)
			: $this->title;

		\add_settings_section(
			$this->section,
			$this->title,
			function () {},
			$this->page
		);

		foreach ($this->fields as $field) {
			\register_setting($this->page, $field['name']);

			$id = esc_attr($this->get_id($field));

			$field_title = is_callable($field['title'])
				? call_user_func($field['title'])
				: $field['title'];

			\add_settings_field(
				$field['name'],
				"<label for='$id'>" . $field_title . '</label>',
				function () use ($field) {
					$this->render_input($field);
				},
				$this->page,
				$this->section
			);
		}
	}

	function add_field($field): Section
	{
		if (isset($field['default'])) {
			$option = $field['name'];
			\add_filter("default_option_{$option}", function () use ($field) {
				return $field['default'];
			});
		}

		$this->fields[] = $field;
		return $this;
	}

	function get_id(array $field): string
	{
		return $this->section . "-option-{$field['name']}";
	}

	// prettier-ignore
	function render_input(array $field)
    {
        $render_description = $field['description'];
        $placeholder = $field['placeholder'] ?? '';
        $disabled = $field['disabled'] ?? false;
        $type = $field['type'] ?? 'input';
        $rows = $field['rows'] ?? '25';
        $option = $field['name'];

        if ($type === 'input' || $type === 'password') { ?>
					<input
						type="<?php echo esc_attr($type === 'password' ? 'password' : 'text'); ?>"
						style="width: 100%"
						data-1p-ignore
              <?php echo $disabled ? 'disabled' : ''; ?>
              <?php if ($placeholder) {
                  echo "placeholder='";
                  echo esc_attr($placeholder);
                  echo "'";
              } ?>
						name="<?php echo esc_attr($option); ?>"
						id="<?php echo esc_attr($this->get_id($field)); ?>"
						value="<?php echo esc_attr( get_option($option, '') ); ?>"

					/>
        <?php } elseif ($type === 'textarea') { ?>

					<textarea
						style="width: 100%"
						rows=<?php echo esc_attr($rows); ?>
						type="text"
						id="<?php echo esc_attr($this->get_id($field)); ?>"
						name="<?php echo esc_attr($option); ?>"
						value=""

					><?php echo esc_textarea( get_option($option, '') ); ?></textarea>

        <?php } elseif ($type === 'checkbox') { ?>

					<input
						type="checkbox"
						name="<?php echo esc_attr($option); ?>"
						id="<?php echo esc_attr($this->get_id($field)); ?>"
						value="1"
              <?php checked(1, get_option($option), true); ?>
              <?php echo $disabled ? 'disabled' : ''; ?> />

        <?php }
        ?>

			<p class="description">
          <?php if ($render_description) { $render_description(); } ?>
				<sub>(<?php echo esc_html($option); ?>)</sub>
			</p>
        <?php
    }
}
