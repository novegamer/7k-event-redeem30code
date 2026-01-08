<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TSKGB Auto-Redeem API</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #0f172a; color: #f8fafc; }
        .log-container::-webkit-scrollbar { width: 4px; }
        .log-container::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
        .status-success { color: #10b981; }
        .status-fail { color: #f43f5e; }
        .status-process { color: #3b82f6; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-lg bg-slate-900 rounded-3xl border border-slate-800 shadow-2xl overflow-hidden">
        <div class="p-6 bg-gradient-to-r from-red-600 to-red-800">
            <h1 class="text-2xl font-black italic text-white">TSKGB API REDEEMER</h1>
            <p class="text-xs text-red-100 opacity-80 uppercase tracking-widest">GitHub Pages Version (CORS Proxy)</p>
        </div>

        <div class="p-6 space-y-6">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2">Member Code (PID)</label>
                <input type="text" id="pid" 
                    class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-xl font-mono text-red-400 outline-none focus:border-red-500 transition-all" 
                    placeholder="กรอก Member Code">
            </div>

            <button onclick="startRedeem()" id="btnStart"
                class="w-full py-4 bg-red-600 hover:bg-red-500 text-white font-black rounded-xl text-lg shadow-lg shadow-red-900/20 transition-all active:scale-95">
                🚀 เริ่มเติมโค้ดหลังบ้าน (17 รายการ)
            </button>

            <div class="bg-black rounded-2xl border border-slate-800 p-5">
                <div class="flex justify-between items-center mb-4">
                    <span id="progressLabel" class="text-[10px] font-bold text-slate-500 uppercase">Process Log</span>
                    <span id="counter" class="text-xs font-mono text-red-500">0/17</span>
                </div>
                
                <div class="w-full bg-slate-800 h-1.5 rounded-full mb-4 overflow-hidden">
                    <div id="progressBar" class="bg-red-500 h-full w-0 transition-all duration-300"></div>
                </div>

                <div id="logBox" class="h-60 overflow-y-auto space-y-2 text-[11px] font-mono log-container">
                    <div class="text-slate-600 italic">เตรียมพร้อมสำหรับการเติมโค้ด...</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const CODES = [
            "HAPPYNEWYEAR2026", "7S7E7V7E7N7", "DANCINGPOOKI", "BRANZEBRANSEL", 
            "GRACEOFCHAOS", "SENAHAJASENA", "CHAOSESSENCE", "77EVENT77", 
            "100MILLIONHEARTS", "KEYKEYKEY", "POOKIFIVEKINDS", "LETSGO7K", 
            "GOLDENKINGPEPE", "HALFGOODHALFEVIL", "DELLONSVSKRIS", "TARGETWISH", "OBLIVION"
        ];

        // ใช้ CORS Proxy เพื่อให้ยิงข้ามเว็บได้
        const PROXY_URL = "https://api.allorigins.win/raw?url=";
        const TARGET_API = "https://couponview.netmarble.com/coupon/tskgb/apply";

        function addLog(msg, statusClass = '') {
            const logBox = document.getElementById('logBox');
            const time = new Date().toLocaleTimeString();
            const div = document.createElement('div');
            div.className = statusClass;
            div.innerHTML = `<span class="text-slate-700">[${time}]</span> ${msg}`;
            logBox.appendChild(div);
            logBox.scrollTop = logBox.scrollHeight;
        }

        async function startRedeem() {
            const pid = document.getElementById('pid').value.trim();
            if (!pid) return alert("กรุณากรอก PID ก่อนครับ");

            document.getElementById('btnStart').disabled = true;
            document.getElementById('logBox').innerHTML = "";
            addLog("Starting automated background process...", "status-process");

            for (let i = 0; i < CODES.length; i++) {
                const code = CODES[i];
                const progress = `${i + 1}/${CODES.length}`;
                
                document.getElementById('counter').innerText = progress;
                document.getElementById('progressBar').style.width = `${((i + 1) / CODES.length) * 100}%`;
                
                addLog(`[${progress}] Sending: ${code}`, "status-process");

                try {
                    // เตรียมข้อมูล Payload
                    const params = new URLSearchParams();
                    params.append('pid', pid);
                    params.append('channelCode', '100');
                    params.append('couponCode', code);
                    params.append('worldId', '');
                    params.append('nickname', '');

                    // ยิง Request ผ่าน Proxy
                    const response = await fetch(PROXY_URL + encodeURIComponent(TARGET_API), {
                        method: 'POST',
                        body: params,
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        }
                    });

                    const data = await response.json();

                    if (data.resultCode === "SUCCESS") {
                        addLog(`SUCCESS: ${code} - ${data.resultMsg}`, "status-success");
                    } else {
                        addLog(`FAILED: ${code} - ${data.resultMsg}`, "status-fail");
                    }

                } catch (error) {
                    addLog(`ERROR: Network issues or Proxy limit`, "status-fail");
                }

                // หน่วงเวลา 2 วินาทีเพื่อให้ดูเหมือนมนุษย์และไม่โดนแบน IP
                await new Promise(resolve => setTimeout(resolve, 2000));
            }

            document.getElementById('btnStart').disabled = false;
            addLog("ALL CODES PROCESSED.", "status-success");
            alert("ดำเนินการเติมโค้ดครบทั้งหมดแล้ว!");
        }

        // Save PID อัตโนมัติ
        document.getElementById('pid').addEventListener('input', (e) => {
            localStorage.setItem('tsk_pid_github', e.target.value);
        });

        window.onload = () => {
            const saved = localStorage.getItem('tsk_pid_github');
            if (saved) document.getElementById('pid').value = saved;
        };
    </script>
</body>
</html>
