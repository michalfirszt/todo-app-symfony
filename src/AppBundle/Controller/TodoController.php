<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Todo;

class TodoController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $entityManager = $this->getDoctrine()->getManager();

        $tasks = $entityManager
                ->getRepository(Todo::class)
                ->findOverview();
        
        return $this->render('todo/index.html.twig', ['tasks' => $tasks]);
    }
}
