<?php
namespace verb\util;
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
        p('MetaInfo=>get:');
        $reflection = new \ReflectionClass($inst);//AnnotationTest

        p($reflection);
        $reader= new AnnotationReader($reflection);
        p('reader:');
        p($reader);

        $info = array();

        p('docComment:');
        p($reflection->getDocComment());

        if($record_doc){
            if(false !== ($doc = $reflection->getDocComment())){
                $info['doc'] = $doc;
            }
        }

        if($select !== null){
            $select = array_flip($select);//反转数组中所有的键以及它们关联的值
        }

        p("获取类注解:");
        foreach ($reader->getClassAnnotations($reflection, $record_doc) as $id =>$ann ){

            if($select !==null && !array_key_exists($id, $select)){
                continue;
            }
            $ann=$ann[0];//可能有多个重名的, 只取第一个
            $info[$id] = $ann;
            p('info:');
            p($info);
            
        }

        p('获取方法注解:');
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
        p('获取属性注解:');
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
        p('获取的注解');
        p($info);
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
