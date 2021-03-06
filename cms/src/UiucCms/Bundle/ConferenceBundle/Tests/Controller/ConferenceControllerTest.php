<?php

namespace UiucCms\Bundle\ConferenceBundle\Tests\Controller;

use UiucCms\Bundle\TestUtilityBundle\TestFixtures\FunctionalTestCase;
use UiucCms\Bundle\ConferenceBundle\Form\Type\ConferenceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UiucCms\Bundle\ConferenceBundle\Entity\Conference;
use UiucCms\Bundle\ConferenceBundle\DataFixtures\ORM\Test\LoadConference;
use \Exception;

use \DateTime;
use \DateInterval;

class ConferenceControllerTest extends FunctionalTestCase
{
    private $client;
    private $router;
    private $profile_url;
    private $index_url;
    private $create_conf_url;
    private $view_created_conf_url;
    private $test_conf_url;
    private $direct_enroll_url;
    private $enrolled_in_url;

    private $validName = "RailTEC UIUC";
    private $shortName = "Ra";
    private $validYear = "2014";
    private $validCity = "Champaign";
    private $validTopic = "Trains";
    private $validMaxEnrollment = "5";
    private $validCoverFee = "10.99";
    private $invalidYear = "2013";
    private $invalidEndTime;
    private $lateStartTime; 
    private $validStartTime; 
    private $validEndTime;

    protected function setUp()
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->router = $this->container->get('router');
        $this->index_url = $this->router->generate(
            'uiuc_cms_conference_list',
            array(),
            true
        );
        $this->create_conf_url = $this->router->generate(
            'uiuc_cms_conference_create',
            array(),
            true
        );
     
        $this->view_created_conf_url = $this->router->generate(
            'uiuc_cms_conference_view_created',
            array(),
            true
        );
        $this->test_conf_url = $this->router->generate(
            'uiuc_cms_conference_display',
            array( "id" => 1 ),
            true
        );
        $this->direct_enroll_url = $this->router->generate(
            'uiuc_cms_conference_enrollInfo',
            array( "id" => 1 ),
            true
        );

