<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class OfferType extends Enum
{
    const INTERNET = 1; //id
    const VOICE = 2;
    const BUNDLES = 3;
    const PACKAGES = 4;
    const PREPAID_PLANS = 5;
    const START_UP_OFFERS = 6;
    const POSTPAID_PLANS = 7;
    const ICON_PLANS = 8;
    const OTHERS = 9;
    const BALANCE_TRANSFER = 10;
    const EMERGENCY_BALANCE = 11;
    const AMAR_OFFER_PREPAID = 12;
    const BONDHO_SIM_OFFER = 13;
    const MNP_OFFERS = 14;
    const DEVICE_OFFERS = 15;
    const FOUR_G_OFFERS = 16;
    const AMAR_OFFER_POSTPAID = 17;
    const MFS_OFFERS = 18;
    const CALL_RATE = 19;
}
