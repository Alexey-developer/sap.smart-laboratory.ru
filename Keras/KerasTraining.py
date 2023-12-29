import numpy as np
import glob
import matplotlib.pyplot as plt

from keras.preprocessing.image import ImageDataGenerator, img_to_array, image
from keras.models import Model, load_model
from keras.applications.vgg16 import VGG16
from keras.callbacks import ModelCheckpoint, EarlyStopping, TensorBoard
from tensorflow.keras.optimizers import SGD
from keras.layers import Dense, Flatten
from keras import backend as K

K.set_image_data_format('channels_last')

# GLOBALS
BATCH_SIZE = 4
QUANTITY_OF_EPOCHS = 8
SHAPE_HEIGHT = 270
SHAPE_WIDTH = 480

LOAD_WEIGHTS = False
WEIGHTS_FILENAME = 'weights/vgg/New-weights-improvement-18-0.11.hdf5'

LOAD_MODEL = False
MODEL_NAME = 'model'
MODEL_PATH = 'S:\\sap.smart-laboratory.ru\\Keras\\models\\' + MODEL_NAME + '.h5'

def set_data_generators():
    train_data_generator = ImageDataGenerator(
        rescale=1./255,
        zoom_range=0.1,
        width_shift_range= 0.1,
        height_shift_range= 0.1,
        horizontal_flip=True
    )
    validation_data_generator = ImageDataGenerator(
        rescale=1./255,
    )
    return train_data_generator, validation_data_generator

def set_flows_from_directories(train_data_generator, validation_data_generator):
    train_flow_generator = train_data_generator.flow_from_directory(
        'S:\\sap.smart-laboratory.ru\\Keras\\data\\img_train',
        target_size=(SHAPE_HEIGHT, SHAPE_WIDTH),
        batch_size=BATCH_SIZE
    )
    validation_flow_generator = validation_data_generator.flow_from_directory(
        'S:\\sap.smart-laboratory.ru\\Keras\\data\\img_val',
        target_size=(SHAPE_HEIGHT, SHAPE_WIDTH),
        batch_size=BATCH_SIZE,
        shuffle=False
    )
    test_flow_generator = validation_data_generator.flow_from_directory(
        'S:\\sap.smart-laboratory.ru\\Keras\\data\\img_val',
        target_size=(SHAPE_HEIGHT, SHAPE_WIDTH),
        batch_size=BATCH_SIZE
    )
    return train_flow_generator, validation_flow_generator, test_flow_generator

def set_model(quantity_of_classes):
    base_model = VGG16(weights='imagenet', include_top=False, input_shape=((SHAPE_HEIGHT,SHAPE_WIDTH,3)))#include_top=False => используем собственную полносвязную сеть на 2 класса

    x = Flatten()(base_model.output)
    x = Dense(400, activation='relu', name='dd1')(x)
    x = Dense(400, activation='relu', name='dd2')(x)

    top_model=Dense(quantity_of_classes, activation='softmax', name='output')(x)

    model = Model(inputs=base_model.input, outputs=top_model)

    if(LOAD_WEIGHTS):
        model.load_weights(WEIGHTS_FILENAME, by_name=True)

    model.summary()

    return model

def set_callbacks():
    filepath = "S:\\sap.smart-laboratory.ru\\Keras\\weights\\vgg\\weights-improvement-{epoch:02d}-{val_loss:.2f}.hdf5"
    checkpoint = ModelCheckpoint(filepath, monitor='val_loss', verbose=1, save_best_only=True, mode='max')
    early = EarlyStopping(monitor="val_loss", mode="max", patience=20)
    tensorboard = TensorBoard(
        log_dir="KerasTrainingFormula1New\\KerasTrainingFormula1\\logs\\",
        write_graph=False, #This eats a lot of space. Enable with caution!
        #histogram_freq = 1,
        write_images=True,
        # batch_size = BATCH_SIZE,
        # write_grads=True
    )
    callbacks_list = [checkpoint, tensorboard, early]
    return callbacks_list

def test_model(model):
    im_files = glob.glob('S:\\sap.smart-laboratory.ru\\Keras\\data\\img_test\\' + '*jpg')
    for im_file in im_files:
        img1 = image.load_img(im_file,target_size = (SHAPE_HEIGHT,SHAPE_WIDTH))
        # show_image(img1)
        imga = image.img_to_array(img1)
        img1 = np.expand_dims(imga, axis=0)
        img1 /= 255.0
        p_res= model.predict(img1)
        print(p_res, im_file)

def show_image(image):
    # image = np.array(image)
    # image = image / 255
    plt.figure()
    plt.imshow(image)
    plt.colorbar()
    plt.grid(False)
    plt.show()

def print_message(message, stop = False, error = 'Error detected. Program is stopped!', quantity_of_dash = 50):
    print('-' * quantity_of_dash)
    print(message)
    print('-' * quantity_of_dash)
    if(stop):
        import sys
        sys.exit(error)

def main():
    if(LOAD_MODEL):
        model = load_model(MODEL_PATH)
    else:
        train_data_generator, validation_data_generator = set_data_generators()
        train_flow_generator, validation_flow_generator, test_flow_generator = set_flows_from_directories(train_data_generator, validation_data_generator)
        quantity_of_classes = len(train_flow_generator.class_indices)

        model = set_model(quantity_of_classes)
        callbacks_list = set_callbacks()

        model.compile(
            loss='categorical_crossentropy',
            optimizer=SGD(
                learning_rate=0.00025,
                momentum=0.8,
                nesterov=True
            ),
            metrics=['accuracy']
        )

        print_message('Compiling of model has been finished')

        number_of_steps_per_epoch=train_flow_generator.samples // BATCH_SIZE
        number_of_validation_steps=validation_flow_generator.samples // BATCH_SIZE

        print_message("steps_per_epoch %d"% number_of_steps_per_epoch)
        print_message("validation_steps %d"% number_of_validation_steps)
        print_message("picture size %dx%d"% (SHAPE_HEIGHT, SHAPE_WIDTH))

        print_message('Training the model...')
        # for i in range(1000):
        #     show_image(train_flow_generator.next()[0][0])

        model.fit(
            train_flow_generator,
            steps_per_epoch=number_of_steps_per_epoch,
            epochs=QUANTITY_OF_EPOCHS,
            verbose=1,
            callbacks=callbacks_list,
            validation_data=validation_flow_generator,
            validation_steps=number_of_validation_steps
        )

        print_message('Training of the model has been completed')

        loss, accuracy = model.evaluate(test_flow_generator, steps=1)
        print_message("\nLoss: %.2f, Accuracy: %.2f%%" % (loss, accuracy*100))

        model.save(MODEL_PATH)

        print_message("Model has been saved to disk")
    test_model(model)
    return 0

main()
