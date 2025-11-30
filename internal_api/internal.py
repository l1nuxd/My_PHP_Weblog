from flask import Flask, jsonify
import pymysql

app = Flask(__name__)

# Database configuration
db_config = {
    'host': 'localhost',
    'user': 'dbuser',
    'password': 'dbpassword',
    'database': 'Webapp'
}

@app.route('/api/user/<int:user_id>', methods=['GET'])
def get_user(user_id):
    try:
        conn = pymysql.connect(**db_config)
        cursor = conn.cursor(pymysql.cursors.DictCursor)
        cursor.execute("SELECT id, name, username, email, created_at, updated_at, reset_token, profile_picture, bio FROM users WHERE id = %s", (user_id,))
        user = cursor.fetchone()
        cursor.close()
        conn.close()
        if user:
            return jsonify(user)
        else:
            return jsonify({'error': 'User not found'}), 404
    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    app.run(host="127.0.0.1", port=5000, debug=False)