<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;

class VoteController extends AbstractController
{
    /**
     * @Route("/votes/{id}/vote/{direction<up|down>}", methods="POST")
     */
    public function vote($id, $direction, EntityManagerInterface $entityManager)
    {
        $message = $entityManager->getRepository(Message::class)->findOneBy(['id'=>$id]);

        $currentVoteCount = $message->getVotes();
        if ($direction === 'up')
        {
            $currentVoteCount+=1;
        } elseif ($direction === 'down') {
            $currentVoteCount-=1;

        }
        $message->setVotes($currentVoteCount);
        $entityManager->flush();

        return $this->json(['votes'=>$currentVoteCount]);
    }
}
