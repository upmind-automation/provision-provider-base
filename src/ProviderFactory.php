<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase;

use Illuminate\Contracts\Filesystem\Filesystem;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Upmind\ProvisionBase\Exception\InvalidDataSetException;
use Upmind\ProvisionBase\Exception\InvalidProviderConfigurationException;
use Upmind\ProvisionBase\Provider\Contract\HasSystemInfo;
use Upmind\ProvisionBase\Provider\Contract\LogsDebugData;
use Upmind\ProvisionBase\Provider\Contract\ProviderInterface;
use Upmind\ProvisionBase\Provider\Contract\StoresFiles;
use Upmind\ProvisionBase\Registry\Registry;
use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\StorageConfiguration;
use Upmind\ProvisionBase\Provider\DataSet\SystemInfo;
use Upmind\ProvisionBase\Provider\Storage\Storage;
use Upmind\ProvisionBase\Registry\Data\CategoryRegister;
use Upmind\ProvisionBase\Registry\Data\ProviderRegister;

/**
 * Factory class for creating Provider instance/register wrapper instances
 */
class ProviderFactory
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var SystemInfo
     */
    protected $systemInfo;

    /**
     * @param Registry $registry
     */
    public function __construct(
        Registry $registry,
        Filesystem $filesystem,
        LoggerInterface $logger,
        ?SystemInfo $systemInfo = null
    ) {
        $this->registry = $registry;
        $this->filesystem = $filesystem;
        $this->logger = $logger;
        $this->systemInfo = $systemInfo ?? $this->createSystemInfo();
    }

    /**
     * @param string|CategoryRegister $category Category identifier, class or register
     * @param string|ProviderRegister $provider Provider identifier, class or register
     * @param array|DataSet $providerConfiguration Provider configuration data
     * @param array|StorageConfiguration|null $storageConfiguration Storage configuration data
     *
     * @throws \Upmind\ProvisionBase\Exception\InvalidProviderConfigurationException If the given config data is invalid
     *
     * @return Provider Provider wrapper
     */
    public function create($category, $provider, $providerConfiguration, $storageConfiguration = null): Provider
    {
        if (!$categoryRegister = $this->registry->getCategory($category)) {
            throw new RuntimeException('Category not found');
        }

        if (!$providerRegister = $categoryRegister->getProvider($provider)) {
            throw new RuntimeException('Provider not found');
        }

        if (!$providerConfiguration instanceof DataSet) {
            $dataSetClass = $providerRegister->getConstructor()->getParameter()->getClass();
            /** @var DataSet $providerConfiguration */
            $providerConfiguration = $dataSetClass::create($providerConfiguration);
        }

        try {
            $providerConfiguration->validateIfNotYetValidated();
        } catch (InvalidDataSetException $e) {
            throw InvalidProviderConfigurationException::fromInvalidDataSet($e);
        }

        $providerClass = $providerRegister->getClass();
        /** @var ProviderInterface $providerInstance */
        $providerInstance = new $providerClass($providerConfiguration);

        if ($providerInstance instanceof LogsDebugData) {
            $providerInstance->setLogger($this->getLogger());
        }

        if ($providerInstance instanceof StoresFiles) {
            if (!$storageConfiguration instanceof StorageConfiguration) {
                $storageConfiguration = new StorageConfiguration($storageConfiguration ?? []);
            }
            $storageConfiguration->validateIfNotYetValidated();

            $providerInstance->setStorage($this->createStorage($storageConfiguration, $providerRegister));
        }

        if ($providerInstance instanceof HasSystemInfo) {
            $providerInstance->setSystemInfo($this->getSystemInfo());
        }

        return new Provider($providerRegister, $providerInstance);
    }

    /**
     * Create a storage instance using the given configuration for the given provider.
     * The Storage instance's path will be computed from the configuration base
     * path and the category and provider identifiers.
     */
    public function createStorage(StorageConfiguration $storeConfig, ProviderRegister $providerRegister): Storage
    {
        $path = sprintf(
            '%s/%s/%s',
            rtrim($storeConfig->base_path, '/'),
            $providerRegister->getCategory()->getIdentifier(),
            $providerRegister->getIdentifier()
        );

        return new Storage($this->filesystem, $path, $storeConfig->secret_key);
    }

    /**
     * Create default system info.
     */
    public function createSystemInfo(): SystemInfo
    {
        $outgoingIp = isset($_SERVER['SERVER_ADDR'])
            ? $_SERVER['SERVER_ADDR']
            : gethostbyname(gethostname() ?: php_uname('n'));

        return new SystemInfo([
            'outgoing_ips' => [$outgoingIp],
        ]);
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function setFilesystem(Filesystem $filesystem): void
    {
        $this->filesystem = $filesystem;
    }

    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    public function setSystemInfo(SystemInfo $systemInfo): void
    {
        $this->systemInfo = $systemInfo;
    }

    public function getSystemInfo(): SystemInfo
    {
        return $this->systemInfo;
    }
}
