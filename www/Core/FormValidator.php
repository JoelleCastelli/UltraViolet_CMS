<?php

namespace App\Core;

class FormValidator
{

    public static function check($config, $data): array
    {

        self::validateCSRFToken($config['config']['referer'], $data['csrfToken']);
        $errors = [];

        // Remove field from config if it's a file upload
        foreach ($config["fields"] as $name => $configList) {
            foreach ($configList as $key => $value) {
                if($key === 'type' && $value == 'file') {
                    unset($config["fields"][$name]);
                }
            }
        }

        if(count($data) != count($config["fields"])) {
            $errors[] = "Tentative de HACK - Faille XSS";
        } else {
            foreach ($config["fields"] as $fieldName => $fieldConfig) {

                $fieldName = str_replace("[]", "", $fieldName);

                if(!isset($data[$fieldName])) {
                    $errors[] = "Tentative de hack : le champ ".$fieldConfig['label']." est requis !";
                    return $errors;
                }

                if(is_string($data[$fieldName]))
                    $data[$fieldName] = trim($data[$fieldName]);

                // check if required field is not empty but still allow 0
                if (isset($fieldConfig['required']) && empty($data[$fieldName]) && $data[$fieldName] !== "0" && $data[$fieldName] !== 0) {
                    $errors[] = "Le champ ".$fieldConfig['label']." est obligatoire !";
                    return $errors;
                }

                if(!Helpers::isStrictlyEmpty($data[$fieldName])) {
                    self::textInputValidator($data[$fieldName], $fieldConfig, $errors);
                    self::numberInputValidator($data[$fieldName], $fieldConfig, $errors);
                    self::passwordValidator($fieldName, $data[$fieldName], $fieldConfig, $errors);
                    self::passwordConfirmationValidator($fieldName, $data, $fieldConfig, $errors);
                    self::emailInputValidator($data[$fieldName], $fieldConfig, $errors);
                    self::dateInputValidator($data[$fieldName], $fieldConfig, $errors);
                    self::optionsValidator($data[$fieldName], $fieldConfig);
                    self::colorValidator($data[$fieldName], $fieldConfig, $errors);
                }
            }
        }
        return $errors;
    }

    public static function textInputValidator($textInput, $fieldConfig, &$errors) {
        if($fieldConfig["type"] == "text" || $fieldConfig["type"] == "textarea" ) {
            if (!empty($fieldConfig["minLength"]) && strlen($textInput) < $fieldConfig["minLength"]) {
                $errors[] = $fieldConfig["error"];
            }
            if (!empty($fieldConfig["maxLength"]) && strlen($textInput) > $fieldConfig["maxLength"]) {
                $errors[] = $fieldConfig["error"];
            }
            if (!empty($fieldConfig["regex"]) && !preg_match($fieldConfig["regex"], $textInput)) {
                $errors[] = $fieldConfig["error"];
            }
        }
    }

    public static function numberInputValidator($numberInput, $fieldConfig, &$errors) {
        if($fieldConfig["type"] == "number") {
            if (isset($fieldConfig["min"]) && is_numeric($fieldConfig["min"]) && $numberInput < $fieldConfig["min"] ) {
                $errors[] = $fieldConfig["error"];
            }
            if (isset($fieldConfig["max"]) && is_numeric($fieldConfig["max"]) && $numberInput > $fieldConfig["max"]) {
                $errors[] = $fieldConfig["error"];
            }
        }
    }

    public static function passwordValidator($fieldName, $password, $fieldConfig, &$errors) {
        if ($fieldName == "pwd") {
            if (!preg_match($fieldConfig["regex"], $password)  ) {
                $errors[] = $fieldConfig["error"];
            }
        }
    }

    public static function passwordConfirmationValidator($fieldName, $data, $fieldConfig, &$errors) {
        if ($fieldName == "pwdConfirm") {
            if ($data['pwdConfirm'] !== $data[$fieldConfig["confirm"]]) {
                $errors[] = $fieldConfig["error"];
            }
        }
    }

    public static function emailInputValidator($field, $fieldConfig, &$errors) {
        if ($fieldConfig["type"] == "email") {
            if(!filter_var($field, FILTER_VALIDATE_EMAIL)){
                $errors[] = $fieldConfig["error"];
            }
        }
    }

    public static function dateInputValidator($date, $fieldConfig, &$errors) {
        if($fieldConfig["type"] == "date" || $fieldConfig["type"] == "datetime-local") {

            if($fieldConfig["type"] == "datetime-local")
                $format = "Y-m-d\TH:i";
            else
                $format = "Y-m-d";

            $d = \DateTime::createFromFormat($format, $date);
            
            if ($d && $d->format($format) == $date) {

                if (!empty($fieldConfig["min"]) && (new \DateTime($date) <= new \DateTime($fieldConfig["min"]))) {
                    $errors[] = $fieldConfig["error"];
                }
                if (!empty($fieldConfig["max"]) && (new \DateTime($date) >= new \DateTime($fieldConfig["max"]))) {
                    $errors[] = $fieldConfig["error"];
                }
            }
        }
    }

    public static function optionsValidator($fieldContent, $fieldConfig) {
    
        $correctOptions = [];

        if(in_array($fieldConfig["type"], ['select', 'radio', 'checkbox'])) {

            if(in_array($fieldConfig["type"], ['select', 'radio'])) {
       
                $correctOptions = [false];

                if(isset($fieldConfig['multiple']) && $fieldConfig['multiple'] == true ){
                    foreach ($fieldConfig['options'] as $option) {
                        foreach ($fieldContent as $content) {
                            if ($content == $option['value']) {
                                $correctOptions = [true];
                            }
                        }
                    }
                }else{
                    foreach ($fieldConfig['options'] as $option) {
                        if ($fieldContent == $option['value']) {
                            $correctOptions = [true];
                        }
                    }
                }

            } elseif($fieldConfig["type"] == 'checkbox') {
                foreach ($fieldContent as $key => $value) {
                    $correctOptions[$key] = false;
                    foreach ($fieldConfig['options'] as $option) {
                        if ($value == $option['value']) {
                            $correctOptions[$key] = true;
                        }
                    }
                }
            }

            if (in_array(false, $correctOptions)) {
                $errors[] = "Tentative de hack : l'option n'existe pas !";
                return $errors;
            }
        }
    }

    public static function colorValidator($fieldContent, $fieldConfig, &$errors) {
        if($fieldConfig['type'] == 'color') {
            if(!preg_match('/^#([0-9a-f]{3}){1,2}$/', $fieldContent)) {
                $errors[] = $fieldConfig["error"];
            }
        }
    }

    public static function validateCSRFToken($referer, $formToken) {
        if(isset($_SESSION['csrfTokens']) && isset($formToken)) {
            $key = array_search($formToken, $_SESSION['csrfTokens']);
            if(in_array($formToken, $_SESSION['csrfTokens'])) {
                if($_SERVER['REQUEST_URI'] !== $referer) {
                    trigger_error("CSRF attack: incorrect referer", E_USER_ERROR);
                }
            } else {
                trigger_error("CSRF attack: values are different", E_USER_ERROR);
            }
        } else {
            trigger_error("CSRF attack: one of the values is not present", E_USER_ERROR);
        }
        unset($_SESSION['csrfToken'][$key]);
    }

}