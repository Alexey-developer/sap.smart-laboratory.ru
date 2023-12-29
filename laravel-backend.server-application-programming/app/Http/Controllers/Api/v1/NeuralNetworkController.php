<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Api\v1\NeuralNetwork;
use Illuminate\Http\Request;

use App\Http\Requests\Api\v1\NeuralNetworkRequest;
use App\Http\Requests\Api\v1\PredictRequest;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Storage;

class NeuralNetworkController extends Controller
{
    public function predict(PredictRequest $request)
    {
        $neural_network = NeuralNetwork::findOrFail($request->neural_network_id);
        if (Auth::user()->id != $neural_network->user_id) {
            return ['error' => 'Невозможно получить предсказание в чужой нейросети!'];
        }

        if (!$request->hasFile('image')) {
            return ['error' => 'no_file'];
        }
        $path = $request->file('image')->store("public\\predict-images_id-$request->neural_network_id");

        $model = $neural_network->models()->firstOrFail();

        // $command = escapeshellcmd('python3.8 /var/www/domains/backend.sap.smart-laboratory.ru/python/predict.py ' . '/var/www/domains/backend.sap.smart-laboratory.ru/' . str_replace('public\\', 'storage_local/', $path) . ' ' . '/var/www/domains/backend.sap.smart-laboratory.ru/storage_local/' . str_replace(['//'], '/', $model->path . '/' . $model->name));
        $command = escapeshellcmd('C:\Users\Пользователь\AppData\Local\Programs\Python\Python38\python.exe S:\server_application_programming\cnn\CNN\main.py ' . str_replace('public', 'S:\server_application_programming\laravel-backend.server-application-programming\public\storage', $path) . ' ' . str_replace('upload', 'S:\server_application_programming\laravel-backend.server-application-programming\public\storage\upload', str_replace(['//', '/'], '\\', $model->path . '\\' . $model->name)));
        $output = shell_exec($command);

        $output = preg_replace('/\s\s+/', ' ', $output);

        $sp1 = strpos($output, '[[');
        $sp2 = strpos($output, ']]');

        $output = substr($output, $sp1 + 2, $sp2 - $sp1 - 2);

        $prediction_output = explode(' ', $output);

        $entities = $neural_network->entities;

        $results = [];

        $count = 0;
        foreach ($prediction_output as $po) {
            array_push($results, 'Вероятность принадлежности классу <b>' .  $entities[$count]->name . '</b> равна: <b>' . number_format(((float)$po * 100), 0, '', '') . '%</b>');
            $count++;
        }

        Storage::delete($path);

        return [
            'prediction_output' => $results,
        ];
    }

    public function get_user_neural_networks()
    {
        return Auth::user()->neural_networks()->with('entities')->get();
    }
    public function store_neural_network(NeuralNetworkRequest $request)
    {
        $neural_network = new NeuralNetwork;
        $neural_network->name = $request->name;
        $neural_network->description = $request->description;
        $neural_network->user_id = Auth::user()->id;
        $neural_network->save();

        return $neural_network;
    }
    public function get_user_neural_network_by_id(Request $request)
    {
        $neural_network = NeuralNetwork::find($request->id);
        if (!$neural_network) {
            return ['error' => 'Нейросеть не найдена!'];
        }
        if (Auth::user()->id != $neural_network->user_id) {
            return ['error' => 'Невозможно получить чужую нейросеть!'];
        }
        return [
            'neural_network' => $neural_network,
            'classes' => $neural_network->entities,
            'model' => $neural_network->models()->first()
        ];
    }
    public function delete_neural_network(Request $request)
    {
        $neural_network = NeuralNetwork::findOrFail($request->id);
        if (Auth::user()->id != $neural_network->user_id) {
            return ['error' => 'Невозможно удалить чужую нейросеть!'];
        }
        $neural_network->delete();

        return 'success';
    }
}
