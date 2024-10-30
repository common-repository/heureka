<?php

namespace HeurekaDeps\Wpify\PostType;

use WP_Post_Type;
abstract class AbstractCustomPostType implements PostTypeInterface
{
    /** @var WP_Post_Type */
    private $post_type;
    public function __construct()
    {
        $this->init();
        $this->setup();
    }
    private function init()
    {
        add_action('init', array($this, 'register_post_type'));
    }
    public function setup()
    {
    }
    public function register_post_type()
    {
        if (!post_type_exists($this->get_post_type_key())) {
            $this->post_type = register_post_type($this->get_post_type_key(), $this->get_args());
        }
        if (!$this->post_type || is_wp_error($this->post_type)) {
            throw new PostTypeException("Cannot create post type " . $this->get_post_type_key());
        }
    }
    public abstract function get_post_type_key() : string;
    public function get_args() : array
    {
        return array('label' => \ucfirst($this->get_post_type_key()), 'labels' => $this->generate_labels($this->get_post_type_key(), $this->get_post_type_key()), 'description' => \sprintf(__('Custom post type %s', 'wpify-post-type'), \ucfirst($this->get_post_type_key())), 'show_ui' => \true, 'show_in_rest' => \true);
    }
    protected function generate_labels(string $singular, string $plural)
    {
        return array('name' => \sprintf(_x('%s', 'post type general name', 'wpify-post-type'), \ucfirst($plural)), 'singular_name' => \sprintf(_x('%s', 'post type singular name', 'wpify-post-type'), \ucfirst($singular)), 'add_new' => \sprintf(_x('Add New', $this->get_post_type_key(), 'wpify-post-type')), 'add_new_item' => \sprintf(__('Add New %s', 'wpify-post-type'), \ucfirst($singular)), 'edit_item' => \sprintf(__('Edit %s', 'wpify-post-type'), \ucfirst($singular)), 'new_item' => \sprintf(__('New %s', 'wpify-post-type'), \ucfirst($singular)), 'view_item' => \sprintf(__('View %s', 'wpify-post-type'), \ucfirst($singular)), 'view_items' => \sprintf(__('View %s', 'wpify-post-type'), \ucfirst($plural)), 'search_items' => \sprintf(__('Search %s', 'wpify-post-type'), \ucfirst($plural)), 'not_found' => \sprintf(__('No %s found.', 'wpify-post-type'), \strtolower($plural)), 'not_found_in_trash' => \sprintf(__('No %s found in Trash.', 'wpify-post-type'), \strtolower($plural)), 'parent_item_colon' => \sprintf(__('Parent %s:', 'wpify-post-type'), \ucfirst($singular)), 'all_items' => \sprintf(__('All %s', 'wpify-post-type'), \ucfirst($plural)), 'archives' => \sprintf(__('%s Archives', 'wpify-post-type'), \ucfirst($singular)), 'attributes' => \sprintf(__('%s Attributes', 'wpify-post-type'), \ucfirst($singular)), 'insert_into_item' => \sprintf(__(
            /** @lang text */
            'Insert into %s',
            'wpify-post-type'
        ), \strtolower($singular)), 'uploaded_to_this_item' => \sprintf(__('Uploaded to this %s', 'wpify-post-type'), \strtolower($singular)), 'featured_image' => \sprintf(_x('Featured image', $this->get_post_type_key(), 'wpify-post-type')), 'set_featured_image' => \sprintf(_x('Set featured image', $this->get_post_type_key(), 'wpify-post-type')), 'remove_featured_image' => \sprintf(_x('Remove featured image', $this->get_post_type_key(), 'wpify-post-type')), 'use_featured_image' => \sprintf(_x('Use as featured image', $this->get_post_type_key(), 'wpify-post-type')), 'filter_items_list' => \sprintf(__('Filter %s list', 'wpify-post-type'), \strtolower($plural)), 'filter_by_date' => \sprintf(_x('Filter by date', $this->get_post_type_key(), 'wpify-post-type')), 'items_list_navigation' => \sprintf(__('%s list navigation', 'wpify-post-type'), \ucfirst($plural)), 'items_list' => \sprintf(__('%s list', 'wpify-post-type'), \ucfirst($plural)), 'item_published' => \sprintf(__('%s published.', 'wpify-post-type'), \ucfirst($singular)), 'item_published_privately' => \sprintf(__('%s published privately.', 'wpify-post-type'), \ucfirst($singular)), 'item_reverted_to_draft' => \sprintf(__('%s reverted to draft.', 'wpify-post-type'), \ucfirst($singular)), 'item_scheduled' => \sprintf(__('%s scheduled.', 'wpify-post-type'), \ucfirst($singular)), 'item_updated' => \sprintf(__('%s updated.', 'wpify-post-type'), \ucfirst($singular)), 'item_link' => \sprintf(_x('%s Link', 'navigation link block title', 'wpify-post-type'), \ucfirst($singular)), 'item_link_description' => \sprintf(_x('A link to a %s.', 'navigation link block description', 'wpify-post-type'), \strtolower($singular)));
    }
    public function get_post_type() : ?WP_Post_Type
    {
        return $this->post_type;
    }
}
