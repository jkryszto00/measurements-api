<?php

namespace App\Interfaces;

interface MeasurementInterface extends BaseInterface
{
    public function allWithPagination(int $perPage, array $filters);
    public function autosuggestion(array $data);
    public function detection();
}
