<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Purchase
 *
 * @ORM\Table(name="purchase")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PurchaseRepository")
 */
class Purchase
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
     * @ORM\Column(name="date_realisation", type="datetime")
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_de_livraison", type="datetime",nullable=true)
     */
    private $delivery_date;

    /**
     * @var string
     * @ORM\Column(name="code", type="string")
     */
    private $code;

    /**
     * @var string
     * @ORM\Column(name="delivery_code", type="string",nullable=true)
     */
    private $deliveryCode;


    /**
     * @var bool
     *
     * @ORM\Column(name="is_delivered", type="boolean")
     */
    private $isDelivered;

    /**
     * @ORM\Column(name="products", type="array",nullable=false)
     */
    private $products;


    /**
     * @var string
     * @ORM\Column(name="customer_firstname", type="string")
     */
    private $customerFirstname;

    /**
     * @var string
     * @ORM\Column(name="customer_lastname", type="string")
     */
    private $customerLastname;

    /**
     * @var string
     * @ORM\Column(name="customer_email", type="string")
     */
    private $customerEmail;

    /**
     * @var string
     * @ORM\Column(name="customer_address1", type="string")
     */
    private $customerAddress1;

    /**
     * @var string
     * @ORM\Column(name="customer_address2", type="string",nullable=true)
     */
    private $customerAddress2;

    /**
     * @var string
     * @ORM\Column(name="customer_country", type="string")
     */
    private $customerCountry;

    /**
     * @var string
     * @ORM\Column(name="customer_city", type="string")
     */
    private $customerCity;

    /**
     * @var string
     * @ORM\Column(name="customer_postalCode", type="string",length=255)
     */
    private $customerPostalCode;

    /**
     * Constructor
     */
    public function __construct()
    {
      $this->date = new \DateTime();
      $this->code = uniqid('beone',false);
    }



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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Purchase
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set isDelivered
     *
     * @param boolean $isDelivered
     *
     * @return Purchase
     */
    public function setIsDelivered($isDelivered)
    {
        $this->isDelivered = $isDelivered;

        return $this;
    }

    /**
     * Get isDelivered
     *
     * @return boolean
     */
    public function getIsDelivered()
    {
        return $this->isDelivered;
    }

    /**
     * Set products
     *
     * @param array $products
     *
     * @return Purchase
     */
    public function setProducts($products)
    {
        $this->products = $products;

        return $this;
    }

    /**
     * Get products
     *
     * @return array
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Set customerFirstname
     *
     * @param string $customerFirstname
     *
     * @return Purchase
     */
    public function setCustomerFirstname($customerFirstname)
    {
        $this->customerFirstname = $customerFirstname;

        return $this;
    }

    /**
     * Get customerFirstname
     *
     * @return string
     */
    public function getCustomerFirstname()
    {
        return $this->customerFirstname;
    }

    /**
     * Set customerLastname
     *
     * @param string $customerLastname
     *
     * @return Purchase
     */
    public function setCustomerLastname($customerLastname)
    {
        $this->customerLastname = $customerLastname;

        return $this;
    }

    /**
     * Get customerLastname
     *
     * @return string
     */
    public function getCustomerLastname()
    {
        return $this->customerLastname;
    }

    /**
     * Set customerAddress1
     *
     * @param string $customerAddress1
     *
     * @return Purchase
     */
    public function setCustomerAddress1($customerAddress1)
    {
        $this->customerAddress1 = $customerAddress1;

        return $this;
    }

    /**
     * Get customerAddress1
     *
     * @return string
     */
    public function getCustomerAddress1()
    {
        return $this->customerAddress1;
    }

    /**
     * Set customerAddress2
     *
     * @param string $customerAddress2
     *
     * @return Purchase
     */
    public function setCustomerAddress2($customerAddress2)
    {
        $this->customerAddress2 = $customerAddress2;

        return $this;
    }

    /**
     * Get customerAddress2
     *
     * @return string
     */
    public function getCustomerAddress2()
    {
        return $this->customerAddress2;
    }

    /**
     * Set customerCountry
     *
     * @param string $customerCountry
     *
     * @return Purchase
     */
    public function setCustomerCountry($customerCountry)
    {
        $this->customerCountry = $customerCountry;

        return $this;
    }

    /**
     * Get customerCountry
     *
     * @return string
     */
    public function getCustomerCountry()
    {
        return $this->customerCountry;
    }

    /**
     * Set customerCity
     *
     * @param string $customerCity
     *
     * @return Purchase
     */
    public function setCustomerCity($customerCity)
    {
        $this->customerCity = $customerCity;

        return $this;
    }

    /**
     * Get customerCity
     *
     * @return string
     */
    public function getCustomerCity()
    {
        return $this->customerCity;
    }

    /**
     * Set customerPostalCode
     *
     * @param string $customerPostalCode
     *
     * @return Purchase
     */
    public function setCustomerPostalCode($customerPostalCode)
    {
        $this->customerPostalCode = $customerPostalCode;

        return $this;
    }

    /**
     * Get customerPostalCode
     *
     * @return string
     */
    public function getCustomerPostalCode()
    {
        return $this->customerPostalCode;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Purchase
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

    /**
     * Set customerEmail
     *
     * @param string $customerEmail
     *
     * @return Purchase
     */
    public function setCustomerEmail($customerEmail)
    {
        $this->customerEmail = $customerEmail;

        return $this;
    }

    /**
     * Get customerEmail
     *
     * @return string
     */
    public function getCustomerEmail()
    {
        return $this->customerEmail;
    }

    /**
     * Set deliveryDate
     *
     * @param \DateTime $deliveryDate
     *
     * @return Purchase
     */
    public function setDeliveryDate($deliveryDate)
    {
        $this->delivery_date = $deliveryDate;

        return $this;
    }

    /**
     * Get deliveryDate
     *
     * @return \DateTime
     */
    public function getDeliveryDate()
    {
        return $this->delivery_date;
    }

    /**
     * Set deliveryCode
     *
     * @param string $deliveryCode
     *
     * @return Purchase
     */
    public function setDeliveryCode($deliveryCode)
    {
        $this->deliveryCode = $deliveryCode;

        return $this;
    }

    /**
     * Get deliveryCode
     *
     * @return string
     */
    public function getDeliveryCode()
    {
        return $this->deliveryCode;
    }
}
