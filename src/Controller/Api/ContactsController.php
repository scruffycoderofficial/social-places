<?php

namespace App\Controller\Api;

use App\Entity\Contact;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mime\Email;
use App\Repository\ContactRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Class ContactsController
 *
 * @package App\Controller\Api
 */
class ContactsController extends ApiController
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ContactRepository
     */
    private $contactRepository;

    /**
     * ContactsController constructor.
     *
     * @param LoggerInterface $logger
     * @param ContactRepository $contactRepository
     */
    public function __construct(ContactRepository $contactRepository, LoggerInterface $logger, MailerInterface $mailer)
    {
        $this->contactRepository = $contactRepository;

        $this->logger = $logger;

        $this->mailer = $mailer;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $result = $this->contactRepository->findAll();

        if ($result === null) {
            return $this->json($result, Response::HTTP_NOT_FOUND);
        }

        return $this->json($result, Response::HTTP_OK);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function add(Request $request): JsonResponse
    {
        $request = $this->transformJsonBody($request);

        try {

            $contact = new Contact();

            $contact->setName($request->get('name'));
            $contact->setEmail($request->get('email'));
            $contact->setGender($request->get('gender'));
            $contact->setContent($request->get('content'));

            $this->contactRepository->add($contact, true);

        } catch (OptimisticLockException | ORMException $e) {

            $this->logger->error($e->getMessage());
        }

        $result = $this->contactRepository->findByEmail($request->get('email'));

        $userExists = !empty($result);

        if ($userExists) {

            $email = (new Email())
                ->from('no-reply@assessments.com')
                ->to($request->get('email'))
                ->subject('Social Places Assessment Demo')
                ->text('Welcome to Social Places Assessment Demo.')
                ->html('<p>Welcome to Social Places Assessment Demo.</p>');

            $this->mailer->send($email);

            return $this->json($result, Response::HTTP_CREATED);
        }

        return $this->json([], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}