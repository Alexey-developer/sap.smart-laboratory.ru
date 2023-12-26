<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Api\v1\Entity;
use App\Models\Api\v1\NeuralNetwork;
use App\Models\Api\v1\Image;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Http\Requests\Api\v1\EntityRequest;

use ZipArchive;

class EntityController extends Controller
{
    public function store_neural_network_entity(EntityRequest $request)
    {
        $neural_network = NeuralNetwork::findOrFail($request->neural_network_id);
        if (!$neural_network) {
            return ['error' => 'Нейросеть не найдена!'];
        }
        if (Auth::user()->id != $neural_network->user_id) {
            return ['error' => 'Невозможно создать класс в чужой нейросети!'];
        }
        $entity = new Entity;
        $entity->name = $request->name;
        $entity->neural_network_id = $neural_network->id;
        $entity->save();

        return $entity;
    }
    public function get_entity_by_id(Request $request)
    {
        $entity = Entity::find($request->id);
        if (!$entity) {
            return ['error' => 'Класс не найден!'];
        }

        $neural_network = NeuralNetwork::find($entity->neural_network_id);
        if (!$neural_network) {
            return ['error' => 'Нейросеть не найдена!'];
        }

        if (Auth::user()->id != $neural_network->user_id) {
            return ['error' => 'Невозможно получить чужую нейросеть!'];
        }
        return [
            'entity' => $entity,
            'images' => $entity->images
        ];
    }
    public function download_entity_images(Request $request)
    {
        $entity = Entity::find($request->id);
        if (!$entity) {
            return ['error' => 'Класс не найден!'];
        }

        $neural_network = NeuralNetwork::find($entity->neural_network_id);
        if (!$neural_network) {
            return ['error' => 'Нейросеть не найдена!'];
        }

        if (Auth::user()->id != $neural_network->user_id) {
            return ['error' => 'Невозможно получить данные чужой нейросети!'];
        }

        $images = $entity->images;

        $zip = new ZipArchive;

        $zip_name = 'images-' . \Carbon\Carbon::now()->timestamp . rand(999, 9999999999999) . '.zip';

        // if ($zip->open(sys_get_temp_dir() . '\\' . $zip_name, ZipArchive::CREATE) === TRUE) {

        //     foreach ($images as $image) {
        //         $filename = 'image-' . \Carbon\Carbon::now()->timestamp . rand(999, 9999999999999) . '.jpg';
        //         $tempImage = tempnam(sys_get_temp_dir(), $filename);
        //         copy("https:$image->small_size_url", $tempImage);

        //         $zip->addFile($tempImage, $filename);
        //     }

        //     $zip->close();
        //     // return response()->download(sys_get_temp_dir() . '\\' . $zip_name, $zip_name);
        //     return ['download_url' => sys_get_temp_dir() . '\\' . $zip_name];
        // } else {

        // Image::make('http://f2b9x.s87.it/images/1/FR_laura-kithorizontal.gif')->save(public_path('images/saveAsImageName.jpg'));
        if ($zip->open(storage_path() . '\\app\\public\\' . $zip_name, ZipArchive::CREATE) === TRUE) {

            foreach ($images as $image) {
                $filename = 'image-' . \Carbon\Carbon::now()->timestamp . rand(999, 9999999999999) . '.jpg';
                $tempImage = tempnam(sys_get_temp_dir(), $filename);
                copy("https:$image->small_size_url", $tempImage);

                $zip->addFile($tempImage, $filename);
            }

            $zip->close();
            // return response()->download(sys_get_temp_dir() . '\\' . $zip_name, $zip_name);
            return ['download_url' => storage_path() . '\\app\\public\\' . $zip_name];
        } else {
            return ['error' => 'Ошибка создания архива!'];
        }
    }
    public function add_images2entity(Request $request)
    {
        $entity = Entity::find($request->entity_id);
        if (!$entity) {
            return ['error' => 'Класс не найден!'];
        }

        $neural_network = NeuralNetwork::find($entity->neural_network_id);
        if (!$neural_network) {
            return ['error' => 'Нейросеть не найдена!'];
        }

        if (Auth::user()->id != $neural_network->user_id) {
            return ['error' => 'Невозможно обновлять чужую нейросеть!'];
        }

        foreach ($request->images as $image) {

            $find_image = Image::where('small_size_url', $image['image'])->where('entity_id', $entity->id)->first();
            if (!$find_image) {
                $new_image = new Image;
                $new_image->small_size_url = $image['image'];
                $new_image->webpage_url = $image['snippet']['url'];
                $new_image->name = $image['snippet']['title'];
                $new_image->entity_id = $entity->id;

                $new_image->save();
            }
        }

        return [
            'ok' => 'ok'
        ];
    }
    public function delete_neural_network_entity(Request $request)
    {
        $entity = Entity::findOrFail($request->id);
        $neural_network = NeuralNetwork::findOrFail($entity->neural_network_id);
        if (Auth::user()->id != $neural_network->user_id) {
            return ['error' => 'Невозможно удалить класс чужой нейросети!'];
        }
        $entity->delete();

        return 'success';
    }
}
