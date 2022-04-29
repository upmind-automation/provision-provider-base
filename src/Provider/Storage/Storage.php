<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Provider\Storage;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Str;

/**
 * Filesystem decorator which enforces a strict base directory path and en/decrypts
 * objects with a secret key.
 */
final class Storage implements Filesystem
{
    /**
     * @var string
     */
    private const SECRET_KEY_CIPHER = 'AES-256-CBC';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Base directory/path, containing leading and trailing slash.
     *
     * @var string
     */
    private $basePath;

    /**
     * @var Encrypter
     */
    private $crypto;

    /**
     * Generate a random secret key.
     *
     * @return string Base64-encoded secret key
     */
    public static function generateRandomKey()
    {
        return base64_encode(Encrypter::generateKey(self::SECRET_KEY_CIPHER));
    }

    /**
     * @param Filesystem $filesystem Filesystem instance
     * @param string $basePath Base directory/path
     * @param string $secretKey Base64-encoded secret key used to en/decrypt files
     */
    public function __construct(Filesystem $filesystem, string $basePath, string $secretKey)
    {
        $this->filesystem = $filesystem;
        $this->basePath = sprintf('/%s/', trim($basePath, " \t\n\r\0\x0B/")); //trim whitespace, add slashes
        $this->crypto = new Encrypter(base64_decode($secretKey), self::SECRET_KEY_CIPHER);

        $this->filesystem->makeDirectory($this->basePath);
    }

    /**
     * Get the given path as an absolute path starting from the instance $basePath.
     *
     * @param string|string[] $path Single path or array of paths
     *
     * @return string|string[]
     */
    private function getFullPath($path)
    {
        if (is_iterable($path)) {
            $return = [];

            foreach ($path as $i => $p) {
                $return[$i] = $this->getFullPath($p);
            }

            return $return;
        }

        return Str::start($this->normalizeSubPath($path), $this->basePath);
    }

    /**
     * Get the given full path as a relative path by removing the instance $basePath.
     *
     * @param string|string[] $path Single path or array of paths
     *
     * @return string|string[]
     */
    private function getRelativePath($path)
    {
        if (is_iterable($path)) {
            $return = [];

            foreach ($path as $i => $p) {
                $return[$i] = $this->getRelativePath($p);
            }

            return $return;
        }

        $pattern = '/^' . preg_quote(ltrim($this->basePath, '/'), '/') . '/';

        return preg_replace($pattern, '', $path);
    }

    /**
     * Remove leading slash and resolve "double-dots" (../) up to the sub-path root
     * so when prepended by the base path, it's not possible to traverse outside
     * of the instance's $basePath.
     *
     * @param string $path
     *
     * @return string
     */
    private function normalizeSubPath($path): string
    {
        $path = ltrim((string)$path, '/'); //remove leading slash
        $parts = array_values(array_filter(explode('/', $path))); //get parts, filter out double-slashes

        foreach ($parts as $i => $part) {
            if ($part === '..') {
                // remove the previous part
                $parent = $i - 1;

                while ($parent >= 0 && !array_key_exists($parent, $parts)) {
                    $parent -= 1;
                }

                unset($parts[$i]); // remove this part
                unset($parts[$parent]); // remove parent part
            }
        }

        return implode('/', $parts);
    }

    /**
     * Get the entire content from a stream as a string.
     *
     * @param resource|string|null $stream
     */
    private function getStreamAsString($stream): ?string
    {
        if (is_null($stream) || is_string($stream)) {
            return $stream;
        }

        return stream_get_contents($stream);
    }

    /**
     * Get the given string as a stream.
     *
     * @param string|null $string
     *
     * @return resource
     */
    private function getStringAsStream($string)
    {
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, (string)$string);
        rewind($stream);

