from database.db import get_db

def create_ticket(user_id, message):
    db = get_db()
    cursor = db.cursor(dictionary=True)

    cursor.execute("""
        INSERT INTO tickets (user_id, message, status)
        VALUES (%s, %s, 'open')
    """, (user_id, message))

    db.commit()
    cursor.close()
    db.close()


def get_user_tickets(user_id):
    db = get_db()
    cursor = db.cursor(dictionary=True)

    cursor.execute("""
        SELECT * FROM tickets
        WHERE user_id=%s
        ORDER BY id DESC
    """, (user_id,))

    tickets = cursor.fetchall()
    db.close()

    return tickets