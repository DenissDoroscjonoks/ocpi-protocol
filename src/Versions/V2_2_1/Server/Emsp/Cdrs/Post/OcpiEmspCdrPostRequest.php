<?php

declare(strict_types=1);

namespace Chargemap\OCPI\Versions\V2_2_1\Server\Emsp\Cdrs\Post;

use Chargemap\OCPI\Common\Server\OcpiUpdateRequest;
use Chargemap\OCPI\Common\Utils\PayloadValidation;
use Chargemap\OCPI\Versions\V2_2_1\Common\Factories\CdrFactory;
use Chargemap\OCPI\Versions\V2_2_1\Common\Models\Cdr;
use Psr\Http\Message\ServerRequestInterface;
use UnexpectedValueException;

class OcpiEmspCdrPostRequest extends OcpiUpdateRequest
{
    private Cdr $cdr;

    public function __construct(ServerRequestInterface $request)
    {
        parent::__construct($request);
        PayloadValidation::coerce('V2_2_1/eMSP/Server/CDRs/cdrPostRequest.schema.json', $this->jsonBody);
        $cdr = CdrFactory::fromJson($this->jsonBody);
        if ($cdr === null) {
            throw new UnexpectedValueException('Cdr cannot be null');
        }
        $this->cdr = $cdr;
    }

    public function getCdr(): Cdr
    {
        return $this->cdr;
    }
}
