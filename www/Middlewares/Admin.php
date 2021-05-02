<?php

namespace App\Middleware;

use App\Core\Request;

class Admin {

    // TODO : assigner les erreurs + vérifier la connexion
    public function handle() {
        $user = Request::getUser();
        if (!($user && $user->isLogged() && $user->isAdmin())) {
            die("Il faut avoir les droits admin");
        }
    }

}