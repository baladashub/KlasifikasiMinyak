import os
from dotenv import load_dotenv
from sqlalchemy import create_engine

# Load file .env
load_dotenv(dotenv_path='../../.env')  # Sesuaikan path jika perlu

# Ambil data dari .env
db_user = os.getenv('DB_USERNAME')
db_pass = os.getenv('DB_PASSWORD')
db_host = os.getenv('DB_HOST')
db_name = os.getenv('DB_DATABASE')
db_port = os.getenv('DB_PORT', 3306)

# Buat connection string
connection_string = f"mysql+pymysql://{db_user}:{db_pass}@{db_host}:{db_port}/{db_name}"

try:
    engine = create_engine(connection_string)
    conn = engine.connect()
    print("✅ Koneksi ke database berhasil!")
    conn.close()
except Exception as e:
    print("❌ Gagal koneksi ke database:")
    print(e)
