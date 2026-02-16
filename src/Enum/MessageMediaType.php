<?php

namespace Bot\Enum;

enum MessageMediaType: string
{
    case PHOTO = 'photo';
    case VIDEO = 'video';
    case DOCUMENT = 'document';
    case VOICE = 'voice';
    case VIDEO_NOTE = 'video_note';
    case LOCATION = 'location';
    case CONTACT = 'contact';
}
