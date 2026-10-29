<?php

namespace App\Enums;

enum ProviderStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Suspended = 'suspended';
}
