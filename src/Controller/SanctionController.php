<?php

namespace App\Controller;

use App\Document\Moderation\Sanction;
use App\Entity\SanctionQuery;
use Doctrine\ODM\MongoDB\DocumentManager;
use Knp\Component\Pager\PaginatorInterface;
use MongoDB\BSON\Regex;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SanctionController
 * @package App\Controller
 * @Route("/sanctions")
 */
class SanctionController extends AbstractController
{

    /**
     * @Route("", name="sanctions")
     * @param Request $request
     * @param DocumentManager $dm
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function home(Request $request, DocumentManager $dm, PaginatorInterface $paginator): Response
    {

        $sanctionQuery = new SanctionQuery();
        $form = $this->createFormBuilder($sanctionQuery)
            ->setMethod('GET')
            ->add('memberId', TextType::class, [
                'required' => false
            ])
            ->add('moderatorId', TextType::class, [
                'required' => false
            ])
            ->add('reason', TextType::class, [
                'required' => false
            ])
            ->add('sanctionStatus', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'Terminée' => true,
                    'En cours' => false
                ]
            ])
            ->add('beforeDate', DateTimeType::class, [
                'required' => false,
                'input' => 'timestamp',
                'widget' => 'single_text'
            ])
            ->add('afterDate', DateTimeType::class, [
                'required' => false,
                'input' => 'timestamp',
                'widget' => 'single_text'
            ])
            ->add('sanctionType', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'Bannissement Discord' => 'hardban',
                    'Bannissement' => 'ban',
                    'Mute' => 'mute',
                    'Avertissement' => 'warn'
                ]
            ])
            ->add('submit', SubmitType::class, ['label' => 'Rechercher'])
            ->getForm();

        $form->handleRequest($request);
        $query = $dm->createQueryBuilder(Sanction::class)
            ->find()
            ->sort('_id', 'DESC');

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var SanctionQuery $data */
            $data = $form->getData();
            if ($data->getMemberId()) $query->field('memberId')->equals($data->getMemberId());
            if ($data->getModeratorId()) $query->field('moderator')->equals($data->getModeratorId());
            if ($data->getReason()) $query->field('reason')->equals(new Regex('/' . $data->getReason() . '/'));
            if ($data->getSanctionStatus() !== null) $query->field('revoked')->equals($data->getSanctionStatus());
            if ($data->getSanctionType()) $query->field('type')->equals($data->getSanctionType());
            if ($data->getSanctionId()) $query->field('sanctionId')->equals($data->getSanctionId());
            if ($data->getAfterDate()) $query->field('start')->gte($data->getAfterDate() * 1000);
            if ($data->getBeforeDate()) $query->field('start')->lte($data->getBeforeDate() * 1000);
        }
        return $this->render('sanctions/home.html.twig', [
            'sanctions' => $paginator->paginate(
                $query->getQuery(),
                $request->query->getInt('page', 1)
            ),
            'search' => $form->createView()
        ]);
    }

}
