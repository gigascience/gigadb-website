<?php

declare(strict_types=1);

trait LoadingFixtureTrait
{
    public function loadFixture($name, $modelClass)
    {
        $data = require codecept_data_dir() . $name . '.php';
        foreach ($data as $record) {
            $model = new $modelClass();
            foreach ($record as $attr => $value) {
                $model->$attr = $value;
            }
            $model->save(false);
        }
    }
}
