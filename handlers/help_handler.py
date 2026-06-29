def register_help_handler(bot):

    @bot.message_handler(commands=['help'])
    def help(message):

        HELP_TEXT = """🛠 Support Bot Help

✨ How to use:
1️⃣ Click "Create Ticket" to submit your issue
2️⃣ Describe your problem clearly
3️⃣ Wait for admin response
4️⃣ Check "My Tickets" to track status

📂 Ticket Status:
• Open → waiting for reply
• Answered → admin replied
• Closed → resolved

💡 Tips:
• Be clear and detailed
• You can attach your problem step by step

🚀 We’re here to help you as fast as possible!
"""

        bot.send_message(
            message.chat.id,
            HELP_TEXT
        )