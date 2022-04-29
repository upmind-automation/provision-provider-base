<?php

namespace Upmind\ProvisionBase\Provider\Contract;

use Upmind\ProvisionBase\Provider\DataSet\AboutData;
use Upmind\ProvisionBase\Provider\DataSet\DataSet;

/**
 * Root interface which all Provider classes must implement.
 *
 * Provider classes must extend from their parent Category and implement each of
 * their abstract methods.
 */
interface ProviderInterface extends CategoryInterface
{
    // This actually isn't possible because PHP wont allow covariant parameter types
    // /**
    //  * Providers always receive their configuration as an associative array
    //  * passed to their constructor.
    //  *
    //  * @param DataSet $configuration Assoc array of configuration params
    //  */
    // public function __construct(DataSet $configuration);

    /**
     * Returns information about the provider such as name + description.
     */
    public static function aboutProvider(): AboutData;
}
