<?php

namespace Parable\Auth;

class Authentication
{
    /** @var null|\Model\Users */
    protected $user;

    /** @var \Parable\Framework\Toolkit */
    protected $toolkit;

    /** @var \Parable\Http\Values\Session */
    protected $session;

    /** @var bool */
    protected $authenticated = false;

    /** @var array */
    protected $authenticationData = [];

    public function __construct(
        \Parable\Framework\Toolkit $toolkit,
        \Parable\Http\Values\Session $session
    ) {
        $this->toolkit = $toolkit;
        $this->session = $session;

        $this->initialize();
    }

    /**
     * Initialize the authentication, picking up on session data if possible.
     *
     * @return bool
     */
    public function initialize()
    {
        if ($this->checkAuthentication()) {
            $data = $this->getAuthenticationData();
            if (!isset($data['user_id'])) {
                return false;
            }
            $userId = $data['user_id'];
            $user = $this->toolkit->getRepository(\Model\Users::class)->getById($userId);
            if (!$user) {
                $this->setAuthenticated(false);
                $this->setAuthenticationData([]);
                return false;
            }
            $this->setUser($user);
            return true;
        }
        return false;
    }

    /**
     * @param $password
     *
     * @return bool|string
     */
    public function generatePasswordHash($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Checks whether there's an auth session
     *
     * @return bool
     */
    protected function checkAuthentication()
    {
        $authSession = $this->session->get('auth');
        if ($authSession) {
            $this->setAuthenticated($authSession['authenticated']);
            $this->setAuthenticationData($authSession['data']);
            return true;
        }
        return false;
    }

    /**
     * Sets whether there's an authenticated user or not
     *
     * @param bool $value
     *
     * @return $this
     */
    public function setAuthenticated($value = true)
    {
        $this->authenticated = (bool)$value;
        return $this;
    }

    /**
     * Checks whether there's an authenticated user or not
     *
     * @return bool
     */
    public function isAuthenticated()
    {
        return $this->authenticated;
    }

    /**
     * Set the data for the user currently authenticated
     *
     * @param array $data
     *
     * @return $this
     */
    public function setAuthenticationData(array $data)
    {
        $this->authenticationData = $data;
        return $this;
    }

    /**
     * Return the authentication data
     *
     * @return array
     */
    public function getAuthenticationData()
    {
        return $this->authenticationData;
    }

    /**
     * Set the authenticated user entity
     *
     * @param \Model\Users $user
     *
     * @return $this
     */
    public function setUser(\Model\Users $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Return the user entity
     *
     * @return null|\Model\Users
     */
    public function getUser()
    {
        if (!$this->user) {
            $this->initialize();
        }
        return $this->user;
    }

    /**
     * Check whether the provided password matches the password hash
     *
     * @param string $passwordProvided
     * @param string $passwordHash
     *
     * @return bool
     */
    public function authenticate($passwordProvided, $passwordHash)
    {
        if (password_verify($passwordProvided, $passwordHash)) {
            $this->setAuthenticated(true);
            $this->session->set('auth', [
                'authenticated' => true,
                'data' => $this->authenticationData,
            ]);
        } else {
            $this->revokeAuthentication();
        }
        return $this->isAuthenticated();
    }

    /**
     * Revoke an existing authentication
     *
     * @return $this
     */
    public function revokeAuthentication()
    {
        $this->user = null;
        $this->setAuthenticated(false);
        $this->session->remove('auth');

        return $this;
    }
}
