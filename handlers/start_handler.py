from keyboards.inline_keyboard import main_keyboard

def register_start_handler(bot):

    @bot.message_handler(commands=['start'])
    def start(message):

        bot.send_message(
            message.chat.id,
            f"""👋 Welcome, {message.from_user.first_name}!

🤖 I’m your assistant bot.

⚡ I can help you quickly handle your requests with simple commands and buttons.

📌 Use the menu below to get started.
""",
            reply_markup=main_keyboard()
        )