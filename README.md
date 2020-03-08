# validation
light validator

# Usage
```php
use \Flatphp\Validation\Validator;

$data = ['username' => 'tom', 'email' => '', 'password' => '', 'age' => 10];

$res = Validator::validate($data, array(
    'username' => ['required|length:3,20', array('required' => 'username required', 'length' => 'length range 3-20')],
    'email' => ['email', 'invalid email'],
    'password' => 'required'
), $failed);

if (!$res) {
    echo $failed['msg'];
}

// failed is referenced value if fail, format: ['on' => 'required', 'msg' => 'username required']


// single value validate

$value = 'hello';
$res = Validator::validateOne($value, 'required|length:2,20', array(
    'required' => 'cannot be empty',
), $failed);


// simple use
$res = Validator::isEmail('test@gmail.com');
```

# How to custom your own validate method
* Just extends Validator class and add your own method
* Or Just write global function
*notes:*
method|function should be like isXxx($value) or isXxx($value, $params), example:
```php
function isZero($value)
{
    return $value === 0;
}

function isHasstr($value, $param)
{
    return strpos($value, $param) !== false;
}
```

# Methods
| method | rule | note |
| --- | --- | --- |
| isRequired | require | except 0 |
| isNotempty | notempty | just empty() check |
| isEmail | email |  |
| isDate | date |  |
| isDatetime | datetime |  |
| isUrl | url |  |
| isMatch | match | preg_match patten |
| isLength | length | second parameter is the length range, e.g. 10,20 |
| isRange | range | second parameter is range, e.g. 10,20 |
| isBool | bool | [true, false, 0, 1, '0', '1'] |
| isString | string |  |
| isInt | int |  |
| isNumeric | numeric |  |
| isArray | array |  |
| isEqual | equal | == |
| isSame | same | === |
| isIp | ip |  |
| isJson | json |  |
| isIn | in | second parameter e.g. aa,bb,cc |
| isNotin | notin |  |
