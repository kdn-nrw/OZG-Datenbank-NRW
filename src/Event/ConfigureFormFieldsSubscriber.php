<?php


namespace App\Event;


use Mindbase\UserBundle\Event\ConfigureFormFieldsEvent;
use Sonata\CoreBundle\Form\Type\DatePickerType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\VarDumper\VarDumper;

class ConfigureFormFieldsSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ConfigureFormFieldsEvent::class => 'configureUserFields',
        ];
    }

    /**
     * @param ConfigureFormFieldsEvent $event
     */
    public function configureUserFields(ConfigureFormFieldsEvent $event)
    {
        $formMapper = $event->getFormMapper();
        $formMapper->remove('dateOfBirth');
        $now = new \DateTime();
        $formMapper
            ->tab('User')
                ->with('Profile')
                    ->add('website', UrlType::class, ['required' => false])
                    //->add('biography', TextType::class, ['required' => false])
                    ->add('timezone', TimezoneType::class, ['required' => false])
                    ->add('phone', null, ['required' => false])
                    ->add('dateOfBirth', DatePickerType::class, [
                        'years' => range(1900, $now->format('Y')),
                        'dp_min_date' => '1-1-1900',
                        'dp_max_date' => $now->format('c'),
                        'required' => false,
                    ])
                ->end()
            ->end();
    }
}