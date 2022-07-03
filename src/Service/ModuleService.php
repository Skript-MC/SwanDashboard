<?php

namespace App\Service;

use App\Document\SwanModule;
use App\Repository\SwanModuleRepository;

class ModuleService
{

    private SwanModuleRepository $moduleRepository;

    public function __construct(SwanModuleRepository $moduleRepository)
    {
        $this->moduleRepository = $moduleRepository;
    }

    public function getDisabledModules(): array
    {
        $disabledModules = $this->moduleRepository->disabledModules();
        $modules = [];
        foreach ($disabledModules as $module) {
            if (array_key_exists($module->getStore(), $modules)) {
                $modules[$module->getStore()][] = $module;
            } else {
                $modules[$module->getStore()] = [$module];
            }
        }
        return $modules;
    }

    public function getModules(): array
    {
        $disabledModules = $this->moduleRepository->findAll();
        $modules = [];
        foreach ($disabledModules as $module) {
            if (array_key_exists($module->getStore(), $modules)) {
                $modules[$module->getStore()][] = $module;
            } else {
                $modules[$module->getStore()] = [$module];
            }
        }
        return $modules;
    }

    public function toggleModule(string $moduleId, bool $status): ?SwanModule
    {
        return $this->moduleRepository->changeModuleStatus($moduleId, $status);
    }

}
