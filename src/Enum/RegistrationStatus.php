<?php

namespace App\Enum;

enum RegistrationStatus: string
{
    case Confirmed = 'confirmed';
    case Waitlist = 'waitlist';
    case Cancelled = 'cancelled';
}
