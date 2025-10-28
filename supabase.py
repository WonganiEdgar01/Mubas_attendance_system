# SPDX-FileCopyrightText: 2021 ladyada for Adafruit Industries
# SPDX-License-Identifier: MIT

import time
import json
from datetime import datetime

import board
import busio
from digitalio import DigitalInOut, Direction

import adafruit_fingerprint

# Supabase imports
from supabase import create_client, Client

# Supabase configuration
SUPABASE_URL = "https://jzxmzmaszmdjftyhilgu.supabase.co"  # Replace with your Supabase URL
SUPABASE_KEY = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Imp6eG16bWFzem1kamZ0eWhpbGd1Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTcyNTQwNDAsImV4cCI6MjA3MjgzMDA0MH0.uASNA1MhRzwbd9BR3ox8vDqvpJMZojChchu8Lc01kN4"  # Replace with your Supabase anon key

# Initialize Supabase client
supabase: Client = create_client(SUPABASE_URL, SUPABASE_KEY)

led = DigitalInOut(board.D13)
led.direction = Direction.OUTPUT

uart = busio.UART(board.TX, board.RX, baudrate=57600)

# If using with a computer such as Linux/RaspberryPi, Mac, Windows with USB/serial converter:
# import serial
# uart = serial.Serial("/dev/ttyUSB0", baudrate=57600, timeout=1)

# If using with Linux/Raspberry Pi and hardware UART:
# import serial
# uart = serial.Serial("/dev/ttyS0", baudrate=57600, timeout=1)

finger = adafruit_fingerprint.Adafruit_Fingerprint(uart)

##################################################

def create_database_tables():
    """Create the necessary tables in Supabase (run this once)"""
    try:
        # Create fingerprints table
        fingerprint_table_sql = """
        CREATE TABLE IF NOT EXISTS fingerprints (
            id SERIAL PRIMARY KEY,
            finger_id INTEGER UNIQUE NOT NULL,
            user_name VARCHAR(255),
            enrolled BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMPTZ DEFAULT NOW(),
            last_accessed TIMESTAMPTZ DEFAULT NOW()
        );
        """
        
        # Create access_logs table
        access_logs_table_sql = """
        CREATE TABLE IF NOT EXISTS access_logs (
            id SERIAL PRIMARY KEY,
            finger_id INTEGER NOT NULL,
            confidence INTEGER,
            access_time TIMESTAMPTZ DEFAULT NOW(),
            status VARCHAR(50) DEFAULT 'granted',
            FOREIGN KEY (finger_id) REFERENCES fingerprints(finger_id)
        );
        """
        
        # Execute SQL using Supabase RPC (if you have SQL functions set up)
        # Or use the REST API to create tables via SQL editor in Supabase dashboard
        print("Please create the following tables in your Supabase SQL editor:")
        print(fingerprint_table_sql)
        print(access_logs_table_sql)
        
    except Exception as e:
        print(f"Error creating tables: {e}")

def store_fingerprint_in_supabase(finger_id, confidence=None, user_name=None):
    """Store fingerprint data in Supabase PostgreSQL"""
    try:
        # Check if fingerprint already exists
        existing = supabase.table('fingerprints').select("*").eq('finger_id', finger_id).execute()
        
        fingerprint_data = {
            'finger_id': finger_id,
            'user_name': user_name or f"User_{finger_id}",
            'enrolled': True,
            'last_accessed': datetime.now().isoformat()
        }
        
        if existing.data:
            # Update existing record
            result = supabase.table('fingerprints').update(fingerprint_data).eq('finger_id', finger_id).execute()
            print(f"Updated fingerprint data in Supabase for ID: {finger_id}")
        else:
            # Insert new record
            result = supabase.table('fingerprints').insert(fingerprint_data).execute()
            print(f"Stored new fingerprint data in Supabase with ID: {finger_id}")
        
        return True
        
    except Exception as e:
        print(f"Error storing fingerprint in Supabase: {e}")
        return False

