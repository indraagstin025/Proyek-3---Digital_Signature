<?php

namespace App\Enums;

enum DocumentStatus : string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case ARCHIVED =  'archived';
}

?>