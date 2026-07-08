import time
from database import db
from bot import bot


def check_admin_messages():

    db = db.get_db()
    cursor = db.cursor(dictionary=True)

    cursor.execute("""
        SELECT *
        FROM messages
        WHERE sender='admin'
        AND sent=0
    """)

    messages = cursor.fetchall()


    for msg in messages:

        cursor.execute("""
        SELECT users.telegram_id
        FROM tickets
        JOIN users ON tickets.user_id = users.telegram_id
        WHERE tickets.id=%s
        """, (msg["ticket_id"],))

        ticket = cursor.fetchone()

        if ticket:
            bot.send_message(
                ticket["telegram_id"],
                msg["message"]
            )

            cursor.execute("""
                UPDATE messages
                SET sent=1
                WHERE id=%s
            """, (msg["id"],))

            db.commit()


    cursor.close()
    db.close()



while True:
    check_admin_messages()
    time.sleep(3)