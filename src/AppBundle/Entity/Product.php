<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 */
class Product
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
     * @var \DateTime
     *
     * @ORM\Column(name="date_added", type="datetime")
     */
    private $dateAdded;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string",length=255, unique=true)
     */
    private $name;


    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text",nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    private $priceUnit;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Image", mappedBy="product", cascade={"persist"})
    */
    private $images;


    /**
     * @var string
     *
     * @ORM\Column(name="img_star_name", type="string", length=255,nullable=true)
     */
    private $imgStarName;  //Permet de ne pas charger toutes les images pour afficher la vedette


    /**
     * @var string
     *
     * @ORM\Column(name="img_star_back_name", type="string", length=255,nullable=true)
     */
    private $imgStarBackName;  //Image on hover

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ProductFamily")
     * @ORM\JoinColumn(nullable=false)
    */
    private $family;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\SizeProduct")
    */
    private $sizes;


    /**
     * @var bool
     *
     * @ORM\Column(name="is_hidden", type="boolean")
     */
    private $isHidden;



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
     * Constructor
     */
    public function __construct()
    {
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sizes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->dateAdded = new \DateTime();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Product
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
     * Set priceUnit
     *
     * @param string $priceUnit
     *
     * @return Product
     */
    public function setPriceUnit($priceUnit)
    {
        $this->priceUnit = $priceUnit;

        return $this;
    }

    /**
     * Get priceUnit
     *
     * @return string
     */
    public function getPriceUnit()
    {
        return $this->priceUnit;
    }

    /**
     * Set imgStarName
     *
     * @param string $imgStarName
     *
     * @return Product
     */
    public function setImgStarName($imgStarName)
    {
        $this->imgStarName = $imgStarName;

        return $this;
    }

    /**
     * Get imgStarName
     *
     * @return string
     */
    public function getImgStarName()
    {
        return $this->imgStarName;
    }

    /**
     * Set isHidden
     *
     * @param boolean $isHidden
     *
     * @return Product
     */
    public function setIsHidden($isHidden)
    {
        $this->isHidden = $isHidden;

        return $this;
    }

    /**
     * Get isHidden
     *
     * @return boolean
     */
    public function getIsHidden()
    {
        return $this->isHidden;
    }

    /**
     * Add image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return Product
     */
    public function addImage(\AppBundle\Entity\Image $image)
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param \AppBundle\Entity\Image $image
     */
    public function removeImage(\AppBundle\Entity\Image $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Set family
     *
     * @param \AppBundle\Entity\ProductFamily $family
     *
     * @return Product
     */
    public function setFamily(\AppBundle\Entity\ProductFamily $family)
    {
        $this->family = $family;

        return $this;
    }

    /**
     * Get family
     *
     * @return \AppBundle\Entity\ProductFamily
     */
    public function getFamily()
    {
        return $this->family;
    }


    /**
     * Set description
     *
     * @param string $description
     *
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set imgStarBackName
     *
     * @param string $imgStarBackName
     *
     * @return Product
     */
    public function setImgStarBackName($imgStarBackName)
    {
        $this->imgStarBackName = $imgStarBackName;

        return $this;
    }

    /**
     * Get imgStarBackName
     *
     * @return string
     */
    public function getImgStarBackName()
    {
        return $this->imgStarBackName;
    }

    /**
     * Add size
     *
     * @param \AppBundle\Entity\SizeProduct $size
     *
     * @return Product
     */
    public function addSize(\AppBundle\Entity\SizeProduct $size)
    {
        $this->sizes[] = $size;

        return $this;
    }

    /**
     * Remove size
     *
     * @param \AppBundle\Entity\SizeProduct $size
     */
    public function removeSize(\AppBundle\Entity\SizeProduct $size)
    {
        $this->sizes->removeElement($size);
    }

    /**
     * Get sizes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSizes()
    {
        return $this->sizes;
    }

    /**
     * Set $dateAdded
     *
     * @param \DateTime $dateAdded
     *
     * @return Product
     */
    public function setDateAdded($dateAdded)
    {
        $this->$dateAdded = $dateAdded;

        return $this;
    }

    /**
     * Get $dateAdded
     *
     * @return \DateTime
     */
    public function getDateAdded()
    {
        return $this->$dateAdded;
    }

}
