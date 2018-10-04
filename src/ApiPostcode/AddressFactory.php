<?php

namespace DevMob\Postcodes\Providers\ApiPostcode;

use Adbar\Dot;
use DevMob\Postcodes\Address\Address;
use DevMob\Postcodes\Traits\AccessesProperties;

class AddressFactory
{
    use AccessesProperties;

    /**
     * Create an instance of Address.
     *
     * @param  array $data
     * @return \DevMob\Postcodes\Address\Address
     */
    public function create(array $data): Address
    {
        $data = new Dot($data);

        return new Address(
            $this->get($data, 'postcode'),
            $this->get($data, 'house_number'),
            $this->get($data, 'street'),
            $this->get($data, 'city'),
            $this->get($data, 'province'),
            $this->get($data, 'latitude'),
            $this->get($data, 'longitude')
        );
    }
}
