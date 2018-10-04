<?php

namespace DevMob\Postcodes\Providers\PostcodeApiNu;

use Adbar\Dot;
use DevMob\Postcodes\Traits\AccessesProperties;

class AddressFactory
{
    use AccessesProperties;

    /**
     * Convert data to an address instance.
     *
     * @param  array $data
     * @return \DevMob\Postcodes\Providers\PostcodeApiNu\Address
     */
    public function create(array $data): Address
    {
        $data = new Dot($data);
        $location = $this->get($data, 'geo.center.wgs84.coordinates');
        $houseNumber = $this->get($data, 'number') . $this->get($data, 'letter');

        $address = new Address(
            $this->get($data, 'postcode'),
            $houseNumber,
            $this->get($data, 'street'),
            $this->get($data, 'city.label'),
            $this->get($data, 'province.label'),
            $location[1],
            $location[0],
            $data->all()
        );

        return $address
            ->withYear($this->get($data, 'year'))
            ->withLetter($this->get($data, 'letter'))
            ->withAddition($this->get($data, 'addition'))
            ->withSurface($this->get($data, 'surface'))
            ->withType($this->get($data, 'type'))
            ->withPurpose($this->get($data, 'purpose'));
    }
}
