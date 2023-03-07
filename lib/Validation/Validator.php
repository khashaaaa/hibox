<?php
/**
 * @author abir
 * @license BSD
 */

class Validator
{
    protected $rules = array();
    protected $errors = array();
    protected $data = array();

    public function __construct($data, $fields = array())
    {
        if ($fields) {
            foreach ($fields as $key) {
                if (! isset($data[$key])) {
                    $data[$key] = null;
                }

                $this->data[$key] = isset($data[$key]) ? $data[$key] : null;
            }
        } else {
            $this->data = $data;
        }
    }

    public function addRule($rule, $key, $message = '', $required = true)
    {
        $this->rules[] = array($rule, $key, $message, $required);
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function validate()
    {
        $this->errors = array();
        foreach ($this->rules as $data) {
            /** @var $rule IRule */
            list($rule, $key, $message, $req) = $data;
            if (! array_key_exists($key, $this->data)) {
                $this->data[$key] = null;
            }

            $validate_value = $this->data[$key];

            if ($req && !$rule->test($validate_value)
                || !$req && "$validate_value" !== "" && !$rule->test($validate_value)) {

                $this->errors[] = new ValidationError($message ? $message : $rule->getMessage(), $key, $validate_value);
            }
        }

        return $this->errors ? false : true;
    }

    /**
     * Save Validator state
     * @param string $id
     * @return string key
     */
    public function store($id = null)
    {
        $key = get_class() . ($id ? "|$id" : '');
        Session::start();
        Session::set($key, $this);

        return $key;
    }

    /**
     * Restore saved Validator
     * @param string $id
     * @return Validator
     */
    public static function restore($id = null)
    {
        $key = get_class() . ($id ? "|$id" : '');

        Session::start();
        return Session::extract($key, new self(array()));
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function hasErrors()
    {
        return count($this->errors) > 0;
    }

    public function getLastError()
    {
        return array_key_exists(0, $this->errors) ? $this->errors[0] : null;
    }

    public function addAliasStringValidator($key, $error)
    {
        $aliasRegexp = '/^[^' . preg_quote('\!"\'`#$%&*+,:;<>=?[]^{}|/ ', '/') . ']+$/i';
        $this->addRule(new Regexp($aliasRegexp), $key, $error);
        return $this;
    }
}
