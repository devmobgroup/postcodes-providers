<?php

namespace DevMob\Postcodes\Providers\Tests\PostcodeApiNu;

use DevMob\Postcodes\Exceptions\BadRequestException;
use DevMob\Postcodes\Exceptions\NoSuchCombinationException;
use DevMob\Postcodes\Providers\PostcodeApiNu\Address;
use DevMob\Postcodes\Providers\PostcodeApiNu\Exceptions\RequestLimitExceededException;
use DevMob\Postcodes\Providers\PostcodeApiNu\PostcodeApiNu;
use DevMob\Postcodes\Providers\Tests\TestCase;
use InvalidArgumentException;

class PostcodeApiNuTest extends TestCase
{
    const VALID_CONFIG = [
        'key' => 'VALID_API_KEY',
    ];

    const POSTCODE = '3011ED';
    const NUMBER = '50';

    public function test_status_200_ok()
    {
        // Arrange
        $provider = new PostcodeApiNu(self::VALID_CONFIG);
        $client = $this->createClientMock(200, __DIR__ . '/mocks/200_ok.json');
        $provider->setClient($client);

        // Act
        $actual = $provider->lookup(self::POSTCODE, self::NUMBER);
        $first = $actual[0];

        // Assert
        $this->assertCount(1, $actual);
        $this->assertInstanceOf(Address::class, $first);
        $this->assertEquals('3011ED', $first->getPostcode());
        $this->assertEquals('50', $first->getHouseNumber());
        $this->assertEquals('Schiedamsedijk', $first->getStreet());
        $this->assertEquals('Rotterdam', $first->getCity());
        $this->assertEquals('Zuid-Holland', $first->getProvince());
        $this->assertEquals(51.9165164, $first->getLatitude());
        $this->assertEquals(4.4815228, $first->getLongitude());

        $this->assertEquals(1954, $first->getYear());
        $this->assertEquals(null, $first->getLetter());
        $this->assertEquals(null, $first->getAddition());
        $this->assertEquals(301, $first->getSurface());
        $this->assertEquals('Verblijfsobject', $first->getType());
        $this->assertEquals('kantoorfunctie', $first->getPurpose());
    }

    public function test_status_200_ok_but_no_results()
    {
        // Expect
        $this->expectException(NoSuchCombinationException::class);

        // Arrange
        $provider = new PostcodeApiNu(self::VALID_CONFIG);
        $client = $this->createClientMock(200, __DIR__ . '/mocks/200_ok_but_no_results.json');
        $provider->setClient($client);

        // Act
        $provider->lookup(self::POSTCODE, self::NUMBER);
    }

    public function test_status_400_bad_request_number()
    {
        // Expect
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Query parameter \'number\' has to be numeric.');

        // Arrange
        $provider = new PostcodeApiNu(self::VALID_CONFIG);
        $client = $this->createClientMock(400, __DIR__ . '/mocks/400_bad_request_number.json');
        $provider->setClient($client);

        // Act
        $provider->lookup(self::POSTCODE, self::NUMBER);
    }

    public function test_status_400_bad_request_postcode()
    {
        // Expect
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Query parameter \'postcode\' has an invalid format.');

        // Arrange
        $provider = new PostcodeApiNu(self::VALID_CONFIG);
        $client = $this->createClientMock(400, __DIR__ . '/mocks/400_bad_request_postcode.json');
        $provider->setClient($client);

        // Act
        $provider->lookup(self::POSTCODE, self::NUMBER);
    }

    public function test_status_401_unauthorized()
    {
        // Expect
        $this->expectException(BadRequestException::class);

        // Arrange
        $config = [
            'key' => 'INVALID_API_KEY',
        ];
        $provider = new PostcodeApiNu($config);
        // API returns 403 but the docs says 401...
        $client = $this->createClientMock(401, __DIR__ . '/mocks/401_unauthorized.json');
        $provider->setClient($client);

        // Act
        $provider->lookup(self::POSTCODE, self::NUMBER);
    }

    public function test_status_429_too_many_requests()
    {
        // Expect
        $this->expectException(RequestLimitExceededException::class);

        // Arrange
        $provider = new PostcodeApiNu(self::VALID_CONFIG);
        $client = $this->createClientMock(429, __DIR__ . '/mocks/429_too_many_requests.json');
        $provider->setClient($client);

        // Act
        $provider->lookup(self::POSTCODE, self::NUMBER);
    }

    public function test_no_config_given()
    {
        // Expect
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Configuration is missing the \'key\' key.');

        // Arrange
        $config = [];
        $provider = new PostcodeApiNu($config);

        // Act
        $provider->lookup(self::POSTCODE, self::NUMBER);
    }
}
