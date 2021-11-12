<?php

namespace App\Repositories;

abstract class BaseRepository implements RepositoryInterface
{
    protected $model;

    //khởi tạo

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct()
    {
        $this->setModel();
    }


    abstract public function getModel();

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function setModel()
    {
        $this->model = app()->make(
            $this->getModel()
        );
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function create(array $attributes = [])
    {
        return $this->model->create($attributes);
    }

    public function update($id, $attribute)
    {
        $result = $this->find($id);
        if( $result ) {
            $result->update($attribute);
            return $result;
        }
        return false;
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function delete($id): bool
    {
        $result = $this->find($id);
        if( $result ) {
            $result->delete();

            return true;
        }
        return false;
    }

}
