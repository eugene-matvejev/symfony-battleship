<?php

namespace EM\FoundationBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * @since 23.0
 */
class UserSessionsFixture extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    use ContainerAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $om)
    {
        $session = $this->container->get('em.foundation_bundle.model.player_session')->authenticate(
            UsersFixture::TEST_PLAYER_EMAIL,
            UsersFixture::TEST_PLAYER_PASSWORD
        );

        $om->persist($session);

        $om->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }
}
