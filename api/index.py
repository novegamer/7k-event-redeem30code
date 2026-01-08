from flask import Flask, request, jsonify
import requests

app = Flask(__name__)

@app.route('/api/redeem', methods=['POST'])
def redeem():
    data = request.json
    pid = data.get('pid')
    code = data.get('code')
    
    # URL จากรูป Headers ที่คุณส่งมา
    base_url = "https://coupon.netmarble.com/api/coupon/reward"
    
    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
        "Referer": "https://coupon.netmarble.com/tskgb",
        "Accept": "application/json"
    }
    
    # พารามิเตอร์ตามรูป Payload
    params = {
        "gameCode": "tskgb",
        "couponCode": code.strip(),
        "langCd": "TH_TH",
        "pid": pid
    }
    
    try:
        # ใช้ GET ตามรูป Request Method
        response = requests.get(base_url, params=params, headers=headers, timeout=10)
        return jsonify(response.json())
    except Exception as e:
        return jsonify({"resultCode": "ERROR", "resultMsg": f"เชื่อมต่อล้มเหลว: {str(e)}"}), 500

def handler(event, context):
    return app(event, context)
