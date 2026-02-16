<?php

namespace Bot\Enum;

enum ForwardOriginType: string
{
    case USER = 'user';
    case HIDDEN_USER = 'hidden_user';
    case CHAT = 'chat';
    case CHANNEL = 'channel';
}
