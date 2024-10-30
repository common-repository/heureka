<?php

namespace HeurekaDeps\Wpify\Model\Relations;

use HeurekaDeps\Wpify\Model\Interfaces\PostModelInterface;
use HeurekaDeps\Wpify\Model\Interfaces\RelationInterface;
use HeurekaDeps\Wpify\Model\Interfaces\UserRepositoryInterface;
class PostAuthorRelation implements RelationInterface
{
    /** @var PostModelInterface */
    private $model;
    /** @var UserRepositoryInterface */
    private $repository;
    public function __construct(PostModelInterface $model, UserRepositoryInterface $repository)
    {
        $this->model = $model;
        $this->repository = $repository;
    }
    public function fetch()
    {
        return isset($this->model->author_id) ? $this->repository->get($this->model->author_id) : null;
    }
    public function assign()
    {
    }
}
