from telebot.types import InlineKeyboardMarkup, InlineKeyboardButton

def main_keyboard():
    
    markup = InlineKeyboardMarkup(row_width=1)

    markup.add(
        InlineKeyboardButton("📝 Create Ticket", callback_data="create_ticket"),
        InlineKeyboardButton("📂 My Tickets", callback_data="my_tickets"),
        InlineKeyboardButton("ℹ️ Help", callback_data="help")
    )

    return markup