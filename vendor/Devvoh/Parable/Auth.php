<?php
/**
 * @package     Devvoh Components
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Parable;

class Auth {

    protected $authenticated        = false;
    protected $authenticationData   = [];
    protected $user                 = null;

    public function initialize() {
        if ($this->checkAuthentication()) {
            $data = $this->getAuthenticationData();
            $userId = $data['user_id'];
            $user = App::createRepository('User')->getById($userId);
            if (!$user) {
                $this->setAuthenticated(false);
                $this->setAuthenticationData([]);
                return false;
            }
            $this->setUser($user);
            return true;
        }
    }

    public function checkAuthentication() {
        $authSession = App::getSession()->get('auth');
        if ($authSession) {
            $this->setAuthenticated($authSession['authenticated']);
            $this->setAuthenticationData($authSession['data']);
            return true;
        }
        return false;
    }

    public function setAuthenticated($value = true) {
        $this->authenticated = (bool)$value;
        return $this;
    }

    public function isAuthenticated() {
        return $this->authenticated;
    }

    public function setAuthenticationData($data = []) {
        $this->authenticationData = $data;
        return $this;
    }

    public function getAuthenticationData() {
        return $this->authenticationData;
    }

    public function setUser($user) {
        $this->user = $user;
        return $this;
    }

    public function getUser() {
        return $this->user;
    }

    public function authenticate($passwordProvided, $passwordHash) {
        if (password_verify($passwordProvided, $passwordHash)) {
            $this->setAuthenticated(true);
            App::getSession()->set('auth', [
                'authenticated' => true,
                'data' => $this->authenticationData,
            ]);
        } else {
            $this->setAuthenticated(false);
            App::getSession()->remove('auth');
        }
        return $this->isAuthenticated();
    }

}