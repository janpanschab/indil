<?php

namespace Indil\Config;

use \Nette\Utils\Html;

class Texy extends \Texy {
    
    const   LEFT = '<',
            RIGHT = '>',
            ONLY_LINK = TRUE,
            NO_LINK = FALSE;

    /** @var Nette\DI\IContainer */
    public $context;

    public function __construct(\Nette\DI\IContainer $context) {
        parent::__construct();
        $this->context = $context;
    }

    function scriptHandler($invocation, $cmd, $args, $raw) {
        switch ($cmd) {
            case 'img':
                // arguments: id, type, [align, [link]]
                $texy = $invocation->getTexy();
                $image = $this->context->createFile($args[0]); // id
                $link = TRUE;
                $lightbox = TRUE;
                $align = FALSE;
                
                if (isset($args[2])) {
                    switch ($args[2]) {
                        case self::LEFT:
                            $align = 'left';
                            break;
                        case self::RIGHT:
                            $align = 'right';
                            break;
                    }
                    
                    if (isset($args[3])) {
                        switch ($args[3]) {
                            case self::ONLY_LINK:
                                $link = TRUE;
                                $lightbox = FALSE;
                                break;
                            case self::NO_LINK:
                                $link = FALSE;
                                $lightbox = FALSE;
                                break;
                        }
                    }
                }
                $img = Html::el('img')->addAttributes(array(
                    'src' => $image->getLink($args[1]),
                    'alt' => $image->name,
                ));
                if ($align) {
                    $img->addAttributes(array(
                        'class' => $align
                    ));
                }
                if ($link) {
                    $code = Html::el('a')->addAttributes(array(
                        'href' => $image->getLink('lightbox'),
                        'title' => $image->name
                    ));
                    if ($lightbox) {
                        $code->addAttributes(array(
                            'data-lightbox' => 'fancybox'
                        ));
                    }
                    $code->add($img);
                } else {
                    $code = $img;
                }
                return $texy->protect($code, Texy::CONTENT_REPLACED);
            
            case 'gallery':
                $texy = $invocation->getTexy();
                $mediaModel = $this->context->createMedia();
                
                $template = new \Nette\Templating\FileTemplate(LIBS_DIR . '/indil/config/templates/gallery.latte');
                //$template->setCacheStorage(new \Nette\Caching\Storages\PhpFileStorage('temp'));
                $template->onPrepareFilters[] = function($template) {
                    $template->registerFilter(new \Nette\Latte\Engine);
                };
                
                $images = array();
                foreach ($mediaModel->getFiles($args[0]) as $i => $img) {
                    $image = $this->context->createFile($img->id);
                    if ($image->isImage()) {
                        $images[] = $image;
                    }
                }
                $template->images = $images;
                $code = $template->render();
                
                return $texy->protect($code, Texy::CONTENT_REPLACED);

            case 'doc':
                // arguments: id
                $texy = $invocation->getTexy();
                $doc = $this->context->createFile($args[0]);
                $code = Html::el('a')->href($doc->url)->setText($doc->name);
                return $texy->protect($code, Texy::CONTENT_REPLACED);
                
            case 'youtube':
                $code = '<iframe title="YouTube přehrávač" width="332" height="270" src="http://www.youtube.com/embed/' . $args[0] . '" frameborder="0" allowfullscreen></iframe>';
                return $invocation->getTexy()->protect($code, Texy::CONTENT_BLOCK);

            default: // neumime zpracovat, zavolame dalsi handler v rade
                return $invocation->proceed();
        }
    }

    function imageHandler($invocation, $image) {
        $texy = $invocation->getTexy();
        if (String::startsWith($image->URL, '/')) {
            $url = $image->URL;
        } else {
            $url = $texy->prependRoot($image->URL, $texy->imageModule->root);
        }
        $link = str_replace('_nahled', '', $url);
        $thumb = Image::fromFile(WWW_DIR . $url);
        $width = $image->width ? $image->width : $thumb->getWidth();
        $height = $image->height ? $image->height : $thumb->getHeight();

        $code = '<a href="' . $link . '" title="' . $image->modifier->title . '" rel="lightbox">'
                . '<img src="' . $url . '" alt="' . $image->modifier->title . '" class="' . $image->modifier->hAlign . '" width="' . $width . '" height="' . $height . '" />'
                . '</a>';

        return $texy->protect($code, Texy::FILTER_IMAGE);
    }

}