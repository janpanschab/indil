<?php

namespace AdminModule;

use Indil\Form;

class ConcertPresenter extends BasePresenter {

    public function renderDefault() {
        $concertModel = $this->context->createConcert();
        $this->template->planned = $concertModel->listPlanned();
    }

    public function actionEdit($id) {
        $concertModel = $this->context->createConcert();
        $concert = $concertModel->get($id);
        
        if (isset($concert['date'])) {
            $concert['date'] = $concert['date']->format('d. m. Y H:i');
        }
        
        if ($concert) {
            $this['concertEdit']->setDefaults($concert);
        }
        $this->template->form = $this['concertEdit'];
    }
    
    protected function createComponentConcertEdit() {
        $form = new Form;
        $form->addText('date', 'Datum')
                ->setDefaultValue(date('j. n. Y') . ' 20:00')
                ->addRule(Form::FILLED, 'Vyplňte datum článku')
                ->getControlPrototype()->class('datum');
        $form->addText('city', 'Město')
                ->addRule(Form::FILLED, 'Vyplňte město kde se koncert koná');
        $form->addText('place', 'Místo');
        $form->addTextArea('info');
        $form->addSubmit('send', 'Uložit')
                ->getControlPrototype()->class('flr');
        $form->onSuccess[] = array($this, 'submitConcertEdit');
        return $form;
    }

    public function submitConcertEdit($form) {
        if ($form['send']->isSubmittedBy()) {
            $id = $this->getParam('id');
            $values = $form->getValues();
            $values['date'] = $this->dateToDB($values['date']);
            $concertModel = $this->context->createConcert();
            if ($id) {
                $concertModel->update($id, $values);
                $this->flashMessage('Článek byl změněn', 'positive');
            } else {
                $concertModel->insert($values);
                $this->flashMessage('Článek byl uložen', 'positive');
            }
        }
        $this->redirect('default');
    }

}