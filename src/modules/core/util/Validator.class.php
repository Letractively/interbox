<?php

// TODO share function among fields with the same attribute name (in an array)
// TODO >=, >; <=, <
/**
 * @version 0.2.20130105
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright 2012-2013 InterBox Core 2.0 for PHP, GuZhiji Studio
 * @package interbox.core.util
 */
class Validator {

    /**
     * <code>
     * array(
     *      'field_attribute'=>array(
     *          'ismethod' => [TRUE / FALSE], // optional, FALSE by default
     *          'function'  => 'function name',
     *          'error'       => 'error message'
     *      )
     * )
     * </code>
     * where 'ismethod' means that the function is a method of the validator class.
     * @var array 
     */
    protected $attributes = array(
        'len_max' => array(
            'ismethod' => TRUE,
            'function' => 'validateMaxLen',
            'error' => 'exceed_max_length'
        ),
        'len_min' => array(
            'ismethod' => TRUE,
            'function' => 'validateMinLen',
            'error' => 'below_min_length'
        ),
        'val_max' => array(
            'ismethod' => TRUE,
            'function' => 'validateMaxVal',
            'error' => 'exceed_max_value'
        ),
        'val_min' => array(
            'ismethod' => TRUE,
            'function' => 'validateMinVal',
            'error' => 'below_min_value'
        )
    );
    private $errors;
    private $fields;
    private $encoding;

    /**
     * @param array $fields 
     * <code>
     * array(
     *      'field_name' => array(
     *          'len_max' => 255,
     *          'len_min' => 5,
     *          'field_attribute' => [attribute value]
     *      )
     * );
     * </code>
     * where field attributes should be defined in the class attribute {@link $attributes}.
     * @param string $encoding
     */
    function __construct($fields, $encoding = 'utf8') {
        $this->fields = $fields;
        $this->encoding = $encoding;
        $this->errors = array();
    }

    /**
     * validate a value against the given field defined in the class attribute {@link $fields}.
     * 
     * @param string $field
     * @param mixed $value
     * @return boolean 
     */
    public function validate($field, $value) {
        if (isset($this->fields[$field])) {// field found
            $err = FALSE;
            // iterate attributes defined for the field
            foreach ($this->fields[$field] as $att => $attv) {
                if (!isset($this->attributes[$att]))
                    continue; // attribute not found: ignore
                // method or function
                if (isset($this->attributes[$att]['ismethod']) &&
                        $this->attributes[$att]['ismethod'])
                    $func = array($this, $this->attributes[$att]['function']);
                else
                    $func = $this->attributes[$att]['function'];
                // error message
                if (!call_user_func($func, $attv, $value)) {
                    $this->errors[] = array(
                        $field,
                        $this->attributes[$att]['error']
                    );
                    $err = TRUE;
                }
            }
            return !$err;
        } else { // field not found
            return FALSE;
        }
    }

    public function validateAll(array $form) {
        $valid = TRUE;
        foreach (array_keys($this->fields) as $field) {
            if (isset($form[$field])) {
                $valid = $valid & $this->validate($field, $form[$field]);
            } else if (isset($this->fields[$field]['required']) &&
                    $this->fields[$field]['required']) {//TODO isnull?
                // required
                $valid = FALSE;
                $this->errors[] = array(
                    $field,
                    'required'
                );
            }
        }
        return $valid;
    }

    public function clearErrors() {
        $this->errors = array();
    }

    public function getErrors() {
        return $this->errors;
    }

    public function errorExists() {
        return !empty($this->errors);
    }

    public function validateMaxLen($attvalue, $value) {
        return $attvalue >= mb_strlen($value, $this->encoding);
    }

    public function validateMinLen($attvalue, $value) {
        return $attvalue <= mb_strlen($value, $this->encoding);
    }

    public function validateMaxVal($attvalue, $value) {
        return $attvalue >= $value;
    }

    public function validateMinVal($attvalue, $value) {
        return $attvalue <= $value;
    }

}
