<?php

namespace App\Http\Controllers;

use App\Services\GetGeoDataService;
use Illuminate\Http\Request;

class GeoDataController extends Controller
{
    /**
     * @var GetGeoDataService
     */
    protected $getGeoDataService;

    /**
     * @param GetGeoDataService $getGeoDataService
     */
    public function __construct(GetGeoDataService $getGeoDataService)
    {
        $this->getGeoDataService = $getGeoDataService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGeoData(Request $request)
    {
        $countryName = $request->get('country_name');

        $result = $this->getGeoDataService->getCountryData($countryName);

        return response()->json([
            'status' => $result ? config('messages.success_status') : config('messages.error_status'),
            'data' => $result,
            'message' => $result ? config('messages.success_message') : config('messages.error_message')
        ]);
    }
}
