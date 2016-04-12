<?php

namespace AppBundle\Entity;

use Knp\JsonSchemaBundle\Annotations as Json;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table()
 * @Json\Schema("bla")
 */
class Bla
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @Groups({"basic"}) 
     * @Json\Ignore
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\Type(type="string")
     * @Groups({"basic"})
     */
    private $nombre;

    /**
     * @ORM\OneToMany(targetEntity="Ble", mappedBy="mi_bla", cascade={"merge"})
     * @Groups({"basic"})
     */
    private $mis_ble;

 
    public function __construct($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return EstadoTurno
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }
}
