<?php

declare(strict_types=1);

class LegacyManuscriptForm extends CModel
{
    public $model;

    public function __construct($model, $scenario = '')
    {
        $this->model = $model;

        foreach ($this->model->attributes as $key => $value) {
            $this->$key = $value;
        }

    }

    public function rules()
    {
        $rules = $this->model->rules();

        $map = [
            'integer' => ['numerical', ['integerOnly' => true]],
            'number' => ['numerical', []],
            'boolean' => ['boolean', []],
            'string' => ['length', []],
            'required' => ['required', []],
            'safe' => ['safe', []],
            'default' => ['default', []],
            'email' => ['email', []],
            'url' => ['url', []],
        ];

        $finalRules = [];

        foreach ($rules as $rule) {
            $attributes = $rule[0];
            $validator = $rule[1];
            $options = array_slice($rule, 2);

            if (in_array($validator, ['required', 'safe', 'email', 'url', 'boolean'])) {
                $finalRules[] = [$attributes, $validator];
                continue;
            }

            if (isset($map[$validator])) {
                $mappedValidator = $map[$validator][0];
                $mappedOptions = $map[$validator][1];
                $finalOptions = array_merge($mappedOptions, $options);

                if ($validator === 'string') {
                    if (isset($options['max'])) {
                        $finalOptions['max'] = $options['max'];
                    }
                    if (isset($options['min'])) {
                        $finalOptions['min'] = $options['min'];
                    }
                }

                $finalRules[] = array_merge([$attributes, $mappedValidator], $finalOptions);
            } else {
                $finalRules[] = [$attributes, 'safe'];
            }
        }

        return $finalRules;
    }

    public function attributeLabels()
    {
        return $this->model->attributeLabels();
    }

    public function attributeNames()
    {
        return array_keys($this->model->attributes);
    }

    public function __get($name)
    {
        return $this->model->$name;
    }

    public function __set($name, $value)
    {
        $this->model->$name = $value;
        $this->$name = $value;
    }

    public function validate($attributes = null, $clearErrors = true)
    {
        $result = $this->model->validate($attributes, $clearErrors);

        foreach ($this->model->getErrors() as $attr => $errors) {
            foreach ($errors as $error) {
                $this->addError($attr, $error);
            }
        }

        return $result;
    }
}
