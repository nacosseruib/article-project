<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Library\AnyFileUploadClass;
use Image;
use DB;

class ParentController extends Controller
{

    public function __construct()
    {

    }


    //Return Array of String/Numeric - Reuseable Image File Upload Module
    public function uploadAnyFile($file = null, $uploadCompletePathName = null, $maxFileSize = 10, $newExtension = null, $newRadFileName = true)
    {
         $data = new AnyFileUploadClass($file, $uploadCompletePathName, $maxFileSize, $newExtension, $newRadFileName);
         return $data->return();
     }//end function


     //Return String - Upload Path
     public function uploadPath()
     {
        return $this->getUploadPath = env('UPLOADPATHROOT', null);
     }

    //Return String - Download Path
    public function downloadPath()
    {
        return $this->getDownloadPath = env('DOWNLOADPATHROOT', null);
    }


    //Return Nothing : Void - Create Image Thumbnail after upload the image to a path
    public function createThumbnail($pathSource = null, $pathDestination = null, $width = 300, $height = 300, $is_resize_canvas = 0)
    {
        //Resize Image
        $resizeImageWidth    = ($width ? $width - ($width/4) : 0);
        $resizeImageHeight   = ($height ? $height - ($height/4) : 0);
        //Resize Canvas
        $resizeCanvasWidth    = ($is_resize_canvas == 1 ? $width : 0);
        $resizeCanvasHeight   = ($is_resize_canvas == 1 ? $height : 0);
        if($pathDestination != null)
        {
            try{
                //copy file
                ($pathSource ? copy($pathSource, $pathDestination) : null);

                //Resize Image with canvas
                $img = Image::make($pathDestination)->resize($resizeImageWidth,  $resizeImageHeight, function ($constraint) {
                    $constraint->aspectRatio();
                })->resizeCanvas($resizeCanvasWidth, $resizeCanvasHeight, 'center', false, '#ffffff');

                $img->save($pathDestination);

            }catch(\Throwable $errorThrown){

            }
        }
        return;
    }


    public function sendResponse($result, $message, $status = 200)
    {
    	$response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];
        //Log Task

        return response()->json($response, $status);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($errorMessages = [], $error, $status = 404)
    {
    	$response = [
            'success' => false,
            'message' => $error,
        ];
        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }
        //Log Task
        return response()->json($response, $status);
    }



}//end class