def update_access_log(finger_id, confidence):
    """Update access log when fingerprint is detected"""
    try:
        access_data = {
            'finger_id': finger_id,
            'confidence': confidence,
            'access_time': datetime.now().isoformat(),
            'status': 'granted'
        }
        
        # Insert access log
        supabase.table('access_logs').insert(access_data).execute()
        
        # Update last_accessed in fingerprints table
        supabase.table('fingerprints').update({
            'last_accessed': datetime.now().isoformat()
        }).eq('finger_id', finger_id).execute()
        
        print(f"Access logged for fingerprint ID: {finger_id}")
        return True
        
    except Exception as e:
        print(f"Error logging access: {e}")
        return False

def delete_fingerprint_from_supabase(finger_id):
    """Delete fingerprint data from Supabase"""
    try:
        # Delete access logs first (due to foreign key constraint)
        supabase.table('access_logs').delete().eq('finger_id', finger_id).execute()
        
        # Delete fingerprint record
        result = supabase.table('fingerprints').delete().eq('finger_id', finger_id).execute()
        
        print(f"Fingerprint data deleted from Supabase: {finger_id}")
        return True
    except Exception as e:
        print(f"Error deleting fingerprint from Supabase: {e}")
        return False

def get_all_fingerprints():
    """Retrieve all fingerprints from Supabase"""
    try:
        result = supabase.table('fingerprints').select("*").execute()
        return result.data
    except Exception as e:
        print(f"Error retrieving fingerprints: {e}")
        return []

def get_access_logs(finger_id=None, limit=10):
    """Retrieve access logs from Supabase"""
    try:
        query = supabase.table('access_logs').select("*").order('access_time', desc=True).limit(limit)
        
        if finger_id:
            query = query.eq('finger_id', finger_id)
            
        result = query.execute()
        return result.data
    except Exception as e:
        print(f"Error retrieving access logs: {e}")
        return []

def get_fingerprint():
    """Get a finger print image, template it, and see if it matches!"""
    print("Waiting for image...")
    while finger.get_image() != adafruit_fingerprint.OK:
        pass
    print("Templating...")
    if finger.image_2_tz(1) != adafruit_fingerprint.OK:
        return False
    print("Searching...")
    if finger.finger_search() != adafruit_fingerprint.OK:
        return False
    return True


def get_fingerprint_detail():
    """Get a finger print image, template it, and see if it matches!
    This time, print out each error instead of just returning on failure"""
    print("Getting image...", end="")
    i = finger.get_image()
    if i == adafruit_fingerprint.OK:
        print("Image taken")
    else:
        if i == adafruit_fingerprint.NOFINGER:
            print("No finger detected")
        elif i == adafruit_fingerprint.IMAGEFAIL:
            print("Imaging error")
        else:
            print("Other error")
        return False

    print("Templating...", end="")
    i = finger.image_2_tz(1)
    if i == adafruit_fingerprint.OK:
        print("Templated")
    else:
        if i == adafruit_fingerprint.IMAGEMESS:
            print("Image too messy")
        elif i == adafruit_fingerprint.FEATUREFAIL:
            print("Could not identify features")
        elif i == adafruit_fingerprint.INVALIDIMAGE:
            print("Image invalid")
        else:
            print("Other error")
        return False

    print("Searching...", end="")
    i = finger.finger_fast_search()
    # This block needs to be refactored when it can be tested.
    if i == adafruit_fingerprint.OK:
        print("Found fingerprint!")
        return True
    else:
        if i == adafruit_fingerprint.NOTFOUND:
            print("No match found")
        else:
            print("Other error")
        return False


