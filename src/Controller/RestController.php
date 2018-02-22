<?php

namespace MNC\RestBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use League\Fractal\TransformerAbstract;
use MNC\AgileBundle\ObjectManager\AbstractObjectManager;
use MNC\RestBundle\ApiProblem\ApiError;
use MNC\RestBundle\ApiProblem\ApiProblem;
use MNC\RestBundle\ApiProblem\ApiProblemException;
use MNC\RestBundle\Fractalizer\Fractalizer;
use MNC\RestBundle\Helper\RestInfo;
use MNC\RestBundle\Helper\RestInfoInterface;
use MNC\RestBundle\Helper\RouteActionVerb;
use MNC\RestBundle\Manager\AbstractResourceManager;
use MNC\RestBundle\Security\OwnableResourceVoter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Annotation\Route;

/**
 * This controller serves as a base controller for Rapid Api development. To
 * learn how to use it, see the docs for more info.
 *
 * @package ApiBundle\Controller
 * @author Matías Navarro Carter <mnavarro@option.cl>
 * @docs https://github.com/mnavarrocarter/rest-bundle/blob/master/src/Resources/docs/1.rest-controller.md
 */
abstract class RestController extends Controller
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $entity;
    /**
     * @var string
     */
    protected $identifier;
    /**
     * @var string
     */
    protected $form;
    /**
     * @var string
     */
    protected $transformer;
    /**
     * @var string
     */
    protected $manager;
    /**
     * @var string
     */
    protected $action;
    /**
     * @var RestInfoInterface
     */
    private $restInfo;

    /**
     * This method boots the controller giving it the RestInfo object.
     * Sets all the necessary params for this controller to work properly.
     * Also passes the RestInfo to the Resource Manager.
     * @param RestInfoInterface $restInfo
     */
    public function boot(RestInfoInterface $restInfo)
    {
        $this->name = $restInfo->getName();
        $this->entity = $restInfo->getEntityClass();
        $this->form = $restInfo->getFormClass();
        $this->identifier = $restInfo->getIdentifier();
        $this->action = $restInfo->getActionVerb();
        $this->transformer = $restInfo->getTransformerClass();
        $this->manager = $restInfo->getManagerClass();
        $this->restInfo = $restInfo;
        $manager = $this->getManager();
        if ($manager instanceof AbstractResourceManager) {
            $manager->setRestInfo($restInfo);
        }
    }

    /**
     * @return RestInfoInterface
     */
    protected function getRestInfo()
    {
        return $this->restInfo;
    }

    /**
     * @return AbstractResourceManager|EntityManagerInterface
     */
    protected function getManager()
    {
        if ($this->manager === null) {
            return $this->get('doctrine.orm.entity_manager');
        }
        return $this->get($this->manager);
    }

    /**
     * @return TransformerAbstract
     */
    protected function getTransformer()
    {
        return $this->get($this->transformer);
    }

    /**
     * Gets a single resource from the database, based on the identifier.
     * @param $id
     * @return QueryBuilder
     */
    protected function getResourceByIdentifierQuery($id)
    {
        /** @var EntityRepository $repo */
        $repo = $this->getDoctrine()->getRepository($this->entity);
        $query = $repo->createQueryBuilder($this->name);
        if (strpos(',', $id) === false) {
            $id = explode(',', $id);
            return $query->andWhere($query->expr()->in($this->name . '.' . $this->identifier, ':identifier'))
                ->setParameter('identifier', $id);
        }
        return $query->andWhere($this->name.'.'.$this->identifier = ':identifier')
            ->setParameter('identifier', $id);
    }

    /**
     * Build an absolute route to include in the location header.
     * @param      $entity
     * @param null $route
     * @return string
     */
    protected function buildLocationHeaderUrl($entity, $route = null)
    {
        $pa = PropertyAccess::createPropertyAccessor();
        if ($route === null) {
            $route = 'api_'.$this->name.'_show';
        }
        return $this->get('router')->generate($route, ['id' => $pa->getValue($entity, $this->identifier)], 0);
    }

    /**
     * @param mixed  $attributes
     * @param null   $subject
     * @param string $message
     */
    protected function denyAccessUnlessGranted($attributes, $subject = null, $message = 'Access Denied.')
    {
        if (!$this->isGranted($attributes, $subject)) {
            $request = $this->get('request_stack')->getCurrentRequest();
            $message = sprintf('You do not have permissions to %s the requested resource', RouteActionVerb::findVerb($request->attributes->get('_route')));
            throw $this->createAccessDeniedException($message);
        }
    }

    /**
     * @param string          $message
     * @param \Exception|null $previous
     * @return ApiProblemException|NotFoundHttpException
     */
    protected function createNotFoundException($message = 'Not Found.', \Exception $previous = null)
    {
        $apiProblem = ApiProblem::create(404, $message);
        return $apiProblem->toException();
    }

    /**
     * @param string          $message
     * @param \Exception|null $previous
     * @return ApiProblemException|\Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    protected function createAccessDeniedException($message = 'Access Denied.', \Exception $previous = null)
    {
        $apiProblem = ApiProblem::create(403, $message);
        return $apiProblem->toException();
    }

    /**
     * @param string          $message
     * @param \Exception|null $previous
     * @return ApiProblemException
     */
    protected function createBadRequestException($message = 'Bad Request.', \Exception $previous = null)
    {
        $apiProblem = ApiProblem::create(400, $message);
        return $apiProblem->toException();
    }

    /**
     * @param FormInterface $form
     * @return ApiProblemException
     */
    protected function createValidationErrorException(FormInterface $form)
    {
        $normalizedForm = $this->get('serializer')->normalize($form);
        $apiProblem = ApiProblem::create(400, $normalizedForm['errors'], ApiError::TYPE_VALIDATION_ERROR);
        return $apiProblem->toException();
    }

    /**
     * Builds a response based on the data provided. Tries to guess if should be
     * paginated or not.
     * @param       $data
     * @param int   $statusCode
     * @param array $headers
     * @return JsonResponse
     * @throws \Exception
     * @throws \HttpResponseException
     */
    protected function createResourceResponse($data = null, $statusCode = 200, $headers = [])
    {
        if ($data === null && $statusCode !== 204) {
            throw new \HttpResponseException("You cannot have null data without a 204 status code");
        }

        if ($data === null) {
            return new JsonResponse(null, 204);
        }

        $array = $this->fractalize($data, $this->getTransformer());

        return JsonResponse::create($array, $statusCode, $headers);
    }

    /**
     * @param null $class
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getRepo($class = null) {
        if ($class) {
            return $this->getDoctrine()->getRepository($class);
        }
        return $this->getDoctrine()->getRepository($this->entity);
    }

    /**
     * @param                     $data
     * @param TransformerAbstract $transformer
     * @return array
     * @throws \Exception
     */
    protected function fractalize($data, TransformerAbstract $transformer)
    {
        return $this->get(Fractalizer::class)->fractalize($data, $transformer);
    }
}