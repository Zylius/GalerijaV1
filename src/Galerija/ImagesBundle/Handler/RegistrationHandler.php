<?php

namespace Galerija\ImagesBundle\Handler;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\UserEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Model\UserInterface;
/**
 * Klasė skirta nukreipti užsiregistravusį vartotoją į prieš tai busvusį puslapį
 */
class RegistrationHandler extends ContainerAware implements EventSubscriberInterface
{

    /**
     * Konstruktorius, nustato container'į
     */
    public function __construct($container = null){
        $this->container = $container;
    }

    /**
     * Užregistruoja event'us
     *
     * @return Array registruoti įvykiai
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
            FOSUserEvents::REGISTRATION_INITIALIZE => 'onRegistrationInitialize',
        );
    }

    /**
     * Sėkėmės atveju grąžiname atsaką į prieš tai buvusį puslapį
     *
     * @param FormEvent $event informacija apie pateiktą formą
     */
    public function onRegistrationSuccess(FormEvent $event)
    {
        $event->setResponse(new RedirectResponse($event->getRequest()->headers->get('referer')));
    }

    /**
     * Tik prasidėjus reigstracijai patikrina formą, jei ji neteisinga grąžina vartotoją atgal.
     *
     * @param UserEvent $event informacija apie vartotoją
     */
    public function onRegistrationInitialize(UserEvent $event)
    {
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->container->get('fos_user.registration.form.factory');
        $form = $formFactory->createForm();
        $form->setData($event->getUser());
        if ('POST' === $event->getRequest()->getMethod()) {
            $form->bind($event->getRequest());

            if (!$form->isValid()) {
                $message = $this->getErrorMessages($form);
                $event->getRequest()->getSession()->getFlashBag()->add('error',$message);
                $event->setResponse(new RedirectResponse($event->getRequest()->headers->get('referer')));
            }
        }
    }

    /**
     * Suranda <b>neišverstas</b> formos klaidas.
     *
     * @param \Symfony\Component\Form\Form $form
     * @return String pirmoji rasta klaida
     */
    private function getErrorMessages(\Symfony\Component\Form\Form $form) {
        $message = "Klaida";
        if ($form->count() > 0) {
            foreach ($form->all() as $child) {
                /**
                 * @var \Symfony\Component\Form\Form $child
                 */
                if (!$child->isValid()) {
                    $message = $this->getErrorMessages($child);
                }
            }
        } else {
            /**
             * @var \Symfony\Component\Form\FormError $error
             */
            foreach ($form->getErrors() as $key => $error) {
                if($error->getMessage() != null){
                    $message = $error->getMessageTemplate();
                    return $message;
                }
            }
        }
        return $message;
    }
}