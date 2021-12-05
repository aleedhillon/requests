<?php

namespace App\Services;

abstract class Model
{
    protected DB $db;

    public function __construct()
    {
        $this->db = new DB;
    }

    abstract public function getTable(): string ;

    public function get(array $attributes = ['*'])
    {
        return $this->db->select($this->getTable(), $attributes);
    }

    public function find(int $id, array $attributes = ['*'])
    {
        return $this->db->find($this->getTable(), $id, $attributes);
    }

    public function create(array $data)
    {
        return $this->find($this->db->insertOne($this->getTable(), $data));
    }

    public function whereExists(string $attribute, $value)
    {
        return $this->db->whereExistsInTable($this->getTable(), $attribute, $value);
    }
}
