<?php
namespace App\Controller;

use App\Entity\Message;
use App\Entity\Users;
use App\Entity\Follow;

use App\Security\LoginFromAuthenticator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;

class LuckyController extends AbstractController
{
    /**
    * @Route("/", name="app_homepage")
    */
    public function homepage(EntityManagerInterface $entityManager): Response
    {
        $follows = $entityManager->getRepository(Follow::class)->findFollowsByFollowerId($this->getUser()->getId());


        $messages = $entityManager->getRepository(Message::class)->findBy(['userId'=>$follows], ['date' => 'DESC']);
        $users = $entityManager->getRepository(Users::class)->findBy(['id'=>$follows]);

        $infoMsg ='';
        if(sizeof($messages)==0)
            $infoMsg = 'No messages';

        return $this->render('lucky/homepage.html.twig',[
            'messages'=>$messages,
            'users'=>$users,
            'infoMsg'=>$infoMsg
        ]);
    }

    /**
     * @Route("/lucky/{slug}", name="app_lucky_show")
     */
    public function show($slug, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(Users::class)->findOneBy(['name'=>$slug]);

        if(is_null($user))
            return $this->render('lucky/info.html.twig', ['info'=>'User '.$slug.' not found.']);

        $userId = $user->getId();
        $messages = $entityManager->getRepository(Message::class)->findBy(['userId' => $userId], ['date' => 'DESC']);

        $infoMsg ='';
        if(sizeof($messages)==0)
            $infoMsg = 'No messages';

        $follows = $entityManager->getRepository(Follow::class)->findFollowsByFollowerId($this->getUser()->getId());
        $follow = in_array($userId, $follows);

        return $this->render('lucky/show.html.twig', [
            'messages'=>$messages,
            'userName'=>$slug,
            'follow'=>$follow,
            'followerId'=>$this->getUser()->getId(),
            'userId'=>$userId,
            'infoMsg'=>$infoMsg
    ]);
    }
}

