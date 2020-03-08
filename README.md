# validation
light validator

# Install
```
composer require flatphp/validation
```

# Usage
```php
use \Flatphp\Validation\Validator;

// data value
$data = ['username' => 'tom', 'email' => '', 'password' => '', 'age' => 10];

// validate rules (验证规则和错误消息)
$rules = array(
    'username' => ['required|length:3,20', array('required' => 'username required', 'length' => 'length range 3-20')],
    'email' => ['email', 'invalid email'],
    'password' => 'required'
);

// validate
$res = Validator::validate($data, $rules, $failed);

// if failed echo failed message
if (!$res) {
    echo $failed['msg'];
}


// failed is referenced value if fail, format: ['on' => 'required', 'msg' => 'username required']
```

## failed referenced value $failed 格式
```
// failed e.g.
array(
    'on' => 'required',
    'msg' => 'username required'
)
```


## single value validate
```php
use \Flatphp\Validation\Validator;
$value = 'hello';
$res = Validator::validateOne($value, 'required|length:2,20', array(
    'required' => 'cannot be empty',
), $failed);
```

## simple use
```php
$res = Validator::isEmail('test@gmail.com');
```


# How to custom your own validate method 加入定制自己的验证方法
* Just extends Validator class and add your own method (继承)
* Or Just write global function (写全局函数)
*notes:*
method|function should be like isXxx($value) or isXxx($value, $params), example:   
方法或函数格式必须是这样的，参考以下：
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

# Methods 已有的方法和规则
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
