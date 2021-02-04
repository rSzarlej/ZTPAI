<?php


namespace App\Controller;


use App\Entity\Follow;
use App\Entity\Message;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use \Datetime;
use \DatetimeZone;

class ButtonsController extends AbstractController
{
    /**
     * @Route("/lucky/saysmthbtn", name="app_saysmthbtn")
     */
    public function saySmthBtn(): Response
    {
        return $this->render('lucky/saysmth.html.twig');
    }

    /**
     * @Route("/lucky/saysmth", methods="POST", name="app_saysmth")
     */
    public function saySmth(EntityManagerInterface $entityManager, Request $request): Response
    {
        $msgText = $request->get('text');

        $message = new Message();

        $now = new DateTime(null, new DateTimeZone('Europe/London'));
        $now->format('Y-m-d H:i:s');

        $message->setUserId($this->getUser()->getId())
            ->setImageId(1)
            ->setVotes(0)
            ->setMsgText($msgText)
            ->setDate($now);


        $entityManager->persist($message);
        $entityManager->flush();
        return $this->render('lucky/info.html.twig',[
            'info'=>'Message added'
        ]);
    }

    /**
     * @Route("/lucky/showmine", name="app_showmine")
     */
    public function showMine(EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(Users::class)->findOneBy(['id'=>$this->getUser()->getId()])->getName();

        $response = $this->forward('App\Controller\LuckyController::show', [
            'slug'  => $user,
        ]);

        return $response;
    }

    /**
     * @Route("/lucky/searchusrbtn", name="app_searchusrbtn")
     */
    public function searchUsrBtn(): Response
    {
        return $this->render('lucky/searchusr.html.twig');
    }

    /**
     * @Route("/lucky/searchusr", methods="POST", name="app_searchusr")
     */
    public function searchUsr(Request $request): Response
    {
        $userName = $request->get('userName');
        $response = $this->forward('App\Controller\LuckyController::show', [
            'slug'  => $userName,
        ]);

        return $response;
    }

    /**
     * @Route("/lucky/{slug}/follow", methods="POST", name="app_followUsr")
     */
    public function followUsr($slug, EntityManagerInterface $entityManager, Request $request): Response
    {
        $userId = $request->request->get('userId');

        $follows = $entityManager->getRepository(Follow::class)->findFollowsByFollowerId($slug);
        $isFollowed = in_array($userId, $follows);

        $userName = $entityManager->getRepository(Users::class)->findOneBy(['id'=>$userId])->getName();

        if($isFollowed)
        {
            //remove

            $id = $entityManager->getRepository(Follow::class)->findOneBy(['user1Id'=>$this->getUser()->getId(), 'user2Id'=>$userId])->getId();
            $ref = $entityManager->getReference(Follow::class, $id);
            $entityManager->remove($ref);
            $entityManager->flush();
            $info = $userName.' is no longer followed.';
        }
        else {
            //add follow
            $follow = new Follow();
            $follow->setUser1Id($this->getUser()->getId())
                ->setUser2Id($userId);
            $entityManager->persist($follow);
            $entityManager->flush();
            $info = $userName.' is now followed.';
        }


        return $this->render('lucky/info.html.twig',[
            'info'=>$info
            ]);
    }
}