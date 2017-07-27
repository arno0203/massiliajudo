<?php


class MassiliaJudo_Form_Builder
{
    /**
     * @param $object
     * @param null $idSelected
     * @param null $idElement
     * @param string $label
     * @param string $class
     * @param bool $isRequired
     * @return string
     */
    public static function buildSelect($object = null, $name='', $idSelected = null, $idElement= null, $label = '', $class = '', $isRequired = true)
    {
        if(is_null($class)){
            return '';
        }
        $list = call_user_func(array($object, 'getList'));

        if(is_null($idElement)){
            $idElement = uniqid('massiliajudo_');
        }

        if($label != ''){
            $label = '<label for="$idElement">'.$label.'</label>: ';
        }

        $option = '';
        $required =  ($isRequired == true)? 'required' : '';
        if($isRequired){
            $option .='<option value="" disabled>Selectionnez un élément</option>'."\n";
        }
        foreach ($list as $obj){
            $selected = ($obj->id == $idSelected)?' selected ': '';
            $option .='<option value="'.$obj->id.'"'.$selected.'>'.$obj->name.'</option>'."\n";
        }
        return $label.sprintf('<select name="%s" class="%s" %s id="%s">%s</select>',$name, $class, $required, $idElement, $option);
    }

    /**
     * @param string $value
     * @param null $idElement
     * @param string $label
     * @param string $placeholder
     * @param string $class
     * @return string
     */
    public static function buildText($name='', $value = '', $idElement = null, $label = '', $placeholder = '', $class = '' ){
        if(is_null($idElement)){
            $idElement = uniqid('massiliajudo_');
        }

        if($label != ''){
            $label = '<label for="$idElement">'.$label.'</label>: ';
        }

        $text = sprintf('<input type="text" id="%s" name="%s" value="%s" class="%s" placeholder="%s"/>', $idElement, $name, $value, $class, $placeholder);
        return $label.$text;

    }

    /**
     * @param string $value
     * @param null $idElement
     * @param string $label
     * @param string $class
     * @return string
     */
    public static function buildDate($value = '', $idElement = null, $label = '', $class =''){
        return self::buildText($value, $idElement, $label, '25/12/2000', $class);
    }

    /**
     * @param string $name
     * @param null $idElement
     * @param string $value
     * @param string $class
     * @return string
     */
    public static function buildSubmit($name = '', $idElement = null, $value = '', $class =''){
        return sprintf('<input type="submit" name="%s" id="%s" value="%s" class="%s" />', $name, $idElement, $value, $class);
    }

    public static function buildHidden($name = '', $idElement = null, $value = '', $class = ''){
        if(is_null($idElement)){
            $idElement = uniqid('massiliajudo_');
        }
        return sprintf('<input type="hidden" name="%s" id="%s" value="%s" class="%s">', $name, $idElement, $value, $class);
    }

}