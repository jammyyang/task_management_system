<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Todo;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TodoController extends Controller{
    /**
     * @Route("/todos", name="todo_list")
     */
    public function listAction(Request $request){
        $todos = $this->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->findAll();

        $paginator = $this->get('knp_paginator');
        $results = $paginator->paginate(
            $todos,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit',10)
        );

        return $this->render('todo/index.html.twig',
            array('todos'=> $results
            ));
    }

    /**
     * @Route("/todo/create", name="todo_create")
     */

    public function createAction(Request $request){
        $todo = new Todo;

        $form= $this -> createFormBuilder($todo)
            ->add('name', TextType::class, array('attr'=>array('class'=>'form-control', 'style' => 'margin-bottom:15px')))
            ->add('category', TextType::class, array('attr'=>array('class'=>'form-control', 'style' => 'margin-bottom:15px')))
            ->add('description', TextareaType::class, array('attr'=>array('class'=>'form-control', 'style' => 'margin-bottom:15px')))
            ->add('priority', ChoiceType::class, array('choices'=>array('Low'=>'low', 'Normal'=>'Normal', 'High' => 'High'), 'attr'=>array('class'=>'form-control', 'style' => 'margin-bottom:15px')))
            ->add('due_date', DateTimeType::class, array('attr'=>array('class'=>'formcontrol', 'style' => 'margin-bottom:15px')))
            ->add('status', ChoiceType::class, array('choices'=>array('Incomplete'=>'Incomplete', 'In Progress'=>'In Progress', 'Complete' => 'Complete'), 'attr'=>array('class'=>'form-control', 'style' => 'margin-bottom:15px')))
            ->add('save', SubmitType::class, array('label'=> 'Create Todo', 'attr' => array('class'=>'btn btn-primary', 'style' => 'margin-bottom:15px')))
            ->getForm();
            
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            //Get Data
            $name = $form['name']->getData();
            $category = $form['category']->getData();
            $description = $form['description']->getData();
            $priority = $form['priority']->getData();
            $status = $form['status']->getData();
            $due_date = $form['due_date']->getData();

            $now = new\DateTime('now');

            $todo->setName($name);
            $todo->setCategory($category);
            $todo->setDescription($description);
            $todo->setPriority($priority);
            $todo->setStatus($status);
            $todo->setDueDate($due_date);
            $todo->setCreateDate($now);

            $em = $this->getDoctrine()->getManager();

            $em->persist($todo);
            $em->flush();

            $this->addFlash(
                'notice',
                'Todo Added'
            );

            return $this->redirectToRoute('todo_list');
        }
            
        return $this->render('todo/create.html.twig', array(
            'form'=>$form -> createView()
        ));
    }

    /**
     * @Route("/todo/edit/{id}", name="todo_edit")
     */

    public function editAction($id, Request $request){
        $todo = $this->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->find($id);

            $now = new\DateTime('now');

            $todo->setName($todo->getName());
            $todo->setCategory($todo->getCategory());
            $todo->setDescription($todo->getDescription());
            $todo->setPriority($todo->getPriority());
            $todo->setStatus($todo->getStatus());
            $todo->setDueDate($todo->getDueDate());
            $todo->setCreateDate($now);

        $form= $this -> createFormBuilder($todo)
            ->add('name', TextType::class, array('attr'=>array('class'=>'form-control', 'style' => 'margin-bottom:15px')))
            ->add('category', TextType::class, array('attr'=>array('class'=>'form-control', 'style' => 'margin-bottom:15px')))
            ->add('description', TextareaType::class, array('attr'=>array('class'=>'form-control', 'style' => 'margin-bottom:15px')))
            ->add('priority', ChoiceType::class, array('choices'=>array('Low'=>'low', 'Normal'=>'Normal', 'High' => 'High'), 'attr'=>array('class'=>'form-control', 'style' => 'margin-bottom:15px')))
            ->add('status', ChoiceType::class, array('choices'=>array('Incomplete'=>'Incomplete', 'In Progress'=>'In Progress', 'Complete' => 'complete'), 'attr'=>array('class'=>'form-control', 'style' => 'margin-bottom:15px')))
            ->add('due_date', DateTimeType::class, array('attr'=>array('class'=>'formcontrol', 'style' => 'margin-bottom:15px')))
            ->add('save', SubmitType::class, array('label'=> 'Update Todo', 'attr' => array('class'=>'btn btn-primary', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            //Get Data
            $name = $form['name']->getData();
            $category = $form['category']->getData();
            $description = $form['description']->getData();
            $priority = $form['priority']->getData();
            $status = $form['status']->getData();
            $due_date = $form['due_date']->getData();

            $now = new\DateTime('now');

            $em = $this->getDoctrine()->getManager();
            $todo = $em->getRepository('AppBundle:Todo') ->find($id);

            $todo->setName($name);
            $todo->setCategory($category);
            $todo->setDescription($description);
            $todo->setPriority($priority);
            $todo->setStatus($status);
            $todo->setDueDate($due_date);
            $todo->setCreateDate($now);

            $em->flush();

            $this->addFlash(
                'notice',
                'Todo Updated'
            );

            return $this->redirectToRoute('todo_list');
        }

        return $this->render('todo/edit.html.twig', array(
            'todo' => $todo,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/todo/details/{id}", name="todo_details")
     */

    public function detailsAction($id){
        $todos = $this->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->find($id);

        return $this->render('todo/details.html.twig', array(
            'todo' => $todos
        ));
    }
    /**
     * @Route("/todo/delete/{id}", name="todo_delete")
     */
    public function deleteAction($id){
        $em = $this->getDoctrine()->getManager();
        $todo = $em->getRepository('AppBundle:Todo')->find($id);

        $em->remove($todo);
        $em->flush();

        $this->addFlash(
            'notice',
            'Todo Removed'
         );

        return $this->redirectToRoute('todo_list');
    }
}
