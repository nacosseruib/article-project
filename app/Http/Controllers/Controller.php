<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * @OA\Info(
     *   title="Article Website",
     *   version="1.0.0",
     *   description="This is an API for data and serve the data in JSON format",
     *   @OA\Contact(
     *     email="samsontopeajax@gmail.com",
     *     name="Developer"
     *   )
     * )
     */
}
