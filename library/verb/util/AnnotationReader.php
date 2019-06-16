<?php
namespace verb\util;

//初始化全局的AnnotationReader，并增加对自定义Annotation的支持
class AnnotationReader{
	public function __construct(){
		$this->parser= new DocParser();
		Logger::debug('初始化文档分析器');
	}

	//获取类注解
	public function getClassAnnotations(\ReflectionClass $class, $record_doc=false)
	{
		Logger::debug('getClassAnnotations');
		$cn = $class->getName();
		if(isset($this->cache[$cn]['class'])){//判断是否在本类缓存中
			return $this->cache[$cn]['class'];
		}
		$this->cache[$cn]['class'] = array();//初始化注解未空
		$annots = $this->parser->parse($class->getDocComment(), 'class '.$cn, $record_doc);
		
		foreach ($annots as $annot){
		    $key = $annot[0];
		    $annot = $annot[1];
		    $this->cache[$cn]['class'][$key][]=$annot;//填充到缓存中
		}
		return $this->cache[$cn]['class'];

	}
	
	/**
	 * 获取方法注解
	 * {@inheritDoc}
	 */
	public function getMethodAnnotations(\ReflectionMethod $method, $record_doc=false)
	{

		Logger::debug('getMethodAnnotations');
		$cn = $method->getDeclaringClass()->getName();
		
		$id = $method->getName();
		if(isset($this->cache[$cn]['method'][$id])){
		        return $this->cache[$cn]['method'][$id];
		}
		$this->cache[$cn]['method'][$id] = array();
		$annots =  $this->parser->parse($method->getDocComment(), 'method '.$cn.'::'.$id.'()', $record_doc);
		foreach ($annots as $annot){
		    $key = $annot[0];
		    $annot = $annot[1];
		   
			$this->cache[$cn]['method'][$id][$key][]=$annot;
		}
		return $this->cache[$cn]['method'][$id];
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function getPropertyAnnotations(\ReflectionProperty $property, $record_doc=false)
	{
		Logger::debug('getPropertyAnnotations');
		$cn = $property->getDeclaringClass()->getName();
		$id = $property->getName();


		if(isset($this->cache[$cn]['property'][$id])){
			return $this->cache[$cn]['property'][$id];
		}

		$this->cache[$cn]['property'][$id] = array();
		$annots =  $this->parser->parse($property->getDocComment(), 'property '.$cn.'::$'.$id, $record_doc);
		foreach ($annots as $annot){
		    $key= $annot[0];
		    $annot= $annot[1];
		    
			$this->cache[$cn]['property'][$id][$key][]=$annot;
		}
		return $this->cache[$cn]['property'][$id];
	}
	private $cache=array() ;
	private $parser ;
}

