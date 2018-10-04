<?php

namespace DevMob\Postcodes\Providers\PostcodeApiNu;

use DevMob\Postcodes\Address\Address as BaseAddress;

class Address extends BaseAddress
{
    /**
     * @var int
     */
    private $year;

    /**
     * @var string|null
     */
    private $letter;

    /**
     * @var string|null
     */
    private $addition;

    /**
     * @var int
     */
    private $surface;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $purpose;

    /**
     * Create a copy of this address and set the year attribute.
     *
     * @param  int $year
     * @return \DevMob\Postcodes\Providers\PostcodeApiNu\Address
     */
    public function withYear(int $year): self
    {
        $address = clone $this;
        $address->year = $year;

        return $address;
    }

    /**
     * Create a copy of this address and set the letter attribute.
     *
     * @param  string|null $letter
     * @return \DevMob\Postcodes\Providers\PostcodeApiNu\Address
     */
    public function withLetter(?string $letter): self
    {
        $address = clone $this;
        $address->letter = $letter;

        return $address;
    }

    /**
     * Create a copy of this address and set the addition attribute.
     *
     * @param  string|null $addition
     * @return \DevMob\Postcodes\Providers\PostcodeApiNu\Address
     */
    public function withAddition(?string $addition): self
    {
        $address = clone $this;
        $address->addition = $addition;

        return $address;
    }

    /**
     * Create a copy of this address and set the surface attribute.
     *
     * @param  int $surface
     * @return \DevMob\Postcodes\Providers\PostcodeApiNu\Address
     */
    public function withSurface(int $surface): self
    {
        $address = clone $this;
        $address->surface = $surface;

        return $address;
    }

    /**
     * Create a copy of this address and set the type attribute.
     *
     * @param  string $type
     * @return \DevMob\Postcodes\Providers\PostcodeApiNu\Address
     */
    public function withType(string $type): self
    {
        $address = clone $this;
        $address->type = $type;

        return $address;
    }

    /**
     * Create a copy of this address and set the purpose attribute.
     *
     * @param  string $purpose
     * @return \DevMob\Postcodes\Providers\PostcodeApiNu\Address
     */
    public function withPurpose(string $purpose): self
    {
        $address = clone $this;
        $address->purpose = $purpose;

        return $address;
    }

    /**
     * Year of construction.
     *
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * House number letter.
     *
     * @return string|null
     */
    public function getLetter(): ?string
    {
        return $this->letter;
    }

    /**
     * House number addition.
     *
     * @return string|null
     */
    public function getAddition(): ?string
    {
        return $this->addition;
    }

    /**
     * Surface area.
     *
     * @return int
     */
    public function getSurface(): int
    {
        return $this->surface;
    }

    /**
     * Address type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Address' purpose.
     *
     * @return string
     */
    public function getPurpose(): string
    {
        return $this->purpose;
    }
}
