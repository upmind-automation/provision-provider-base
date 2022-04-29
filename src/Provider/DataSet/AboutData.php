<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Provider\DataSet;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;

/**
 * @property-read string $name Name of the provision category or provider
 * @property-read string $description Description of the provision category or provider
 */
class AboutData extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'name' => ['string', 'required', 'max:50'],
            'description' => ['string', 'required', 'max:300'],
        ]);
    }

    /**
     * @return self $this
     */
    public function setName($name): AboutData
    {
        $this->setValue('name', $name);

        return $this;
    }

    /**
     * @return self $this
     */
    public function setDescription($description): AboutData
    {
        $this->setValue('description', $description);

        return $this;
    }
}
