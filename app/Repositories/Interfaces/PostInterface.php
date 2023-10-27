<?php

namespace App\Repositories\Interfaces;

interface PostInterface
{
    public function index($array);

    public function store($array);

    public function detail($id);

    public function delete($id);

    public function update($array);

}
