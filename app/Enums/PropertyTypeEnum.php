<?php

namespace App\Enums;

enum PropertyTypeEnum : string {
    case SINGLE = 'Single-family house';
    case TOWNHOUSE = 'Townhouse';
    case MULTIFAMILY = 'Multi-family house';
    case BUNGALOW = 'Bungalow';
}