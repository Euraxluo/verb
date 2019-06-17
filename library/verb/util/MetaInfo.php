<?php
namespace verb\util;

use verb\exception\ClassNotFoundException;

class AnnotationTest{
    /**
     * @return void
     */
    public function test(){
        
    }
}
/**
 * 元信息
 * 处理注释中的@annotation, 生成以@annotation为key的数组
 * @author caoym
 */
class MetaInfo
{
    /**
     * 获取元信息
     * @param object|class $inst
     * @param boolean $record_doc 是否加载注释文本, 如果是
     * @param array $select, 只取选中的几个
     * @return array
     */
    static function get($inst, $record_doc=false, $select=null){
        Logger::debug('get meta info');
        try{
            $reflection = new \ReflectionClass($inst);//AnnotationTest
            $reader= new AnnotationReader($reflection);
        }catch(\ReflectionException $e){
            p("1.请注意检查类名与文件名是否一致
2.请注意使用命名空间namespace
3.请将视图控制文件放在名如*ctrl*的文件夹中
4.此错误为\ReflectionException错误
5.解决办法需要更改\\verb\\route\\InitRoute::initRouteTree()");
            throw new ClassNotFoundException($e->getMessage().",please check that the classname same as filename", $inst);
        }

        $info = array();
        if($record_doc){
            if(false !== ($doc = $reflection->getDocComment())){
                $info['doc'] = $doc;
            }
        }

        if($select !== null){
            $select = array_flip($select);//反转数组中所有的键以及它们关联的值
        }
        Logger::debug('获取类注解');
        foreach ($reader->getClassAnnotations($reflection, $record_doc) as $id =>$ann ){

            if($select !==null && !array_key_exists($id, $select)){
                continue;
            }
            $ann=$ann[0];//可能有多个重名的, 只取第一个
            $info[$id] = $ann;
        }

        Logger::debug('获取方法注解');
        foreach ($reflection->getMethods() as $method ){
            foreach ( $reader->getMethodAnnotations($method, $record_doc) as $id => $ann){
                if($select !==null && !array_key_exists($id, $select)){
                    continue;
                }
                $ann=$ann[0];//可能有多个重名的, 只取第一个
                $info += array($id=>array());
                $info[$id][$method->getName()] = $ann;
            }
        }
        Logger::debug('获取属性注解');
        foreach ($reflection->getProperties() as $property ){
            foreach ( $reader->getPropertyAnnotations($property, $record_doc) as $id => $ann){
                if($select !==null && !array_key_exists($id, $select)){
                    continue;
                }
                $ann = $ann[0];//可能有多个重名的, 只取第一个
                $info += array($id=>array());
                $info[$id][$property->getName()] = $ann;
            }
        }
        return $info;//全部的注解
    }
    
    static function testAnnotation(){//判断是否可以正常获取注解
        isTrue(count(self::get(new AnnotationTest(),true)), 'Annotation dose not work! If opcache is enable, please set opcache.save_comments=1 and opcache.load_comments=1');
    }
    /**
     * 有效的元信息
     * @var unknown
     */
    private static $valid=array();
    
}
