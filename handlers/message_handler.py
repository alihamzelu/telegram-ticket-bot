from database.ticket import create_ticket
from database.user import get_or_create_user
from utils.state import user_state

def register_message_handler(bot):

    @bot.message_handler(func=lambda m: True)
    def handle_message(message):

        user_id = message.from_user.id

        if user_state.get(user_id) == "CREATE_TICKET":

            db_user = get_or_create_user(
                telegram_id=user_id,
                name=message.from_user.first_name,
                username=message.from_user.username
            )

            create_ticket(db_user["id"], message.text)

            bot.send_message(
                message.chat.id,
                """✅ Ticket created successfully!

👀 Our team is looking at your ticket right now.
⏱ We'll get back to you as soon as possible.
📌 You can check your ticket status anytime from "My Tickets".

Thank you for your patience! 🙏"""
            )

            user_state.pop(user_id, None)