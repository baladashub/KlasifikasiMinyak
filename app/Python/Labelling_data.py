import pymysql
from sqlalchemy import create_engine, text  # ⬅ Tambahkan `text`
import pandas as pd
from dotenv import load_dotenv
import os
import sys
import traceback

# Load .env dari project Laravel
env_path = os.path.join(os.path.dirname(__file__), '../../.env')
print(f"Loading .env from: {os.path.abspath(env_path)}")
load_dotenv(dotenv_path=env_path)

# Ambil konfigurasi dari .env
db_user = os.getenv('DB_USERNAME')
db_pass = os.getenv('DB_PASSWORD')
db_host = os.getenv('DB_HOST')
db_name = os.getenv('DB_DATABASE')

print(f"Database config - Host: {db_host}, Database: {db_name}, User: {db_user}")

try:
    # Tes koneksi langsung dengan pymysql
    print("Testing direct MySQL connection...")
    try:
        conn = pymysql.connect(
            host=db_host,
            user=db_user,
            password=db_pass,
            database=db_name,
            port=3306,
            connect_timeout=5
        )
        print(" Direct MySQL connection successful!")
        
        with conn.cursor() as cursor:
            cursor.execute("SHOW TABLES")
            tables = cursor.fetchall()
            print(f"Tables in database: {[table[0] for table in tables]}")
            
            cursor.execute("SELECT COUNT(*) FROM daily_entries")
            count = cursor.fetchone()[0]
            print(f"Records in daily_entries: {count}")
        
        conn.close()
    except pymysql.Error as e:
        print(f" MySQL Error: {e}", file=sys.stderr)
        print(f"Error Code: {e.args[0]}", file=sys.stderr)
        print(f"Error Message: {e.args[1]}", file=sys.stderr)
        raise

    # SQLAlchemy connection
    connection_string = f'mysql+pymysql://{db_user}:{db_pass}@{db_host}/{db_name}'
    print(f"Attempting to connect with SQLAlchemy...")
    engine = create_engine(connection_string)
    
    with engine.connect() as connection:
        print(" SQLAlchemy connection successful!")

        #  Gunakan text() di semua execute() SQLAlchemy
        result = connection.execute(text("SELECT COUNT(*) as total FROM daily_entries"))
        count = result.scalar()
        print(f" Found {count} records in daily_entries table")

        # Ambil data yang belum diberi label
        df = pd.read_sql(text("SELECT id, avg_alb, avg_air, avg_kotoran FROM daily_entries WHERE label IS NULL"), connection)
        print(f" Found {len(df)} records without labels")

        if len(df) > 0:
            def tentukan_grade(row):
                if row['avg_alb'] <= 3.5 and row['avg_air'] <= 0.2 and row['avg_kotoran'] <= 0.2:
                    return "Grade 1"
                else:
                    return "Grade 2"

            df['label'] = df.apply(tentukan_grade, axis=1)

            output_path = os.path.join(os.path.dirname(__file__), "label_output.json")
            df[['id', 'label']].to_json(output_path, orient='records')
            print(f" Labels calculated and saved to {output_path}")
        else:
            print(" No records need labeling")

except Exception as e:
    print(f" Error: {str(e)}", file=sys.stderr)
    print("Stack trace:", file=sys.stderr)
    traceback.print_exc(file=sys.stderr)
    sys.exit(1)
