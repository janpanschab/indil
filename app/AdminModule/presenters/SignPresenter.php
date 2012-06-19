<?php

namespace AdminModule;

use Nette\Security as NS,
    Indil\Form;

class SignPresenter extends \BasePresenter {

    /** @persistent */
    public $backlink = '';
    
    public function actionIn() {
        $this->template->form = $this['signInForm'];
    }
    
    public function actionOut() {
        $this->getUser()->logout();
        $this->flashMessage('Byli jste odhlášeni.');
        $this->redirect('in');
    }

    protected function createComponentSignInForm() {
        $form = new Form;
        $form->addText('email', 'E-mail:')
                ->setRequired('Vyplňte svůj e-mail.')
                ->addRule(Form::EMAIL, 'E-mail není ve správném tvaru.');
        $form->addPassword('password', 'Heslo:')
                ->setRequired('Vyplňte své heslo.')
                ->addRule(Form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků.', 6);
        $form->addCheckbox('remember', 'Trvalé přihlášení');
        $form->addSubmit('signIn', 'Přihlásit se');
        $form->onSuccess[] = callback($this, 'signInFormSubmitted');
        
        return $form;
    }

    public function signInFormSubmitted($form) {
        try {
            $values = $form->getValues();
            $this->user->login($values->email, $values->password);
            $this->application->restoreRequest($this->backlink);
            $this->redirect('Default:');
        } catch (NS\AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }

}
