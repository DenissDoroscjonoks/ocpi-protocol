<?php

declare(strict_types=1);

namespace Chargemap\OCPI\Versions\V2_2_1\Common\Factories;

use Chargemap\OCPI\Versions\V2_2_1\Common\Models\AuthenticationMethod;
use Chargemap\OCPI\Versions\V2_2_1\Common\Models\Session;
use Chargemap\OCPI\Versions\V2_2_1\Common\Models\SessionStatus;
use DateTime;
use stdClass;

class SessionFactory
{
    public static function fromJson(?stdClass $json): ?Session
    {
        if ($json === null) {
            return null;
        }

        $session = new Session(
            $json->country_code,
            $json->party_id,
            $json->id,
            new DateTime($json->start_date_time),
            !empty($json->end_date_time) ? new DateTime($json->end_date_time) : null,
            $json->kwh,
            CdrTokenFactory::fromJson($json->cdr_token),
            new AuthenticationMethod($json->auth_method),
            $json->authorization_reference ?? null,
            $json->location_id,
            $json->evse_uid,
            $json->connector_id,
            $json->meter_id ?? null,
            $json->currency,
            $json->charging_periods,
            !empty($json->total_cost) ? PriceFactory::fromJson($json->total_cost) : null,
            new SessionStatus($json->status),
            new DateTime($json->last_updated)
        );

        if (!empty($json->charging_periods)) {
            foreach ($json->charging_periods as $chargingPeriod) {
                $session->addChargingPeriod(ChargingPeriodFactory::fromJson($chargingPeriod));
            }
        }

        return $session;
    }
}
