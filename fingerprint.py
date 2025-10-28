import time
import serial
import adafruit_fingerprint
from flask import Flask, jsonify, request
from flask_cors import CORS

# -----------------------------
# Initialize UART connection
# -----------------------------
try:
    uart = serial.Serial("/dev/serial0", baudrate=57600, timeout=1)
    finger = adafruit_fingerprint.Adafruit_Fingerprint(uart)
except serial.SerialException as e:
    uart = None
    finger = None
    print(f"? Could not open serial port: {e}")

# -----------------------------
# Verify sensor connection
# -----------------------------
def verify_sensor(retries=5, delay=2):
    if finger is None:
        return False
    for i in range(retries):
        print(f"Checking sensor... Attempt {i+1}/{retries}")
        if finger.verify_password() == adafruit_fingerprint.OK:
            print("? Sensor connected successfully!")
            return True
        else:
            print("?? Sensor not responding, retrying...")
            time.sleep(delay)
    print("? Failed to connect to sensor after multiple attempts.")
    return False

# -----------------------------
# Capture and search fingerprint
# -----------------------------
def get_fingerprint():
    if finger is None:
        print("?? Finger object not initialized")
        return False
    print("Place your finger on the sensor...")
    while True:
        result = finger.get_image()
        if result == adafruit_fingerprint.OK:
            print("?? Image captured!")
            break
        elif result == adafruit_fingerprint.NOFINGER:
            time.sleep(0.1)
        else:
            print("?? Error reading image")
            return False

    if finger.image_2_tz(1) != adafruit_fingerprint.OK:
        print("?? Error converting image")
        return False

    result = finger.finger_search()
    if result == adafruit_fingerprint.OK:
        print(f"? Finger matched! ID: {finger.finger_id}, Confidence: {finger.confidence}")
        return True
    else:
        print("? No match found")
        return False

# -----------------------------
# Enroll a new fingerprint
# -----------------------------
def enroll_fingerprint(finger_id):
    if finger is None:
        print("?? Finger object not initialized")
        return False
    print(f"Enrolling new fingerprint in ID #{finger_id}...")
    
    # Step 1: Capture first image
    for attempt in range(1, 6):
        print(f"Place finger for first scan (Attempt {attempt}/5)")
        while True:
            result = finger.get_image()
            if result == adafruit_fingerprint.OK:
                break
            elif result == adafruit_fingerprint.NOFINGER:
                time.sleep(0.1)
            else:
                print("?? Error reading image")
                return False

        if finger.image_2_tz(1) == adafruit_fingerprint.OK:
            break
        print("?? Failed to convert image, try again.")
    else:
        print("?? Enrollment failed at first scan.")
        return False

    print("Remove finger...")
    time.sleep(2)

    # Step 2: Capture second image
    for attempt in range(1, 6):
        print(f"Place same finger for second scan (Attempt {attempt}/5)")
        while True:
            result = finger.get_image()
            if result == adafruit_fingerprint.OK:
                break
            elif result == adafruit_fingerprint.NOFINGER:
                time.sleep(0.1)
            else:
                print("?? Error reading image")
                return False

        if finger.image_2_tz(2) == adafruit_fingerprint.OK:
            break
        print("?? Failed to convert image, try again.")
    else:
        print("?? Enrollment failed at second scan.")
        return False

    # Step 3: Create template
    if finger.create_model() != adafruit_fingerprint.OK:
        print("?? Error creating fingerprint model")
        return False

    # Step 4: Store template
    if finger.store_model(finger_id) == adafruit_fingerprint.OK:
        print(f"? Fingerprint enrolled successfully in ID #{finger_id}!")
        return True
    else:
        print("?? Failed to store fingerprint")
        return False

# -----------------------------
# Flask API
# -----------------------------
app = Flask(__name__)
CORS(app)


@app.route("/finger/status", methods=["GET"])
def api_status():
    status_ok = verify_sensor()
    return jsonify({
        "sensor_connected": bool(status_ok)
    }), (200 if status_ok else 503)


@app.route("/finger/scan", methods=["GET"])  # GET for parity with bscanner.py
def api_scan():
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


if __name__ == "__main__":
    # Expose to LAN like bscanner.py
    app.run(host="0.0.0.0", port=5000)
