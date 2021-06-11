<?php

namespace App\Core\Utils;

use App\Models\Post;
use App\Models\User;
use App\Models\ValidationToken;
use App\Repositories\PostRepository;
use App\Repositories\UserRepository;
use App\Repositories\ValidationTokenRepository;

class Repository
{
    public static function user(User $model = null)
    {
        return new UserRepository($model ?? new User());
    }

    public static function validationToken(ValidationToken $model = null)
    {
        return new ValidationTokenRepository($model ?? new ValidationToken());
    }

    public static function post(Post $model = null)
    {
        return new PostRepository($model ?? new Post());
    }
}
