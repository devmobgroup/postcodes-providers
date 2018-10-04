<?php

namespace DevMob\Postcodes\Providers\Tests\ApiPostcode;

use DevMob\Postcodes\Exceptions\BadRequestException;
use DevMob\Postcodes\Exceptions\NoSuchCombinationException;
use DevMob\Postcodes\Providers\ApiPostcode\ApiPostcode;
use DevMob\Postcodes\Providers\Tests\TestCase;
use InvalidArgumentException;

class ApiPostcodeTest extends TestCase
{
    const VALID_CONFIG = [
        'token' => 'VALID_API_TOKEN',
    ];

    const POSTCODE = '3011ED';
    const NUMBER = '50';

    public function test_status_200_ok()
    {
        // Arrange
        $provider = new ApiPostcode(self::VALID_CONFIG);
        $client = $this->createClientMock(200, __DIR__ . '/mocks/200_ok.json');
        $provider->setClient($client);

        // Act
        $actual = $provider->lookup(self::POSTCODE, self::NUMBER);

        // Assert
        $this->assertCount(1, $actual);
        $this->assertEquals('3011ED', $actual[0]->getPostcode());
        $this->assertEquals('50', $actual[0]->getHouseNumber());
        $this->assertEquals('Schiedamsedijk', $actual[0]->getStreet());
        $this->assertEquals('Rotterdam', $actual[0]->getCity());
        $this->assertEquals('Zuid-Holland', $actual[0]->getProvince());
        $this->assertEquals(51.9165168, $actual[0]->getLatitude());
        $this->assertEquals(4.4815226, $actual[0]->getLongitude());
    }

    public function test_status_400_bad_request_number()
    {
        // Expect
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Cannot resolve address for postcode: ' . self::POSTCODE);

        // Arrange
        $provider = new ApiPostcode(self::VALID_CONFIG);
        $client = $this->createClientMock(400, __DIR__ . '/mocks/400_bad_request_number.json');
        $provider->setClient($client);

        // Act
        $provider->lookup(self::POSTCODE, self::NUMBER);
    }

    public function test_status_400_bad_request_postcode()
    {
        // Expect
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Given postcode incorrect');

        // Arrange
        $provider = new ApiPostcode(self::VALID_CONFIG);
        $client = $this->createClientMock(400, __DIR__ . '/mocks/400_bad_request_postcode.json');
        $provider->setClient($client);

        // Act
        $provider->lookup(self::POSTCODE, self::NUMBER);
    }

    public function test_status_401_unauthorized()
    {
        // Expect
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('access denied');

        // Arrange
        $config = [
            'token' => 'INVALID_API_KEY',
        ];
        $provider = new ApiPostcode($config);
        $client = $this->createClientMock(401, __DIR__ . '/mocks/401_unauthorized.json');
        $provider->setClient($client);

        // Act
        $provider->lookup(self::POSTCODE, self::NUMBER);
    }

    public function test_status_404_not_found()
    {
        // Expect
        $this->expectException(NoSuchCombinationException::class);

        // Arrange
        $provider = new ApiPostcode(self::VALID_CONFIG);
        $client = $this->createClientMock(404, __DIR__ . '/mocks/404_not_found.json');
        $provider->setClient($client);

        // Act
        $provider->lookup(self::POSTCODE, self::NUMBER);
    }

    public function test_no_config_given()
    {
        // Expect
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Configuration is missing the \'token\' key.');

        // Arrange
        $config = [];
        $provider = new ApiPostcode($config);

        // Act
        $provider->lookup(self::POSTCODE, self::NUMBER);
    }
}
