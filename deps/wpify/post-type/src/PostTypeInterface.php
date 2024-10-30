<?php

namespace HeurekaDeps\Wpify\PostType;

use WP_Post_Type;
interface PostTypeInterface
{
    public function setup();
    public function get_post_type_key() : string;
    public function get_post_type() : ?WP_Post_Type;
    public function get_args() : array;
}
