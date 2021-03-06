<?php
/**
 * Created by PhpStorm.
 * User: Georges
 * Date: 02/03/2017
 * Time: 16:32
 */
namespace EC\NotepadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use EC\NotepadBundle\Entity\Categorie;
use EC\NotepadBundle\Form\CategorieType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;




class CategorieControlleur extends Controller
{
    /**
     * @Route("/categorie/liste", name="list_cat")
     */
    public function listAction()
    {
        $catdbread = $this->getDoctrine()->getRepository('ECNotepadBundle:Categorie');

        $categories = $catdbread->findAll();

        return $this->render('ECNotepadBundle:Notepad:listcat.html.twig', array('categories' => $categories,));
    }
    /**
     * @Route("/categorie/ajouter", name = "ajt_cat")
     */
    public function ajouterAction(Request $request)
    {
        // on commence par cree une nouvelle categorie
        $categorie = new Categorie();

        // on recupère le formulaire
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        // si le formulaire à ete soumis
        if ($form->isSubmitted() && $form->isValid()) {
            $save = $this
                ->getDoctrine()
                ->getRepository('ECNotepadBundle:Categorie')
                ->findOneByNom($categorie->getNom());
            if(!$save) {
                //on enregistre le produit en base de donnée
                $em = $this->getDoctrine()->getManager();
                $em->persist($categorie); // prepare l'objet pour l'insere dans la base de donnée
                $em->flush(); // évacu les données vers la base de donnée
            }
            else {
                echo 'La catégorie '.$categorie->getNom().' est déja enregistrée';
                //return $this->redirectToRoute('ajt_cat', array('message' => 'La catégorie '+ $categorie->getNom() +' Existe déja'));
            }

            //return new Response('Categorie Ajouté !');
            return $this->redirectToRoute('list_cat');
        }
        $formView = $form->createView();
        // on genère le HTML du formulaire et on rend la vue
        return $this->render('ECNotepadBundle:Notepad:ajoutercat.html.twig', array('form'=>$formView));
    }

    /**
     * @Route("/categorie/editer/{id}", name="edit_cat", requirements = { "id" = "\d+" })
     */
    public function editecatAction(Request $request, $id)
    {
        $cat = new Categorie();
        $save = $this ->getDoctrine()->getRepository('ECNotepadBundle:Categorie')->find($id);

        $form = $this->createFormBuilder($cat) ->add('nom', TextType::class, array('data' => $save -> getNom(),))->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $save->setNom($cat->getNom());
            $repository = $this->getDoctrine()->getRepository('ECNotepadBundle:Categorie');
            $reposi = $repository->findOneBynom($cat->getNom());

            if(!$reposi){
                $em = $this->getDoctrine()->getManager();
                $em->persist($save);
                $em->flush();
            }
            else {
                echo 'La catégorie '.$cat->getNom().' est déja enregistrée';
            }
            return $this->redirectToRoute('list_cat');
            //return $this->redirect($this->generateUrl('list_cat', array('id' => $cat->getId())));
        }

        //return $this->render('ECNotepadBundle:Notepad:ajoutercat.html.twig', array('form' => $form->createView(),));
        $formView = $form->createView();
        return $this->render('ECNotepadBundle:Notepad:ajoutercat.html.twig', array('form'=>$formView));
    }

    /**
     * @Route("/categorie/supprimer/{id}", name="del_cat", requirements = { "id" = "\d+" })
     */
    public function supprimercatAction($id)
    {
        $cat = $this->getDoctrine()->getRepository('ECNotepadBundle:Categorie')->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($cat);
        $em->flush();
        return $this->redirect($this->generateUrl('list_cat', array('id' => $cat->getId())));
    }

}