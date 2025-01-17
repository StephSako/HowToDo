<?php

namespace App\Controller;

use App\Controller\PanelData\Categories;
use App\Entity\Forum;
use App\Entity\ForumReporting;
use App\Entity\Tutorial;
use App\Entity\TutorialReporting;
use App\Form\ForumReportType;
use App\Form\TutorialReportType;
use App\Repository\ForumRepository;
use App\Repository\ReportingLabelRepository;
use App\Repository\TutorialRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportController extends AbstractController
{

    /**
     * @var ReportingLabelRepository
     */
    private $mfr;
    /**
     * @var TutorialRepository
     */
    private $tr;
    /**
     * @var ForumRepository
     */
    private $fr;
    /**
     * @var ObjectManager
     */
    private $em;

    private $categories;

    /**
     * ReportController constructor.
     * @param ObjectManager $em
     * @param Categories $categories
     * @param ReportingLabelRepository $mfr
     * @param TutorialRepository $tr
     * @param ForumRepository $fr
     */
    public function __construct(ObjectManager $em, Categories $categories, ReportingLabelRepository $mfr, TutorialRepository $tr, ForumRepository $fr)
    {
        $this->mfr = $mfr;
        $this->tr = $tr;
        $this->fr = $fr;
        $this->em = $em;
        $this->categories = $categories->getCategories();
    }

    /**
     * @Route("/tutorial/report/{id}", name="reporttutorial")
     * @param Request $request
     * @param Tutorial $tutorial
     * @return Response
     */
    public function reporttutorial(Request $request, Tutorial $tutorial):Response
    {
        $report = new TutorialReporting($this->getUser(), $tutorial);
        $form = $this->createForm(TutorialReportType::class, $report);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $this->em->persist($report);
            $this->em->flush();
            $this->addFlash('successreport', 'Merci. Nous traiterons votre demande dans les plus brefs délais.');
            return $this->redirectToRoute('home.tutorials');
        }

        return $this->render('pages/signalement_form.html.twig', [
            'tutorial' => $tutorial,
            'categories' => $this->categories,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/forum/report/{id}", name="reportforum")
     * @param Request $request
     * @param Forum $forum
     * @return Response
     */
    public function reportforum(Request $request, Forum $forum):Response
    {   $report = new ForumReporting($this->getUser(), $forum);
        $form = $this->createForm(ForumReportType::class, $report);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $this->em->persist($report);
            $this->em->flush();
            $this->addFlash('successreport', 'Merci. Nous traiterons votre demande dans les plus brefs délais.');
            return $this->redirectToRoute('home.forums');
        }

        return $this->render('pages/signalement_form.html.twig', [
            'forum' => $forum,
            'categories' => $this->categories,
            'form' => $form->createView()
        ]);
    }
}
