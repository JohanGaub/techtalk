<?php

declare(strict_types=1);

namespace App\Test\Controller;

use App\Entity\Topic;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class TopicControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/topic/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Topic::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Topic index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request(Request::METHOD_GET, sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'topic[label]' => 'Testing',
            'topic[currentPlace]' => 'Testing',
            'topic[reviewedAt]' => 'Testing',
            'topic[userProposer]' => 'Testing',
            'topic[userReviewer]' => 'Testing',
            'topic[userPresenter]' => 'Testing',
        ]);

        self::assertResponseRedirects('/sweet/food/');

        self::assertSame(1, $this->getRepository()->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Topic();
        $fixture->setLabel('My Title');
        $fixture->setCurrentPlace('My Title');
        $fixture->setReviewedAt('My Title');
        $fixture->setUserProposer('My Title');
        $fixture->setUserReviewer('My Title');
        $fixture->setUserPresenter('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request(Request::METHOD_GET, sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Topic');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Topic();
        $fixture->setLabel('Value');
        $fixture->setCurrentPlace('Value');
        $fixture->setReviewedAt('Value');
        $fixture->setUserProposer('Value');
        $fixture->setUserReviewer('Value');
        $fixture->setUserPresenter('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request(Request::METHOD_GET, sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'topic[label]' => 'Something New',
            'topic[currentPlace]' => 'Something New',
            'topic[reviewedAt]' => 'Something New',
            'topic[userProposer]' => 'Something New',
            'topic[userReviewer]' => 'Something New',
            'topic[userPresenter]' => 'Something New',
        ]);

        self::assertResponseRedirects('/topic/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getLabel());
        self::assertSame('Something New', $fixture[0]->getCurrentPlace());
        self::assertSame('Something New', $fixture[0]->getValidatedAt());
        self::assertSame('Something New', $fixture[0]->getUserProposer());
        self::assertSame('Something New', $fixture[0]->getUserValidator());
        self::assertSame('Something New', $fixture[0]->getUserPresenter());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Topic();
        $fixture->setLabel('Value');
        $fixture->setCurrentPlace('Value');
        $fixture->setReviewedAt('Value');
        $fixture->setUserProposer('Value');
        $fixture->setUserReviewer('Value');
        $fixture->setUserPresenter('Value');

        $this->manager->remove($fixture);
        $this->manager->flush();

        $this->client->request(Request::METHOD_GET, sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/topic/');
        self::assertSame(0, $this->repository->count([]));
    }
}
