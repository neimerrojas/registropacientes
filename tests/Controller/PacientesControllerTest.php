<?php

namespace App\Test\Controller;

use App\Entity\Pacientes;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PacientesControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/pacientes/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Pacientes::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Paciente index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'paciente[nombre]' => 'Testing',
            'paciente[identificacion]' => 'Testing',
            'paciente[telefono]' => 'Testing',
            'paciente[correo]' => 'Testing',
        ]);

        self::assertResponseRedirects('/sweet/food/');

        self::assertSame(1, $this->getRepository()->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Pacientes();
        $fixture->setNombre('My Title');
        $fixture->setIdentificacion('My Title');
        $fixture->setTelefono('My Title');
        $fixture->setCorreo('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Paciente');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Pacientes();
        $fixture->setNombre('Value');
        $fixture->setIdentificacion('Value');
        $fixture->setTelefono('Value');
        $fixture->setCorreo('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'paciente[nombre]' => 'Something New',
            'paciente[identificacion]' => 'Something New',
            'paciente[telefono]' => 'Something New',
            'paciente[correo]' => 'Something New',
        ]);

        self::assertResponseRedirects('/pacientes/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getNombre());
        self::assertSame('Something New', $fixture[0]->getIdentificacion());
        self::assertSame('Something New', $fixture[0]->getTelefono());
        self::assertSame('Something New', $fixture[0]->getCorreo());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Pacientes();
        $fixture->setNombre('Value');
        $fixture->setIdentificacion('Value');
        $fixture->setTelefono('Value');
        $fixture->setCorreo('Value');

        $$this->manager->remove($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/pacientes/');
        self::assertSame(0, $this->repository->count([]));
    }
}
