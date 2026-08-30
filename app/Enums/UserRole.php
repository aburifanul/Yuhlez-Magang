<?php

namespace App\Enums;

enum UserRole: string
{
    case ROOT = 'root';
    case COMPANY = 'company';
    case INTERN = 'intern';
}