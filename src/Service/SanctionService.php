<?php

namespace App\Service;

use App\Repository\SanctionRepository;
use Doctrine\ODM\MongoDB\DocumentManager;

class SanctionService
{
    private SanctionRepository $sanctionRepository;
    private DocumentManager $documentManager;

    public function __construct(SanctionRepository $sanctionRepository, DocumentManager $documentManager)
    {
        $this->sanctionRepository = $sanctionRepository;
        $this->documentManager = $documentManager;
    }


}
