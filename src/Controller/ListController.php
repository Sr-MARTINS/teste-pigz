<?php

namespace App\Controller;

use App\Entity\Listas;
use App\Repository\ListasRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ListController extends AbstractController
{
    
    #[Route('/listas', name: 'listas.index', methods: ['GET'])]
    public function index(ListasRepository $listasRepository): JsonResponse
    {
        $listas = $listasRepository->findBy(['usuario' => $this->getUser()]);

        return $this->json(['data' => $listas], 200,[],  ['groups' => 'user'] );
 
    } 

    #[Route('/listas', name: 'listas.create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em,
     ValidatorInterface $validator)
    {
        $data = json_decode($request->getContent(), true);

        $lista = new Listas();
        $lista->setTitulo($data['titulo'] ?? '');
        $lista->setIsPublico($data['is_publico'] ?? false);
        $lista->setUsuario($this->getUser());
        $lista->setCreatedAt(new \DateTimeImmutable());
        $lista->setUpdatedAt(new \DateTimeImmutable());

        $erros = $validator->validate($lista);

        if(count($erros) > 0) {
            $mensagens = [];

            foreach ($erros as $violacao) {
                $mensagens[] = [
                    'campo' => $violacao->getPropertyPath(),
                    'mensagem' => $violacao->getMessage()
                ];
            }

            return $this->json([
                'message' => 'Existem erros de validação.',
                'errors' => $mensagens
            ], 400);
        }

        $em->persist($lista);
        $em->flush();

        return $this->json(['data' => $lista], 201,[],  ['groups' => 'user']);
    }

    #[Route('/listas/{id}', name: 'listas.show', methods:['GET'])]
    public function show( $id, ListasRepository $listasRepository)
    {
        $list = $listasRepository->findLista($id, $this->getUser());

        if(null == $list) {
            return new JsonResponse(['message' => 'Lista nao encontrada'], 404);
        }

        return $this->json(['data' => $list], 200,[],  ['groups' => 'user'] );
    }

    #[Route('/listas/{id}', name: 'listas.update', methods:['PUT'])]
    public function update( $id, ListasRepository $listasRepository, Request $request, EntityManagerInterface $em,ValidatorInterface $validator)
    {
        $lista = $listasRepository->findOneBy([
            "id" => $id,
            "usuario" => $this->getUser()
        ] );

        if(null == $lista) {
            return new JsonResponse(['message' => 'Lista nao encontrada'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $lista->setTitulo($data['titulo'] ?? $lista->getTitulo());
        $lista->setIsPublico($data['is_publico'] ?? $lista->isPublico());

        $erros = $validator->validate($lista);

        if(count($erros) > 0) {
            $mensagens = [];

            foreach ($erros as $violacao) {
                $mensagens[] = [
                    'campo' => $violacao->getPropertyPath(),
                    'mensagem' => $violacao->getMessage()
                ];
            }

            return $this->json([
                'message' => 'Existem erros de validação.',
                'errors' => $mensagens
            ], 400);
        }
       
        $em->persist($lista);
        $em->flush();

        return $this->json(['data' => $lista], 200,[],  ['groups' => 'user'] );
    }

    #[Route('/listas/{id}', name: 'listas.delete', methods:['DELETE'])]
    public function delete( $id, ListasRepository $listasRepository)
    {
        $lista = $listasRepository->findOneBy([
            "id" => $id,
            "usuario" => $this->getUser()
        ] );

        if(null == $lista) {
            return new JsonResponse(['message' => 'Lista nao encontrada'], 404);
        }

        $listasRepository->remove($lista, true);

        return $this->json([ 'message' => 'Lista deletada com successo']);
    }
}
