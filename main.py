import telebot
from handlers import register_all_handlers
from handlers.callback_handler import register_callback_handler

bot = telebot.TeleBot("8991681537:AAH-Cz9qXCosWOAmmbFtMyp6cXwhiow5AaA")

register_all_handlers(bot)
register_callback_handler(bot)

bot.polling(none_stop=True)