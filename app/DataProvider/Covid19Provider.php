<?php


namespace App\DataProvider;


use App\DataProvider\Contracts\DataProviderContract;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;

class Covid19Provider implements DataProviderContract
{
    /**
     * @var \GuzzleHttp\Client $client
     */
    private $client;

    /**
     * @var array
     */
    private $cachedResponses = [];

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.covid19api.com',
        ]);
    }

    /**
     * Parse the response.
     *
     * @param string $data
     * @return \Illuminate\Support\Collection
     */
    private function response(string $data)
    {
        $data = \GuzzleHttp\json_decode($data, true);

        return collect($data);
    }

    /**
     * @param string $uri
     * @param array $data
     * @param string $method
     * @return string
     * @throws GuzzleException
     */
    private function request($uri, $data = [], $method = 'GET')
    {
        $request = [];

        $request[$method == 'POST' ? 'json' : 'query'] = $data;

        $signature = $method . ':' . $uri;

        if (!empty($this->cachedResponses[$signature])) {
            return $this->cachedResponses[$signature];
        }

        return $this->cachedResponses[$signature] = $this->client->request(
            $method, $uri, $request
        )->getBody()->getContents();
    }

    public function countriesTotal(): array
    {
        $response = $this->response($this->request('summary'));

        return array_map(function ($item) {
            return [
                'country' => [
                    'name' => data_get($item, 'Country'),
                    'code' => data_get($item, 'CountryCode'),
                    'slug' => data_get($item, 'Slug'),
                ],
                'stats' => [
                    'new_confirmed' => (int)data_get($item, 'NewConfirmed'),
                    'total_confirmed' => (int)data_get($item, 'TotalConfirmed'),
                    'new_deaths' => (int)data_get($item, 'NewDeaths'),
                    'total_deaths' => (int)data_get($item, 'TotalDeaths'),
                    'new_recovered' => (int)data_get($item, 'NewRecovered'),
                    'total_recovered' => (int)data_get($item, 'TotalRecovered'),
                ],
                'date' => Carbon::parse(data_get($item, 'Date'))->format('Y-m-d H:i:s'),
            ];
        }, $response->get('Countries', []));
    }

    public function globalStats($country = null): array
    {
        $response = $this->response($this->request('summary'));

        $data = $response->get('Global');

        if ($country) {
            $data = Arr::first($response->get('Countries'), function ($item) use ($country) {
                return $item['CountryCode'] == $country;
            });
        }

        return [
            'new_confirmed' => data_get($data, 'NewConfirmed'),
            'total_confirmed' => data_get($data, 'TotalConfirmed'),
            'new_deaths' => data_get($data, 'NewDeaths'),
            'total_deaths' => data_get($data, 'TotalDeaths'),
            'new_recovered' => data_get($data, 'NewRecovered'),
            'total_recovered' => data_get($data, 'TotalRecovered'),
        ];
    }
}
