<?php
namespace verb;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use verb\exception\ClassNotFoundException;
use verb\exception\Forbidden;

class Mould
{
    protected $twig;
    public $tVar = [];
    protected $loader;
    protected $options = [
        'loader'        => APP . '/views', //path/to/templates,默认为用户application目录下的views
        'cache_path'    => ROOT . '/log/twig', //twig的缓存目录,默认为log下的twig
        'auto_reload'   => true, //是否根据文件更新时间，自动更新缓存
        'debug'         => true, //是否开启twig的debug模式
    ];

    /**
     * 视图类初始化
     * @param  array $options 缓存参数
     * options = array() 参数列表:
     * 'loader'     => string,path/to/templates,默认为用户application目录下的views
     * 'cache_path' => string,twig的缓存目录,默认为log下的twig
     * 'auto_reload'=> bool,是否根据文件更新时间，自动更新缓存
     * 'debug'      => bool,是否开启twig的debug模式
     */
    public function __construct($options = [])
    {
        if (!empty($options)) { //合并option
            $this->options = array_merge($this->options, $options);
        }
        if (empty($this->options['cache_path'])) { //判断路径是否为空，如果为空，就使用系统tmp目录
            $this->options['cache_path'] = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'verb_twig_cache' . DIRECTORY_SEPARATOR;
        } elseif (substr($this->options['cache_path'], -1) != DIRECTORY_SEPARATOR) { //加线
            $this->options['cache_path'] .= DIRECTORY_SEPARATOR;
        }
        if (COMPOSER && class_exists('\Twig\Loader\FilesystemLoader') && class_exists('\Twig\Environment')) { //如果引入了第三方插件就使用twig模板引擎
            $this->loader = new \Twig\Loader\FilesystemLoader($this->options['loader']); //默认加载器
            $this->twig = new \Twig\Environment($this->loader, [
                'cache' => $this->options['cache_path'], //path/to/compilation_cache
                'auto_reload' => $this->options['auto_reload'],  //根据文件更新时间，自动更新缓存
                'debug' => $this->options['debug']
            ]); //这是一个默认设置的environment
        }
    }
    /**
     * 将数组或者k-v组合为array，以render到模板文件中
     *
     * @param array|string $name
     * @param string $value
     * @return void
     */
    public function assign($name, $value = '')
    { //接受一对K,V
        if (is_array($name)) {
            $this->tVar = array_merge($this->tVar, $name);
        } else {
            $this->tVar[$name] = $value;
        }
    }
    /**
     * 添加模板路径
     *
     * @param string|array $paths
     * @param string $namespace
     * @return void
     */
    public function addTemplateFile($paths,$namespace='__main__'){
        if (!\is_array($paths)) {//判断是否是数组
            $paths = [$paths];
        }
        if($this->loader != null){
            foreach ($paths as $path) {
                $path =  str_replace(SEPARATOR, DIRECTORY_SEPARATOR, $path);
                $this->loader->addPath($path, $namespace);
            }
        }else{
            throw new ClassNotFoundException('Twig not found');
        }
    }
    /**
     * 设置模板路径
     *
     * @param string|array $paths
     * @param string $namespace
     * @return void
     */
    public function setTemplateFile($paths,$namespace='__main__'){
        $this->loader->setPaths($paths,$namespace);
    }
    /**
     * 使用php来作为模板引擎渲染变量
     *
     * @param string $template
     * @param array $vars
     * @return void
     */
    public function rendorPHP($template, $vars = [])
    {
        extract($this->tVar); //将assign数组中的内容打散
        if (!empty($vars)) {
            extract($vars); //如果$vars也有值，需要打散
        }
        $template =  $this->options['loader'] . DIRECTORY_SEPARATOR . $template;
        if (is_file($template)) {
            include $template;
        } else {
            throw new Forbidden("php path not found");
        }
    }
    /**
     * 使用twig来作为模板引擎渲染变量
     *
     * @param string $template
     * @param array $vars
     * @param bool $over 默认为true，覆盖，但是不会改边assign的值
     * @return void
     */
    public function rendorTwig($template, $vars = [],$over=true)
    {
        if($over){//是否覆盖
            $vars = $vars+$this->tVar;
        }else{
            $vars = array_merge($vars, $this->tVar);
        }
        if ($this->twig != null) { //判断$twig是否为空
            if (is_array($vars)) { //判断是否为数组
                $template =  $this->twig->loadTemplate($template); //加载模板文件
                $template->display($vars); //渲染带有变量的模板
            }
        }else{
            throw new ClassNotFoundException('Twig not found');
        }
    }
}
