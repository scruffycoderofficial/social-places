<?php

namespace Oro\Bundle\EntityBundle\Controller\Api\Rest;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @RouteResource("entity_data")
 * @NamePrefix("oro_api_")
 */
class EntityDataController extends FOSRestController
{
    /**
     * Patch entity field/s data by new values
     *
     * @param int    $id
     * @param string $className
     *
     * @return Response
     *
     * @throws AccessDeniedException
     *
     * @Rest\Patch("entity_data/{className}/{id}")
     * @ApiDoc(
     *      description="Update entity property",
     *      resource=true,
     *      requirements = {
     *          {"name"="id", "dataType"="integer"},
     *      }
     * )
     */
    public function patchAction($className, $id)
    {
        $data = json_decode($this->get('request_stack')->getCurrentRequest()->getContent(), true);
        list($form, $data) = $this->getManager()->patch($className, $id, $data);

        if ($form->getErrors(true)->count() > 0) {
            $view = $this->view($form, Response::HTTP_BAD_REQUEST);
        } else {
            $statusCode = !empty($data) ? Response::HTTP_OK : Response::HTTP_NO_CONTENT;
            $view = $this->view($data, $statusCode);
        }
        $response = parent::handleView($view);

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getManager()
    {
        return $this->get('oro_entity.manager.api.entity_data_api_manager');
    }
}
