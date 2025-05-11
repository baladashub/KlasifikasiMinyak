import numpy as np
import pandas as pd
from sklearn.model_selection import train_test_split, cross_val_score
from sklearn.neighbors import KNeighborsClassifier
from sklearn.metrics import accuracy_score, confusion_matrix
from sklearn.preprocessing import StandardScaler
import matplotlib.pyplot as plt
from sqlalchemy import create_engine
from dotenv import load_dotenv
import os
import json
import joblib
import logging

# Setup logging ke Laravel log file
laravel_log = os.path.join(os.path.dirname(__file__), '../../../klasifikasiMinyak/storage/logs/laravel.log')
logging.basicConfig(filename=laravel_log, level=logging.INFO, format='%(asctime)s - %(message)s')

# Load .env
env_path = os.path.join(os.path.dirname(__file__), '../../.env')
load_dotenv(dotenv_path=env_path)

db_user = os.getenv('DB_USERNAME')
db_pass = os.getenv('DB_PASSWORD')
db_host = os.getenv('DB_HOST')
db_name = os.getenv('DB_DATABASE')

# Koneksi DB
connection_string = f'mysql+pymysql://{db_user}:{db_pass}@{db_host}/{db_name}'
engine = create_engine(connection_string)
connection = engine.connect()

# Ambil data
df = pd.read_sql("SELECT avg_alb, avg_air, avg_kotoran, label FROM daily_entries WHERE label IS NOT NULL", connection)

# Cek distribusi label
label_counts = df['label'].value_counts().to_dict()
logging.info(f'Distribusi label: {label_counts}')

# Fitur dan target
X = df[['avg_alb', 'avg_air', 'avg_kotoran']]
y = df['label']

# Scaling
scaler = StandardScaler()
X_scaled = scaler.fit_transform(X)

# Split train/test
X_train, X_test, y_train, y_test = train_test_split(X_scaled, y, test_size=0.3, random_state=42)

# Cari nilai k terbaik via cross-validation
neighbors_range = range(1, 21)
cv_scores = []

for k in neighbors_range:
    knn = KNeighborsClassifier(n_neighbors=k)
    score = cross_val_score(knn, X_train, y_train, cv=5)
    cv_scores.append(np.mean(score))

best_k = neighbors_range[np.argmax(cv_scores)]
best_cv_accuracy = max(cv_scores)

# Logging evaluasi cross-validation
logging.info(f'Cross-validation mean accuracy per k: {dict(zip(neighbors_range, cv_scores))}')
logging.info(f'Best k: {best_k} | Best CV Accuracy: {best_cv_accuracy:.4f}')

# Train model terbaik
best_knn = KNeighborsClassifier(n_neighbors=best_k)
best_knn.fit(X_train, y_train)
y_pred = best_knn.predict(X_test)

# Hitung akurasi dan confusion matrix
test_accuracy = accuracy_score(y_test, y_pred)
cm = confusion_matrix(y_test, y_pred)

# Logging hasil evaluasi akhir
logging.info(f'Test Accuracy: {test_accuracy:.4f}')
logging.info(f'Confusion Matrix:\n{cm}')

# Simpan model dan scaler
model_path = os.path.join(os.path.dirname(__file__), 'knn_model.pkl')
scaler_path = os.path.join(os.path.dirname(__file__), 'scaler.pkl')
joblib.dump(best_knn, model_path)
joblib.dump(scaler, scaler_path)

# Simpan hasil ke JSON
result = {
    'accuracy': test_accuracy,
    'best_k': int(best_k),
    'best_cv_accuracy': float(best_cv_accuracy),
    'confusion_matrix': cm.tolist(),
    'label_distribution': label_counts
}

result_path = os.path.join(os.path.dirname(__file__), 'training_result.json')
with open(result_path, 'w') as f:
    json.dump(result, f)
