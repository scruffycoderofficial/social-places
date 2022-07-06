<?php

namespace Oro\Bundle\AttachmentBundle\Controller\Api\Rest;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Oro\Bundle\SoapBundle\Controller\Api\Rest\RestGetController;
use Oro\Bundle\SoapBundle\Entity\Manager\ApiEntityManager;
use Oro\Bundle\SoapBundle\Handler\Context;
use Oro\Bundle\SoapBundle\Model\BinaryDataProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Provides REST API actions for File entity.
 *
 * @RouteResource("file")
 * @NamePrefix("oro_api_")
 */
class FileController extends RestGetController implements ClassResourceInterface
{
    /**
     * Get file.
     *
     * @param Request $request
     * @param int $id
     * @param string $_format
     *
     * @Get(
     *      "/files/{id}",
     *      requirements={"_format"="json|binary"}
     * )
     * @ApiDoc(
     *      description="Get file",
     *      resource=true
     * )
     *
     * @return Response
     */
    public function getAction(Request $request, $id, $_format)
    {
        if ($_format) {
            $request->setRequestFormat($_format);
        }

        return $this->handleGetRequest($id);
    }

    /**
     * Get entity Manager
     *
     * @return ApiEntityManager
     */
    public function getManager()
    {
        return $this->get('oro_attachment.manager.file.api');
    }

    /**
     * {@inheritdoc}
     *
     * Implement a view handler for FOS Rest Bundle in BAP-8351.
     */
    protected function buildResponse($data, $action, $contextValues = [], $status = Response::HTTP_OK)
    {
        if ($status === Response::HTTP_OK) {
            $format = $this->get('request_stack')->getCurrentRequest()->getRequestFormat();
            if ($format === 'binary') {
                if ($action !== self::ACTION_READ) {
                    throw new BadRequestHttpException('Only single file can be returned in the binary format');
                }

                return $this->getBinaryResponse($data, $action, $contextValues, $status);
            } else {
                if ($data instanceof View) {
                    $data->setData($this->postProcessResponseData($data->getData(), $action, $format));
                } elseif (!empty($data)) {
                    $data = $this->postProcessResponseData($data, $action, $format);
                }

                return parent::buildResponse($data, $action, $contextValues, $status);
            }
        } else {
            return parent::buildResponse($data, $action, $contextValues, $status);
        }
    }

    /**
     * @param array  $data
     * @param string $action
     * @param array  $contextValues
     * @param int    $status
     *
     * @return Response
     */
    protected function getBinaryResponse(array $data, $action, $contextValues, $status)
    {
        $headers = isset($contextValues['headers']) ? $contextValues['headers'] : [];
        unset($contextValues['headers']);

        $headers['Content-Type']        = $data['mimeType'] ?: 'application/octet-stream';
        $headers['Content-Length']      = $data['fileSize'];
        $headers['X-Filename']          = $data['filename'];
        $headers['X-Extension']         = $data['extension'];
        $headers['X-Original-Filename'] = $data['originalFilename'];
        $headers['X-Owner']             = $data['owner'];
        $headers['X-CreatedAt']         = $data['createdAt'];
        $headers['X-UpdatedAt']         = $data['updatedAt'];

        /** @var BinaryDataProviderInterface|null $content */
        $content  = $data['content'];
        $response = new Response(
            null !== $content ? $content->getData() : '',
            $status,
            $headers
        );

        $includeHandler = $this->get('oro_soap.handler.include');
        $includeHandler->handle(new Context(
            $this,
            $this->get('request_stack')->getCurrentRequest(),
            $response,
            $action,
            $contextValues
        ));

        return $response;
    }

    /**
     * @param mixed  $data
     * @param string $action
     * @param string $format
     *
     * @return mixed
     */
    protected function postProcessResponseData($data, $action, $format)
    {
        switch ($action) {
            case self::ACTION_READ:
                if (!empty($data)) {
                    $data['content'] = $this->encodeFileContent($data['content'], $format);
                }
                break;
            case self::ACTION_LIST:
                if (!empty($data)) {
                    foreach ($data as &$item) {
                        $item['content'] = $this->encodeFileContent($item['content'], $format);
                    }
                }
                break;
        }

        return $data;
    }

    /**
     * @param BinaryDataProviderInterface|null $content
     * @param string                           $format
     *
     * @return string|null
     */
    protected function encodeFileContent($content, $format)
    {
        return null !== $content
            ? base64_encode($content->getData())
            : null;
    }
}
