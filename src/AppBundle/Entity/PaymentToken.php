<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Payum\Core\Model\Token;

/**
 * @ORM\Table(name="paymenttoken")
 * @ORM\Entity
 */
class PaymentToken extends Token
{
}