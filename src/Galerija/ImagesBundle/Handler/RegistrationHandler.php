<?php

namespace Galerija\ImagesBundle\Handler;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\UserEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Model\UserInterface;

class RegistrationHandler extends ContainerAware implements EventSubscriberInterface
{
    public function __construct($container = null){
        $this->container = $container;
    }
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
            FOSUserEvents::REGISTRATION_INITIALIZE => 'onRegistrationInitialize',
        );
    }

    public function onRegistrationSuccess(FormEvent $event)
    {
        $event->setResponse(new RedirectResponse($event->getRequest()->headers->get('referer')));
    }

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