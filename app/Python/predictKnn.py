import sys
import os
import joblib

# Ambil argumen dari command line
avg_alb = float(sys.argv[1])
avg_air = float(sys.argv[2])
avg_kotoran = float(sys.argv[3])

# Path ke model dan scaler
base_dir = os.path.dirname(__file__)
model_path = os.path.join(base_dir, 'knn_model.pkl')
scaler_path = os.path.join(base_dir, 'scaler.pkl')

# Load model dan scaler
model = joblib.load(model_path)
scaler = joblib.load(scaler_path)

# Transform fitur dengan scaler
features = [[avg_alb, avg_air, avg_kotoran]]
scaled_features = scaler.transform(features)

# Prediksi
prediction = model.predict(scaled_features)

# Print hasil prediksi
print(prediction[0])
