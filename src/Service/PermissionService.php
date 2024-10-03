<?php
namespace App\Service;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class PermissionService
{
    private string $permissionsFilePath;

    public function __construct(string $permissionsFilePath)
    {
        $this->permissionsFilePath = $permissionsFilePath;
    }

    public function loadPermissions(): array
    {
        $filesystem = new Filesystem();

        if (!$filesystem->exists($this->permissionsFilePath)) {
            throw new \RuntimeException('Permissions file does not exist');
        }

        $jsonContent = file_get_contents($this->permissionsFilePath);
        $permissions = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Invalid JSON format');
        }

        return $permissions;
    }
}
