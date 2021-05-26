<?php

namespace App\Core\Utils;

class Validator
{
    private $errors;
    private $form;

    const TYPE_TEXT = 1;
    const TYPE_NUMBER = 2;
    const TYPE_PASSWORD = 3;
    const TYPE_EMAIL = 4;
    const TYPE_DATE = 5;
    const TYPE_TEL = 6;

    const ERROR_EMAIL_DEFAULT = "L'adresse email n'est pas valide";
    const ERROR_REQUIRED = "Ce champs est obligatoire";
    const ERROR_DATE_DEFAULT = "La date n'est pas valide";
    const ERROR_PASSWORD_DEFAULT = "Le mot de passe doit contenir entre 8 et 20 caractères dont au moins 1 minuscule, 1 majuscule, 1 chiffre, et un caractère spécial [?!@#$%^&]";

    public function __construct(array $form = [])
    {
        $this->errors = [];
        $this->form = $form;
    }

    public function validate(array $data)
    {
        foreach ($this->form as $name => $field) {
            if (!isset($data[$name])) {
                throw new \Exception('Le champs ' . $name . ' n\'est pas trouvé');
            }

            if (!empty($field['required']) && !self::isValid($data[$name])) {
                $this->errors[] = ['name' => $name, 'error' => self::ERROR_REQUIRED];
                continue;
            }

            if (isset($field['clone_of']) && isset($data[$field['clone_of']]) && $data[$name] != $data[$field['clone_of']]) {
                $this->errors[] = ['name' => $name, 'error' => $field['error_message']];
                continue;
            }

            if (!$this->validateRequirements($data[$name], $field)) {
                $this->errors[] = ['name' => $name, 'error' => $field['error_message'], 'value' => $data[$name]];
            }
        }

        return !$this->hasErrors();
    }


    public function validateRequiredOnly(array $data)
    {
        foreach ($data as $name => $value) {
            if (!self::isValid($value)) {
                $this->errors[] = ['name' => $name, 'error' => self::ERROR_REQUIRED];
            }
        }
        return !$this->hasErrors();
    }

    public function hasErrors()
    {
        return !empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    private function validateRequirements($value, array $requirements)
    {
        switch ($requirements['type']) {
            case self::TYPE_TEXT:
                if ((isset($requirements['min']) || isset($requirements['max']))) {
                    return self::textLength($value, $requirements['min'] ?? null, $requirements['max'] ?? null);
                }
                break;
            case self::TYPE_NUMBER:
                if ((isset($requirements['min']) || isset($requirements['max']))) {
                    return self::inRange($value, $requirements['min'] ?? null, $requirements['max'] ?? null);
                }
                break;
            case self::TYPE_DATE:
                if (!self::isValidDate($value)) {
                    return false;
                }
                if ((isset($requirements['min']) || isset($requirements['max']))) {
                    return self::dateRange($value, $requirements['min'] ?? null, $requirements['max'] ?? null);
                }
                break;
            case self::TYPE_PASSWORD:
                return self::isValidPassword($value);
            case self::TYPE_EMAIL:
                return self::isValidEmail($value);
            case self::TYPE_TEL:
                return self::isValidPhone($value);
        }

        return true;
    }

    public static function inRange($value, ?int $min, ?int $max)
    {
        if (!is_null($min) && !is_null($max)) return $value >= $min && $value <= $max;
        elseif (!is_null($min) && is_null($max)) return $value >= $min;
        elseif (is_null($min) && !is_null($max)) return $value <= $max;
    }

    public static function textLength(string $text, ?int $min, ?int $max)
    {
        return self::inRange(strlen($text), $min, $max);
    }

    public static function dateRange(string $date, ?int $min, ?int $max)
    {
        return self::inRange(strtotime($date), $min, $max);
    }

    public static function isValidPhone(string $phone)
    {
        return true;
    }

    public static function isValidDate(string $date)
    {
        # Format YYYY-MM-DD
        if (preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $date, $parts)) {
            return checkdate((int)$parts[2], (int)$parts[3], (int)$parts[1]);
        }
        return false;
    }

    public static function isValidEmail(string $email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function isValidPassword(string $password)
    {
        return preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z])(?=.*[?!@#$%^&])(?=\S+$).{8,20}$/", $password);
    }

    public static function isValid($field)
    {
        return !empty($field);
    }

}
