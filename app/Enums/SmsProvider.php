<?php

namespace App\Enums;

enum SmsProvider: string
{
    case ESKIZ = 'eskiz';
    case PLAYMOBILE = 'playmobile';
    case TWILIO = 'twilio';
    case FAKE = 'fake';
}
