<?php declare(strict_types=1);

namespace Arcanist\Tests;

use Arcanist\TTL;
use Mockery as m;
use Arcanist\AbstractWizard;
use Arcanist\Contracts\WizardRepository;
use Arcanist\Repository\CacheWizardRepository;
use Arcanist\Exception\WizardNotFoundException;
use Arcanist\Testing\WizardRepositoryContractTests;

class CacheWizardRepositoryTest extends TestCase
{
    use WizardRepositoryContractTests;

    protected function createRepository(): WizardRepository
    {
        return new CacheWizardRepository(TTL::fromSeconds(24 * 60 * 60));
    }

    /** @test */
    public function it_saves_wizard_data_with_the_correct_ttl(): void
    {
        /** @var AbstractWizard $wizard */
        $wizard = m::mock(AbstractWizard::class)->makePartial();
        $repository = new CacheWizardRepository(TTL::fromSeconds(60));

        $this->travelTo(now());
        $repository->saveData($wizard, ['::data::']);

        $this->travel(61)->seconds();
        $this->expectException(WizardNotFoundException::class);
        $repository->loadData($wizard);
    }

    /** @test */
    public function it_refreshes_the_ttl_when_the_wizard_gets_updated(): void
    {
        /** @var AbstractWizard $wizard */
        $wizard = m::mock(AbstractWizard::class)->makePartial();
        $repository = new CacheWizardRepository(TTL::fromSeconds(60));

        $this->travelTo(now());
        $repository->saveData($wizard, ['::key::' => '::data-1::']);

        $this->travel(59)->seconds();
        $repository->saveData($wizard, ['::key::' => '::data-2::']);

        $this->travel(59)->seconds();
        self::assertEquals(['::key::' => '::data-2::'], $repository->loadData($wizard));
    }
}
