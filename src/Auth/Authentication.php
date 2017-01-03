<?php

namespace Parable\Auth;

class Authentication
{
    /** @var null|\Model\User */
    protected $user                 = null;

    /** @var \Parable\Framework\Toolkit */
    protected $toolkit;

    /** @var \Parable\Http\Values\Session */
    protected $session;

    /** @var bool */
    protected $authenticated      = false;

    /** @var array */
    protected $authenticationData = [];

    /**
     * Auth constructor.
     * @param \Parable\Framework\Toolkit   $toolkit
     * @param \Parable\Http\Values\Session $session
     */
    public function __construct(
        \Parable\Framework\Toolkit   $toolkit,
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
            $userId = $data['user_id'];
            $user = $this->toolkit->getRepository(\Model\User::class)->getById($userId);
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
     * @return $this
     */
    public function setAuthenticationData($data = [])
    {
        $this->authenticationData = $data;
        return $this;
    }

    /**
     * Return the authentication data, if any
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
     * @param \Model\User $user
     *
     * @return $this
     */
    public function setUser(\Model\User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Return the user entity
     *
     * @return \Model\User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Check whether the provided password matches the password hash. If so, return true.
     *
     * @param string $passwordProvided
     * @param string $passwordHash
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
     * Revoke an existing authentication.
     */
    public function revokeAuthentication()
    {
        $this->user = null;
        $this->setAuthenticated(false);
        $this->session->remove('auth');
    }
}
