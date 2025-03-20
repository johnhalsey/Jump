<?php

namespace App\Enums;

enum DefaultProjectStatus: string
{
    case TO_DO = 'To Do';
    case IN_PROGRESS = 'In Progress';
    case DONE = 'Done';
}
