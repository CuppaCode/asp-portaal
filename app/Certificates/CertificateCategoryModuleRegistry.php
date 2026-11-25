<?php

namespace App\Certificates;

use App\Certificates\Contracts\CertificateCategoryModule;
use App\Models\CertificateCategory;

class CertificateCategoryModuleRegistry
{
    /** @var array<string, CertificateCategoryModule> */
    private array $modules = [];

    public function __construct(array $modules, private CertificateCategoryModule $fallbackModule)
    {
        foreach ($modules as $module) {
            if ($module instanceof CertificateCategoryModule) {
                $this->modules[$module->key()] = $module;
            }
        }
    }

    public function resolveByCategory(?CertificateCategory $category): CertificateCategoryModule
    {
        if (!$category) {
            return $this->fallbackModule;
        }

        $code = strtolower(trim((string) $category->code));
        $name = strtolower(trim((string) $category->name));

        if (in_array($name, ['code 95', 'code95', 'code_95'], true)) {
            return $this->resolveByKey('code_95');
        }

        return $this->resolveByKey($code);
    }

    public function resolveByKey(?string $key): CertificateCategoryModule
    {
        if (!$key) {
            return $this->fallbackModule;
        }

        return $this->modules[$key] ?? $this->fallbackModule;
    }

    public function allKeys(): array
    {
        return array_keys($this->modules);
    }
}
