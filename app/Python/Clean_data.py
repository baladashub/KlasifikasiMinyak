import pandas as pd
import sys
from datetime import datetime

# Ambil argumen dari terminal
file_path = sys.argv[1]
output_path = sys.argv[2]

# Load Excel
xls = pd.ExcelFile(file_path)
sheet_names = xls.sheet_names

hari_mapping = {
    "Monday": "Senin", "Tuesday": "Selasa", "Wednesday": "Rabu", "Thursday": "Kamis",
    "Friday": "Jumat", "Saturday": "Sabtu", "Sunday": "Minggu"
}

def proses_blok(df, tanggal_row):
    try:
        tanggal_str = str(df.iloc[tanggal_row, 0]).replace("Tanggal :", "").strip()
        tanggal_asli = datetime.strptime(tanggal_str, "%d - %m - %Y").date()
        hari_asli = str(df.iloc[tanggal_row - 1, 1]).strip()

        jam_row = df.iloc[tanggal_row + 2, 3:]
        alb_row = df.iloc[tanggal_row + 3, 3:]
        air_row = df.iloc[tanggal_row + 4, 3:]
        kot_row = df.iloc[tanggal_row + 5, 3:]

        min_len = min(len(jam_row), len(alb_row), len(air_row), len(kot_row))
        jam_row = jam_row.iloc[:min_len].astype(str).str.extract("(\\d+)")[0].fillna("00").str.zfill(2)

        data = pd.DataFrame({
            "Tanggal": [tanggal_asli] * min_len,
            "Hari": [hari_asli] * min_len,
            "Jam": jam_row.tolist(),
            "ALB": alb_row.iloc[:min_len].values,
            "Air": air_row.iloc[:min_len].values,
            "Kotoran": kot_row.iloc[:min_len].values
        })

        for col in ["ALB", "Air", "Kotoran"]:
            data[col] = data[col].astype(str).str.replace(",", ".").astype(float)

        data["Tanggal"] = pd.to_datetime(data["Tanggal"])
        data["Jam_int"] = data["Jam"].astype(int)
        data.loc[data["Jam_int"].between(2, 6), "Tanggal"] += pd.Timedelta(days=1)
        data["Hari"] = data["Tanggal"].dt.strftime("%A").map(hari_mapping)
        return data.drop(columns="Jam_int")
    except:
        return pd.DataFrame()

semua_data = []
for sheet in sheet_names:
    df_sheet = pd.read_excel(file_path, sheet_name=sheet, header=None)
    tanggal_rows = df_sheet[df_sheet.iloc[:, 0].astype(str).str.contains("Tanggal", na=False)].index.tolist()

    for row in tanggal_rows:
        blok = proses_blok(df_sheet, row)
        if blok.empty: continue
        blok = blok[blok["Jam"] != "00"]
        blok = blok.sort_values("Tanggal").reset_index(drop=True)

        blok_baru = []
        for i in range(0, len(blok), 12):
            sub = blok.iloc[i:i+12]
            blok_baru.append(sub)

            if len(sub) == 12:
                rata2 = {
                    "Tanggal": sub["Tanggal"].iloc[-1],
                    "Hari": f"{sub['Hari'].iloc[0]}-{sub['Hari'].iloc[-1]}",
                    "Jam": "rata rata",
                    "ALB": sub["ALB"].mean(),
                    "Air": sub["Air"].mean(),
                    "Kotoran": sub["Kotoran"].mean()
                }
                blok_baru.append(pd.DataFrame([rata2]))

        semua_data.append(pd.concat(blok_baru, ignore_index=True))

# Gabungkan dan simpan
data_final = pd.concat(semua_data, ignore_index=True)
data_final.to_excel(output_path, index=False)
print(f"✅ Selesai diproses: {output_path}")
