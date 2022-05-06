<?php

namespace App\Controller\Api;

use App\Entity\Contact;
use Psr\Log\LoggerInterface;
use App\Repository\ContactRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class ContactsController
 *
 * @package App\Controller\Api
 */
class ContactsController extends AbstractController
{
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
    public function __construct(LoggerInterface $logger, ContactRepository $contactRepository)
    {
        $this->logger = $logger;

        $this->contactRepository = $contactRepository;
    }

    /**
     * @Route("/api/contacts")
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
     * @Route("/api/contact")
     */
    public function add(Request $request): JsonResponse
    {
        $data = $request->request->all();

        if (empty($data)) {
            return $this->json('Missing required data.', Response::HTTP_NO_CONTENT);
        } else {
            if ($success = $this->createContact($data)) {
                return $this->json(['success' => true], Response::HTTP_OK);
            }
        }
    }

    private function createContact(array $data): bool
    {
        $success = false;

        try {

            $contact = new Contact();

            $contact->setName($data['name']);
            $contact->setEmail($data['email']);
            $contact->setGender($data['gender']);
            $contact->setContent($data['content']);

            $this->contactRepository->add($contact);

            $success = true;

        } catch (OptimisticLockException | ORMException $e) {
            $this->logger->warning($e->getMessage());
        } finally{
            return $success;
        }
    }
}