<?php

namespace App\Serializer;

class AccessGroup
{
    //User
    public const string USER_READ = 'user:read';
    public const string USER_SIGN = 'user:sign';

    //ARTICLE
    public const string ARTICLE_READ = 'article:read';
    public const string ARTICLE_CREATE = 'article:create';
    public const string ARTICLE_DELETE = 'article:delete';

    //Photo
    public const string PHOTO_READ = 'photo:read';
}
