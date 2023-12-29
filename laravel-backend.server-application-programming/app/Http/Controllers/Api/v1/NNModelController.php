<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Api\v1\NNModel;
use App\Models\Api\v1\NeuralNetwork;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;

use Illuminate\Http\UploadedFile;

class NNModelController extends Controller
{
    public function store_neural_network_model(Request $request)
    {
        $neural_network_id = $request->header('X-NN-Id');
        $neural_network = NeuralNetwork::findOrFail($neural_network_id);
        if (!$neural_network) {
            return ['error' => 'Нейросеть не найдена!'];
        }
        if (Auth::user()->id != $neural_network->user_id) {
            return ['error' => 'Невозможно загрузить модель в чужую нейросеть!'];
        }

        // create the file receiver
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        // check if the upload is success, throw exception or return response you need
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }

        // receive the file
        $save = $receiver->receive();

        // check if the upload has finished (in chunk mode it will send smaller files)
        if ($save->isFinished()) {
            // save the file and return any response you need, current example uses `move` function. If you are
            // not using move, you need to manually delete the file by unlink($save->getFile()->getPathname())

            $extra_path = 'models/nn/' . $neural_network->id . '/';

            $saved_file = $this->save_file($save->getFile(), $extra_path);

            $nn_model = new NNModel;
            $nn_model->name = $saved_file['name'];
            $nn_model->original_name = $saved_file['original_name'];
            // $nn_model->size = $saved_file['size'];
            $nn_model->extension = $saved_file['mime_type'];
            $nn_model->path = $saved_file['relative_path'];
            $nn_model->neural_network_id = $neural_network->id;
            $nn_model->save();

            return $nn_model;
            // return response()->json($saved_file);
        }

        // we are in chunk mode, lets send the current progress
        $handler = $save->handler();

        return response()->json([
            "done" => $handler->getPercentageDone(),
            'status' => true
        ]);
    }

    protected function save_file(UploadedFile $file, $extra_path)
    {
        $fileName = $this->create_filename($file);

        // Group files by mime type
        $mime = str_replace('/', '-', $file->getMimeType());

        // Group files by the date (week
        $dateFolder = date("d-m-Y");

        // Build the file path
        $filePath = "upload/{$extra_path}{$mime}/{$dateFolder}";
        $finalPath = storage_path("app/public/" . $filePath);

        // move the file name
        $file->move($finalPath, $fileName);

        return [
            // 'path' => asset('storage_local/' . $filePath),
            'path' => asset($filePath),
            // 'relative_path' => '/storage_local/' . $filePath,
            'relative_path' => $filePath,
            'name' => $fileName,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $mime,
            // 'size' => $file->getSize()
        ];
    }

    protected function create_filename(UploadedFile $file)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = str_replace("." . $extension, "", $file->getClientOriginalName()); // Filename without extension

        // Add timestamp hash to name of the file
        $filename .= "_" . md5(time()) . "." . $extension;

        return $filename;
    }
}
