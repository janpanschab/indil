<?php

namespace AdminModule;

use Nette\Diagnostics\Debugger;

class MediaPresenter extends BasePresenter {
    
    public function renderDefault($folder = 0) {
        $this->user->identity->folder = $folder;
        
        $media = $this->context->createMedia();

        // crumbs
        $this->template->crumbs = $media->getCrumbs($this->user->identity->folder);

        // parent folder
        $this->template->parentFolder = $media->getParentFolder($this->user->identity->folder);
        
        // folders
        $this->template->folders = $media->getFolders($this->user->identity->folder);

        // files
        $this->template->files = $media->getFiles($this->user->identity->folder);

        // layout
        $this->template->layoutTpl = '../@layout.latte';
    }
    
    public function renderInsert($folder) {
        if (isset($folder)) {
            $this->user->identity->folder = $folder;
        }
        $this->renderDefault($this->user->identity->folder);
        
        // layout
        $this->template->layoutTpl = '../@iframe.latte';
    }
    
    public function renderOptions($id) {
        $this->template->file = $this->context->createFile($id);
        $this->template->imageTypes = \Nette\Utils\Neon::decode(file_get_contents(APP_DIR .'/config/image.neon'));
    }
        
    /**
     * FOLDERS
     */
    
    public function handleAddFolder($name) {
        $media = $this->context->createMedia();
        
        try {
            $media->addFolder($name, $this->user->identity->folder);
            $this->flashMessage('Nová složka byla vytvořena.', 'success');
        } catch (\PDOException $e) {
            switch ($e->getCode()) {
                case 23000: $this->flashMessage('Složka s tímto názvem už existuje.', 'error'); break;
                default: $this->flashMessage('Nepodařilo se vytvořit novou složku.', 'error');
            }
        }
        
        $this->redirect('this');
    }
    
    public function handleFolderDelete($id) {
        $folder = $this->context->createFolder($id);
        try {
            $folder->delete();
        } catch (\Indil\MediaException $e) {
            $this->flashMessage($e->getMessage(), 'error');
        }
        
        $this->redirect('this');
    }

    public function handleFolderRename($id, $newname) {
        $media = $this->context->createMedia();
        try {
            $media->renameFolder($id, $newname);
            $this->flashMessage('Složka byla přejmenována.', 'success');
        } catch (\PDOException $e) {
            $this->flashMessage('Složku se nepodařilo přejmenovat.', Debugger::ERROR);
        }
        
        $this->redirect('this');
    }
    
    
    /**
     * FILES
     */
    
    public function handleFileDelete($id) {
        $file = $this->context->createFile($id);
        $file->delete();
        
        $this->redirect('this');
    }

    public function handleFileRename($id, $newname) {
        $media = $this->context->createMedia();
        try {
            $media->renameFile($id, $newname);
            $this->flashMessage('Soubor byl přejmenován.', 'success');
        } catch (\PDOException $e) {
            $this->flashMessage('Soubor se nepodařilo přejmenovat.', Debugger::ERROR);
        }
        
        $this->redirect('this');
    }

    public function renderUpload() {
        $media = $this->context->createMedia();
        $this->template->folders = $media->getAllFolders();
    }

    public function handleFileuploader() {
        $media = $this->context->createMedia();
        $result = $media->upload($this->user->identity->folder);
        echo $result;
        $this->terminate();
    }

}