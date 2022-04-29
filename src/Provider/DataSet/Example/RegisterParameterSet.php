<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Provider\DataSet\Example;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * @property-read string $sld Second-level domain segment
 * @property-read string $tld Top-level domain segment
 * @property-read integer $renew_years Domain name registration period in years
 * @property-read string|null $admin_contact_id Id of admin contact object
 * @property-read string|null $billing_contact_id Id of billing contact object
 * @property-read string|null $tech_contact_id Id of tech contact object
 * @property-read string|null $registrant_id Id of registrant object
 * @property-read Registrant|null $registrant Registrant data
 * @property-read NameServer|null $ns1 Nameserver data
 * @property-read NameServer|null $ns2 Nameserver data
 * @property-read NameServer|null $ns3 Nameserver data
 * @property-read NameServer|null $ns4 Nameserver data
 * @property-read NameServer|null $ns5 Nameserver data
 */
class RegisterParameterSet extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'sld' => ['required', 'alpha-dash'],
            'tld' => ['required', 'alpha-dash-dot'],
            'renew_years' => ['required', 'integer'],
            'admin_contact_id' => ['string'],
            'billing_contact_id' => ['string'],
            'tech_contact_id' => ['string'],
            'registrant_id' => ['required_without:registrant', 'string'],
            'registrant' => ['required_without:registrant_id', Registrant::class],
            'ns1' => [NameServer::class],
            'ns2' => [NameServer::class],
            'ns3' => [NameServer::class],
            'ns4' => [NameServer::class],
            'ns5' => [NameServer::class],
        ]);
    }
}
