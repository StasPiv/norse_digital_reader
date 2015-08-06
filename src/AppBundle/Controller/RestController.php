<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/api")
 */
class RestController extends Controller
{
    /**
     * @Route("/sources/{user}", defaults={"user": 1}, requirements={
     *     "user": "\d+"
     * })
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
}