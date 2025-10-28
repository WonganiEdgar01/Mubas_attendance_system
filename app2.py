from flask import Flask, jsonify, request
from flask_cors import CORS
import time
import serial
import adafruit_fingerprint

app = Flask(__name__)
CORS(app)  # Allow requests from your web application

# -----------------------------
# Scanner mode flags
# -----------------------------
barcode_enabled = False
fingerprint_enabled = True   # fingerprint is active by default

# -----------------------------
# Initialize UART connection for fingerprint sensor
# -----------------------------
try:
    uart = serial.Serial("/dev/serial0", baudrate=57600, timeout=1)
    finger = adafruit_fingerprint.Adafruit_Fingerprint(uart)
    print("✅ Serial port initialized successfully")
except serial.SerialException as e:
    uart = None
    finger = None
    print(f"❌ Could not open serial port: {e}")

# -----------------------------
# Verify sensor connection
# -----------------------------
def verify_sensor(retries=5, delay=2):
    if finger is None:
        print("❌ Finger object is None - sensor not available")
        return False
    for i in range(retries):
        print(f"Checking sensor... Attempt {i+1}/{retries}")
        try:
            if finger.verify_password() == adafruit_fingerprint.OK:
                print("✅ Sensor connected successfully!")
                return True
            else:
                print("⚠️ Sensor not responding, retrying...")
                time.sleep(delay)
        except Exception as e:
            print(f"❌ Error verifying sensor: {e}")
            time.sleep(delay)
    print("❌ Failed to connect to sensor after multiple attempts.")
    return False

# -----------------------------
# Capture and search fingerprint
# -----------------------------
def get_fingerprint():
    if finger is None:
        print("⚠️ Finger object not initialized")
        return False
    print("Place your finger on the sensor...")
    timeout = time.time() + 10  # 10 second timeout
    while time.time() < timeout:
        result = finger.get_image()
        if result == adafruit_fingerprint.OK:
            print("📸 Image captured!")
            break
        elif result == adafruit_fingerprint.NOFINGER:
            time.sleep(0.1)
            continue
        else:
            print("❌ Error reading image")
            return False
    else:
        print("❌ Timeout waiting for fingerprint")
        return False

    if finger.image_2_tz(1) != adafruit_fingerprint.OK:
        print("❌ Error converting image")
        return False

    result = finger.finger_search()
    if result == adafruit_fingerprint.OK:
        print(f"✅ Finger matched! ID: {finger.finger_id}, Confidence: {finger.confidence}")
        return True
    else:
        print("❌ No match found")
        return False

# -----------------------------
# Enroll a new fingerprint
# -----------------------------
def enroll_fingerprint(finger_id):
    if finger is None:
        print("⚠️ Finger object not initialized")
        return False
    print(f"Enrolling new fingerprint in ID #{finger_id}...")

    # Step 1: Capture first image
    for attempt in range(1, 6):
        print(f"Place finger for first scan (Attempt {attempt}/5)")
        timeout = time.time() + 10
        while time.time() < timeout:
            result = finger.get_image()
            if result == adafruit_fingerprint.OK:
                break
            elif result == adafruit_fingerprint.NOFINGER:
                time.sleep(0.1)
                continue
            else:
                print("❌ Error reading image")
                return False
        else:
            print("❌ Timeout waiting for fingerprint")
            return False

        if finger.image_2_tz(1) == adafruit_fingerprint.OK:
            break
        print("⚠️ Failed to convert image, try again.")
    else:
        print("❌ Enrollment failed at first scan.")
        return False

    print("Remove finger...")
    time.sleep(2)

    # Step 2: Capture second image
    for attempt in range(1, 6):
        print(f"Place same finger for second scan (Attempt {attempt}/5)")
        timeout = time.time() + 10
        while time.time() < timeout:
            result = finger.get_image()
            if result == adafruit_fingerprint.OK:
                break
            elif result == adafruit_fingerprint.NOFINGER:
                time.sleep(0.1)
                continue
            else:
                print("❌ Error reading image")
                return False
        else:
            print("❌ Timeout waiting for fingerprint")
            return False

        if finger.image_2_tz(2) == adafruit_fingerprint.OK:
            break
        print("⚠️ Failed to convert image, try again.")
    else:
        print("❌ Enrollment failed at second scan.")
        return False

    # Step 3: Create template
    if finger.create_model() != adafruit_fingerprint.OK:
        print("❌ Error creating fingerprint model")
        return False

    # Step 4: Store template
    if finger.store_model(finger_id) == adafruit_fingerprint.OK:
        print(f"✅ Fingerprint enrolled successfully in ID #{finger_id}!")
        return True
    else:
        print("❌ Failed to store fingerprint")
        return False

