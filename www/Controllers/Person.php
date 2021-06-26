<?php

namespace App\Controller;

use App\Core\Helpers;
use App\Core\View;
use App\Core\FormValidator;
use App\Core\Mail;
use App\Models\Person as PersonModel;

class Person
{
    protected $columnsTable;

    public function __construct()
    {
        $this->columnsTable = [
            "name" => 'Nom et prénom',
            "pseudo" => 'Pseudonyme',
            "mail" => 'Email',
            "checkMail" => 'Verification email',
            "actions" => 'Actions'
        ];
    }

    public function showAllAction() {
        $persons = new PersonModel();
        $persons = $persons->selectWhere('role', 'admin');
        if(!$persons) $persons = [];


        $view = new View("persons/list");
        $view->assign('title', 'Utilisateurs');
        $view->assign('columnsTable', $this->columnsTable);
        $view->assign('bodyScripts', [PATH_TO_SCRIPTS . 'bodyScripts/persons/person.js']);
    }

	public function defaultAction() {
		echo "User default";
	}

	public function deleteAction() {
	    $user = new PersonModel();
	    $user->setId(3);
	    $user->setDeletedAt(Helpers::getCurrentTimestamp());
	    $user->save();
    }

    public function loginAction() {
        $user = new PersonModel();
        $view = new View("login", "front");
        $form = $user->formBuilderLogin();
        $view->assign("form", $form);

        if(!empty($_POST)) {
            $errors = FormValidator::check($form, $_POST);
            if(empty($errors)) {
                $user = $user->findOneBy("email", $_POST['email']);
                if(!empty($user)) {
                    if(password_verify($_POST['password'], $user->getPassword())) {
                        if($user->isEmailConfirmed()) {
                            $_SESSION['loggedIn'] = true;
                            $_SESSION['user_id'] = $user->getId();
                            Helpers::setFlashMessage('success', "Bienvenue ".$user->getPseudo());
                            Helpers::redirect('/');
                        } else {
                            $errors[] = "Merci de confirmer votre adresse e-mail. Renvoyer l'email de confirmation";
                        }
                    } else {
                        $errors[] = "Les identifiants ne sont pas reconnus";
                    }
                } else {
                    $errors[] = "Les identifiants ne sont pas reconnus";
                }
            }
            $view->assign("errors", $errors);
        }
    }

