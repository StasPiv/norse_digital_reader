<?php

namespace AppBundle\Controller;

use AppBundle\Model\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Model\Source;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Route("/api")
 */
class RestController extends Controller
{
    private $sessionKeyForCurrentUser = 'current_user_id';

    /**
     * @Route("/sources/{user}", defaults={"user": 1}, requirements={
     *     "user": "\d+"
     * })
     * @Method({"GET"})
     * @param integer $user
     * @return string
     */
    public function sourcesAction($user)
    {
        $qb = $this->getDoctrine()->getEntityManager()->createQueryBuilder();

        $qb->select(array('fs.id','fs.type','fs.source'))
           ->from('AppBundle:UserSource', 'us')
           ->innerJoin('AppBundle:FeedSource', 'fs', 'WITH', 'fs.id = us.sourceId')
           ->where('us.userId = ' . (int)$user)
           ->orderBy('fs.id', 'DESC');

        return new JsonResponse(['users' => $qb->getQuery()->getResult()]);
    }

    /**
     * @Route("/feeds/{source}", defaults={"source": 0}, requirements={
     *     "source": "\d+"
     * })
     * @Method({"GET"})
     * @param integer $source
     * @return string
     */
    public function feedsAction($source)
    {
        $qb = $this->getDoctrine()->getEntityManager()->createQueryBuilder();

        $qb->select(array('f.id', 'f.title', 'f.content'))
           ->from('AppBundle:Feed', 'f')
           ->where('f.sourceId = ' . (int)$source)
           ->orderBy('f.id', 'DESC');

        return new JsonResponse(['feeds' => $qb->getQuery()->getResult()]);
    }

    /**
     * @Route("/update/{sourceId}", defaults={"source": 0}, requirements={
     *     "sourceId": "\d+"
     * })
     * @Method({"PUT"})
     * @param integer $sourceId
     * @return string
     */
    public function updateAction($sourceId)
    {
        $source = new Source();
        $source->setEm($this->getDoctrine()->getEntityManager());

        $result = $source->update($sourceId);

        return new JsonResponse(['result' => $result]);
    }

    /**
     * @Route("/source/add/")
     * @Method({"PUT","POST"})
     * @param Request $request
     * @return string
     */
    public function addAction(Request $request)
    {
        $newSource = $request->request->get('source');
        $type = $request->request->get('type');

        $source = new Source();
        $source->setEm($this->getDoctrine()->getEntityManager());

        return new JsonResponse(['result' => $source->add($newSource, $type, $this->getCurrentUserId())]);
    }

    /**
     * @Route("/source/remove/")
     * @Method({"DELETE"})
     * @param Request $request
     * @return string
     */
    public function removeAction(Request $request)
    {
        $sourceId = $request->request->get('source');

        $source = new Source();
        $source->setEm($this->getDoctrine()->getEntityManager());

        return new JsonResponse(['result' => $source->remove($sourceId, 0, $this->getCurrentUserId())]);
    }

    /**
     * @Route("/register/")
     * @Method({"POST"})
     * @param Request $request
     * @return string
     */
    public function registerAction(Request $request)
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $repeatPassword = $request->request->get('repeat');

        $user = new User($email);
        $user->setEm($this->getDoctrine()->getEntityManager());

        $result = $user->register($password, $repeatPassword, $userId);

        if ($result) {
            $this->setCurrentUserId($userId);
        }

        return new JsonResponse(['result' => $result, 'user_id' => $userId]);
    }

    /**
     * @Route("/auth/")
     * @Method({"PUT","POST"})
     * @param Request $request
     * @return string
     */
    public function authAction(Request $request)
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        $user = new User($email);
        $user->setEm($this->getDoctrine()->getEntityManager());

        $result = $user->auth($password, $userId);

        if ($result) {
            $this->setCurrentUserId($userId);
        }

        return new JsonResponse(['result' => $result]);
    }

    /**
     * @Route("/logout/")
     * @Method({"PUT","POST"})
     * @param Request $request
     * @return string
     */
    public function logoutAction(Request $request)
    {
        $session = new Session();
        $session->remove($this->sessionKeyForCurrentUser);
        return new JsonResponse(['result' => true]);
    }

    /**
     * @return integer
     */
    private function getCurrentUserId()
    {
        $session = new Session();
        return $session->get($this->sessionKeyForCurrentUser);
    }

    /**
     * @param integer $userId
     */
    private function setCurrentUserId($userId)
    {
        $session = new Session();
        $session->set($this->sessionKeyForCurrentUser, $userId);
    }

}