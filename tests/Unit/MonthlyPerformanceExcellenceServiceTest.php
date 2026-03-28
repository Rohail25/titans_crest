<?php

namespace Tests\Unit;

use App\Services\MonthlyPerformanceExcellenceService;
use PHPUnit\Framework\TestCase;

class MonthlyPerformanceExcellenceServiceTest extends TestCase
{
    public function test_scenario_a_falls_back_to_4000_tier_when_10000_tier_leg_requirement_fails(): void
    {
        $tier = MonthlyPerformanceExcellenceService::resolveQualifyingTier(12000, 2);

        $this->assertNotNull($tier);
        $this->assertSame(4000.0, $tier['target_volume']);
        $this->assertSame(80.0, $tier['reward_amount']);
        $this->assertSame(2, $tier['min_legs']);
    }

    public function test_scenario_b_hits_40000_tier_when_volume_and_legs_match(): void
    {
        $tier = MonthlyPerformanceExcellenceService::resolveQualifyingTier(45000, 4);

        $this->assertNotNull($tier);
        $this->assertSame(40000.0, $tier['target_volume']);
        $this->assertSame(1200.0, $tier['reward_amount']);
        $this->assertSame(4, $tier['min_legs']);
    }

    public function test_high_volume_with_low_legs_falls_back_to_previous_target_not_smallest(): void
    {
        $tier = MonthlyPerformanceExcellenceService::resolveQualifyingTier(85000, 2);

        $this->assertNotNull($tier);
        $this->assertSame(40000.0, $tier['target_volume']);
        $this->assertSame(1200.0, $tier['reward_amount']);
        $this->assertSame(4, $tier['min_legs']);
    }
}
