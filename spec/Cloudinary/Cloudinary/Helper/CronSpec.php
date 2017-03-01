<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Cloudinary_Cloudinary_Helper_CronSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Cloudinary_Cloudinary_Helper_Cron');
    }

    function it_validates_true_when_migration_is_not_running(
        \Cloudinary_Cloudinary_Model_Migration $migration
    )
    {
        $migration->hasStarted()->willReturn(false);
        $this->validate($migration, 10)->shouldReturn(true);
    }

    function it_validates_true_when_migration_is_running_and_time_elapsed_is_less_than_cron_interval(
        \Cloudinary_Cloudinary_Model_Migration $migration
    )
    {
        $migration->hasStarted()->willReturn(true);
        $migration->timeElapsed()->willReturn(1);
        $this->validate($migration, 10)->shouldReturn(true);
    }

    function it_validates_false_when_migration_is_running_and_time_elapsed_is_more_than_cron_interval_and_no_batches_have_been_processed(
        \Cloudinary_Cloudinary_Model_Migration $migration
    )
    {
        $migration->hasStarted()->willReturn(true);
        $migration->timeElapsed()->willReturn(9999);
        $migration->hasProgress()->willReturn(false);
        $this->validate($migration, 10)->shouldReturn(false);
    }

    function it_validates_true_when_migration_is_running_and_time_elapsed_is_more_than_cron_interval_and_batches_have_been_processed(
        \Cloudinary_Cloudinary_Model_Migration $migration
    )
    {
        $migration->hasStarted()->willReturn(true);
        $migration->timeElapsed()->willReturn(9999);
        $migration->hasProgress()->willReturn(true);
        $this->validate($migration, 10)->shouldReturn(true);
    }
}
