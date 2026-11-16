<?php

namespace App\Form;

use App\Dto\RepairRequest;
use App\Entity\Ship;
use App\Enum\TicketPriority;
use App\Repository\ShipRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * @extends AbstractType<RepairRequest>
 */
final class BookRepairType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ship', EntityType::class, [
                'class' => Ship::class,
                'mapped' => false,
                'data' => $options['ship'],
                'placeholder' => 'Choose a ship',
                'query_builder' => static fn (ShipRepository $ships): QueryBuilder => $ships->createQueryBuilder('ship')->orderBy('ship.name'),
                'constraints' => [new NotNull(message: 'Which ship needs fixing?')],
            ])
            ->add('reporterEmail', EmailType::class, [
                'label' => 'Your email',
            ])
            ->add('priority', EnumType::class, [
                'class' => TicketPriority::class,
                'expanded' => true,
                'choice_label' => static fn (TicketPriority $priority): string => $priority->label(),
            ])
            ->add('description', TextareaType::class, [
                'label' => 'What\'s wrong?',
                'attr' => ['rows' => 5],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RepairRequest::class,
            'ship' => null,
        ]);
        $resolver->setAllowedTypes('ship', [Ship::class, 'null']);
    }
}
