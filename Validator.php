<?php namespace Flatphp\Validation;


/**
 * If want to add custom validate method, you could extends this class or just define isXxx($value) function
 */
class Validator
{
    /**
     * validate values
     * @param array $data
     * @param array $rules
     * @param array &$failed
     * @return bool
     * @throws ValidationException
     */
    public static function validate(array $data, array $rules, &$failed = [])
    {
        foreach ($rules as $key => $rule) {
            $messages = null;
            if (is_array($rule)) {
                if (isset($rule[1])) {
                    $messages = $rule[1];
                }
                $rule = $rule[0];
            }
            $res = self::validateItem($data[$key], $rule, $messages, $key, $failed);
            if (!$res) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param $value
     * @param $rule
     * @param null $messages
     * @param array $failed
     * @return bool
     * @throws ValidationException
     */
    public static function validateOne($value, $rule, $messages = null, &$failed = [])
    {
        return self::validateItem($value, $rule, $messages, null, $failed);
    }

    /**
     * validate one value
     * @param mixed $value
     * @param string $rule
     * @param mixed $messages
     * @param string $key
     * @param array &$failed
     * @return bool
     * @throws ValidationException
     */
    protected static function validateItem($value, $rule, $messages = null, $key = null, &$failed = [])
    {
        $rule = explode('|', $rule);
        foreach ($rule as $method) {
            if (strpos($method, ':')) {
                $method = explode(':', $method, 2);
                $param = $method[1];
                $method = trim($method[0]);
            } else {
                $param = null;
                $method = trim($method);
            }
            $self_method = 'is'. ucfirst($method);
            if (method_exists(get_called_class(), $self_method)) {
                $res = static::$self_method($value, $param);
            } elseif (function_exists($self_method)) {
                if (null === $param) {
                    $res = $self_method($value);
                } else {
                    $res = $self_method($value, $param);
                }
            } else {
                throw new ValidationException('method '. $method .' not exists');
            }
            if (!$res) {
                if (is_string($messages)) {
                    $msg = $messages;
                } elseif (isset($messages[$method])) {
                    $msg = $messages[$method];
                } else {
                    $msg = $key . ' validate failed on '. $method;
                }
                $failed = array(
                    'on' => $method,
                    'msg' => $msg
                );
                return false;
            }
        }
        return true;
    }


    /**
     * Not empty
     * @param mixed $value
     * @return bool
     */
    public static function isRequired($value)
    {
        if (is_null($value)) {
            return false;
        } elseif (is_string($value) && trim($value) === '') {
            return false;
        } elseif ((is_array($value) || $value instanceof \Countable) && count($value) < 1) {
            return false;
        }
        return true;
    }

    /**
     * @param $value
     * @return bool
     */
    public static function isNotempty($value)
    {
        return !empty($value);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isEmail($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isDate($value)
    {
        if (strtotime($value) === false) {
            return false;
        }
        $date = date_parse($value);
        return checkdate($date['month'], $date['day'], $date['year']);
    }

    /**
     * @param string $value
     * @param string $format
     * @return bool
     */
    public static function isDatetime($value, $format = 'Y-m-d H:i:s')
    {
        $d = \DateTime::createFromFormat($format, $value);
        return $d && $d->format($format) == $value;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isUrl($value)
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * @param string $value
     * @param string $pattern
     * @return bool
     */
    public static function isMatch($value, $pattern)
    {
        return (bool)preg_match($pattern, $value);
    }

    /**
     * @param string $value
     * @param string|array $param
     * @return bool
     */
    public static function isLength($value, $param)
    {
        $len = mb_strlen($value, 'UTF-8');
        return self::isRange($len, $param);
    }

    /**
     * @param mixed $value
     * @param string|array $param
     * @return bool
     */
    public static function isRange($value, $param)
    {
        if (!is_array($param)) {
            $param = explode(',', $param);
        }
        $min = $param[0];
        $max = null;
        if (isset($param[1])) {
            $max = $param[1];
        }
        return ($value >= $min && (null === $max || $value <= $max));
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isBool($value)
    {
        return in_array($value, [true, false, 0, 1, '0', '1'], true);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isString($value)
    {
        return is_string($value);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isInt($value)
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isNumeric($value)
    {
        return is_numeric($value);
    }

    /**
     * @param $value
     * @return bool
     */
    public static function isArray($value)
    {
        return is_array($value);
    }

    /**
     * @param mixed $value
     * @param mixed $param
     * @return bool
     */
    public static function isEqual($value, $param)
    {
        return $value == $param;
    }

    /**
     * @param mixed $value
     * @param mixed $param
     * @return bool
     */
    public static function isSame($value, $param)
    {
        return $value === $param;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isIp($value)
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * If is a json
     * @param $value
     * @return bool
     */
    public static function isJson($value)
    {
        json_decode($value);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * @param mixed $value
     * @param array|string $param
     * @return bool
     */
    public static function isIn($value, $param)
    {
        if (!is_array($param)) {
            $param = explode(',', $param);
        }
        return in_array($value, $param);
    }

    /**
     * @param mixed $value
     * @param array|string $param
     * @return bool
     */
    public static function isNotin($value, $param)
    {
        return !static::isIn($value, $param);
    }
}