<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailJob;
use App\Jobs\StoreJsonFile;
use App\Mail\SendEmailMailable;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Sales;
use App\Models\Assets;

class TestController extends Controller
{
    public function ChunkJsonFile(Request $request)
    {
        Assets::where('company_id', "=", 1)->delete();
        $array = [
            '}
{',
            "}\r\n{"
        ];
        $filePath = (storage_path('app/public/Assets/resource_inventory.json'));
        $jsonFix = str_replace($array, "},{", file_get_contents($filePath));
        $data['jsonFile'] = json_decode('[' . $jsonFix . ']');
        $json = $data['jsonFile'];
        $chunkedData =  array_chunk($json ,200);
        foreach ($chunkedData as $key => $value) {
            dispatch(new StoreJsonFile($chunkedData[$key]));
        }

        return 'done';
    }
    
    public function store()
    {
        $array = [
            '}
{',
            "}\r\n{"
        ];
        $path = resource_path('temp');
        $files = glob("$path/*json");
        // dd($files);
        foreach ($files as $key => $file) {
        $filePath = $path."/"."temp".$key.".json";
        $jsonFix = str_replace($array, "},{", file_get_contents($filePath));
        }

        return $files;
    }
}
