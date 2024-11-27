<?php

namespace App\DTO;

use App\Entity\EntityInterface;
use App\Entity\User;
use App\Serializer\AccessGroup;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserDTO implements DTOInterface
{
    #[Groups([
        AccessGroup::USER_READ,
        AccessGroup::ARTICLE_CREATE,
        AccessGroup::ARTICLE_READ
    ])]
    public ?int $id;

    #[Groups([
        AccessGroup::USER_READ,
        AccessGroup::USER_SIGN,
        AccessGroup::ARTICLE_READ
    ])]
    #[Property(example: 'Jane')]
    public string $firstName;

    #[Groups([
        AccessGroup::USER_READ,
        AccessGroup::USER_SIGN,
        AccessGroup::ARTICLE_READ
    ])]
    #[Property(example: 'Doe')]
    public string $lastName;

    #[Groups([
        AccessGroup::USER_READ,
        AccessGroup::USER_SIGN,
    ])]
    #[Property(example: 'user@example.com')]
    #[NotBlank(groups: [AccessGroup::USER_SIGN])]
    #[Email(groups: [AccessGroup::USER_SIGN])]
    public string $email;

    #[Groups([
        AccessGroup::USER_READ,
    ])]
    #[Property(
        type: 'array',
        items: new Items(type: 'string', example: 'ROLE_USER')
    )]
    public array $roles;

    #[Groups([
        AccessGroup::USER_SIGN,
    ])]
    #[Property(example: '12345_Aa')]
    #[NotBlank(groups: [AccessGroup::USER_SIGN])]
    public string $password;

    #[Ignore]
    public function getEntityObject(): EntityInterface
    {
        return new User();
    }
}
