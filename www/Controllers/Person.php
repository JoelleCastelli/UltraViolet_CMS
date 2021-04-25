<?php

namespace App\Controller;

use App\Core\Helpers;
use App\Core\View;
use App\Core\FormValidator;
use App\Models\Person as PersonModel;
use App\Models\Page;

class Person
{

    public function showAllAction() {
        $view = new View("users/list");
        $view->assign('title', 'Utilisateurs');
    }

	public function defaultAction() {
		echo "User default";
	}

	public function deleteAction() {
	    $user = new PersonModel();
	    $user->setId(11);
	    $user->setDeletedAt(Helpers::getCurrentTimestamp());
	    $user->save();
    }

    public function connectionAction() {
        $user = new PersonModel();
        $view = new View("connection");

        $form = $user->formBuilderLogin();

        if(!empty($_POST)) {

            $errors = FormValidator::check($form, $_POST);
            if(empty($errors)){

                $person = $user->selectWhere("email",htmlspecialchars($_POST['email']));
                if(!empty($person))
                {

                    $person = $person[0];
                    if(password_verify($_POST['pwd'], $person->getPassword() ))
                    {
                        echo "connection succed" . "<br>";
                    }else {
                        echo "connection failed". "<br>";
                    }
                }else{
                    echo "person not exist". "<br>";
                }

            }else {
                echo "errors". "<br>";
            }
        }

        $view->assign("form", $form);

    }

	public function registerAction() {

		$user = new PersonModel();
		$form = $user->formBuilderRegister();
        $view = new View("register");

        if(!empty($_POST)) {

            $errors = FormValidator::check($form, $_POST);
            if(empty($errors)){

                // Init some values
                $dateNow = new \DateTime('now');
                $updatedAt = $dateNow->format("Y-m-d H:i:s");
                $pwd = password_hash(htmlspecialchars($_POST["pwd"]), PASSWORD_DEFAULT);

                // Required
				$user->setFullName(htmlspecialchars($_POST["fullName"]));
				$user->setPseudo(htmlspecialchars($_POST["pseudo"]));
                $user->setEmail(htmlspecialchars($_POST["email"]));
                $user->setPassword($pwd);
                $user->setUpdatedAt($updatedAt);

                // Default
                $user->setRole('user');
                $user->setOptin(0);
                $user->setUvtrMediaId(1);
                $user->setDeletedAt(null);

				$user->save();
			}else{
                $view->assign("errors", $errors);
			}
		}

        $view->assign("form", $form);
        $view->assign("formLogin", $user->formBuilderLogin());
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

	public function showAction(){
		
		//Affiche la vue user intégrée dans le template du front
		$view = new View("user"); 
	}
	
}
