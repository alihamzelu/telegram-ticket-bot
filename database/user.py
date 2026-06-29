from database.db import get_db

def get_or_create_user(telegram_id, name, username):
    db = get_db()
    cursor = db.cursor(dictionary=True)

    cursor.execute("SELECT * FROM users WHERE telegram_id=%s", (telegram_id,))
    user = cursor.fetchone()

    if user:
        db.close()
        return user

    cursor.execute("""
        INSERT INTO users (telegram_id, name, username)
        VALUES (%s, %s, %s)
    """, (telegram_id, name, username))

    db.commit()

    cursor.execute("SELECT * FROM users WHERE telegram_id=%s", (telegram_id,))
    user = cursor.fetchone()

    db.close()
    return user