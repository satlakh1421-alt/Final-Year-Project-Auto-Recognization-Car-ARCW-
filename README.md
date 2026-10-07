# 🚗 Automated Recognition Car Wash (ARCW)
An integrated smart car wash automation and vehicle recognition system developed as a Final Year Project. ARCW combines computer vision for automatic license plate recognition (ALPR), hardware-level sensor monitoring, and a centralized web dashboard to streamline and automate car wash management.

## 🚀 Key Features
- **Automated License Plate Recognition (ALPR/ANPR):** Captures vehicle entry using camera feeds and extracts plate numbers via OCR for quick identification.
- **Hardware Integration & Automation:** Communicates with microcontrollers and proximity/motion sensors to automate wash cycles and gate mechanisms.
- **Centralized Web Dashboard:** Multi-role management interface to monitor wash bay status, manage queues, view transaction histories, and track system analytics in real time.
- **Vehicle & Customer Tracking:** Records wash frequencies, package preferences, and entry/exit timestamps to improve service efficiency.
- 
## 🛠️ System Architecture & Tech Stack
- **Computer Vision & Processing:** Python, OpenCV, Tesseract OCR
- **Web Interface:** HTML5, CSS3, JavaScript, PHP
- **Database:** MySQL
- **Hardware & Sensors:** Raspberry Pi / Arduino, ultrasonic/infrared sensors, relay modules, USB webcam

## 📂 Project Structure
Final-Year-Project-Auto-Recognization-Car-ARCW-/
│
├── camera_recognition/   # Python scripts for camera capture & OCR processing
├── web_dashboard/        # Web portal frontend (HTML, CSS, JS) & PHP backend
│   ├── css/
│   ├── js/
│   └── api/              # Backend endpoints for hardware and UI communication
├── database/             # MySQL schema and initialization scripts (.sql)
├── hardware/             # Microcontroller pin configurations and control scripts
└── README.md             # Project documentation

## ⚙️ Getting Started & Setup
Prerequisites
1. Python 3.8+
2. Apache & MySQL (e.g., via XAMPP)
3. Tesseract OCR engine installed locally
4. Webcam or connected camera module

**Installation Steps**
1. **Clone the repository:**
git clone [https://github.com/satlakh1421-alt/Final-Year-Project-Auto-Recognization-Car-ARCW-.git](https://github.com/satlakh1421-alt/Final-Year-Project-Auto-Recognization-Car-ARCW-.git)
cd Final-Year-Project-Auto-Recognization-Car-ARCW-
2. **Database Configuration:**
3. Launch your MySQL server via XAMPP.
4. Import the SQL script found in database/ into a new database (e.g., arcw_db).
5.Update your database credentials inside web_dashboard/api/config.php (or relevant config file).
6. Deploy Web Dashboard:
7. Move or symlink the web_dashboard/ directory into your local server root (e.g., htdocs/arcw).
8. Access the dashboard at http://localhost/arcw.
9. Run the Recognition Script:

**Install required Python dependencies:**
pip install opencv-python pytesseract mysql-connector-python
Start the detection service:
python camera_recognition/main.py

## 🤝 Contributing
1. Contributions, feedback, and optimization suggestions are welcome!
2. Fork the Project
3. Create your Feature Branch (git checkout -b feature/NewImprovement)
4. Commit your Changes (git commit -m 'Add new sensor trigger module')
5. Push to the Branch (git push origin feature/NewImprovement)
6. Open a Pull Request

## 📝 License
This project was developed for educational and academic demonstration purposes under the MIT License. See LICENSE for more information.
