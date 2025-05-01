<?php
/**
 * Create Service post type.
 *
 * @package iwpdev/simplybook-integration
 */

namespace Iwpdev\SimplybookIntegration\Admin\CustomPostTypes;

/**
 * CreateServicePostType class file.
 */
class CreateServicePostType {
	/**
	 * CreateServicePostType construct.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Init actions and filters.
	 *
	 * @return void
	 */
	private function init(): void {
		add_action( 'init', [ $this, 'register_post_type' ] );
	}

	/**
	 * Register service post type.
	 *
	 * @return void
	 */
	public function register_post_type(): void {
		register_post_type(
			'services',
			[
				'label'         => null,
				'labels'        => [
					'name'               => __( 'Послуги', 'simplybook-integration' ),
					'singular_name'      => __( 'Послуга', 'simplybook-integration' ),
					'add_new'            => __( 'Додати послугу', 'simplybook-integration' ),
					'add_new_item'       => __( 'Додати послугу', 'simplybook-integration' ),
					'edit_item'          => __( 'Редагувати послугу', 'simplybook-integration' ),
					'new_item'           => __( 'Нова послуга', 'simplybook-integration' ),
					'view_item'          => __( 'Переглянути послугу', 'simplybook-integration' ),
					'search_items'       => __( 'Пошук Послуги', 'simplybook-integration' ),
					'not_found'          => __( 'Послуга не знайдено', 'simplybook-integration' ),
					'not_found_in_trash' => __( 'Послуга в смітті не знайдено', 'simplybook-integration' ),
					'menu_name'          => __( 'Послуги', 'simplybook-integration' ),
				],
				'description'   => '',
				'public'        => true,
				'show_in_rest'  => true,
				'menu_position' => 10,
				'menu_icon'     => 'dashicons-index-card',
				'hierarchical'  => false,
				'supports'      => [
					'title',
					'editor',
					'author',
					'thumbnail',
					'excerpt',
					'revisions',
				],
				'taxonomies'    => [],
				'has_archive'   => false,
				'rewrite'       => true,
				'query_var'     => true,
			]
		);
	}
}
