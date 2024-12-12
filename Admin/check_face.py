import cv2
import sys

def detect_face(image_path):
    face_cascade = cv2.CascadeClassifier(cv2.data.haarcascades + 'haarcascade_frontalface_default.xml')
    image = cv2.imread(image_path)
    gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)

    faces = face_cascade.detectMultiScale(gray, scaleFactor=1.1, minNeighbors=5, minSize=(30, 30))
    return len(faces) > 0  # Returns True if at least one face is detected 

if __name__ == "__main__":
    image_path = sys.argv[1]
    if detect_face(image_path):
        print("FACE_DETECTED")
    else:
        print("NO_FACE")