        $this->invalidEndTime = (new DateTime('now'))
            ->add(DateInterval::createFromDateString('-1 days'));
        $this->lateStartTime = (new DateTime('now'))
            ->add(DateInterval::createFromDateString('10 days'));
        $this->validStartTime = (new DateTime('now'));
        $this->validEndTime = (new DateTime('now'))
            ->add(DateInterval::createFromDateString('5 days'));
    }

    protected static function getDataFixtures()
    {
        $list = parent::getDataFixtures();
        $list[] = new LoadConference();
        return $list;
    }

    private function populateDateForm($form, $startDate, $endDate)
    {
        $form['conference[register_begin_date]'] = $startDate->format('Y-m-d');
        $form['conference[register_end_date]'] = $endDate->format('Y-m-d');
        return $form;
    }

    /**
     * Checks to see that the index page properly lists conferences.
     */
    public function testIndex()
    {
        $this->authenticateUser($this->client);
        $crawler = $this->client->request('GET', $this->index_url);
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Conferences")')->count());
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Rails Conference")')->count());
    }

    /**
     * Checks to see that admins have permission to create conferences.
     */
    public function testCreatePermissionsAdmin()
    {
        $this->authenticateSuperuser($this->client);
        $crawler = $this->client->request('GET', $this->create_conf_url);
        $this->assertTrue(
            $crawler->filter(
                'html:contains("Create a New Conference")')->count() > 0);
    }
    
    /**
     * Checks to see that access is denied for users trying to create a conference.
     */
    public function testCreatePermissionsUser()
    {
        $this->authenticateUser($this->client);
        $crawler = $this->client->request('GET', $this->create_conf_url);
        $this->assertTrue(
            $crawler->filter(
                'html:contains("Access Denied")')->count() > 0);
    }

    /**
     * Checks to see that an admin can view their created conferences.
     */
    public function testViewCreatedPermissionsAdmin()
    {
        $this->authenticateSuperuser($this->client);
        $crawler = $this->client->request('GET', $this->view_created_conf_url);
        $this->assertTrue(
            $crawler->filter(
                'html:contains("Your Conferences")')->count() > 0);
    }
    
    /**
     * Checks to see that a user cant access the page for listing created conferences.
     */
    public function testViewCreatedPermissionsUser()
    {
        $this->authenticateUser($this->client);
        $crawler = $this->client->request('GET', $this->view_created_conf_url);
        $this->assertTrue(
            $crawler->filter(
                'html:contains("Access Denied")')->count() > 0);
    }

    /**
     * Tests that the validator passes for expected output.
     */
    public function testSuccessfulValidator()
    {
        $this->authenticateSuperuser($this->client); 
        $crawler = $this->client->request('GET', $this->create_conf_url);
        $buttonNode = $crawler->selectButton('Create');
        $form = $buttonNode->form();

        $form->disableValidation();

        $form['conference[name]'] = $this->validName;
        $form['conference[year]'] = $this->validYear;
        $form['conference[city]'] = $this->validCity;
        $form['conference[topics]'] = $this->validTopic;
        $form['conference[max_enrollment]'] = $this->validMaxEnrollment;
        $form['conference[cover_fee]'] = $this->validCoverFee;

        $form = $this->populateDateForm(
            $form, 
            $this->validStartTime, 
            $this->validEndTime);
    
        $crawler = $this->client->submit($form);

        $crawler = $this->client->followRedirect(); 

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Details for")')->count());

    }

    /**
     * Tests the validator for making sure a name has been provided.
     */
    public function testNoNameValidator()
    {
        $this->authenticateSuperuser($this->client); 
        $crawler = $this->client->request('GET', $this->create_conf_url);
        $buttonNode = $crawler->selectButton('Create');
        $form = $buttonNode->form();

        $form->disableValidation();

        $form['conference[year]'] = $this->validYear;
        $form['conference[city]'] = $this->validCity;
        $form['conference[topics]'] = $this->validTopic;
        $form['conference[max_enrollment]'] = $this->validMaxEnrollment;
        $form['conference[cover_fee]'] = $this->validCoverFee;

        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("enter a name")')->count());

    }
  
    /**
     * Tests the validator for making sure a name is long enough.
     */
    public function testShortNameValidator()
    {
        $this->authenticateSuperuser($this->client); 
        $crawler = $this->client->request('GET', $this->create_conf_url);
        $buttonNode = $crawler->selectButton('Create');
        $form = $buttonNode->form();

        $form->disableValidation();

        $form['conference[name]'] = $this->shortName;
        $form['conference[year]'] = $this->validYear;
        $form['conference[city]'] = $this->validCity;
        $form['conference[topics]'] = $this->validTopic;
        $form['conference[max_enrollment]'] = $this->validMaxEnrollment;
        $form['conference[cover_fee]'] = $this->validCoverFee;

        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("minimum length 3")')->count());

    }

    /**
     * Tests the validator for making sure a year is set.
     */
    public function testNoYearValidator()
    {
        $this->authenticateSuperuser($this->client); 
        $crawler = $this->client->request('GET', $this->create_conf_url);
        $buttonNode = $crawler->selectButton('Create');
        $form = $buttonNode->form();

        $form->disableValidation();

        $form['conference[name]'] = $this->validName;
        $form['conference[city]'] = $this->validCity;
        $form['conference[topics]'] = $this->validTopic;
        $form['conference[max_enrollment]'] = $this->validMaxEnrollment;
        $form['conference[cover_fee]'] = $this->validCoverFee;

        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("enter a year")')->count());

    }

    /**
     * Tests the validator for making sure a topic is set.
     */
    public function testNoTopicValidator()
    {
        $this->authenticateSuperuser($this->client); 
        $crawler = $this->client->request('GET', $this->create_conf_url);
        $buttonNode = $crawler->selectButton('Create');
        $form = $buttonNode->form();

        $form->disableValidation();

        $form['conference[name]'] = $this->validName;
        $form['conference[year]'] = $this->validYear;
        $form['conference[city]'] = $this->validCity;
        $form['conference[max_enrollment]'] = $this->validMaxEnrollment;
        $form['conference[cover_fee]'] = $this->validCoverFee;

        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("least one topic")')->count());

    }

    /**
     * Tests the validator for catching conference set to occur in the past.
     */
    public function testInvalidStartDate()
    {
        $this->authenticateSuperuser($this->client); 
        $crawler = $this->client->request('GET', $this->create_conf_url);
        $buttonNode = $crawler->selectButton('Create');
        $form = $buttonNode->form();

        $form->disableValidation();

        $form['conference[name]'] = $this->validName;
        $form['conference[year]'] = $this->validYear;
        $form['conference[city]'] = $this->validCity;
        $form['conference[topics]'] = $this->validTopic;
        $form['conference[max_enrollment]'] = $this->validMaxEnrollment;
        $form['conference[cover_fee]'] = $this->validCoverFee;
   
        $form = $this->populateDateForm(
            $form, 
            $this->validStartTime, 
            $this->invalidEndTime);

        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("date in the future")')->count());

    }

    /**
     * Tests the validator for catching registration starting dates that occur
     * after the registration end date.
     */
    public function testLateStartDate()
    {
        $this->authenticateSuperuser($this->client); 
        $crawler = $this->client->request('GET', $this->create_conf_url);
        $buttonNode = $crawler->selectButton('Create');
        $form = $buttonNode->form();

        $form->disableValidation();

        $form['conference[name]'] = $this->validName;
        $form['conference[year]'] = $this->validYear;
        $form['conference[city]'] = $this->validCity;
        $form['conference[topics]'] = $this->validTopic;
        $form['conference[max_enrollment]'] = $this->validMaxEnrollment;
        $form['conference[cover_fee]'] = $this->validCoverFee;

        $form = $this->populateDateForm(
            $form, 
            $this->lateStartTime, 
            $this->validEndTime);

        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("after the start")')->count());

    }

    /**
     * Test the validator to check if the years match for registration and
     * when the conference is set to happen.
     */
    public function testMismatchingYear()
    {
        $this->authenticateSuperuser($this->client); 
        $crawler = $this->client->request('GET', $this->create_conf_url);
        $buttonNode = $crawler->selectButton('Create');
        $form = $buttonNode->form();

        $form->disableValidation();

        $form['conference[name]'] = $this->validName;
        $form['conference[year]'] = $this->invalidYear;
        $form['conference[city]'] = $this->validCity;
        $form['conference[topics]'] = $this->validTopic;
        $form['conference[max_enrollment]'] = $this->validMaxEnrollment;
        $form['conference[cover_fee]'] = $this->validCoverFee;
 
        $form = $this->populateDateForm(
            $form, 
            $this->validStartTime, 
            $this->validEndTime);

        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Year must be")')->count());

    }
    
    /**
     * Checks to see if registration closing logic works.
     */
    public function testRegistrationClosed()
    {
        $this->authenticateUser($this->client);
        $crawler = $this->client->request('GET', $this->test_conf_url);
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("conf.status.closed")')->count());
    }
    
    /**
     * Checks to see if using a direct url to enroll in a closed conference fails.
     */
    public function testDirectRegistrationClosed()
    {
        $this->authenticateUser($this->client);
        $crawler = $this->client->request('GET', $this->direct_enroll_url);
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("conf.status.closed")')->count());
    }
}
