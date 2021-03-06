<?php
/**
 * @author: mix
 * @date: 20.09.14
 */

namespace PhpStruct;

use PhpParser\Token;

/**
 * Class Base
 *
 * @method hasBrackets
 * @method setBrackets
 * @method getComment
 * @method setComment
 * @method hasHeadBlankLine
 * @method setHeadBlankLine
 * @method isAbstract
 * @method setAbstract
 * @method isFinal
 * @method setFinal
 * @method isStatic
 * @method setStatic
 * @method getVisibility
 * @method setVisibility
 * @method isConst
 * @method setConst
 *
 * @package PhpStruct
 */
abstract class Base
{

    /**
     * @var Modifiers
     */
    private $modifiers = null;

    private $initTokenId = null;

    private $label;

    public function hasScope() {
        return false;
    }

    public function __call($name, $args){
        if($this->getModifiers()){
            $obj = $this->modifiers;
        }else{
            if(strpos($name, "set") ===0 ){
                $this->modifiers = new Modifiers();
                $obj = $this->modifiers;
            }else{
                $obj = new Modifiers();
            }
        }
        return call_user_func_array([$obj,$name],$args);
    }

    /**
     * @return Modifiers
     */
    public function getModifiers(){
        return $this->modifiers;
    }

    public function setModifiers(Modifiers $mod){
        $this->modifiers = $mod;
    }

    public function copyModifiers(Base $from){
        $this->modifiers = $from->getModifiers();
    }

    public function setInitToken(Token $token){
        $this->initTokenId = $token->getId();
    }

    public function setInitTokenId($token){
        $this->initTokenId = $token;
    }

    public function getInitToken(){
        return $this->initTokenId;
    }

    public static function __set_state(array $data){

        $fields = [];
        foreach (static::getConstructorFields() as $name) {
            if (array_key_exists($name, $data)) {
                $fields[] = $data[$name];
            } else {
                throw new FailException("unknown field $name in " . get_called_class());
            }

            unset($data[$name]);
        }
        $out = new static(...$fields);
        foreach($data as $key => $value){

            if($value === null){
                continue;
            }

            if($value === false){
                continue;
            }

            if(is_array($value)){
                $methodName = "add" . rtrim(ucfirst($key), "s");
                if(!method_exists($out, $methodName)){
                    $methodName = rtrim($methodName, "e");
                }
                foreach($value as $item){
                    $out->$methodName($item);
                }
            }else{
                $methodName = "set" . ucfirst($key);
                $out->$methodName($value);
            }

        }
        return $out;
    }

    public static function getConstructorFields(){
        return [];
    }

    public function setLabel($label){
        $this->label = $label;
    }

    public function getLabel(){
        return $this->label;
    }

    /**
     * @return Base[]
     */
    abstract public function getChildren();
}