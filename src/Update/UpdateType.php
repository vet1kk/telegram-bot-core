<?php

namespace Bot\Update;

enum UpdateType: string
{
    case MESSAGE = 'message';
    case EDITED_MESSAGE = 'edited_message';
    case CALLBACK_QUERY = 'callback_query';
    case INLINE_QUERY = 'inline_query';
    case CHOSEN_INLINE_RESULT = 'chosen_inline_result';
}
