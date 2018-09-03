<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Todo;

class TodoController extends Controller
{
    private $categories = ['Homework' => 'homework', 
                            'Job' => 'job', 
                            'Rest' => 'rest',
                            'Sport' => 'sport'];

    private $priorities = ['Low' => 'low', 
                            'Medium' => 'medium', 
                            'High' => 'high'];

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $entityManager = $this->getDoctrine()->getManager();

        $tasks = $entityManager
                ->getRepository(Todo::class)
                ->findOverview();

        $numberOfRows = $entityManager
                        ->getRepository(Todo::class)
                        ->findNumberOfTasks();
        
        $totalNumber = $numberOfRows[0];

        return $this->render('todo/index.html.twig', ['tasks' => $tasks, 
                                                    'rows' => $totalNumber]);
    }

    /**
     * @Route("/{id}/details", name="details_page")
     */
    public function detailsAction($id)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $task = $entityManager
                ->getRepository(Todo::class)
                ->find($id);
        
        return $this->render('todo/details.html.twig', ['task' => $task]);
    }

    /**
     * @Route("/create", name="create_page")
     * 
     * @return Response
     */
    public function createAction(Request $request)
    {
        $task = new Todo();

        $createForm = $this->createFormBuilder($task)
                        ->add('name', TextType::class)
                        ->add('description', TextareaType::class)
                        ->add('category', ChoiceType::class, ['choices' => $this->categories])
                        ->add('priority', ChoiceType::class, ['choices' => $this->priorities])
                        ->add('dueDate', DateType::class)
                        ->add('submit', SubmitType::class)
                        ->getForm();

        $createForm->handleRequest($request);

        if($createForm->isSubmitted() && $createForm->isValid())
        {
            $currentDate = new\DateTime('now');

            $task->setName($createForm['name']->getData());
            $task->setDescription($createForm['description']->getData());
            $task->setCategory($createForm['category']->getData());
            $task->setPriority($createForm['priority']->getData());
            $task->setDueDate($createForm['dueDate']->getData());
            $task->setCreateDate($currentDate);
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Task Added'
            );

            return $this->redirectToRoute('homepage');
        }
        
        return $this->render('todo/create.html.twig', ['form' => $createForm->createView()]);
    }

    /**
     * @Route("/{id}/edit", name="edit_page")
     * 
     * @return Response
     */
    public function editAction($id, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $task = $entityManager
                ->getRepository(Todo::class)
                ->find($id);

        $editForm = $this->createFormBuilder($task)
                        ->add('name', TextType::class)
                        ->add('description', TextareaType::class)
                        ->add('category', ChoiceType::class, ['choices' => $this->categories])
                        ->add('priority', ChoiceType::class, ['choices' => $this->priorities])
                        ->add('dueDate', DateType::class)
                        ->add('submit', SubmitType::class)
                        ->getForm();

        $editForm->handleRequest($request);

        if($editForm->isSubmitted() && $editForm->isValid())
        {
            $task->setName($editForm['name']->getData());
            $task->setDescription($editForm['description']->getData());
            $task->setCategory($editForm['category']->getData());
            $task->setPriority($editForm['priority']->getData());
            $task->setDueDate($editForm['dueDate']->getData());
            
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Task Edited'
            );

            return $this->redirectToRoute('homepage');
        }

        return $this->render('todo/edit.html.twig', ['form' => $editForm->createView()]);
    }
}
