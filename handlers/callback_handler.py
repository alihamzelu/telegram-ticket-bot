from utils.state import user_state
from database.user import get_or_create_user
from database.ticket import create_ticket, get_user_tickets

def register_callback_handler(bot):

    @bot.callback_query_handler(func=lambda call: True)
    def callback_handler(call):
        user_id = call.from_user.id
        data = call.data

        bot.answer_callback_query(call.id)

        db_user = get_or_create_user(
            telegram_id=user_id,
            name=call.from_user.first_name,
            username=call.from_user.username
        )

        if data == "create_ticket":
            user_state[user_id] = "CREATE_TICKET"

            bot.send_message(
                call.message.chat.id,
                "📝 Please describe your issue in detail."
            )

        # 📂 لیست تیکت‌ها
        elif data == "my_tickets":

            tickets = get_user_tickets(db_user["id"])

            if not tickets:
                bot.send_message(
                    call.message.chat.id,
                    "📂 You have no tickets yet."
                )
                return

            text = "📂 Your Tickets:\n\n"

            for t in tickets:
                text += f"#{t['id']} - {t['status']}\n"

            bot.send_message(
                call.message.chat.id,
                text
            )

        # ℹ️ help
        elif data == "help":
            bot.send_message(
                call.message.chat.id,
                "ℹ️ Use /help to see full instructions."
            )

        else:
            bot.send_message(
                call.message.chat.id,
                "❌ Unknown action."
            )