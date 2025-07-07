import glob
import io
import json
import os
import zipfile

import requests
from jinja2 import Template
from correo import main

id = 11

# URL del ZIP con los JSONs
ZIP_URL = "https://crm.hawkins.es/api/autoseo/json/storage"

# Descargar ZIP
def descargar_zip():
    response = requests.get(ZIP_URL, params={'id': '15'})
    response.raise_for_status()
    with zipfile.ZipFile(io.BytesIO(response.content)) as zip_ref:
        zip_ref.extractall("reportes")

# descargar_zip()

# Leer todos los archivos JSON extraídos
def cargar_jsons():
    json_data_list = []
    version_dates = []
    for filename in sorted(os.listdir("reportes")):
        if filename.endswith(".json"):
            with open(os.path.join("reportes", filename), "r", encoding="utf-8") as f:
                try:
                    data = json.load(f)
                    json_data_list.append(data)
                    version_dates.append(data.get('uploaded_at', '-'))
                except Exception as e:
                    print(f"Error al procesar {filename}: {e}")
    return json_data_list, version_dates

json_data_list, version_dates = cargar_jsons()
if not json_data_list:
    print("No se encontraron archivos JSON válidos.")
    exit(1)

seo_data = json_data_list[0]

# --- Construir lista única de keywords de detalles_keywords ---
all_keywords = set()
for data in json_data_list:
    for item in data.get("detalles_keywords", []):
        all_keywords.add(item.get("keyword", ""))
all_keywords = sorted(all_keywords)

def get_keyword_evolution(keyword, json_data_list):
    values = []
    for data in json_data_list:
        found = False
        for item in data.get("detalles_keywords", []):
            if item.get("keyword", "") == keyword:
                values.append(item.get("total_results", None))
                found = True
                break
        if not found:
            values.append(None)
    return values

# --- Short Tail y Long Tail: solo keywords que estén en short_tail/long_tail, pero valores de detalles_keywords ---
short_tail_labels = seo_data.get("short_tail", [])
long_tail_labels = seo_data.get("long_tail", [])

def build_chartjs_datasets_from_keywords(keywords, json_data_list):
    datasets = []
    for kw in keywords:
        datasets.append({
            'label': kw,
            'data': get_keyword_evolution(kw, json_data_list)
        })
    return datasets

short_tail_chartjs_datasets = build_chartjs_datasets_from_keywords(short_tail_labels, json_data_list)
long_tail_chartjs_datasets = build_chartjs_datasets_from_keywords(long_tail_labels, json_data_list)
detalle_keywords_chartjs_datasets = build_chartjs_datasets_from_keywords(all_keywords, json_data_list)

detalle_keywords_labels = all_keywords

# --- PAA: evolución por pregunta ---
def normalize_question(q):
    return q.strip().lower()

all_paa_questions = set()
for data in json_data_list:
    for item in data.get("people_also_ask", []):
        q = item.get("question", "")
        if q:
            all_paa_questions.add(normalize_question(q))
all_paa_questions = sorted(all_paa_questions)

paa_label_map = {}
for data in json_data_list:
    for item in data.get("people_also_ask", []):
        q = item.get("question", "")
        if q:
            paa_label_map[normalize_question(q)] = q  # keep original for display

def get_paa_evolution(question_norm, json_data_list):
    values = []
    for data in json_data_list:
        found = False
        for item in data.get("people_also_ask", []):
            if normalize_question(item.get("question", "")) == question_norm:
                values.append(item.get("total_results", None))
                found = True
                break
        if not found:
            values.append(None)
    return values

def build_chartjs_datasets_from_paa(questions_norm, json_data_list):
    datasets = []
    for q_norm in questions_norm:
        datasets.append({
            'label': paa_label_map.get(q_norm, q_norm),
            'data': get_paa_evolution(q_norm, json_data_list)
        })
    return datasets

