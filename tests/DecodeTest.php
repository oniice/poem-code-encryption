<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Poem\Encode;

/**
 * @coversDefaultClass Poem\Encode
 */
class DecodeTest extends TestCase
{

    public function valueProviders()
    {
        return [
            [
                'ALL THINGS BRIGHT AND BEAUTIFUL ALL CREATURES GREAT AND SMALL',
                ['E', 'G', 'J', 'H', 'B'],
                'Message begins. Attack planned for next Tuesday, require guns and explosives in advance. Please advise delivery schedule. Message ends',
                'SSNS SEDX LEIX FICX MUVE GUSS EEAS NRAX ASEX DSSX ERLE NLRX TAEX NSDX GYPE EIUX EQAD AXVX NPEX PDLX XNLX IIEX TNSX EOYX CADX KNEX TDMX AAEG AGVX OVHX REEX SDCA BEEN TUIX',
                'MESSAGEBEGINSATTACKPLANNEDFORNEXTTUESDAYREQUIREGUNSANDEXPLOSIVESINADVANCEPLEASEADVISEDELIVERYSCHEDULEMESSAGEENDSXXXXXXXXXXXXXXXXXXXXXXXX'

            ],
            [
                '\'Twas brillig, and the slithy toves
                Did gyre and gimble in the wabe.
                All mimsy were the borogoves,
                And the mome raths outgrabe.',
                'DNUHA',
                'I have deposited in the  county of Bedford about four miles from Bufordsin an excavation or vault six feet below the surface of the ground the following',
                'VUFDVHN TABIEEG AOTRRTU SBLEIAO DORVEFW IEEXXCL HCUOOWO ENOSAED DTUIUST EYRNLUH OFINSFF POMATRE EFFAEOO HBUOLGX IEOFNOR IROATTI NDMTBHN TDSCFEL',
                'IHAVEDEPOSITEDINTHECOUNTYOFBEDFORDABOUTFOURMILESFROMBUFORDSINANEXCAVATIONORVAULTSIXFEETBELOWTHESURFACEOFTHEGROUNDTHEFOLLOWINGX'
            ],
        ];
    }


    /**
     * Test that a string is correctly encoded
     *
     * @param $poem
     * @param $indicators
     * @param $message
     * @param $encoded
     *
     * @dataProvider valueProviders
     * @covers ::encode
     */
    public function testStringIsEncodedCorrectlyOne($poem, $indicators, $message, $encoded, $unencrypted): void
    {
        $encoder = new Encode();

        $encodedGrid = $encoder->encode($poem, $indicators, $message);

        $grid = $encoder->decode($encoded, $encodedGrid->getKey());

        $this->assertEquals($unencrypted, $grid->getOriginalMessage());
    }

}