	public function registerAction() {
		$user = new PersonModel();
        $view = new View('register', 'front');
        $form = $user->formBuilderRegister();
        $view->assign("form", $form);

        if(!empty($_POST)) {
            $errors = FormValidator::check($form, $_POST);
            if(empty($errors)) {
                if($user->findOneBy("pseudo", $_POST['pseudo'])) {
                    $errors[] = 'Ce pseudonyme est indisponible';
                }
                if($user->findOneBy("email", $_POST['email'])) {
                    $errors[] = 'Cette adresse e-mail est déjà utilisée';
                }
                if(empty($errors)) {
                    $user->setPseudo(htmlspecialchars($_POST['pseudo']));
                    $user->setEmail(htmlspecialchars($_POST['email']));
                    $user->setPassword(password_hash(htmlspecialchars($_POST['pwd']), PASSWORD_DEFAULT));
                    //$user->setDefaultProfilePicture();
                    $user->setMediaId(1);
                    $user->generateEmailKey();

                    $to   = $_POST['email'];
                    $from = 'ultravioletcms@gmail.com';
                    $name = 'Ultaviolet';
                    $subj = 'Confirmation mail';
                    $msg = $user->verificationMail($_POST['pseudo'], $user->getEmailKey());
                    
                    $mail = new Mail();
                    $mail->sendMail($to, $from, $name, $subj, $msg);
                    $user->save();
                
                    Helpers::setFlashMessage('success', "Votre compte a bien été créé ! Un e-mail de confirmation
                    vous a été envoyé sur " .$_POST['email'].". </br> Cliquez sur le lien dans ce mail avant de vous connecter.");
                    Helpers::redirect('/connexion');
                }
			}
            $view->assign("errors", $errors);
		}
	}

    public function getUsersAction() {
        if(!empty($_POST['role'])) {
            $users = new PersonModel();
            $users = $users->selectWhere('role', htmlspecialchars($_POST['role']));
            
            if(!$users) $users = [];
            
            $usersArray = [];
            foreach ($users as $user) {
                $usersArray[] = [
                    $this->columnsTable['name'] => $user->getFullName(),
                    $this->columnsTable['pseudo'] => $user->getPseudo(),
                    $this->columnsTable['mail'] => $user->getEmail(),
                    $this->columnsTable['checkMail'] => $user->isEmailConfirmed(),
                    $this->columnsTable['actions'] => $user->generateActionsMenu(),
                ];
            }
            echo json_encode(["users" => $usersArray]);
        }
    }

	public function logoutAction() {
        session_destroy();
        Helpers::redirect('/');
    }

    public function updateAction()
    {
        $user = new UserModel();
        $user->setId(3);
        $user->setFirstname("NON");
        $user->setCountry("pr");
        $user->setRole("6");
        $user->save();
    }

	//Method : Action
	public function addAction(){
		
		//Récupérer le formulaire
		//Récupérer les valeurs de l'internaute si il y a validation du formulaire
		//Vérification des champs (uncitié de l'email, complexité du pwd, ...)
		//Affichage du résultat

	}

    public function updatePersonAction($id) {
        $view = new View("utilisateurs/update");
        $view->assign('title', 'Modification d\'un utilisateur');
        $view->assign('param2', $id);
    }

    public function deletePersonAction() {
        if(!empty($_POST['personId'])) {
            $persons = new PersonModel();
            $persons->setId($_POST['personId']);
            $persons->delete();
        }
    }

    

	public function showAction(){
		
		//Affiche la vue user intégrée dans le template du front
		$view = new View("user"); 
	}

    public function verificationAction($pseudo, $key){

        $view = new View("userVerification", "front");
        $user = new PersonModel();
        $user = $user->select()->where("pseudo", $pseudo)->andWhere("emailkey", $key)->first();
        
        if(!empty($user))
        {
            if($user->isEmailConfirmed() != true)
            {
                $user->setEmailConfirmed(1);

                $user->save();
                Helpers::setFlashMessage('success', "Votre compte à bien était activée.");
                Helpers::redirect('/connexion');
            }else
            {
                Helpers::setFlashMessage('error', "Votre compte est déja activée");
            }

        }else 
        {
            Helpers::setFlashMessage('error', "Aucun utilisateur trouvé");
        }
	}	

    public function forgetPasswordMailAction(){
		
		$user = new PersonModel();
        $view = new View("forgetPassword", "front");
        $form = $user->formBuilderForgetPassword();
        $view->assign("form", $form);

        if(!empty($_POST)) {
            $errors = FormValidator::check($form, $_POST);
            if(empty($errors)) {
                $user = $user->findOneBy("email", $_POST['email']);
                if(!empty($user)) {
                    $to   = $_POST['email'];
                    $from = 'ultravioletcms@gmail.com';
                    $name = 'Ultaviolet';
                    $subj = 'Changée de mot de passe';
                    $msg = $user->forgetPasswordMail($user->getId(), $user->getEmailKey());
                    
                    $mail = new Mail();
                    $mail->sendMail($to, $from, $name, $subj, $msg);
                    Helpers::setFlashMessage('success', " Un e-mail
                    vous a été envoyé sur " .$_POST['email']);
                    Helpers::redirect('/connexion');
                } else {
                    $errors[] = "Aucun compte n'a été trouvé";
                }
            }
            $view->assign("errors", $errors);
        }
	}

    public function resetPasswordAction($id, $key){

        $user = new PersonModel();
        $view = new View("resetPassword", "front");
        $form = $user->formBuilderResetPassword($id, $key);
        $view->assign("form", $form);
        if(!empty($_POST)) {
            $errors = FormValidator::check($form, $_POST);
            if(empty($errors)) {
                $user = $user->select()->where("id", $id)->andWhere("emailkey", $key)->first();
                if(!empty($user)) {
                    if(password_verify($_POST['password'], $user->getPassword())) {
                        Helpers::setFlashMessage('error', "Le mot de passe corespond au mot de passe déjà enregistré."); 
                    } else {
                        $user->setPassword(password_hash(htmlspecialchars($_POST['pwd']), PASSWORD_DEFAULT));
                        $user->save();
                        Helpers::setFlashMessage('success', "Votre mot de passe à bien était changée.");
                        Helpers::redirect('/connexion');
                    }
                } else {
                    $errors[] = "Les identifiants ne sont pas reconnus";
                }
            }
            $view->assign("errors", $errors);
        }
	}
}
