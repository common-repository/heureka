<?php

namespace HeurekaDeps\Wpify\PostType;

use WP_Post_Type;
abstract class AbstractBuiltinPostType implements PostTypeInterface
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
        add_action('init', array($this, 'resolve_post_type'), \PHP_INT_MAX);
    }
    public function setup()
    {
    }
    public function resolve_post_type()
    {
        $this->post_type = get_post_type_object($this->get_post_type_key());
        if (!$this->post_type || is_wp_error($this->post_type)) {
            throw new PostTypeException("Cannot resolve post type " . $this->get_post_type_key());
        }
    }
    public abstract function get_post_type_key() : string;
    public function get_args() : array
    {
        return \json_decode(\json_encode($this->get_post_type()), \true);
    }
    public function get_post_type() : ?WP_Post_Type
    {
        return $this->post_type;
    }
}
