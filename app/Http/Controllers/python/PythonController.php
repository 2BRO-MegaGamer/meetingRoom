<?php

namespace App\Http\Controllers\python;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Illuminate\Support\Facades\Response;
class PythonController extends Controller
{
    public function select_job() {
        $job = [
            'voice_to_text'=>["detail"=>"Give voice file and receive text","href"=>"/python/voice_to_text"],
            'convert_file'=>["detail"=>"Give file and receive any type","href"=>"/python/convert_file"],
            'reduce_noise'=>["detail"=>"Upload the audio file and reduce the noise","href"=>"python/reduce_noise"],
            'volume_changer'=>["detail"=>"Upload the audio file and change volume","href"=>"python/volume_changer"],
        ];
        return view("python.select_job")->with(["job"=>$job]);
    }
    public function show_voice_to_text_form() {
        Storage::deleteDirectory('python/inaction/voice_to_text_'.auth()->user()->id);
        return view("python.jobs_form.voice_to_text");
    }
    public function show_convert_file_form() {
        Storage::deleteDirectory('python/inaction/convert_file_'.auth()->user()->id);
        return view("python.jobs_form.convert_file");
    }
    public function show_reduce_noise_form() {
        Storage::deleteDirectory('python/inaction/reduce_noise_'.auth()->user()->id);
        return view("python.jobs_form.reduce_noise");
    }
    public function show_volume_changer_form() {
        Storage::deleteDirectory('python/inaction/volume_changer_'.auth()->user()->id);
        return view("python.jobs_form.volume_changer");
    }
    // Fa-IR Fa-IR Fa-IR
    public function voice_to_text_create_python_file(Request $request) {
        $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));
        if (!$receiver->isUploaded()) {
            return "fail";
        }
        $file = $receiver->receive();
        if ($file->isFinished()) {
            $file->storeAs(
                ('python/inaction/voice_to_text_'.auth()->user()->id)."/medias",str_replace(" ","_",$file->getClientOriginalName())
            );
            $language = $request->language;
            $python_file = "python/voice_to_text.py";
            $python_file_base_text = Storage::get($python_file);
            $python_file_base_text = str_replace('{{$language}}',$language,$python_file_base_text);//{{$language}}
            $python_file_base_text = str_replace('{{$user_id}}',auth()->user()->id,$python_file_base_text);//{{$user_id}}
            $new_direction_name = "/python/inaction/voice_to_text_".auth()->user()->id."/".auth()->user()->id."_voice_to_text.py";
            Storage::put($new_direction_name,$python_file_base_text);
            unlink($file->getPathname());
        }
        $handler = $file->handler();
        return [
            'done' => $handler->getPercentageDone(),
            'status' => true
        ];
    }
    public function voice_to_text_run_python_file(Request $request) {
        $public_path=storage_path();
        $user_id = auth()->user()->id;
        $cd_command = "cd ". $public_path."\app\python\inaction && python -m voice_to_text_".$user_id.".".$user_id."_voice_to_text";
        exec($cd_command,$output_1);
        return $output_1;
    }
    public function voice_to_text_result_txt(Request $request) {
        $user_id = auth()->user()->id;
        $file = Storage::get(('python/inaction/voice_to_text_'.$user_id.'/result.txt'));
        if ($file == null) {
            $this->result_txt($user_id);
        }else{
            $file = ($file == "")?"EMPTY":$file;
            Storage::deleteDirectory('python/inaction/voice_to_text_'.$user_id);
            return $file;
        }
    }
    public function reduce_noise_create_python_file(Request $request) {
        $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));
        if (!$receiver->isUploaded()) {
            return "fail";
        }
        $file = $receiver->receive();
        if ($file->isFinished()) {
            $file->storeAs(
                ('python/inaction/reduce_noise_'.auth()->user()->id)."/medias",str_replace(" ","_",$file->getClientOriginalName())
            );
            $python_file = "python/reduce_noise.py";
            $python_file_base_text = Storage::get($python_file);
            $python_file_base_text = str_replace('{{$user_id}}',auth()->user()->id,$python_file_base_text);//{{$user_id}}
            $new_direction_name = "/python/inaction/reduce_noise_".auth()->user()->id."/".auth()->user()->id."_reduce_noise.py";
            Storage::put($new_direction_name,$python_file_base_text);
            unlink($file->getPathname());
        }
        $handler = $file->handler();
        return [
            'done' => $handler->getPercentageDone(),
            'status' => true
        ];
    }
    public function reduce_noise_run_python_file(Request $request) {
        $public_path=storage_path();
        $user_id = auth()->user()->id;
        $cd_command = "cd ". $public_path."\app\python\inaction && python -m reduce_noise_".$user_id.".".$user_id."_reduce_noise";
        exec($cd_command,$output_1);
        $files_want_reduce_noise = Storage::allFiles(('python/inaction/reduce_noise_'.$user_id.'/medias/'));
        $result =[];
        for ($i=0; $i < count($files_want_reduce_noise); $i++) { 
            $fixed_name_only = str_replace('python/inaction/reduce_noise_'.$user_id.'/medias/',"",$files_want_reduce_noise[$i]);
            $result[$fixed_name_only]["url"] = str_replace("/"," ",$files_want_reduce_noise[$i]);
        }
        return $result;
    }
    public function volume_changer_create_python_file(Request $request) {
        $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));
        if (!$receiver->isUploaded()) {
            return "fail";
        }
        $file = $receiver->receive();
        if ($file->isFinished()) {
            $file->storeAs(
                ('python/inaction/volume_changer_'.auth()->user()->id)."/medias",str_replace(" ","_",$file->getClientOriginalName())
            );
            unlink($file->getPathname());
        }
        $handler = $file->handler();
        return [
            'done' => $handler->getPercentageDone(),
            'status' => true
        ];
    }
    public function volume_changer_run_python_file(Request $request) {
        $volume = ($request->volume == null)?"0":$request->volume;
        $python_file = "python/volume_changer.py";
        $python_file_base_text = Storage::get($python_file);
        log::alert($volume);
        $python_file_base_text = str_replace('{{$user_id}}',auth()->user()->id,$python_file_base_text);//{{$user_id}}
        $python_file_base_text = str_replace('{{$volume}}',$volume,$python_file_base_text);//{{$volume}}
        $new_direction_name = "/python/inaction/volume_changer_".auth()->user()->id."/".auth()->user()->id."_volume_changer.py";
        Storage::put($new_direction_name,$python_file_base_text);
        $public_path=storage_path();
        $user_id = auth()->user()->id;
        $cd_command = "cd ". $public_path."\app\python\inaction && python -m volume_changer_".$user_id.".".$user_id."_volume_changer";
        exec($cd_command,$output_1);
        $files_want_volume_changer = Storage::allFiles(('python/inaction/volume_changer_'.$user_id.'/medias/'));
        $result =[];
        for ($i=0; $i < count($files_want_volume_changer); $i++) { 
            $fixed_name_only = str_replace('python/inaction/volume_changer_'.$user_id.'/medias/',"",$files_want_volume_changer[$i]);
            $result[$fixed_name_only]["url"] = str_replace("/"," ",$files_want_volume_changer[$i]);
        }
        return $result;
    }

    public function convert_file_upload(Request $request) {
        $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));
        if (!$receiver->isUploaded()) {
            return "fail";
        }
        $file = $receiver->receive();
        if ($file->isFinished()) {
            $file->storeAs(
                ('python/inaction/convert_file_'.auth()->user()->id)."/medias",str_replace(" ","_",$file->getClientOriginalName())
            );
            Storage::makeDirectory('python/inaction/convert_file_'.auth()->user()->id."/medias/convert");
            unlink($file->getPathname());
        }
        $handler = $file->handler();
        return [
            'done' => $handler->getPercentageDone(),
            'status' => true
        ];
    }
    public function convert_file_run_python_file(Request $request) {
        $convert_to = $request->to_type;
        $public_path=storage_path();
        $user_id = auth()->user()->id;
        $files_want_convert = Storage::allFiles(('python/inaction/convert_file_'.$user_id.'/medias/'));
        $result = [];
        for ($i=0; $i < count($files_want_convert); $i++) { 
            $fixed_name_only = str_replace('python/inaction/convert_file_'.$user_id.'/medias/',"",$files_want_convert[$i]);
            $fixed_name_path = str_replace("python/inaction/","",$files_want_convert[$i]);
            $convert_name_path = 'convert_file_'.$user_id.'/medias/convert/'. explode(".",$fixed_name_only)[0] .".".$convert_to;
            $cd_command = "cd ". $public_path."\app\python\inaction && ffmpeg -i ".$fixed_name_path." ".$convert_name_path." 2>&1";
            $output_1 = shell_exec($cd_command);
            $output_1 = (strpos($output_1,"Error") != null)?"Error":"done";
            $result[$fixed_name_only]=[];
            if ($output_1 == "done") {
                $result[$fixed_name_only]["url"] = str_replace("/"," ",'python/inaction/'.$convert_name_path);
            }
            $result[$fixed_name_only]["message"] = $output_1;
        }
        return $result;
    }
    public function convert_file_download(string $path) {
        $path = str_replace(" ","/",$path);
        $get_user_id = explode("/medias/convert/",explode("python/inaction/",$path)[1]);
        $user_id = auth()->user()->id;
        if ($get_user_id[0] == "convert_file_". $user_id) {
            return Response::download(storage_path('app/'. $path))->deleteFileAfterSend(true);
        }else{
            return false;
        }
    }
    public function reduce_noise_download(string $path) {
        $path =str_replace(" ","/",$path);
        $get_user_id = explode("/medias/",explode("python/inaction/",$path)[1]);
        $user_id = auth()->user()->id;
        if ($get_user_id[0] == "reduce_noise_". $user_id) {
            return Response::download(storage_path('app/'.$path))->deleteFileAfterSend(true);
        }else{
            return false;
        }
    }
    public function volume_changer_download(string $path) {
        $path =str_replace(" ","/",$path);
        $get_user_id = explode("/medias/",explode("python/inaction/",$path)[1]);
        $user_id = auth()->user()->id;
        if ($get_user_id[0] == "volume_changer_". $user_id) {
            return Response::download(storage_path('app/'.$path))->deleteFileAfterSend(true);
        }else{
            return false;
        }
    }
}
