<?php

namespace App\Controller;

use App\Document\Moderation\Sanction;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Knp\Component\Pager\PaginatorInterface;
use MongoDB\BSON\Regex;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $sanctions = $paginator->paginate(
            $dm->createQueryBuilder(Sanction::class)
                ->find()
                ->sort('_id', 'DESC')
                ->getQuery(),
            $request->query->getInt('page', 1)
        );
        return $this->render('sanctions/home.html.twig', [
            'sanctions' => $sanctions
        ]);
    }

    /**
     * @Route("/search", name="sanctions:search", methods={"POST"})
     * @param Request $request
     * @param DocumentManager $dm
     * @return Response
     */
    public function search(Request $request, DocumentManager $dm): Response
    {
        $memberId = $request->request->get('memberId');
        $moderatorId = $request->request->get('moderatorId');
        $reason = $request->request->get('reason');
        $sanctionStatus = $request->request->get('sanctionStatus');
        $afterDate = $request->request->get('afterDate');
        $beforeDate = $request->request->get('beforeDate');
        $sanctionType = $request->request->get('sanctionType');
        $query = $dm->createQueryBuilder(Sanction::class);
        if (isset($memberId) && $memberId !== "")
            $query->field('memberId')->equals($memberId);
        if (isset($moderatorId) && $moderatorId !== "")
            $query->field('moderator')->equals($moderatorId);
        if (isset($reason) && $reason !== "")
            $query->field('reason')->equals(new Regex('/' . $reason . '/'));
        if (isset($sanctionStatus) && $sanctionStatus !== "")
            $query->field('revoked')->equals($sanctionStatus == "true");
        if (isset($afterDate) && $afterDate !== "") {
            $date = strtotime($afterDate);
            $query->field('start')->gte($date * 1000);
        }
        if (isset($beforeDate) && $beforeDate !== "") {
            $date = strtotime($beforeDate);
            $query->field('start')->lte($date * 1000);
        }
        if (isset($sanctionType) && $sanctionType !== "")
            $query->field('type')->equals($sanctionType);
        try {
            $results = $query
                ->sort('start', 'DESC')
                ->getQuery()
                ->execute();
        } catch (MongoDBException $e) {
            return $this->redirectToRoute('messages');
        }

        return $this->render('sanctions/search.html.twig', [
            'results' => $results
        ]);

    }

}
