<?php

namespace rootcause0\LaravelFilepond\Http\Controllers;

use App\Models\UnitAttachment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use rootcause0\LaravelFilepond\Filepond;

class FilepondController extends BaseController
{
    /**
     * @var Filepond
     */
    private $filepond;

    public function __construct(Filepond $filepond)
    {
        $this->filepond = $filepond;
    }

    /**
     * Uploads the file to the temporary directory
     * and returns an encrypted path to the file
     *
     * @param  Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        $damaged_photo = array();
        $odometer_photo = array();
        $interior_photo = array();

        if(isset($request->damaged_photo)) {
            foreach ($request->damaged_photo as $file) {
                array_push($damaged_photo, $file);
            }
        }
        if(isset($request->odometer_photo)) {
            foreach ($request->odometer_photo as $file) {
                array_push($odometer_photo, $file);
            }
        }
        if(isset($request->interior_photo)) {
            foreach ($request->interior_photo as $file) {
                array_push($interior_photo, $file);
            }
        }




        $path = config('filepond.temporary_files_path', 'filepond');
        $disk = config('filepond.temporary_files_disk', 'local');
        foreach($damaged_photo as $file)
		{
        if (! ($newFile = $file->storeAs($path . DIRECTORY_SEPARATOR . Str::random(), $file->getClientOriginalName(), $disk)))
        {
            return Response::make('Could not save file', 500, [
                'Content-Type' => 'text/plain',
            ]);
         }
	    }
        foreach($odometer_photo as $file)
        {
            if (! ($newFile = $file->storeAs($path . DIRECTORY_SEPARATOR . Str::random(), $file->getClientOriginalName(), $disk)))
            {
                return Response::make('Could not save file', 500, [
                    'Content-Type' => 'text/plain',
                ]);
            }
        }
        foreach($interior_photo as $file)
        {
            if (! ($newFile = $file->storeAs($path . DIRECTORY_SEPARATOR . Str::random(), $file->getClientOriginalName(), $disk)))
            {
                return Response::make('Could not save file', 500, [
                    'Content-Type' => 'text/plain',
                ]);
            }
        }
        return Response::make($this->filepond->getServerIdFromPath(Storage::disk($disk)->path($newFile)), 200, [
            'Content-Type' => 'text/plain',
        ]);
    }

    /**
     * Takes the given encrypted filepath and deletes
     * it if it hasn't been tampered with
     *
     * @param  Request $request
     *
     * @return mixed
     */
    public function delete(Request $request)
    {

        $seperateUrl = explode('/',$request->source);
        $onlyFileName = end($seperateUrl);
        UnitAttachment::where('path', 'LIKE', '%'.$onlyFileName.'%')->delete();
        if (unlink(storage_path('\app\public\admission\\'.$onlyFileName)))
         {
            return Response::make('', 200, [
                'Content-Type' => 'text/plain',
            ]);
         }

        return Response::make('', 500, [
            'Content-Type' => 'text/plain',
        ]);
    }
}
