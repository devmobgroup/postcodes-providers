<?php

namespace DevMob\Postcodes\Providers\ApiPostcode;

use DevMob\Postcodes\Exceptions\BadRequestException;
use DevMob\Postcodes\Exceptions\BadResponseException;
use DevMob\Postcodes\Exceptions\JsonException;
use DevMob\Postcodes\Exceptions\NoSuchCombinationException;
use DevMob\Postcodes\Providers\HttpProvider;
use DevMob\Postcodes\Util\Json;
use DevMob\Postcodes\Util\Uri;
use GuzzleHttp\Psr7\Request;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @link https://api-postcode.nl/documentation.html
 */
class ApiPostcode extends HttpProvider
{
    protected const HOST = 'http://json.api-postcode.nl';

    /**
     * @var array
     */
    private $config;

    /**
     * @var \DevMob\Postcodes\Providers\ApiPostcode\AddressFactory
     */
    private $addressFactory;

    /**
     * Constructor.
     *
     * @param array $config
     * @param \DevMob\Postcodes\Providers\ApiPostcode\AddressFactory|null $addressFactory
     */
    public function __construct(array $config, ?AddressFactory $addressFactory = null)
    {
        $this->validateConfig($config);

        $this->config = $config;
        $this->addressFactory = $addressFactory ?: new AddressFactory();
    }

    /**
     * Create http lookup request.
     *
     * @param  array $input
     * @return \Psr\Http\Message\RequestInterface
     */
    protected function request(array $input): RequestInterface
    {
        $query = [
            'postcode' => $input['postcode'],
            'number' => $input['number']
        ];

        $uri = Uri::create(self::HOST, $query);

        $headers = [
            'token' => $this->config['token'],
        ];

        return new Request('GET', $uri, $headers);
    }

    /**
     * Parse http lookup response.
     *
     * @param  \Psr\Http\Message\ResponseInterface $response
     * @param  array $input
     * @return \DevMob\Postcodes\Address\Address[]
     * @throws \DevMob\Postcodes\Exceptions\NoSuchCombinationException
     * @throws \DevMob\Postcodes\Exceptions\ProviderException
     */
    protected function parse(ResponseInterface $response, array $input): array
    {
        switch ($response->getStatusCode()) {
            case 400:
            case 401:
                throw new BadRequestException($this->getError($response));
            case 404:
                throw new NoSuchCombinationException($input['postcode'], $input['number']);
        }

        try {
            $decoded = Json::decode($response);
        } catch (JsonException $e) {
            throw new BadResponseException('Received a malformed JSON response.', $response, $e);
        }

        return [$this->addressFactory->create($decoded)];
    }

    /**
     * Validate configuration.
     *
     * @param  array $config
     * @return void
     */
    private function validateConfig(array $config): void
    {
        if (! isset($config['token'])) {
            throw new InvalidArgumentException('Configuration is missing the \'token\' key.');
        }
    }

    /**
     * Get the error message from the response.
     *
     * @param  \Psr\Http\Message\ResponseInterface $response
     * @return string
     */
    private function getError(ResponseInterface $response): string
    {
        $default = 'Unknown error: no error message in response.';

        try {
            return Json::decode($response)['error'] ?? $default;
        } catch (JsonException $e) {
            return $default;
        }
    }
}
