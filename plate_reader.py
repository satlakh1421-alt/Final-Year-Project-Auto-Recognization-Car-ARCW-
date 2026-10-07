import sys  # import sys module to access command line arguments
import cv2  # import OpenCV library for image processing
import pytesseract  # import pytesseract for OCR (text recognition)
import re  # import regex module for pattern matching

pytesseract.pytesseract.tesseract_cmd = r"C:\Program Files\Tesseract-OCR\tesseract.exe"  # set path to Tesseract OCR executable

if len(sys.argv) < 2:  # check if user provided image path as argument
    print("")  # print empty output if no argument given
    sys.exit(0)  # exit the program safely

image_path = sys.argv[1]  # get image path from command line argument

img = cv2.imread(image_path)  # read image from given path

if img is None:  # check if image failed to load
    print("")  # print empty output if image not found
    sys.exit(0)  # exit program

gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)  # convert image from color (BGR) to grayscale

# enlarge image a bit
gray = cv2.resize(gray, None, fx=2, fy=2, interpolation=cv2.INTER_CUBIC)  # resize image to make text clearer (2x scale)

# try both normal and inverted threshold
_, thresh1 = cv2.threshold(gray, 140, 255, cv2.THRESH_BINARY)  # apply binary threshold (white text on black)
_, thresh2 = cv2.threshold(gray, 140, 255, cv2.THRESH_BINARY_INV)  # apply inverted threshold (black text on white)

def clean_text(text):  # function to clean OCR output text
    return re.sub(r'[^A-Z0-9]', '', text.upper())  # remove all non-alphanumeric characters and convert to uppercase

def score_plate(text):  # function to score how likely text is a license plate
    score = 0  # initialize score
    if re.fullmatch(r'^[A-Z]{1,3}\d{1,4}[A-Z]{0,3}$', text):  # check if text matches license plate pattern
        score += 100  # give high score if pattern matches
    if 4 <= len(text) <= 8:  # check if length is reasonable for plate
        score += 20  # add score for valid length
    if re.search(r'[A-Z]', text) and re.search(r'\d', text):  # check if contains both letters and numbers
        score += 20  # add score if both exist
    return score  # return total score

texts = []  # list to store detected text results

for img_try in [thresh1, thresh2]:  # loop through both threshold images
    text = pytesseract.image_to_string(  # extract text from image using OCR
        img_try,  # input image
        config='--psm 7 -c tessedit_char_whitelist=ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'  # configure OCR to focus on single line and allow only A-Z and 0-9
    )
    texts.append(clean_text(text))  # clean extracted text and store in list

best_text = ""  # variable to store best detected plate
best_score = -1  # initialize best score

for t in texts:  # loop through all detected texts
    s = score_plate(t)  # calculate score for each text
    if s > best_score:  # check if current score is better
        best_score = s  # update best score
        best_text = t  # update best text

print(best_text)  # print the final best detected license plate