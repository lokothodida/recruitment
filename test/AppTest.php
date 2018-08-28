<?php

use PHPUnit\Framework\TestCase;
use lokothodida\Recruitment\App;
use lokothodida\Recruitment\Domain\Job;
use lokothodida\Recruitment\Domain\JobRepository;

class AppTest extends TestCase
{
	public function setUp()
	{
		$this->jobs = new class() implements JobRepository
		{
			private $jobs = [];

		    public function findById(string $jobId): Job
		    {
		    	return $this->jobs[$jobId];
		    }

		    public function save(Job $job): void
		    {
		    	$this->jobs[$job->id()] = $job;
		    }
		};
		$this->app = new App($this->jobs);
	}

	public function testJobsCanBeOpenedForHiring()
	{
		$this->app->openJobForHiring('software-engineer');
		$job = $this->jobs->findById('software-engineer');

		$this->assertEquals('software-engineer', $job->id());
	}

	public function testApplicantsCanApplyForJobs()
	{
		$this->app->openJobForHiring('software-engineer');
		$this->app->applyForJob('software-engineer', 'person');
		$this->assertTrue($this->jobs->findById('software-engineer')->applicantHasApplied('person'));
	}

	public function testJobsCanBeOfferedToApplicants()
	{
		$this->app->openJobForHiring('software-engineer');
		$this->app->applyForJob('software-engineer', 'person');
		$this->app->offerJobToApplicant('software-engineer', 'person');
		$this->assertTrue($this->jobs->findById('software-engineer')->applicantReceivedOffer('person'));
	}

	public function testApplicantsCanAcceptJobOffers()
	{
		$this->app->openJobForHiring('software-engineer');
		$this->app->applyForJob('software-engineer', 'person');
		$this->app->offerJobToApplicant('software-engineer', 'person');
		$this->app->acceptJobOffer('software-engineer', 'person');
		$this->assertTrue($this->jobs->findById('software-engineer')->applicantHasBeenHired('person'));
	}
}
