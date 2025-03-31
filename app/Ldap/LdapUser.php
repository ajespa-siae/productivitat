<?php

namespace App\Ldap;

use LdapRecord\Models\ActiveDirectory\User as BaseUser;
use Illuminate\Contracts\Auth\Authenticatable;

class LdapUser extends BaseUser implements Authenticatable
{
    public static array $objectClasses = ['top', 'person', 'organizationalPerson', 'user'];

    public function getAuthIdentifierName(): string
    {
        return 'samaccountname';
    }

    public function getAuthIdentifier(): string
    {
        return $this->getFirstAttribute('samaccountname') ?? '';
    }

    public function getAuthPassword(): string
    {
        return $this->getFirstAttribute('password') ?? '';
    }

    public function getRememberToken(): string
    {
        return '';
    }

    public function setRememberToken($value): void
    {
    }

    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }

    public function getName(?string $dn = null): ?string
    {
        return $this->getFirstAttribute('displayname') ?? $this->getFirstAttribute('cn');
    }

    public function getEmail(): ?string
    {
        return $this->getFirstAttribute('mail');
    }
}
