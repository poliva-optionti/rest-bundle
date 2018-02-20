<?php

namespace MNC\RestBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use League\Fractal\TransformerAbstract;
use MNC\RestBundle\ApiProblem\ApiError;
use MNC\RestBundle\ApiProblem\ApiProblem;
use MNC\RestBundle\ApiProblem\ApiProblemException;
use MNC\RestBundle\Fractalizer\Fractalizer;
use MNC\RestBundle\Helper\RouteActionVerb;
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
 * @package ApiBundle\Controller
 * @author Matías Navarro Carter <mnavarro@option.cl>
 * @docs http://docs.link.cl
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
    protected $form;
    /**
     * @var string
     */
    protected $transformer;
    /**
     * @var string
     */
    protected $identifier;
    /**
     * @var string
     */
    protected $action;

    /**
     * @Route("", methods={"GET"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function indexAction(Request $request)
    {
        $kernel = $this->get('kernel');
        /** @var EntityRepository $repo */
        $repo = $this->getDoctrine()->getRepository($this->entity);
        $query = $repo->createQueryBuilder($this->name);

        if ($query instanceof Response) {
            return $query;
        }

        return $this->createResourceResponse($query, 200);
    }

    /**
     * @Route("/new", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function newAction(Request $request)
    {
        $entity = new $this->entity;

        $form = $this->createForm($this->form, $entity, [
            'validation_groups' => ['Default', 'New'],
            'csrf_protection' => false
        ]);

        $normalizedForm = $this->get('liform')->transform($form);

        return new JsonResponse($normalizedForm, 200);
    }

    /**
     * @Route("/{id}/edit", methods={"GET"})
     * @param Request $request
     * @param         $id
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function editAction(Request $request, $id)
    {
        $entity = $this->getResourceByIdentifierQuery($id)
            ->getQuery()->getOneOrNullResult();

        if ($entity === null) {
            throw $this->createNotFoundException("The requested $this->name resource could not be found.");
        }

        $form = $this->createForm($this->form, $entity, [
            'validation_groups' => ['Default', 'Update'],
            'csrf_protection' => false
        ]);

        $normalizedForm = $this->get('liform')->transform($form);

        return new JsonResponse($normalizedForm, 200);
    }

    /**
     * @Route("/{id}", methods={"GET"})
     * @param Request $request
     * @param         $id
     * @return Response
     * @throws \Exception
     */
    public function showAction(Request $request, $id)
    {
        $token = $this->get('security.token_storage')->getToken();

        $query = $this->getResourceByIdentifierQuery($id);
        $result = $query->getQuery()->getResult();

        if ($result === null) {
            throw $this->createNotFoundException("The requested $this->name resource could not be found.");
        }

        if (sizeof($result) <= 1) {
            $result = array_shift($result);
            $this->denyAccessUnlessGranted(OwnableResourceVoter::VIEW, $result);
        } else {
            foreach ($result as $item) {
                $this->denyAccessUnlessGranted(OwnableResourceVoter::VIEW, $item);
            }
        }

        return $this->createResourceResponse($result, 200);
    }

    /**
     * @Route("", methods={"POST"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function storeAction(Request $request)
    {
        $token = $this->get('security.token_storage')->getToken();

        $entity = new $this->entity;

        $form = $this->createForm($this->form, $entity , [
            'validation_groups' => ['Default', 'New'],
            'csrf_protection' => false
        ]);

        $form->submit($request->request->all());

        if ($form->isValid() && $form->isSubmitted()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $url = $this->buildLocationHeaderUrl($entity);

            return $this->createResourceResponse($entity, 201, [
                'Location' => $url
            ]);
        }
        throw $this->createValidationErrorException($form);
    }

    /**
     * @Route("/{id}", methods={"PATCH", "PUT", "POST"})
     * @param Request $request
     * @param         $id
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function updateAction(Request $request, $id)
    {
        $token = $this->get('security.token_storage')->getToken();

        $entity = $this->getResourceByIdentifierQuery($id)
            ->getQuery()->getOneOrNullResult();

        if ($entity === null) {
            throw $this->createNotFoundException("The requested $this->name resource could not be found.");
        }

        $form = $this->createForm($this->form, $entity, [
            'validation_groups' => ['Default', 'Update'],
            'csrf_protection' => false
        ]);

        $form->submit($request->request->all(), !$request->isMethod('PATCH'));

        $this->denyAccessUnlessGranted(OwnableResourceVoter::UPDATE, $entity);

        if ($form->isValid() && $form->isSubmitted()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $url = $this->buildLocationHeaderUrl($entity);

            return $this->createResourceResponse($entity, 200, [
                'Location' => $url
            ]);
        }
        throw $this->createValidationErrorException($form);

    }

    /**
     * @Route("/{id}", methods={"DELETE"})
     * @param Request $request
     * @param         $id
     * @return Response
     * @throws \Exception
     */
    public function deleteAction(Request $request, $id)
    {
        $entity = $this->getResourceByIdentifierQuery($id)
            ->getQuery()->getOneOrNullResult();

        if ($entity === null) {
            throw $this->createNotFoundException("The requested $this->name resource could not be found.");
        }

        // TODO: Override this method to throw an Api Problem
        $this->denyAccessUnlessGranted(OwnableResourceVoter::DELETE, $entity);

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->createResourceResponse(null, 204);
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param mixed $entity
     * @return RestController
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * @param mixed $form
     * @return RestController
     */
    public function setForm($form)
    {
        $this->form = $form;
        return $this;
    }

    /**
     * @param $transformer
     * @return $this
     */
    public function setTransformer($transformer)
    {
        $this->transformer = $transformer;
        return $this;
    }

    /**
     * @param mixed $identifier
     * @return RestController
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     * @return RestController
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * Gets a single resource from the database, based on the identifier.
     * @param $id
     * @return QueryBuilder
     */
    private function getResourceByIdentifierQuery($id)
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
    public function denyAccessUnlessGranted($attributes, $subject = null, $message = 'Access Denied.')
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
    public function createNotFoundException($message = 'Not Found.', \Exception $previous = null)
    {
        $apiProblem = ApiProblem::create(404, $message);
        return $apiProblem->toException();
    }

    /**
     * @param string          $message
     * @param \Exception|null $previous
     * @return ApiProblemException|\Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function createAccessDeniedException($message = 'Access Denied.', \Exception $previous = null)
    {
        $apiProblem = ApiProblem::create(403, $message);
        return $apiProblem->toException();
    }

    /**
     * @param string          $message
     * @param \Exception|null $previous
     * @return ApiProblemException
     */
    public function createBadRequestException($message = 'Bad Request.', \Exception $previous = null)
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

        $data = $this->fractalize($data, $this->get($this->transformer));

        return JsonResponse::create($data, $statusCode, $headers);
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