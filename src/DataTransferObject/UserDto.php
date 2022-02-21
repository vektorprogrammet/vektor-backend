<?php

namespace App\DataTransferObject;

class UserDto
{
    public string $firstName;
    public string $lastName;
    public string $fullName;
    public string $username;
    public string $email;
    public string $companyEmail;
    public bool $isAdmin;
}