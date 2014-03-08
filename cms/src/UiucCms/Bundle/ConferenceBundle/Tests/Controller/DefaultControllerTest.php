<?php

namespace UiucCms\Bundle\ConferenceBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use UiucCms\Bundle\ConferenceBundle\Form\Type\ConferenceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UiucCms\Bundle\ConferenceBundle\Entity\Conference;

use \DateTime;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
		$this->assertEquals(3,3);
    }
	
	//test that create page exists
	public function testCreate()
	{
		$client = static::createClient();

        $crawler = $client->request('GET', '/conf/create');

        $this->assertTrue($crawler->filter('html:contains("Create a new conference")')->count() > 0);
	}
	
	//test that submit successfully added to database
	public function testSubmit()
  {
      $client = static::createClient();
      $crawler = $client->request('GET', '/conf/create');
      $form = $crawler->selectButton('Create')->form(); 
      
      $form["conference[name]"] = 'Test';
      $form["conference[year]"] = 2014;
      $form["conference[city]"] = 'Champaign';
      $form["conference[register_begin_date][date][year]"] = "2014";
      $form["conference[register_begin_date][date][month]"] = "2";
      $form["conference[register_begin_date][date][day]"] = "31";
      $form["conference[register_end_date][date][year]"] = "2014";
      $form["conference[register_end_date][date][month]"] = "2";
      $form["conference[register_end_date][date][day]"] = "31";

      $crawler = $client->submit($form);
      
      $this->assertTrue($client->getResponse()->isSuccessful());
      $this->assertTrue($crawler->filter('html:contains("Successfully added element")')->count() > 0);
  }
	
}
