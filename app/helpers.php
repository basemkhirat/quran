<?php
use GeoIp2\Database\Reader;
use PragmaRX\Countries\Package\Countries;

/**
 * Detech user country code
 */
function getUserCountry()
{
    $reader = new Reader(database_path("geoip/GeoLite2-City.mmdb"));
    try {
	$record = $reader->city($_SERVER['HTTP_X_FORWARDED_FOR']);
        return strtolower($record->country->isoCode);
    } catch (Exception $error) {
        return null;
    }
}

function getCountryTitle($code) {

    if(!$code) {
        return null;
    }

    $countries = new Countries();

    return $countries->where('cca2', strtoupper($code))->first()->name_en;
}