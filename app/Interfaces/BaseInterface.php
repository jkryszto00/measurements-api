<?php

namespace App\Interfaces;

interface BaseInterface
{
    public function all();
    public function get(int $id);
    public function store(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
}