paa_labels = [paa_label_map[q] for q in all_paa_questions]
paa_chartjs_datasets = build_chartjs_datasets_from_paa(all_paa_questions, json_data_list)

# --- Tablas comparativas (para la vista) ---
# Short tail
short_tail_table = []
for kw in short_tail_labels:
    row = [kw]
    row += get_keyword_evolution(kw, json_data_list)
    short_tail_table.append(row)
# Long tail
long_tail_table = []
for kw in long_tail_labels:
    row = [kw]
    row += get_keyword_evolution(kw, json_data_list)
    long_tail_table.append(row)
# Detalles
detalle_keywords_table = []
for kw in detalle_keywords_labels:
    row = [kw]
    row += get_keyword_evolution(kw, json_data_list)
    detalle_keywords_table.append(row)
# PAA
paa_table = []
for q in paa_labels:
    row = [q]
    row += get_paa_evolution(q, json_data_list)
    paa_table.append(row)

# --- Search Console mensual ---
def cargar_monthly_performance_json():
    files = glob.glob('reportes/*_monthly_performance.json')
    if not files:
        return None
    with open(files[0], 'r', encoding='utf-8') as f:
        data = json.load(f)
        if not data or not isinstance(data, dict):
            return None
        meses = list(data.keys())
        if not meses:
            return None
        for m in meses:
            if not all(k in data[m] for k in ('clicks','impressions','avg_ctr','avg_position')):
                return None
        return data

monthly_performance = cargar_monthly_performance_json() or {}
sc_months = list(monthly_performance.keys())
sc_clicks = [monthly_performance[m]['clicks'] for m in sc_months] if sc_months else []
sc_impressions = [monthly_performance[m]['impressions'] for m in sc_months] if sc_months else []
sc_avg_ctr = [monthly_performance[m]['avg_ctr'] for m in sc_months] if sc_months else []
sc_avg_position = [monthly_performance[m]['avg_position'] for m in sc_months] if sc_months else []

# Cargar plantilla externa
with open("./templates/template.html", "r", encoding="utf-8") as f:
    template = Template(f.read())

# Renderizar plantilla con todos los datasets
html_rendered = template.render(
    seo=seo_data,
    short_tail_labels=short_tail_labels,
    long_tail_labels=long_tail_labels,
    detalle_keywords_labels=detalle_keywords_labels,
    paa_labels=paa_labels,
    version_dates=version_dates,
    short_tail_chartjs_datasets=short_tail_chartjs_datasets,
    long_tail_chartjs_datasets=long_tail_chartjs_datasets,
    detalle_keywords_chartjs_datasets=detalle_keywords_chartjs_datasets,
    paa_chartjs_datasets=paa_chartjs_datasets,
    short_tail_table=short_tail_table,
    long_tail_table=long_tail_table,
    detalle_keywords_table=detalle_keywords_table,
    paa_table=paa_table,
    sc_months=sc_months,
    sc_clicks=sc_clicks,
    sc_impressions=sc_impressions,
    sc_avg_ctr=sc_avg_ctr,
    sc_avg_position=sc_avg_position,
    sc_has_data=bool(sc_months),
)

# Guardar resultado
with open("informe_seo.html", "w", encoding="utf-8") as f:
    f.write(html_rendered)

print("✅ Informe generado: informe_seo.html")


url = "https://crm.hawkins.es/api/autoseo/reports/upload"  # Cambia esta URL por la de destino real

with open("informe_seo.html", "rb") as f:
    files = {"file": ("informe_seo.html", f, "text/html")}
    data = {"id": "15"}  # Asegúrate de poner el ID correcto aquí
    try:
        response = requests.post(url, files=files, data=data)
        if response.status_code == 200:
            print("✅ Informe enviado correctamente al servidor.")
        else:
            print(f"❌ Error al enviar el informe. Código de estado: {response.status_code}")
            print("Respuesta del servidor:", response.text)
    except Exception as e:
        print(f"❌ Excepción al enviar el informe: {e}")

print('Enviando correo')
main(id)