<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    /**
     * Response of api request
     *
     * @var array
     */
    protected $ApiResponse = [
        'status' => true,
        'message' => 'success',
        'data' => null
    ];

    protected $Client;
    /**
     * @var
     */
    protected $RequestValidate;

    /**
     * @param Client $Client
     */
    public function __construct(Client $Client)
    {
        $this->Client = $Client;
    }

    /**
     * @param $request
     * @return void
     */
    private function validateGetClientRequest($request): void
    {
        $this->RequestValidate = Validator::make($request->all(), [
            "email" => 'required|email'
        ]);

        if ($this->RequestValidate->fails()) {
            $this->ApiResponse['status'] = 'false';
            $this->ApiResponse['message'] = 'error';
            $this->ApiResponse['data'] = $this->RequestValidate->getMessageBag();
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getClient(Request $request): JsonResponse
    {
        try {
            $this->validateGetClientRequest($request);
            if($this->RequestValidate->fails()){
                return response()->json($this->ApiResponse, 200);
            }

            $email = $request['email'] ?? null;
            $client = $this->Client->getLoyaltyPointsClientByEmail($email);
            $this->ApiResponse['message'] = 'Email invalid or client disabled';
            if($client->count() > 0){
                $this->ApiResponse['message'] = 'success';
                $this->ApiResponse['data'] = $client->first();
            }
            return response()->json($this->ApiResponse, 200);
        } catch (\Exception $e) {
            Log::error("ERROR: ClientController::getClient -> {$e}");
            $this->ApiResponse['status'] = false;
            $this->ApiResponse['message'] = 'error';
            $this->ApiResponse['data'] = $e->getMessage();
            return response()->json($this->ApiResponse, 500);
        }
    }
}
