<?php

namespace UiucCms\Bundle\ConferenceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Conference
 */
class Conference
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $year;

    /**
     * @var string
     */
    private $city;

    /**
     * @var \DateTime
     */
    private $registerBeginDate;

    /**
     * @var \DateTime
     */
    private $registerEndDate;

    /**
     * @var array
     */
    private $topics;

    /**
     * @var integer
     */
    private $maxEnrollment;

    /**
     * @var float
     */
    private $coverFee;

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
     * Set name
     *
     * @param string $name
     * @return Conference
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
     * Set year
     *
     * @param integer $year
     * @return Conference
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Conference
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set registerBeginDate
     *
     * @param \DateTime $registerBeginDate
     * @return Conference
     */
    public function setRegisterBeginDate($registerBeginDate)
    {
        $this->registerBeginDate = $registerBeginDate;

        return $this;
    }

    /**
     * Get registerBeginDate
     *
     * @return \DateTime 
     */
    public function getRegisterBeginDate()
    {
        return $this->registerBeginDate;
    }

    /**
     * Set registerEndDate
     *
     * @param \DateTime $registerEndDate
     * @return Conference
     */
    public function setRegisterEndDate($registerEndDate)
    {
        $this->registerEndDate = $registerEndDate;

        return $this;
    }

    /**
     * Get registerEndDate
     *
     * @return \DateTime 
     */
    public function getRegisterEndDate()
    {
        return $this->registerEndDate;
    }

    /**
     * Set topics
     *
     * @param array $topics
     * @return Conference
     */
    public function setTopics($topics)
    {
        $this->topics = $topics;

        return $this;
    }

    /**
     * Get topics
     *
     * @return array 
     */
    public function getTopics()
    {
        return $this->topics;
    }
    /**
     * @var integer
     */
    private $createdBy;


    /**
     * Set createdBy
     *
     * @param integer $createdBy
     * @return Conference
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return integer 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set maxEnrollment
     *
     * @param integer $maxEnrollment
     * @return Conference
     */
    public function setMaxEnrollment($maxEnrollment)
    {
        $this->maxEnrollment = $maxEnrollment;

        return $this;
    }

    /**
     * Get maxEnrollment
     *
     * @return integer 
     */
    public function getMaxEnrollment()
    {
        return $this->maxEnrollment;
    }

    /**
     * Set coverFee
     *
     * @param float $coverFee
     * @return Conference
     */
    public function setCoverFee($coverFee)
    {
        $this->coverFee = $coverFee;

        return $this;
    }

    /**
     * Get coverFee
     *
     * @return float 
     */
    public function getCoverFee()
    {
        return $this->coverFee;
    }
}
