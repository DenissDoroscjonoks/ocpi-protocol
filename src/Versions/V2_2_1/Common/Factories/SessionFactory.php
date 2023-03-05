<?php

declare(strict_types=1);

namespace Chargemap\OCPI\Versions\V2_2_1\Common\Factories;

use Cassandra\Date;
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
            $json->id,
            new DateTime($json->start_datetime),
            property_exists($json, 'end_datetime') ? new DateTime($json->end_datetime) : null,
            $json->kwh,
            CdrTokenFactory::fromJson($json->cdr_token),
            new AuthenticationMethod($json->auth_method),
            $json->meter_id ?? null,
            $json->currency,
            $json->total_cost ? PriceFactory::fromJson($json->total_cost) : null,
            new SessionStatus($json->status),
            new DateTime($json->last_updated),
            $json->country_code,
            $json->party_id,
            new DateTime($json->start_date_time),
            $json->end_date_time ? new DateTime($json->end_date_time) : null,
            $json->location_id,
            $json->evse_uid,
            $json->connector_id,
            $json->authorization_reference
        );

        if (property_exists($json, 'charging_periods')) {
            foreach (ChargingPeriodFactory::arrayFromJsonArray($json->charging_periods) as $chargingPeriod) {
                $session->addChargingPeriod($chargingPeriod);
            }
        }

        return $session;
    }
}
