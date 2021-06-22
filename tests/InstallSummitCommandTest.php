<?php

namespace SteadfastCollective\Summit\Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Spatie\TestTime\TestTime;

class InstallSummitCommandTest extends TestCase
{
    /** @test */
    public function can_publish_config()
    {
        $this->cleanup();

        $this->artisan('summit:install')
            ->expectsConfirmation("Publish config?", 'yes')
            ->expectsConfirmation("Publish migrations?", 'no');
            // ->expectsConfirmation("Publish Nova Resources?", 'no');

        $this->assertFileExists(config_path('summit.php'));
    }

    /** @test */
    public function can_publish_migrations()
    {
        TestTime::freeze();

        $this->cleanup();

        $this->artisan('summit:install')
            ->expectsConfirmation("Publish config?", 'no')
            ->expectsConfirmation("Publish migrations?", 'yes');
            // ->expectsConfirmation("Publish Nova Resources?", 'no');

        $datePrefix = date('Y_m_d_His', time());

        $migrations = collect(File::allFiles(database_path('migrations')))
            ->map(function ($file) {
                return $file->getFilename();
            })
            ->toArray();

        $this->assertTrue(in_array("{$datePrefix}_create_course_block_user_table.php", $migrations));
        $this->assertTrue(in_array("{$datePrefix}_create_course_blocks_table.php", $migrations));
        $this->assertTrue(in_array("{$datePrefix}_create_courses_table.php", $migrations));
        $this->assertTrue(in_array("{$datePrefix}_create_videos_table.php", $migrations));
    }

    /** @test */
    public function can_publish_nova_resources()
    {
        //
    }

    /** @test */
    public function can_publish_everything()
    {
        //
    }

    protected function cleanup()
    {
        // Cleanup configs
        if (File::exists(config_path('summit.php'))) {
            File::delete(config_path('summit.php'));
        }

        // Cleanup migrations
        collect(File::allFiles(database_path('migrations')))
            ->each(function (\SplFileInfo $file) {
                File::delete($file->getPathname());
            });

        // Cleanup Nova stuff... TODO
    }
}
