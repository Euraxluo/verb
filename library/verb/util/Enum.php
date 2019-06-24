<?php
namespace verb\util;
class Enum
{
    /**
     * 默认值
     */
    const __default = null;

    /**
     * 常量，根据子类的调用生成
     *
     * @var string
     */
    protected static $value;

    /**
     * 反射类
     *
     * @var ReflectionClass
     */
    protected static $reflectionClass;

    // 构造函数受保护,打算让反射获取子类的构造函数
    protected function __construct($value = null)
    {
        self::$value = is_null($value) ? static::__default : $value;
    }

    /**
     * 根据常量名输出值
     *
     * @param string $name
     * @param string $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        // 实例化一个反射类 static::class 表示调用者
        $reflectionClass = self::getReflectionClass();
        $constant = $reflectionClass->getConstant(strtoupper($name));//类常量成员的名字必须的大写。
        $construct = $reflectionClass->getConstructor();// 获取调用者的 构造方法
        $construct->setAccessible(true);//把构造函数修饰符改为可访问的
        $static = new static($constant);//直接实例化， PHP 会自动调用__toString 方法
        return $static;//这里就是取出来调用的静态方法名对应的常量值，使用隐式的toString来输出
    }

    /**
     * 实例化一个反射类
     * @return ReflectionClass
     * @throws ReflectionException
     */
    protected static function getReflectionClass()
    {
        if (!self::$reflectionClass instanceof ReflectionClass) {
            self::$reflectionClass = new \ReflectionClass(static::class);
        }
        return self::$reflectionClass;
    }

    /**
     * 返回常量的值
     *
     * @return string
     */
    public function __toString()
    {
        return (string)self::$value;
    }

    /**
     * 判断一个值是否有效 即是否为枚举成员的值
     * @param $val
     * @return bool
     * @throws ReflectionException
     */
    public static function isValid($val)
    {
        return in_array($val, self::toArray());
    }

    /**
     * 转换枚举成员为键值对输出
     * @return array
     * @throws ReflectionException
     */
    public static function toArray()
    {
        return self::getEnumMembers();
    }

    /**
     * 获取枚举的常量成员数组
     * @return array
     * @throws ReflectionException
     */
    public static function getEnumMembers()
    {
        return self::getReflectionClass()
            ->getConstants();
    }

    /**
     * 获取枚举成员值数组
     * @return array
     * @throws ReflectionException
     */
    public static function values()
    {
        return array_values(self::toArray());
    }

    /**
     * 获取枚举成员键数组
     * @return array
     * @throws ReflectionException
     */
    public static function keys()
    {
        return array_keys(self::getEnumMembers());
    }

    /**
     * 判断 Key 是否有效 即存在
     * @param $key
     * @return bool
     * @throws ReflectionException
     */
    public static function isKey($key)
    {

        if( in_array($key, array_keys(self::getEnumMembers())) ){
            return true;
        }
        else return false;
    }

    /**
     * 根据 Key 去获取枚举成员值
     * @param $key
     * @return static
     */
    public static function getKey($key)
    {
        return self::$key();
    }

    /**
     * 格式枚举结果类型
     * 当 type   为true,return bool
     * 当 枚举量 为纯数字 或 type为int,return int
     * @param null|bool|int $type 当此处的值时什么类时 格式化输出的即为此类型
     * @return bool|int|string|null
     */
    public function format($type = null)
    {
        switch (true) {
            case ctype_digit(self::$value) || is_int($type):
                return (int)self::$value;
                break;
            case $type === true:
                return (bool)filter_var(self::$value, FILTER_VALIDATE_BOOLEAN);
                break;
            default:
                return self::$value;
                break;
        }
    }

}
