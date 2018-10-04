<?php

namespace DevMob\Postcodes\Providers\PostcodeApiNu;

use DevMob\Postcodes\Exceptions\BadRequestException;
use DevMob\Postcodes\Exceptions\BadResponseException;
use DevMob\Postcodes\Exceptions\JsonException;
use DevMob\Postcodes\Exceptions\NoSuchCombinationException;
use DevMob\Postcodes\Providers\HttpProvider;
use DevMob\Postcodes\Providers\PostcodeApiNu\Exceptions\RequestLimitExceededException;
use DevMob\Postcodes\Util\Json;
use DevMob\Postcodes\Util\Uri;
use GuzzleHttp\Psr7\Request;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @link https://www.postcodeapi.nu/docs/
 */
class PostcodeApiNu extends HttpProvider
{
    protected const HOST = 'https://api.postcodeapi.nu/v2/addresses';

    /**
     * @var array
     */
    private $config;

    /**
     * @var \DevMob\Postcodes\Providers\PostcodeApiNu\AddressFactory
     */
    private $addressFactory;

    /**
     * Constructor.
     *
     * @param  array $config
     * @param \DevMob\Postcodes\Providers\PostcodeApiNu\AddressFactory|null $addressFactory
     */
    public function __construct(array $config, ?AddressFactory $addressFactory = null)
    {
        $this->config = $config;
        $this->addressFactory = $addressFactory ?: new AddressFactory();

        $this->validateConfig();
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
            'number' => $input['number'],
        ];

        $host = isset($this->config['host']) ? $this->config['host'] : self::HOST;
        $uri = Uri::create($host, $query);

        $headers = [
            'X-Api-Key' => $this->config['key'],
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
            case 403:
                throw new BadRequestException($this->getError($response));
            case 429:
                throw new RequestLimitExceededException($this->getError($response));
        }

        try {
            $decoded = Json::decode($response);
        } catch (JsonException $e) {
            throw new BadResponseException('Received a malformed JSON response.', $response, $e);
        }

        if (! isset($decoded['_embedded']['addresses'])) {
            throw new BadResponseException('Missing addresses array in response.', $response);
        }

        if (count($decoded['_embedded']['addresses']) === 0) {
            throw new NoSuchCombinationException($input['postcode'], $input['number']);
        }

        $addresses = [];
        foreach ($decoded['_embedded']['addresses'] as $address) {
            $addresses[] = $this->addressFactory->create($address);
        }

        return $addresses;
    }

    /**
     * Validate configuration.
     *
     * @return void
     */
    private function validateConfig(): void
    {
        if (! isset($this->config['key'])) {
            throw new InvalidArgumentException('Configuration is missing the \'key\' key.');
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