# -----------------------------
# Barcode Scanner Endpoint
# -----------------------------
@app.route('/scan', methods=['GET'])
def scan_barcode():
    global barcode_enabled, fingerprint_enabled
    if not barcode_enabled:
        return jsonify({"barcode": ""})
    print("Waiting for barcode scan...")
    # Simulate barcode input - replace with actual barcode scanner reading
    barcode = input("Scan barcode: ").strip()

    return jsonify({
        "barcode": barcode,
        "barcode_enabled": barcode_enabled,
        "fingerprint_enabled": fingerprint_enabled
    })

# -----------------------------
# Fingerprint Sensor Endpoints
# -----------------------------
@app.route("/finger/status", methods=["GET"])
def api_status():
    status_ok = verify_sensor()
    return jsonify({
        "sensor_connected": bool(status_ok),
        "fingerprint_enabled": fingerprint_enabled,
        "barcode_enabled": barcode_enabled
    }), (200 if status_ok else 503)

@app.route("/finger/scan", methods=["GET"])
def api_scan():
    print(f"🔍 Fingerprint scan requested - Fingerprint enabled: {fingerprint_enabled}")
    if not fingerprint_enabled:
        return jsonify({"error": "Fingerprint scanning is disabled"}), 403
    
    if not verify_sensor():
        return jsonify({"error": "Sensor not available"}), 503

    matched = get_fingerprint()
    if matched:
        return jsonify({
            "matched": True,
            "finger_id": int(getattr(finger, "finger_id", -1)),
            "confidence": int(getattr(finger, "confidence", 0))
        })
    else:
        return jsonify({
            "matched": False
        })

@app.route("/finger/enroll", methods=["POST"])
def api_enroll():
    if not verify_sensor():
        return jsonify({"error": "Sensor not available"}), 503

    payload = request.get_json(silent=True) or {}
    try:
        finger_id = int(payload.get("id"))
    except (TypeError, ValueError):
        return jsonify({"error": "Missing or invalid 'id' (1-127)"}), 400

    if not (1 <= finger_id <= 127):
        return jsonify({"error": "'id' must be between 1 and 127"}), 400

    success = enroll_fingerprint(finger_id)
    return jsonify({
        "enrolled": bool(success),
        "id": finger_id
    }), (200 if success else 500)

# -----------------------------
# Mode control endpoints - FIXED
# -----------------------------
@app.route('/status', methods=['GET'])
def mode_status():
    return jsonify({
        "barcode_enabled": barcode_enabled,
        "fingerprint_enabled": fingerprint_enabled
    })

@app.route('/barcode/enable', methods=['POST'])
def enable_barcode():
    global barcode_enabled, fingerprint_enabled
    barcode_enabled = True
    fingerprint_enabled = False
    print("✅ Barcode mode enabled, Fingerprint disabled")
    return jsonify({
        "barcode_enabled": True, 
        "fingerprint_enabled": False,
        "message": "Barcode scanning enabled"
    })

@app.route('/barcode/disable', methods=['POST'])
def disable_barcode():
    global barcode_enabled, fingerprint_enabled
    barcode_enabled = False
    fingerprint_enabled = True   # CRITICAL FIX: Ensure fingerprint is enabled
    print("✅ Barcode mode disabled, Fingerprint enabled")
    return jsonify({
        "barcode_enabled": False, 
        "fingerprint_enabled": True,
        "message": "Barcode scanning disabled, fingerprint enabled"
    })

@app.route('/finger/enable', methods=['POST'])
def enable_fingerprint():
    global barcode_enabled, fingerprint_enabled
    barcode_enabled = False
    fingerprint_enabled = True
    print("✅ Fingerprint mode enabled, Barcode disabled")
    return jsonify({
        "fingerprint_enabled": True, 
        "barcode_enabled": False,
        "message": "Fingerprint scanning enabled"
    })

@app.route('/finger/disable', methods=['POST'])
def disable_fingerprint():
    global fingerprint_enabled
    fingerprint_enabled = False
    print("✅ Fingerprint mode disabled")
    return jsonify({
        "fingerprint_enabled": False,
        "message": "Fingerprint scanning disabled"
    })

# -----------------------------
# Root endpoint
# -----------------------------
@app.route('/', methods=['GET'])
def index():
    return jsonify({
        "message": "Flask app with barcode scanner and fingerprint sensor",
        "current_mode": {
            "barcode_enabled": barcode_enabled,
            "fingerprint_enabled": fingerprint_enabled
        },
        "endpoints": {
            "mode_status": "/status",
            "barcode_scan": "/scan",
            "barcode_enable": "/barcode/enable",
            "barcode_disable": "/barcode/disable",
            "fingerprint_status": "/finger/status",
            "fingerprint_scan": "/finger/scan",
            "fingerprint_enroll": "/finger/enroll",
            "fingerprint_enable": "/finger/enable",
            "fingerprint_disable": "/finger/disable"
        }
    })

if __name__ == '__main__':
    print("🔧 Starting Flask server...")
    print("🔧 Testing sensor connection...")
    verify_sensor()
    app.run(host='0.0.0.0', port=5002, debug=True)