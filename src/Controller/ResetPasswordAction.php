<?php
namespace App\Controller;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordAction
{
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;
    /**
     * @var EntityManager
     */
    private $manager;
    /**
     * @var JWTTokenManagerInterface
     */
    private $tokenManager;

    /**
     * ResetPasswordAction constructor.
     * @param ValidatorInterface $validator
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param EntityManagerInterface $manager
     * @param JWTTokenManagerInterface $tokenManager
     */
    public function __construct(ValidatorInterface $validator, UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $manager, JWTTokenManagerInterface $tokenManager)
    {
        $this->validator = $validator;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->manager = $manager;
        $this->tokenManager = $tokenManager;
    }

    public function __invoke(User $data)
    {
        // $reset = new ResetPasswordAction();
        //$reset()
//        var_dump($data->getNewPassword(), $data->getNewRetypedPassword(), $data->getOldPassword());die;

        //validation is only called after we return data from this action
        $this->validator->validate($data);

        $data->setPassword(
            $this->userPasswordEncoder->encodePassword($data, $data->getNewPassword())
        );

        $data->setPasswordChangedDate(time());

        // after password chnge, old tokens are still valid

        $this->manager->flush();

        $token = $this->tokenManager->create($data);

        return new JsonResponse(['token' => $token]);
    }
}