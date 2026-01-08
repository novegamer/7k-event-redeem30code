from flask import Flask, request, jsonify
import requests

# ประกาศ app ไว้ที่ระดับนอกสุดเพื่อป้องกัน TypeError ใน Vercel
app = Flask(__name__)

OFFICIAL_CODES = [
    "SUNWUKONGNO1", "HAPPYNEWYEAR2026", "7S7E7V7E7N7", "DANCINGPOOKI", 
    "BRANZEBRANSEL", "GRACEOFCHAOS", "SENAHAJASENA", "CHAOSESSENCE", 
    "77EVENT77", "100MILLIONHEARTS", "KEYKEYKEY", "POOKIFIVEKINDS", 
    "LETSGO7K", "GOLDENKINGPEPE", "HALFGOODHALFEVIL", "DELLONSVSKRIS", 
    "TARGETWISH", "OBLIVION", "SENASTARCRYSTAL", "SENA77MEMORY"
]

# ใช้ Headers ตรงตามที่คุณระบุในข้อความล่าสุด
CUSTOM_HEADERS = {
    "Content-Type": "application/json",
    "Accept": "application/json",
    "User-Agent": "Mozilla/5.0 (CouponScript)",
    "Origin": "https://coupon.netmarble.com",
    "Referer": "https://coupon.netmarble.com/tskgb"
}

@app.route('/api/get-codes', methods=['GET'])
def get_codes():
    return jsonify({"codes": OFFICIAL_CODES})

# ขั้นตอนที่ 1: ตรวจสอบ PID (Inquiry)
@app.route('/api/check-user', methods=['POST'])
def check_user():
    try:
        pid = request.json.get('pid')
        url = "https://coupon.netmarble.com/api/coupon/inquiry"
        params = {"gameCode": "tskgb", "langCd": "TH_TH", "pid": pid}
        response = requests.get(url, params=params, headers=CUSTOM_HEADERS, timeout=10)
        return jsonify(response.json())
    except Exception as e:
        return jsonify({"errorCode": 500, "errorMessage": str(e)}), 200

# ขั้นตอนที่ 2: แลกรางวัล (Reward) - ใช้ GET ตามรูป image_57ad69.png
@app.route('/api/redeem', methods=['POST'])
def redeem():
    try:
        data = request.json
        url = "https://coupon.netmarble.com/api/coupon/reward"
        # Payload ตรงตามที่คุณต้องการ
        params = {
            "gameCode": "tskgb",
            "langCd": "TH_TH",
            "pid": data.get('pid'),
            "couponCode": data.get('code').strip()
        }
        response = requests.get(url, params=params, headers=CUSTOM_HEADERS, timeout=10)
        return jsonify(response.json())
    except Exception as e:
        return jsonify({"errorCode": 500, "errorMessage": str(e)}), 200
