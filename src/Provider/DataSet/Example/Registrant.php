<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Provider\DataSet\Example;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * @property-read string|null $name Name of the individual
 * @property-read string|null $organisation Name of the organisation
 * @property-read string|null $email Contact email address
 * @property-read string|null $phone Contact phone number
 * @property-read string|null $city Address city
 * @property-read string|null $country_code Address country alpha-2 code
 * @property-read string|null $address1 Address line 1
 * @property-read string|null $postcode Address postal/zip code
 * @property-read string|null $contact_type Contact type
 * @property-read string|null $password Password
 */
class Registrant extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'name' => ['required_without:organisation', 'string'],
            'organisation' => ['required_without:name', 'string'],
            'email' => ['email'],
            'phone' => ['nullable', 'string', 'international_phone'],
            'city' => ['string'],
            'country_code' => ['string', 'size:2'],
            'address1' => ['string'],
            'postcode' => ['nullable', 'string'],
            'contact_type' => ['nullable', 'string'],
            'password' => ['nullable', 'string'],
        ]);
    }
}