def enroll_finger(location):
    """Take a 2 finger images and template it, then store in 'location'"""
    # Get user name for enrollment
    user_name = input("Enter user name (optional): ").strip()
    if not user_name:
        user_name = None
    
    for fingerimg in range(1, 3):
        if fingerimg == 1:
            print("Place finger on sensor...", end="")
        else:
            print("Place same finger again...", end="")

        while True:
            i = finger.get_image()
            if i == adafruit_fingerprint.OK:
                print("Image taken")
                break
            if i == adafruit_fingerprint.NOFINGER:
                print(".", end="")
            elif i == adafruit_fingerprint.IMAGEFAIL:
                print("Imaging error")
                return False
            else:
                print("Other error")
                return False

        print("Templating...", end="")
        i = finger.image_2_tz(fingerimg)
        if i == adafruit_fingerprint.OK:
            print("Templated")
        else:
            if i == adafruit_fingerprint.IMAGEMESS:
                print("Image too messy")
            elif i == adafruit_fingerprint.FEATUREFAIL:
                print("Could not identify features")
            elif i == adafruit_fingerprint.INVALIDIMAGE:
                print("Image invalid")
            else:
                print("Other error")
            return False

        if fingerimg == 1:
            print("Remove finger")
            time.sleep(1)
            while i != adafruit_fingerprint.NOFINGER:
                i = finger.get_image()

    print("Creating model...", end="")
    i = finger.create_model()
    if i == adafruit_fingerprint.OK:
        print("Created")
    else:
        if i == adafruit_fingerprint.ENROLLMISMATCH:
            print("Prints did not match")
        else:
            print("Other error")
        return False

    print("Storing model #%d..." % location, end="")
    i = finger.store_model(location)
    if i == adafruit_fingerprint.OK:
        print("Stored")
        # Store in Supabase after successful local storage
        if store_fingerprint_in_supabase(location, user_name=user_name):
            print("Fingerprint data saved to Supabase!")
        else:
            print("Warning: Fingerprint stored locally but failed to save to Supabase")
    else:
        if i == adafruit_fingerprint.BADLOCATION:
            print("Bad storage location")
        elif i == adafruit_fingerprint.FLASHERR:
            print("Flash storage error")
        else:
            print("Other error")
        return False

    return True


##################################################


def get_num():
    """Use input() to get a valid number from 1 to 127. Retry till success!"""
    i = 0
    while (i > 127) or (i < 1):
        try:
            i = int(input("Enter ID # from 1-127: "))
        except ValueError:
            pass
    return i

def display_database_stats():
    """Display statistics from Supabase database"""
    try:
        fingerprints = get_all_fingerprints()
        print(f"\n--- Database Statistics ---")
        print(f"Total enrolled fingerprints: {len(fingerprints)}")
        
        if fingerprints:
            print("\nEnrolled Users:")
            for fp in fingerprints:
                print(f"  ID {fp['finger_id']}: {fp['user_name']} (Last accessed: {fp['last_accessed']})")
        
        # Show recent access logs
        recent_logs = get_access_logs(limit=5)
        if recent_logs:
            print(f"\nRecent Access Logs:")
            for log in recent_logs:
                print(f"  ID {log['finger_id']}: {log['access_time']} (Confidence: {log['confidence']}%)")
        
    except Exception as e:
        print(f"Error retrieving database stats: {e}")


while True:
    print("----------------")
    if finger.read_templates() != adafruit_fingerprint.OK:
        raise RuntimeError("Failed to read templates")
    print("Fingerprint templates:", finger.templates)
    print("e) enroll print")
    print("f) find print")
    print("d) delete print")
    print("s) sync with Supabase")
    print("v) view database stats")
    print("l) view access logs")
    print("c) create database tables")
    print("----------------")
    c = input("> ")

    if c == "e":
        enroll_finger(get_num())
    elif c == "f":
        if get_fingerprint():
            print("Detected #", finger.finger_id, "with confidence", finger.confidence)
            # Log access in Supabase
            update_access_log(finger.finger_id, finger.confidence)
        else:
            print("Finger not found")
    elif c == "d":
        finger_id = get_num()
        if finger.delete_model(finger_id) == adafruit_fingerprint.OK:
            print("Deleted from sensor!")
            # Also delete from Supabase
            delete_fingerprint_from_supabase(finger_id)
        else:
            print("Failed to delete from sensor")
    elif c == "s":
        # Sync existing fingerprints with Supabase
        print("Syncing existing fingerprints with Supabase...")
        for template_id in finger.templates:
            store_fingerprint_in_supabase(template_id)
        print("Sync completed!")
    elif c == "v":
        display_database_stats()
    elif c == "l":
        finger_id_input = input("Enter finger ID for logs (or press Enter for all): ").strip()
        finger_id = int(finger_id_input) if finger_id_input else None
        logs = get_access_logs(finger_id, limit=20)
        
        if logs:
            print(f"\n--- Access Logs ---")
            for log in logs:
                print(f"ID {log['finger_id']}: {log['access_time']} (Confidence: {log['confidence']}%)")
        else:
            print("No access logs found")
    elif c == "c":
        create_database_tables()