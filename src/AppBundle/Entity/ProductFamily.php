<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductFamily
 *
 * @ORM\Table(name="product_family")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductFamilyRepository")
 */
class ProductFamily
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string",length=255, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string",length=4, unique=true)
     */
    private $code;

    /**
     * @var bool
     *
     * @ORM\Column(name="has_size", type="boolean")
     */
    private $hasSize;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return ProductFamily
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }




    /**
     * Set hasSize
     *
     * @param boolean $hasSize
     *
     * @return ProductFamily
     */
    public function setHasSize($hasSize)
    {
        $this->hasSize = $hasSize;

        return $this;
    }

    /**
     * Get hasSize
     *
     * @return boolean
     */
    public function getHasSize()
    {
        return $this->hasSize;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return ProductFamily
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}
