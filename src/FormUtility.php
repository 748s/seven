<?php

namespace Seven;

use Exception;

/**
 * FormUtility
 *
 * @author Nick Wakeman <nick.wakeman@gmail.com>
 * @since  2016-10-08
 */
class FormUtility
{
    public $data = array();
    public $errors = array();

    public function isString($key, $errorMessage)
    {
        $this->data[$key] = trim($_POST[$key]);
        if ($this->data[$key] == '' && $errorMessage) {
            $this->errors[$key] = $errorMessage;
        }
    }

    public function isCleanString($key, $errorMessage)
    {
        $this->data[$key] = trim(strip_tags($_POST[$key]));
        if ($this->data[$key] == '' && $errorMessage) {
            $this->errors[$key] = $errorMessage;
        }
    }

    public function isMaxLength($key, $maxLength, $errorMessage)
    {
        if (mb_strlen($this->data[$key]) > $maxLength) {
            $this->errors[$key] = $errorMessage;
        }
    }

    public function isMinLength($key, $minLength, $errorMessage)
    {
        if (mb_strlen($this->data[$key]) < $minLength) {
            $this->errors[$key] = $errorMessage;
        }
    }

    public function trimToLength($key, $length)
    {
        $this->data[$key] = substr($this->data[$key], 0, $length);
    }

    public function isCheckbox($key, $errorMessage)
    {
        $this->data[$key] = (isset($_POST[$key]) && $_POST[$key] == 1) ? 1 : 0;
        if ($errorMessage) {
            $this->errors[$key] = $errorMessage;
        }
    }

    public function isInt($key, $errorMessage)
    {
        if (preg_match('/^[1-9][0-9]{0,15}$/', $_POST[$key])) {
            $this->data[$key] = (int) $_POST[$key];
        } elseif ($errorMessage) {
            $this->errors[$key] = $errorMessage;
        }
    }

    public function isMaxValue($key, $maxValue, $errorMessage)
    {
        if ($this->data[$key] > $maxValue) {
            $this->errors[$key] = $errorMessage;
        }
    }

    public function isMinValue($key, $minValue, $errorMessage)
    {
        if ($this->data[$key] < $minValue) {
            $this->errors[$key] = $errorMessage;
        }
    }

    public function isChoices($key, $choices, $errorMessage)
    {
        if (in_array($_POST[$key], $choices)) {
            $this->data[$key] = $_POST[$key];
        } elseif ($errorMessage) {
            $this->errors[$key] = $errorMessage;
        }
    }

    public function isEmailAddress($key, $errorMessage)
    {
        $this->data[$key] = trim($_POST[$key]);
        if (!filter_var(trim($_POST[$key]), FILTER_VALIDATE_EMAIL) && $errorMessage) {
            $this->errors[$key] = $errorMessage;
        }
    }

    public function isDate($key, $format = 'YYYY-MM-DD', $errorMessage)
    {
        switch($format) {
            case 'YYYY-MM-DD':
                $dateArray = explode('-', $_POST[$key]);
                $this->validateDate($dateArray[0], $dateArray[1], $dateArray[2], $key, $errorMessage);
            break;
            case 'YYYY/MM/DD':
                $dateArray = explode('/', $_POST[$key]);
                $this->validateDate($dateArray[0], $dateArray[1], $dateArray[2], $key, $errorMessage);
            break;
            case 'MM-DD-YYYY':
                $dateArray = explode('-', $_POST[$key]);
                $this->validateDate($dateArray[2], $dateArray[1], $dateArray[0], $key, $errorMessage);
            break;
            case 'MM/DD/YYYY':
                $dateArray = explode('/', $_POST[$key]);
                $this->validateDate($dateArray[2], $dateArray[1], $dateArray[0], $key, $errorMessage);
            break;
            default:
                throw new Exception('Invalid date format');
            break;
        }
    }

    private function validateDate($year, $month, $day, $key, $errorMessage)
    {
        if (checkdate($month, $day, $year)) {
            $this->data[$key] = trim($_POST[$key]);
        } elseif ($errorMessage) {
            $this->errors[$key] = $errorMessage;
        }
    }

    public function isRequiredGroup($keys, $errorKey, $errorMessage)
    {
        foreach ($keys as $key) {
            if ($this->data[$key]) {
                return;
            }
        }
        $this->errors[$errorKey] = $errorMessage;
    }

    public function dataMatch($key1, $key2, $errorKey, $errorMessage)
    {
        if ($this->data[$key1] != $this->data[$key2] && $errorMessage) {
            $this->errors[$errorKey] = $errorMessage;
        }
    }

    public function getData($key = null)
    {
        return ($key) ? $this->data[$key] : $this->data;
    }

    public function getErrors($key = null)
    {
        return ($key) ? $this->errors[$key] : $this->errors;
    }
}
