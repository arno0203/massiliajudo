<?php


class MassiliaJudo_Form_Builder
{
    /**
     * @param null $object
     * @param string $name
     * @param null $idSelected
     * @param null $idElement
     * @param string $label
     * @param string $class
     * @param bool $isRequired
     * @param string $error
     * @return string
     */
    public static function buildSelect($object = null, $name='', $idSelected = null, $idElement= null, $label = '', $class = '', $isRequired = true, $error = '')
    {
        if(is_null($class)){
            return '';
        }
        $list = call_user_func(array($object, 'getList'));

        if(is_null($idElement)){
            $idElement = uniqid('massiliajudo_');
        }

        if($label != ''){
            $requiredLabel = '';
            if($isRequired) {
                $requiredLabel = ' <span style="color:red">*</span>';
            }
            $label = '<label for="$idElement">'.$label.$requiredLabel.'</label>';
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
        return '<div class="form-group">'.$label.sprintf('<select name="%s" class="%s" %s id="%s">%s</select>',$name, $class, $required, $idElement, $option).'</div>';
    }

    /**
     * @param string $name
     * @param string $value
     * @param null $idElement
     * @param string $label
     * @param string $placeholder
     * @param string $class
     * @param bool $isRequired
     * @param string $error
     * @return string
     */
    public static function buildText($name='', $value = '', $idElement = null, $label = '', $placeholder = '', $class = '', $isRequired = true, $error = '' ){
        if(is_null($idElement)){
            $idElement = uniqid('massiliajudo_');
        }

        $labelError = $inputErro = $textError =  '';
        if(!empty($error)){
            $labelError = ' style="color:red" ';
            $inputError = ' style="border-color: red !important" ';
            $textError = '<span style="color:red;font-style: italic;font-size: 0.9em">'.$error.'</span>';
        }

        if($label != ''){
            if($isRequired) {
                $requiredLabel = ' <span style="color:red">*</span>';
            }
            $label = '<label for="$idElement" '.$labelError.'>'.$label.$requiredLabel.'</label>'.$textError;
        }



        $text = sprintf('<input type="text" id="%s" name="%s" value="%s" class="%s" placeholder="%s" %s/>', $idElement, $name, $value, $class, $placeholder, $inputError);
        return '<div class="form-group">'.$label.$text.'</div>';

    }

    /**
     * @param string $name
     * @param string $value
     * @param null $idElement
     * @param string $label
     * @param string $class
     * @param bool $isRequired
     * @param string $error
     * @return string
     */
    public static function buildDate($name = '', $value = '', $idElement = null, $label = '', $class ='', $isRequired = true, $error = ''){
        return self::buildText($name, $value, $idElement, $label, '25/12/2000', $class, $isRequired, $error);
    }

    /**
     * @param string $name
     * @param string $value
     * @param null $idElement
     * @param string $label
     * @param string $class
     * @param bool $isRequired
     * @param string $error
     * @return string
     */
    public static function buildPhoneNumber($name = '', $value = '', $idElement = null, $label = '', $class ='', $isRequired = true, $error = ''){
        return self::buildText($name, $value, $idElement, $label, '+33 6 02 03 04 05', $class, $isRequired, $error);
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