        return $stream;
    }

    /**
     * Encrypt a string or file resource handle.
     *
     * @param string $data Plaintext
     *
     * @return string Ciphertext
     */
    private function encrypt($data)
    {
        if (is_null($data) || $data === '') {
            return '';
        }

        if (is_resource($data)) {
            return $this->getStringAsStream(
                $this->crypto->encrypt(
                    $this->getStreamAsString($data)
                )
            );
        }

        return $this->crypto->encryptString($data);
    }

    /**
     * Decrypt a string or file resource handle.
     *
     * @param string|resource $data Ciphertext
     *
     * @return string|resource Plaintext
     */
    private function decrypt($data)
    {
        if (is_null($data) || $data === '') {
            return '';
        }

        if (is_resource($data)) {
            return $this->getStringAsStream(
                $this->crypto->decryptString(
                    $this->getStreamAsString($data)
                )
            );
        }

        return $this->crypto->decryptString($data);
    }

    /**
     * Determine if a file exists.
     *
     * @param  string  $path
     * @return bool
     */
    public function exists($path)
    {
        return $this->filesystem->exists(
            $this->getFullPath($path)
        );
    }

    /**
     * Get the contents of a file.
     *
     * @param  string  $path
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function get($path)
    {
        return $this->decrypt(
            $this->filesystem->get(
                $this->getFullPath($path)
            )
        );
    }

    /**
     * Get a resource to read the file.
     *
     * @param  string  $path
     * @return resource|null The path resource or null on failure.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function readStream($path)
    {
        $stream = $this->filesystem->readStream(
            $this->getFullPath($path)
        );

        return $this->decrypt($stream);
    }

    /**
     * Write the contents of a file.
     *
     * @param  string  $path
     * @param  string|resource  $contents
     * @param  mixed  $options
     * @return bool
     */
    public function put($path, $contents, $options = [])
    {
        return $this->filesystem->put(
            $this->getFullPath($path),
            $this->encrypt($contents),
            $options
        );
    }

    /**
     * Write a new file using a stream.
     *
     * @param  string  $path
     * @param  resource  $resource
     * @param  array  $options
     * @return bool
     *
     * @throws \InvalidArgumentException If $resource is not a file handle.
     * @throws \Illuminate\Contracts\Filesystem\FileExistsException
     */
    public function writeStream($path, $resource, array $options = [])
    {
        return $this->put($path, $resource, $options);
    }

    /**
     * Get the visibility for the given path.
     *
     * @param  string  $path
     * @return string
     */
    public function getVisibility($path)
    {
        return $this->filesystem->getVisibility(
            $this->getFullPath($path)
        );
    }

    /**
     * Set the visibility for the given path.
     *
     * @param  string  $path
     * @param  string  $visibility
     * @return bool
     */
    public function setVisibility($path, $visibility)
    {
        return $this->filesystem->setVisibility(
            $this->getFullPath($path),
            $visibility
        );
    }

    /**
     * Prepend to a file.
     *
     * @param  string  $path
     * @param  string  $data
     * @return bool
     */
    public function prepend($path, $data)
    {
        return $this->filesystem->put(
            $this->getFullPath($path),
            $data . $this->get($path)
        );
    }

    /**
     * Append to a file.
     *
     * @param  string  $path
     * @param  string  $data
     * @return bool
     */
    public function append($path, $data)
    {
        return $this->filesystem->put(
            $this->getFullPath($path),
            $this->get($path) . $data
        );
    }

    /**
     * Delete the file at a given path.
     *
     * @param  string|array  $paths
     * @return bool
     */
    public function delete($paths)
    {
        return $this->filesystem->delete(
            $this->getFullPath($paths)
        );
    }

    /**
     * Copy a file to a new location.
     *
     * @param  string  $from
     * @param  string  $to
     * @return bool
     */
    public function copy($from, $to)
    {
        return $this->filesystem->copy(
            $this->getFullPath($from),
            $this->getFullPath($to)
        );
    }

    /**
     * Move a file to a new location.
     *
     * @param  string  $from
     * @param  string  $to
     * @return bool
     */
    public function move($from, $to)
    {
        return $this->filesystem->move(
            $this->getFullPath($from),
            $this->getFullPath($to)
        );
    }

    /**
     * Get the file size of a given file.
     *
     * @param  string  $path
     * @return int
     */
    public function size($path)
    {
        return $this->filesystem->size(
            $this->getFullPath($path)
        );
    }

    /**
     * Get the file's last modification time.
     *
     * @param  string  $path
     * @return int
     */
    public function lastModified($path)
    {
        return $this->filesystem->lastModified(
            $this->getFullPath($path)
        );
    }

    /**
     * Get an array of all files in a directory.
     *
     * @param  string|null  $directory
     * @param  bool  $recursive
     * @return array
     */
    public function files($directory = null, $recursive = false)
    {
        return $this->getRelativePath(
            $this->filesystem->files(
                $this->getFullPath($directory),
                $recursive
            )
        );
    }

    /**
     * Get all of the files from the given directory (recursive).
     *
     * @param  string|null  $directory
     * @return array
     */
    public function allFiles($directory = null)
    {
        return $this->getRelativePath(
            $this->filesystem->allFiles(
                $this->getFullPath($directory)
            )
        );
    }

    /**
     * Get all of the directories within a given directory.
     *
     * @param  string|null  $directory
     * @param  bool  $recursive
     * @return array
     */
    public function directories($directory = null, $recursive = false)
    {
        return $this->getRelativePath(
            $this->filesystem->directories(
                $this->getFullPath($directory),
                $recursive
            )
        );
    }

    /**
     * Get all (recursive) of the directories within a given directory.
     *
     * @param  string|null  $directory
     * @return array
     */
    public function allDirectories($directory = null)
    {
        return $this->getRelativePath(
            $this->filesystem->allDirectories(
                $this->getFullPath($directory)
            )
        );
    }

    /**
     * Create a directory.
     *
     * @param  string  $path
     * @return bool
     */
    public function makeDirectory($path)
    {
        return $this->filesystem->makeDirectory(
            $this->getFullPath($path)
        );
    }

    /**
     * Recursively delete a directory.
     *
     * @param  string  $directory
     * @return bool
     */
    public function deleteDirectory($directory)
    {
        return $this->filesystem->deleteDirectory(
            $this->getFullPath($directory)
        );
    }
}
