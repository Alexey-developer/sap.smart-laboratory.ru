import numpy as np
import matplotlib.pyplot as plt

from keras.preprocessing.image import image
from keras.models import load_model

import sys

# GLOBALS
SHAPE_HEIGHT = 270
SHAPE_WIDTH = 480

def test_model(model, img):
	img1 = image.load_img(img, target_size=(SHAPE_HEIGHT,SHAPE_WIDTH))
	imga = image.img_to_array(img1)
	img1 = np.expand_dims(imga, axis=0)
	img1 /= 255.0
	p_res= model.predict(img1)
	print(p_res)

def show_image(image):
    # image = np.array(image)
    # image = image / 255
    plt.figure()
    plt.imshow(image)
    plt.colorbar()
    plt.grid(False)
    plt.show()

def main():
    img = ''.join(sys.argv[1:][0])
    model_path = ''.join(sys.argv[1:][1])
    # MODEL_NAME = 'monument_model'
    # model_path = 'S:\\python-practice\\CourseWorkPy\\Lesha\\Keras\\models\\' + MODEL_NAME + '.h5'
    # img = 'S:\server_application_programming\laravel-backend.server-application-programming\public\storage\predict-images_id-1/aIqXehsiete8C0E9TevCRzmQnEprQAA8TXLeJV33.jpg'

    model = load_model(model_path)
    test_model(model, img)
    return 0

if __name__ == '__main__':
    main()
