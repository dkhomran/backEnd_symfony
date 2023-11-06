<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Repository\PersonneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/personne/list', name: 'app_personne_list', methods: ['GET'])]
    public function index(PersonneRepository $personneRepository)
    {
        $users = $personneRepository->findAll();
        return $this->json($users);
    }

    #[Route('/personne/store', name: 'app_personne_store', methods: ['POST'])]
    public function store(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $personne = new Personne();
        $personne->setFirstname($data['firstname']);
        $personne->setLastname($data['lastname']);
        $personne->setEmail($data['email']);
        $personne->setAge($data['age']);
        $this->entityManager->persist($personne);
        $this->entityManager->flush();
        return new JsonResponse(['message' => 'Personne ajoutée avec succès'], Response::HTTP_CREATED);
    }

    #[Route('/personne/delete/{id}', name: 'app_personne_delete')]
    public function delete($id, PersonneRepository $personneRepository) {
        $personne = $personneRepository->find($id);
        if(!$personne) {
            return new JsonResponse(['message' => 'Personne non Trouvée'], Response::HTTP_NOT_FOUND);
        }
        $this->entityManager->remove($personne);
        $this->entityManager->flush();
        return new JsonResponse(['message' => 'Personne supprimée avec succès'], Response::HTTP_OK);
    }

    #[Route('personne/edit/{id}', name: 'app_personne_edit')]
    public function edit($id) : JsonResponse
    {
        $personne = $this->entityManager->getRepository(Personne::class)->find($id);
        return $this->json($personne);
    }

    #[Route('personne/update/{id}', name: 'app_personne_update')]
    public function update($id, Request $request)
    {
        $personne = $this->entityManager->getRepository(Personne::class)->find($id);

        if(!$personne) {
            return new JsonResponse(['message' => 'Personne n\'est pas trouvé']);
        }

        $data = json_decode($request->getContent(), true);

        $personne->setFirstname($data['firstname']);
        $personne->setLastname($data['lastname']);
        $personne->setAge($data['age']);
        $personne->setEmail($data['email']);

        $this->entityManager->persist($personne);
        $this->entityManager->flush();


        return new JsonResponse(['message' => 'la personne est mis a jour']);
    }

}
