<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RecipeController extends AbstractController
{
    #[Route('/recipe', name: 'app_recipe')]
    public function index(PaginatorInterface $paginator, Request $request, RecipeRepository $repository): Response
    {
        $recipes = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt(key: 'page', default: 1),
            limit: 10
        );

        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/recipe/nouveau', name: 'recipe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            
            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                type: 'success',
                message: 'Vos changements ont été enregistrés !'
            );
            
            return $this->redirectToRoute('app_recipe');
        }

        return $this->render('pages/recipe/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/recipe/edit/{id}', name: 'recipe_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        EntityManagerInterface $manager,
        Recipe $recipe
    ): Response
    {
        $form = $this->createForm(type: RecipeType::class, data: $recipe);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();

            //$manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                type: 'success',
                message: 'Vos changements ont été enregistrés !'
            );
            
            return $this->redirectToRoute(route:'app_recipe');
        }

        return $this->render('pages/recipe/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/recipe/remove/{id}', name: 'recipe_remove', methods: ['GET'])]
    public function remove(
        Request $request,
        EntityManagerInterface $manager,
        Recipe $recipe
    ): Response
    {
        $manager->remove($recipe);
        $manager->flush();

        $this->addFlash(
            type: 'success',
            message: 'La recette a été supprimée !'
        );

        return $this->redirectToRoute(route: 'app_recipe');
    }
}
