<?php

namespace App\Core\Utils;

class FormRegistry
{

    public static function getInstallerRegistration()
    {
        return [
            'website_title' => [
                'type' => Validator::TYPE_TEXT,
                'required' => true,
                'max' => 25,
                'error_message' => 'Le titre du site ne doit pas faire plus de 25 caractères',
            ],
            'username' => [
                'type' => Validator::TYPE_TEXT,
                'required' => true,
                'max' => 20,
                'error_message' => 'Le nom d\'utilisateur ne doit pas faire plus de 20 caractères',
            ],
            'password' => [
                'type' => Validator::TYPE_PASSWORD,
                'required' => true,
                'error_message' => Validator::ERROR_PASSWORD_DEFAULT,
            ],
            'password_confirm' => [
                'type' => Validator::TYPE_TEXT,
                'required' => true,
                'clone_of' => 'password',
                'error_message' => 'Les mots de passe ne correspondent pas',
            ],
            'email' => [
                'type' => Validator::TYPE_EMAIL,
                'required' => true,
                'error_message' => Validator::ERROR_EMAIL_DEFAULT,
            ],
        ];
    }

    public static function getPasswordRecovery()
    {
        return [
            'email' => [
                'type' => Validator::TYPE_EMAIL,
                'required' => true,
                'error_message' => Validator::ERROR_EMAIL_DEFAULT,
            ],
        ];
    }

    public static function getPasswordReset()
    {
        return [
            'password' => [
                'type' => Validator::TYPE_PASSWORD,
                'required' => true,
                'error_message' => Validator::ERROR_PASSWORD_DEFAULT,
            ],
            'password_confirm' => [
                'type' => Validator::TYPE_TEXT,
                'required' => true,
                'clone_of' => 'password',
                'error_message' => 'Les mots de passe ne correspondent pas',
            ],
        ];
    }

    public static function getUserDetail()
    {
        return [
            'username' => [
                'type' => Validator::TYPE_TEXT,
                'required' => true,
                'max' => 20,
                'error_message' => 'Le nom d\'utilisateur ne doit pas faire plus de 20 caractères',
            ],
            'email' => [
                'type' => Validator::TYPE_EMAIL,
                'required' => true,
                'error_message' => Validator::ERROR_EMAIL_DEFAULT,
            ],
            'password' => [
                'type' => Validator::TYPE_PASSWORD,
                'required' => true,
                'error_message' => Validator::ERROR_PASSWORD_DEFAULT,
                'optional' => true,
            ],
            'role' => [
                'type' => Validator::TYPE_NUMBER,
                'required' => true,
            ],
        ];
    }

    public static function getUserNew()
    {
        return [
            'username' => [
                'type' => Validator::TYPE_TEXT,
                'required' => true,
                'max' => 20,
                'error_message' => 'Le nom d\'utilisateur ne doit pas faire plus de 20 caractères',
            ],
            'password' => [
                'type' => Validator::TYPE_PASSWORD,
                'required' => true,
                'error_message' => Validator::ERROR_PASSWORD_DEFAULT,
            ],
            'email' => [
                'type' => Validator::TYPE_EMAIL,
                'required' => true,
                'error_message' => Validator::ERROR_EMAIL_DEFAULT,
            ],
            'role' => [
                'type' => Validator::TYPE_NUMBER,
                'required' => true,
            ],
        ];
    }


}
