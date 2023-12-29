import numpy as np
import matplotlib.pyplot as plt

from keras.preprocessing.image import img_to_array
from keras.utils import load_img
from keras.models import load_model

import sys

# GLOBALS
SHAPE_HEIGHT = 270
SHAPE_WIDTH = 480

def test_model(model, img):
    img1 = load_img(img, target_size=(SHAPE_HEIGHT,SHAPE_WIDTH))
    imga = img_to_array(img1)
    img1 = np.expand_dims(imga, axis=0)
    img1 /= 255.0
    p_res= model.predict(img1)
    print(p_res)

def main():
    img = ''.join(sys.argv[1:][0])
    model_path = ''.join(sys.argv[1:][1])

    model = load_model(model_path)
    test_model(model, img)
    return 0

if __name__ == '__main__':
    main()
