<?php

namespace App\Controller;

use App\Document\Message;
use App\Document\MessageEditRequest;
use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MessageController
 * @package App\Controller
 *
 * @IsGranted("ROLE_MEMBER")
 * @Route("/messages")
 */
class MessageController extends AbstractController
{
    /**
     * @Route("", name="messages")
     * @param DocumentManager $dm
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function home(DocumentManager $dm, PaginatorInterface $paginator): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $requests = $paginator->paginate(
            $dm->createQueryBuilder(MessageEditRequest::class)
                ->field('user.id')->equals($user->getId())
                ->getQuery()
        );
        return $this->render('messages/home.html.twig', [
            'requests' => $requests
        ]);
    }

    /**
     * @Route("/auto", name="messages:auto")
     * @param DocumentManager $dm
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function automaticMessages(DocumentManager $dm, PaginatorInterface $paginator): Response
    {
        return $this->render('messages/list.html.twig', [
            'title' => 'Messages rapides',
            'messages' => $paginator->paginate($dm->getRepository(Message::class)->findBy(['messageType' => 'auto']))
        ]);
    }

    /**
     * @Route("/addonpack", name="messages:addonpack")
     * @param DocumentManager $dm
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function addonPacks(DocumentManager $dm, PaginatorInterface $paginator): Response
    {
        return $this->render('messages/list.html.twig', [
            'title' => 'Packs d\'add-ons',
            'messages' => $paginator->paginate($dm->getRepository(Message::class)->findBy(['messageType' => 'addonpack']))
        ]);
    }

    /**
     * @Route("/error", name="messages:error")
     * @param DocumentManager $dm
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function errorDetails(DocumentManager $dm, PaginatorInterface $paginator): Response
    {
        return $this->render('messages/list.html.twig', [
            'title' => 'Détails erreurs',
            'messages' => $paginator->paginate($dm->getRepository(Message::class)->findBy(['messageType' => 'error']))
        ]);
    }

    /**
     * @Route("/{messageId}", name="messages:edit", methods={"GET"})
     * @param Request $request
     * @param DocumentManager $dm
     * @return Response
     */
    public function editMessage(Request $request, DocumentManager $dm): Response
    {
        $message = $dm->getRepository(Message::class)->findOneBy(['_id' => $request->get('messageId')]);
        if (!$message) return new RedirectResponse($this->generateUrl('messages'));
        $messageEditRequest = $dm->createQueryBuilder(MessageEditRequest::class)
            ->field('message.id')->equals($message->getId())
            ->getQuery()
            ->getSingleResult();
        return $this->render('messages/edit.html.twig', [
            'addonPack' => $message,
            'editForbidden' => isset($messageEditRequest)
        ]);
    }

    /**
     * @Route("/edit", name="messages:postEdit", methods={"POST"})
     * @param Request $request
     * @return Response
     * @throws MongoDBException
     */
    public function postEdit(Request $request, DocumentManager $dm): Response
    {
        $messageId = $request->request->get('messageId');
        $newName = $request->request->get('name');
        $newAliases = $request->request->all('aliases');
        $newContent = urldecode($request->request->get('content'));
        if (!$messageId || !$newName || !$newAliases || !$newContent) {
            $this->addFlash('error', 'Certains champs sont vides, merci de réessayer votre édition.');
            return $this->redirectToRoute('messages:edit', ['messageId' => $messageId]);
        }
        $message = $dm->getRepository(Message::class)->findOneBy(['_id' => $messageId]);
        if (!$message) {
            $this->addFlash('error', 'Le message avec identifiant ' . $messageId . ' n\'a pas été trouvé dans nos bases de données.');
            return $this->redirectToRoute('messages:edit', ['messageId' => $messageId]);
        }
        /** @var User $user */
        $user = $this->getUser();

        $edit = new MessageEditRequest();
        $edit->setUser($user);
        $edit->setMessage($message);
        $edit->setNewName($newName);
        $edit->setNewAliases($newAliases);
        $edit->setNewContent($newContent);
        $dm->persist($edit);
        $dm->flush();

        $this->addFlash('success', 'Votre suggestion de modification a été enregistrée. Elle sera traitée prochainement !');
        return $this->redirectToRoute('messages');
    }

}
