<?php

namespace App\Services;

use GuzzleHttp\Client;

class GetGeoDataService
{
    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @param Client $httpClient
     */
    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param $countryName
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCountryData($countryName)
    {
        $result = [];

        $headers = $this->getRequestHeaders();

        $countryData = $this->requestExternalApi(
            'GET',
            '/countries?namePrefix=' . $countryName . '&limit=10',
            $headers
        );

        if ($countryData && $countryData['data']) {
            $countryInfo = $countryData['data'][0];
            $result['country_name'] = $countryInfo['name'];
            $result['country_details']['country_code'] = $countryInfo['code'];
            $countryWikiDataId = $countryInfo['wikiDataId'];

            $countryDetailsData = $this->requestExternalApi(
                'GET',
                '/countries/' . $result['country_details']['country_code'],
                $headers
            );

            if ($countryDetailsData && $countryDetailsData['data']) {
                $countryDetails = $countryDetailsData['data'];
                $result['country_details']['currency'] = $countryDetails['currencyCodes'][0];
                $result['country_details']['flag_image'] = $countryDetails['flagImageUri'];
                $result['capital_city_name'] = $countryDetails['capital'];

                $capitalCityData = $this->requestExternalApi(
                    'GET',
                    '/cities?countryIds=' . $countryWikiDataId . '&namePrefix=' . $result['capital_city_name'],
                    $headers
                );

                if ($capitalCityData && $capitalCityData['data']) {
                    $capitalCityInfo = $capitalCityData['data'][0];
                    $result['capital_city_details']['region'] = $capitalCityInfo['region'];
                    $result['capital_city_details']['region_code'] = $capitalCityInfo['regionCode'];
                    $result['capital_city_details']['latitude'] = $capitalCityInfo['latitude'];
                    $result['capital_city_details']['longitude'] = $capitalCityInfo['longitude'];
                    $result['capital_city_details']['population'] = $capitalCityInfo['population'];
                    $capitalCityWikiDataId = $capitalCityInfo['wikiDataId'];

                    $capitalCityDetailsData = $this->requestExternalApi(
                        'GET',
                        '/cities/' . $capitalCityWikiDataId,
                        $headers
                    );

                    if ($capitalCityDetailsData && $capitalCityDetailsData['data']) {
                        $capitalCityDetails = $capitalCityDetailsData['data'];
                        $result['capital_city_details']['elevation'] = $capitalCityDetails['elevationMeters'] ?? 0;

                        $placesNearCityData = $this->requestExternalApi(
                            'GET',
                            '/places/' . $capitalCityDetails->id . '/nearbyPlaces?radius=' . config('system-settings.geo_near_city_radius'),
                            $headers
                        );

                        if ($placesNearCityData && $placesNearCityData['data']) {
                            foreach ($placesNearCityData['data'] as $nearPlaces) {
                                $result['places_near_city'][] = $nearPlaces['name'];
                            }
                        } else {
                            $result = null;
                        }
                    } else {
                        $result = null;
                    }
                } else {
                    $result = null;
                }
            } else {
                $result = null;
            }
        } else {
            $result = null;
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getRequestHeaders()
    {
        return [
            'X-RapidAPI-Host' => config('system-settings.x_rapid_api_host'),
            'X-RapidAPI-Key' => config('system-settings.x_rapid_api_key'),
        ];
    }

    /**
     * @param $method
     * @param $endpoint
     * @param $headers
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function requestExternalApi($method, $endpoint, $headers)
    {
        try {
            $response = $this->httpClient->request(
                $method,
                config('system-settings.geo_api_url') . $endpoint,
                ['headers' => $headers]
            );
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            return null;
        }
    }
}
