<?php

namespace HeurekaDeps\Wpify\Model;

use HeurekaDeps\Wpify\Model\Abstracts\AbstractTermModel;
use HeurekaDeps\Wpify\Model\Interfaces\PostModelInterface;
use HeurekaDeps\Wpify\Model\Relations\TermPostsRelation;
class PostTag extends AbstractTermModel
{
    /** @var PostModelInterface */
    public $posts;
    protected function posts_relation() : TermPostsRelation
    {
        return new TermPostsRelation($this, 'posts', $this->model_repository()->get_post_repository());
    }
}
