from .start_handler import register_start_handler
from .help_handler import register_help_handler
from .message_handler import register_message_handler

def register_all_handlers(bot):
    register_start_handler(bot)
    register_help_handler(bot)
    register_message_handler(bot)