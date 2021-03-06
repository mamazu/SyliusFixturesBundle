<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\FixturesBundle\Listener;

use Sylius\Bundle\FixturesBundle\Listener\AbstractListener;
use Sylius\Bundle\FixturesBundle\Listener\BeforeSuiteListenerInterface;
use Sylius\Bundle\FixturesBundle\Listener\SuiteEvent;
use Sylius\Bundle\FixturesBundle\Loader\SuiteLoaderInterface;
use Sylius\Bundle\FixturesBundle\Suite\SuiteRegistryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class SuiteLoaderListener extends AbstractListener implements BeforeSuiteListenerInterface
{
    /** @var SuiteRegistryInterface */
    private $suiteRegistry;

    /** @var SuiteLoaderInterface */
    private $suiteLoader;

    public function __construct(SuiteRegistryInterface $suiteRegistry, SuiteLoaderInterface $suiteLoader)
    {
        $this->suiteRegistry = $suiteRegistry;
        $this->suiteLoader   = $suiteLoader;
    }

    /** {@inheritdoc} */
    public function getName(): string
    {
        return 'suite_loader';
    }

    /** {@inheritdoc} */
    public function beforeSuite(SuiteEvent $suiteEvent, array $options): void
    {
        foreach ($options['suites'] as $suiteName) {
            $suite = $this->suiteRegistry->getSuite($suiteName);
            $this->suiteLoader->load($suite);
        }
    }

    /** {@inheritdoc} */
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNode->children()
            ->arrayNode('suites')
                ->performNoDeepMerging()
                ->prototype('scalar')
            ->end();
    }
}