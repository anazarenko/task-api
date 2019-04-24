<?php

namespace App\Module\User;

use App\Database\Database;
use App\Module\AbstractModule;
use App\Utils\PasswordEncoder;
use App\Utils\Response;

/**
 * Class TaskRepository
 * @package App\Module\TaskRepository
 */
class UserModule extends AbstractModule
{
    /** @var UserRepository */
    private $userRepository;
    /** @var TokenRepository */
    private $tokenRepository;
    /** @var UserValidator */
    private $validator;

    /**
     * UserModule constructor.
     * @param array $routeArguments
     */
    public function __construct(array $routeArguments)
    {
        parent::__construct($routeArguments);
        $dbConnection = (new Database())->getConnection();
        $this->userRepository = new UserRepository($dbConnection);
        $this->tokenRepository = new TokenRepository($dbConnection);
        $this->validator = new UserValidator();
    }

    /**
     * @return null|string
     */
    protected function getAction(): ?string
    {
        if ($this->method === 'POST' && isset($this->routeArguments[1])) {
            switch ($this->routeArguments[1]) {
                case 'registration':
                    return 'registration';
                case 'login':
                    return 'login';
            }
        }

        return null;
    }

    /**
     * Registration action
     */
    public function registration(): void
    {
        $data = $this->getDataFromInput();
        $this->validator->validateAuth($data);

        $currentUser = $this->userRepository->findOneByEmail($data->email);

        if ($currentUser !== null) {
            throw new \RuntimeException('This email is already used', Response::STATUS_BAD_REQUEST);
        }

        $user = new User();
        $user->setEmail($data->email)
            ->setPassword(PasswordEncoder::encode($data->password));

        $this->userRepository->saveUser($user);
        $token = $this->generateToken($user->getId());
        $this->tokenRepository->saveToken($token, $user->getId());

        Response::response([
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
            ],
            'token' => $token
        ]);
    }

    /**
     * Login action
     */
    public function login(): void
    {
        $data = $this->getDataFromInput();
        $this->validator->validateAuth($data);

        $currentUser = $this->userRepository->findOneByEmail($data->email);

        if ($currentUser === null || !password_verify($data->password, $currentUser->getPassword())) {
            throw new \RuntimeException('Invalid email or password', Response::STATUS_BAD_REQUEST);
        }

        $token = $this->generateToken($currentUser->getId());
        $this->tokenRepository->saveToken($token, $currentUser->getId());

        Response::response([
            'user' => [
                'id' => $currentUser->getId(),
                'email' => $currentUser->getEmail(),
            ],
            'token' => $token
        ]);
    }

    /**
     * @param int $userId
     * @return string
     */
    private function generateToken(int $userId): string
    {
        return md5(uniqid($userId, true));
    }
}