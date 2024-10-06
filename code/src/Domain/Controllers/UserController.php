<?php

namespace Geekbrains\Homework\Domain\Controllers;

use Exception;
use Geekbrains\Homework\Application\Application;
use Geekbrains\Homework\Application\Render;
use Geekbrains\Homework\Domain\Models\User;
use Geekbrains\Homework\Application\Auth;


class UserController extends AbstractController {


    protected array $actionsPermissions = [
        'actionHash' => ['admin', 'some'],
        'actionSave' => ['admin']
    ];


    public function actionIndex() {
        $users = User::getAllUsersFromStorage();
        $render = new Render();
        if(!$users){
            return $render->renderPage(
                'user-empty.twig', 
                [
                    'title' => 'Список пользователей в хранилище',
                    'message' => "Список пуст или не найден"
                ]);
        }
        else{
            return $render->renderPage(
                'user-index.twig', 
                [
                    'title' => 'Список пользователей в хранилище',
                    'users' => $users
                ]);
        }
    }


    public function actionCreate(): string {
        $render = new Render();
        return $render->renderPageWithForm(
                'user-form.twig', 
                [
                    'title' => 'Форма создания пользователя',
                    'action' => 'save'
                ]);
    }


    public function actionEdit(): string {
        if(User::exists($_POST['id'])) {
            $render = new Render();
            return $render->renderPageWithForm(
                'user-form.twig', 
                [
                    'title' => 'Форма создания пользователя',
                    'action' => 'update',
                    'id' => $_POST['id'],
                    'name' => $_POST['name'],
                    'lastname' => $_POST['lastname'],
                    'birthday' => $_POST['birthday']
                ]);
        }
        else {
            throw new Exception("Пользователь не существует");
        }
    }


    public function actionDelete(): string {
        if(User::exists($_POST['id'])) {
            User::deleteFromStorage($_POST['id']);
            return $this->actionIndex();
        }
        else {
            throw new Exception("Пользователь не существует");
        }
    }


    public function actionSave(): string {
        if(User::validateRequestData()) {
            $user = new User();
            $user->setParamsFromRequestData();
            $user->saveToStorage();
            return $this->actionIndex();
        }
        else {
            throw new Exception("Переданные данные некорректны");
        }
    }

    public function actionUpdate(): string {
        if(User::validateRequestData()) {
            $user = new User();
            $user->setParamsFromRequestData();
            $user->updateInStorage();
            return $this->actionIndex();
        }
        else {
            throw new Exception("Переданные данные некорректны");
        }
    }









    public function actionAuth(): string {
        $render = new Render();
        return $render->renderPageWithForm(
                'user-auth.twig', 
                [
                    'title' => 'Форма логина'
                ]);
    }


    public function actionHash(): string {
        return Auth::getPasswordHash($_GET['pass_string']);
    }

    public function actionLogin(): string {
        $result = false;
        
        if(isset($_POST['login']) && isset($_POST['password'])){
            $result = Application::$auth->proceedAuth($_POST['login'], $_POST['password']);
        }
        
        if(!$result){
            $render = new Render();

            return $render->renderPageWithForm(
                'user-auth.tpl', 
                [
                    'title' => 'Форма логина',
                    'auth-success' => false,
                    'auth-error' => 'Неверные логин или пароль'
                ]);
        }
        else{
            header('Location: /');
            return "";
        }
    }





}